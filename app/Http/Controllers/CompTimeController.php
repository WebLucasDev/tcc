<?php

namespace App\Http\Controllers;

use App\Enums\CollaboratorStatusEnum;
use App\Models\CollaboratorModel;
use App\Models\TimeTrackingModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CompTimeController extends Controller
{
    public function index(Request $request)
    {
        // Filtros
        $collaboratorId = $request->get('collaborator_id');
        $month = $request->get('month', now()->format('Y-m'));

        // Buscar todos os colaboradores para o filtro
        $allCollaborators = CollaboratorModel::where('status', CollaboratorStatusEnum::ACTIVE)
            ->orderBy('name')
            ->get();

        // Calcular dados do banco de horas (agora seguindo CLT - 44h semanais)
        $compTimeData = $this->calculateCompTimeDataCLT($collaboratorId, $month);

        // Calcular resumo dos dados
        $summary = $this->calculateSummary($compTimeData);

        $breadcrumbs = [
            ['label' => 'Gestão de Ponto', 'url' => null],
            ['label' => 'Banco de Horas', 'url' => null]
        ];

        // Se for requisição AJAX, retornar apenas os dados necessários
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => [
                    'summary' => view('auth.time-management.comp-time.partials.summary', compact('compTimeData', 'summary'))->render(),
                    'table' => view('auth.time-management.comp-time.partials.table', compact('compTimeData'))->render(),
                ]
            ]);
        }

        return view('auth.time-management.comp-time.index', compact(
            'breadcrumbs',
            'allCollaborators',
            'compTimeData',
            'summary',
            'collaboratorId',
            'month'
        ));
    }

    /**
     * Calcula os dados do banco de horas seguindo a CLT brasileira (44h semanais)
     */
    private function calculateCompTimeDataCLT($collaboratorId = null, $month = null)
    {
        // Configurar período de análise (mês completo para mostrar todas as semanas)
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        // Query base dos colaboradores
        $collaboratorsQuery = CollaboratorModel::with('workHours')
            ->where('status', \App\Enums\CollaboratorStatusEnum::ACTIVE);

        if ($collaboratorId) {
            $collaboratorsQuery->where('id', $collaboratorId);
        }

        $collaborators = $collaboratorsQuery->orderBy('name')->get();

        $compTimeData = [];

        foreach ($collaborators as $collaborator) {
            $bankHours = $this->calculateCollaboratorBankHoursCLT($collaborator, $startDate, $endDate);

            $compTimeData[] = [
                'collaborator' => $collaborator,
                'bank_hours' => $bankHours
            ];
        }

        return $compTimeData;
    }

    /**
     * Calcula o resumo dos dados do banco de horas
     */
    private function calculateSummary($compTimeData)
    {
        $summary = [
            'total_collaborators' => 0,
            'total_positive_minutes' => 0,
            'total_negative_minutes' => 0,
            'collaborators_with_positive_bank' => 0,
            'collaborators_with_negative_bank' => 0,
            'net_balance_minutes' => 0,
            'total_worked_minutes' => 0,
            'total_expected_minutes' => 0
        ];

        if (is_array($compTimeData) && count($compTimeData) > 0) {
            $summary['total_collaborators'] = count($compTimeData);

            foreach ($compTimeData as $data) {
                $balance = $data['bank_hours']['bank_balance_minutes'] ?? 0;
                $worked = $data['bank_hours']['total_worked_minutes'] ?? 0;

                $summary['total_worked_minutes'] += $worked;

                // Calcular total esperado baseado na jornada individual
                $expectedMinutes = 0;
                if (isset($data['bank_hours']['weekly_details'])) {
                    foreach ($data['bank_hours']['weekly_details'] as $week) {
                        $expectedMinutes += $week['expected_minutes'] ?? 0;
                    }
                }
                $summary['total_expected_minutes'] += $expectedMinutes;

                if ($balance > 0) {
                    $summary['total_positive_minutes'] += $balance;
                    $summary['collaborators_with_positive_bank']++;
                } elseif ($balance < 0) {
                    $summary['total_negative_minutes'] += abs($balance);
                    $summary['collaborators_with_negative_bank']++;
                }
            }

            $summary['net_balance_minutes'] = $summary['total_positive_minutes'] - $summary['total_negative_minutes'];
        }

        return $summary;
    }    /**
     * Calcula o banco de horas de um colaborador seguindo a CLT (44h semanais)
     */
    private function calculateCollaboratorBankHoursCLT($collaborator, $startDate, $endDate)
    {
        // Verificar se o colaborador tem jornada de trabalho definida
        if (!$collaborator->workHours) {
            return $this->getEmptyBankHoursData($startDate, $endDate);
        }

        // Buscar todos os registros do período
        $timeTrackings = TimeTrackingModel::where('collaborator_id', $collaborator->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get()
            ->keyBy(function($item) {
                return $item->date->format('Y-m-d');
            });

        // Calcular limite semanal baseado na jornada individual do colaborador
        $weeklyLimitMinutes = $this->calculateCollaboratorWeeklyLimitMinutes($collaborator);

        $totalBankBalance = 0;
        $totalWorkedMinutes = 0;
        $totalWeeksAnalyzed = 0;
        $weeklyDetails = [];
        $recentDays = [];

        // Encontrar o primeiro domingo do período para começar as semanas
        $currentDate = $startDate->copy();
        while ($currentDate->dayOfWeek !== Carbon::SUNDAY) {
            $currentDate->subDay();
        }

        // Processar semana por semana (Domingo a Sábado)
        while ($currentDate <= $endDate) {
            $weekStart = $currentDate->copy();
            $weekEnd = $currentDate->copy()->addDays(6);

            // Se a semana tem algum dia dentro do período analisado
            if ($weekEnd >= $startDate && $weekStart <= $endDate) {
                $weekWorkedMinutes = 0;
                $weekExpectedMinutes = 0;
                $weekDays = [];

                // Processar cada dia da semana (Domingo a Sábado)
                for ($dayOffset = 0; $dayOffset < 7; $dayOffset++) {
                    $day = $weekStart->copy()->addDays($dayOffset);

                    // Só processar se o dia está dentro do período
                    if ($day >= $startDate && $day <= $endDate) {
                        $dayKey = $day->format('Y-m-d');
                        $tracking = $timeTrackings->get($dayKey);

                        // Calcular horas esperadas para o dia baseado na jornada
                        $expectedDailyMinutes = $this->getExpectedDailyMinutes($collaborator, $day);

                        $dayWorkedMinutes = 0;
                        if ($tracking) {
                            $dayWorkedMinutes = $tracking->total_hours_worked ?? 0;
                        }

                        $weekWorkedMinutes += $dayWorkedMinutes;
                        $weekExpectedMinutes += $expectedDailyMinutes;

                        $weekDays[] = [
                            'date' => $day->copy(),
                            'tracking' => $tracking,
                            'worked_minutes' => $dayWorkedMinutes,
                            'expected_minutes' => $expectedDailyMinutes,
                            'difference_minutes' => $dayWorkedMinutes - $expectedDailyMinutes,
                            'is_weekend' => $day->isWeekend(),
                            'day_name' => $day->locale('pt_BR')->dayName
                        ];

                        // Adicionar aos dias recentes para exibição
                        if ($tracking || $expectedDailyMinutes > 0) {
                            $recentDays[] = [
                                'date' => $day->copy(),
                                'tracking' => $tracking,
                                'worked_minutes' => $dayWorkedMinutes,
                                'expected_minutes' => $expectedDailyMinutes,
                                'difference_minutes' => $dayWorkedMinutes - $expectedDailyMinutes,
                                'status' => $tracking ? $tracking->status : 'ausente',
                                'is_weekend' => $day->isWeekend(),
                                'day_name' => $day->locale('pt_BR')->dayName
                            ];
                        }
                    }
                }

                // Calcular banco de horas da semana
                // CLT: Limite de 44h semanais, mas respeitando a jornada individual
                $weekBankBalance = 0;

                // Só calcula se houver pelo menos um registro ou dia esperado de trabalho
                if ($weekExpectedMinutes > 0 || $weekWorkedMinutes > 0) {
                    // Diferença entre trabalhado e esperado
                    $weekBankBalance = $weekWorkedMinutes - $weekExpectedMinutes;

                    // CLT: Máximo 2h extras por dia, 10h por semana
                    // Se excedeu muito, ajustar (isso é uma proteção)
                    $maxWeeklyOvertime = 10 * 60; // 10 horas em minutos
                    if ($weekBankBalance > $maxWeeklyOvertime) {
                        $weekBankBalance = $maxWeeklyOvertime;
                    }
                }

                $totalBankBalance += $weekBankBalance;
                $totalWorkedMinutes += $weekWorkedMinutes;
                $totalWeeksAnalyzed++;

                $weeklyDetails[] = [
                    'week_start' => $weekStart->copy(),
                    'week_end' => $weekEnd->copy(),
                    'worked_minutes' => $weekWorkedMinutes,
                    'expected_minutes' => $weekExpectedMinutes,
                    'limit_minutes' => $weeklyLimitMinutes,
                    'bank_balance' => $weekBankBalance,
                    'days' => $weekDays
                ];
            }

            // Próxima semana
            $currentDate->addWeek();
        }

        // Calcular total de minutos esperados baseado nas jornadas reais
        $totalExpectedMinutes = 0;
        foreach ($weeklyDetails as $week) {
            $totalExpectedMinutes += $week['expected_minutes'] ?? 0;
        }

        // Calcular média diária esperada
        $totalDaysWithExpectation = collect($recentDays)->where('expected_minutes', '>', 0)->count();
        $standardDailyMinutes = $totalDaysWithExpectation > 0 ?
            round($totalExpectedMinutes / $totalDaysWithExpectation) :
            $this->calculateStandardDailyMinutes($collaborator);

        return [
            'period_start' => $startDate,
            'period_end' => $endDate,
            'total_weeks_analyzed' => $totalWeeksAnalyzed,
            'total_worked_minutes' => $totalWorkedMinutes,
            'weekly_limit_minutes' => $weeklyLimitMinutes,
            'bank_balance_minutes' => $totalBankBalance,
            'weekly_details' => $weeklyDetails,
            'work_days' => collect($recentDays)->sortByDesc('date')->values()->toArray(),
            'work_days_count' => collect($recentDays)->count(),
            'total_standard_minutes' => $totalExpectedMinutes,
            'standard_daily_minutes' => $standardDailyMinutes,
            'collaborator_weekly_minutes' => $this->calculateCollaboratorWeeklyLimitMinutes($collaborator)
        ];
    }

    /**
     * [MÉTODO ANTERIOR - MANTIDO PARA REFERÊNCIA]
     * Calcula o banco de horas de um colaborador específico (método antigo)
     * ATENÇÃO: Este método não segue a CLT. Use calculateCollaboratorBankHoursCLT()
     */
    private function calculateCollaboratorBankHours($collaborator, $startDate, $endDate)
    {
        // Buscar registros de ponto do período
        $timeTrackings = TimeTrackingModel::where('collaborator_id', $collaborator->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        // Calcular carga horária padrão diária do colaborador (em minutos)
        $standardDailyMinutes = $this->calculateStandardDailyMinutes($collaborator);

        $totalWorkedMinutes = 0;
        $totalStandardMinutes = 0;
        $workDays = [];
        $workDaysCount = 0;

        // Primeiro, processar todos os dias com registros (incluindo finais de semana)
        foreach ($timeTrackings as $tracking) {
            $trackingDate = Carbon::parse($tracking->date);

            // CORREÇÃO: Incluir TODOS os dias trabalhados, incluindo finais de semana
            $workDaysCount++;
            $workedMinutes = $tracking->total_hours_worked ?? 0;
            $totalWorkedMinutes += $workedMinutes;

            // Para dias úteis, usar carga padrão. Para finais de semana, carga padrão = 0
            $dayStandardMinutes = $trackingDate->isWeekend() ? 0 : $standardDailyMinutes;
            $totalStandardMinutes += $dayStandardMinutes;

            $workDays[] = [
                'date' => $trackingDate,
                'tracking' => $tracking,
                'worked_minutes' => $workedMinutes,
                'standard_minutes' => $dayStandardMinutes,
                'difference_minutes' => $workedMinutes - $dayStandardMinutes,
                'status' => $tracking->status,
                'is_weekend' => $trackingDate->isWeekend(),
                'day_name' => $trackingDate->locale('pt_BR')->dayName
            ];
        }

        // Calcular saldo do banco de horas
        $bankBalanceMinutes = $totalWorkedMinutes - $totalStandardMinutes;

        return [
            'period_start' => $startDate,
            'period_end' => $endDate,
            'work_days_count' => $workDaysCount,
            'total_worked_minutes' => $totalWorkedMinutes,
            'total_standard_minutes' => $totalStandardMinutes,
            'bank_balance_minutes' => $bankBalanceMinutes,
            'work_days' => collect($workDays)->sortByDesc('date')->take(10)->values()->toArray(), // Últimos 10 dias para exibição
            'standard_daily_minutes' => $standardDailyMinutes,
            'weekly_limit_minutes' => 44 * 60, // Para compatibilidade
            'collaborator_weekly_minutes' => $standardDailyMinutes * 5, // Para compatibilidade
            'weekly_details' => [] // Para compatibilidade
        ];
    }

    /**
     * Calcula a carga horária padrão diária de um colaborador em minutos
     * Agora usa a jornada definida no WorkHoursModel
     */
    private function calculateStandardDailyMinutes($collaborator)
    {
        // Se não tem jornada definida, usar padrão CLT (8h48min)
        if (!$collaborator->workHours) {
            return 528; // 8h48min em minutos (44h/5 dias)
        }

        // Usar a jornada semanal dividida pelos dias ativos
        $weeklyMinutes = $collaborator->workHours->total_weekly_hours * 60;
        $activeDays = count($collaborator->workHours->getActiveDays());

        return $activeDays > 0 ? round($weeklyMinutes / $activeDays) : 0;
    }

    /**
     * Calcula o limite semanal de minutos baseado na jornada do colaborador
     */
    private function calculateCollaboratorWeeklyLimitMinutes($collaborator)
    {
        if (!$collaborator->workHours) {
            return 44 * 60; // CLT padrão: 44h = 2640 minutos
        }

        // Usar a jornada semanal configurada (mas limitada a 44h pela CLT)
        $weeklyMinutes = $collaborator->workHours->total_weekly_hours * 60;
        $cltLimit = 44 * 60; // 2640 minutos

        // Não pode exceder o limite da CLT
        return min($weeklyMinutes, $cltLimit);
    }

    /**
     * Calcula os minutos esperados para um dia específico baseado na jornada
     */
    private function getExpectedDailyMinutes($collaborator, Carbon $date)
    {
        if (!$collaborator->workHours) {
            return $date->isWeekend() ? 0 : 528; // CLT padrão
        }

        $workHours = $collaborator->workHours;
        $dayOfWeek = strtolower($date->format('l')); // monday, tuesday, etc.

        // Verificar se o dia está ativo na jornada
        if (!$workHours->{$dayOfWeek . '_active'}) {
            return 0;
        }

        $totalMinutes = 0;

        // Primeiro período
        if ($workHours->{$dayOfWeek . '_entry_1'} && $workHours->{$dayOfWeek . '_exit_1'}) {
            $entry1 = Carbon::parse($workHours->{$dayOfWeek . '_entry_1'});
            $exit1 = Carbon::parse($workHours->{$dayOfWeek . '_exit_1'});

            if ($exit1->lessThan($entry1)) {
                $exit1->addDay();
            }

            $totalMinutes += $entry1->diffInMinutes($exit1);
        }

        // Segundo período
        if ($workHours->{$dayOfWeek . '_entry_2'} && $workHours->{$dayOfWeek . '_exit_2'}) {
            $entry2 = Carbon::parse($workHours->{$dayOfWeek . '_entry_2'});
            $exit2 = Carbon::parse($workHours->{$dayOfWeek . '_exit_2'});

            if ($exit2->lessThan($entry2)) {
                $exit2->addDay();
            }

            $totalMinutes += $entry2->diffInMinutes($exit2);
        }

        return $totalMinutes;
    }

    /**
     * Retorna dados vazios para banco de horas quando não há jornada definida
     */
    private function getEmptyBankHoursData($startDate, $endDate)
    {
        return [
            'period_start' => $startDate,
            'period_end' => $endDate,
            'total_weeks_analyzed' => 0,
            'total_worked_minutes' => 0,
            'weekly_limit_minutes' => 0,
            'bank_balance_minutes' => 0,
            'weekly_details' => [],
            'work_days' => [],
            'work_days_count' => 0,
            'total_standard_minutes' => 0,
            'standard_daily_minutes' => 0,
            'collaborator_weekly_minutes' => 0
        ];
    }

    /**
     * Retorna a carga horária padrão conforme CLT (44h semanais)
     */
    private function getCLTDailyMinutes()
    {
        // CLT: 44h semanais ÷ 5 dias úteis = 8h48min por dia = 528 minutos
        return 528; // 8 horas e 48 minutos
    }

    /**
     * Retorna a carga horária semanal conforme CLT
     */
    private function getCLTWeeklyMinutes()
    {
        // CLT: 44 horas semanais = 2640 minutos
        return 2640;
    }

    /**
     * Converte minutos para formato HH:MM
     */
    public static function formatMinutesToHours($minutes)
    {
        if ($minutes === 0) {
            return '00:00';
        }

        $isNegative = $minutes < 0;
        $minutes = abs($minutes);

        $hours = intval($minutes / 60);
        $remainingMinutes = $minutes % 60;

        $formatted = sprintf('%02d:%02d', $hours, $remainingMinutes);

        return $isNegative ? '-' . $formatted : $formatted;
    }
}
