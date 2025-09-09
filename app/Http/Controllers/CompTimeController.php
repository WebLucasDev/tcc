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
        $collaboratorsQuery = CollaboratorModel::where('status', \App\Enums\CollaboratorStatusEnum::ACTIVE);

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
            'net_balance_minutes' => 0
        ];

        if (is_array($compTimeData) && count($compTimeData) > 0) {
            $summary['total_collaborators'] = count($compTimeData);

            foreach ($compTimeData as $data) {
                $balance = $data['bank_hours']['bank_balance_minutes'] ?? 0;

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
        // Buscar todos os registros do período
        $timeTrackings = TimeTrackingModel::where('collaborator_id', $collaborator->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get()
            ->keyBy(function($item) {
                return $item->date->format('Y-m-d'); // Usar apenas a data sem hora
            });

        // Limite semanal da CLT: 44 horas = 2640 minutos
        $weeklyLimitMinutes = 44 * 60; // 2640 minutos

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
            $weekEnd = $currentDate->copy()->addDays(6); // Sábado

            // Se a semana tem algum dia dentro do período analisado
            if ($weekEnd >= $startDate && $weekStart <= $endDate) {
                $weekWorkedMinutes = 0;
                $weekDays = [];

                // Processar cada dia da semana (Domingo a Sábado)
                for ($dayOffset = 0; $dayOffset < 7; $dayOffset++) {
                    $day = $weekStart->copy()->addDays($dayOffset);

                    // Só processar se o dia está dentro do período
                    if ($day >= $startDate && $day <= $endDate) {
                        $dayKey = $day->format('Y-m-d');
                        $tracking = $timeTrackings->get($dayKey);

                        $dayWorkedMinutes = 0;
                        if ($tracking) {
                            // CORREÇÃO: Contabilizar TODOS os dias trabalhados (incluindo finais de semana)
                            $dayWorkedMinutes = $tracking->total_hours_worked ?? 0;
                            
                            // Para fins de semana: todo tempo trabalhado é hora extra
                            if ($day->isWeekend()) {
                                // Finais de semana: 100% do tempo é hora extra (adiciona ao banco)
                                $weekWorkedMinutes += $dayWorkedMinutes;
                            } else {
                                // Dias úteis: conta normalmente para a carga semanal
                                $weekWorkedMinutes += $dayWorkedMinutes;
                            }
                        }

                        $weekDays[] = [
                            'date' => $day->copy(),
                            'tracking' => $tracking,
                            'worked_minutes' => $dayWorkedMinutes,
                            'is_weekend' => $day->isWeekend(),
                            'day_name' => $day->locale('pt_BR')->dayName
                        ];

                                                // Adicionar aos dias recentes para exibição
                        if ($tracking) {
                            // CORREÇÃO: Incluir TODOS os dias trabalhados (incluindo finais de semana)
                            // Calcular diferença em relação ao padrão diário
                            $standardDailyMinutes = $day->isWeekend() ? 0 : $this->calculateStandardDailyMinutes($collaborator);
                            $differenceMinutes = $dayWorkedMinutes - $standardDailyMinutes;

                            $recentDays[] = [
                                'date' => $day->copy(),
                                'tracking' => $tracking,
                                'worked_minutes' => $dayWorkedMinutes,
                                'standard_minutes' => $standardDailyMinutes,
                                'difference_minutes' => $differenceMinutes,
                                'status' => $tracking->status,
                                'is_weekend' => $day->isWeekend(),
                                'day_name' => $day->locale('pt_BR')->dayName
                            ];
                        }
                    }
                }

                // Calcular banco de horas da semana
                $weekBankBalance = 0;

                // Só calcula banco de horas se houver pelo menos um registro na semana
                $hasAnyRecord = collect($weekDays)->contains(function($day) {
                    return $day['tracking'] !== null; // CORREÇÃO: Incluir qualquer dia trabalhado (útil ou fim de semana)
                });

                if ($hasAnyRecord) {
                    if ($weekWorkedMinutes > $weeklyLimitMinutes) {
                        // Excedeu 44h: banco positivo (horas extras)
                        $weekBankBalance = $weekWorkedMinutes - $weeklyLimitMinutes;
                    } elseif ($weekWorkedMinutes < $weeklyLimitMinutes) {
                        // Trabalhou menos que 44h: pode ser usado para compensar banco anterior
                        // Mas só conta como déficit se deveria ter trabalhado a semana completa
                        $weekBankBalance = $weekWorkedMinutes - $weeklyLimitMinutes;
                    }
                } else {
                    // Semana sem registros: não conta para banco de horas
                    $weekBankBalance = 0;
                }

                $totalBankBalance += $weekBankBalance;
                $totalWorkedMinutes += $weekWorkedMinutes;
                $totalWeeksAnalyzed++;

                $weeklyDetails[] = [
                    'week_start' => $weekStart->copy(),
                    'week_end' => $weekEnd->copy(),
                    'worked_minutes' => $weekWorkedMinutes,
                    'limit_minutes' => $weeklyLimitMinutes,
                    'bank_balance' => $weekBankBalance,
                    'days' => $weekDays
                ];
            }

            // Próxima semana (próximo domingo)
            $currentDate->addWeek();
        }

        return [
            'period_start' => $startDate,
            'period_end' => $endDate,
            'total_weeks_analyzed' => $totalWeeksAnalyzed,
            'total_worked_minutes' => $totalWorkedMinutes,
            'weekly_limit_minutes' => $weeklyLimitMinutes,
            'bank_balance_minutes' => $totalBankBalance,
            'weekly_details' => $weeklyDetails,
            'work_days' => collect($recentDays)->sortByDesc('date')->values()->toArray(), // Mostrar todos os dias, não apenas 10
            'work_days_count' => collect($recentDays)->count(),
            'total_standard_minutes' => $totalWeeksAnalyzed * $weeklyLimitMinutes, // Para compatibilidade com views
            'standard_daily_minutes' => $weeklyLimitMinutes / 5 // 8h48min por dia útil
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
            'standard_daily_minutes' => $standardDailyMinutes
        ];
    }

    /**
     * Calcula a carga horária padrão diária de um colaborador em minutos
     * NOTA: Para CLT, usar 44h semanais (528 min/dia útil) independente do horário individual
     */
    private function calculateStandardDailyMinutes($collaborator)
    {
        $totalMinutes = 0;

        // Período da manhã
        if ($collaborator->entry_time_1 && $collaborator->return_time_1) {
            $entryTime1 = Carbon::parse($collaborator->entry_time_1);
            $returnTime1 = Carbon::parse($collaborator->return_time_1);
            $totalMinutes += $entryTime1->diffInMinutes($returnTime1);
        }

        // Período da tarde
        if ($collaborator->entry_time_2 && $collaborator->return_time_2) {
            $entryTime2 = Carbon::parse($collaborator->entry_time_2);
            $returnTime2 = Carbon::parse($collaborator->return_time_2);
            $totalMinutes += $entryTime2->diffInMinutes($returnTime2);
        }

        return $totalMinutes;
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
