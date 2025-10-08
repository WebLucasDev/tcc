<?php

namespace App\Http\Controllers;

use App\Models\TimeTrackingModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompTimeEmployessController extends Controller
{
    public function index(Request $request)
    {
        $collaborator = Auth::guard('collaborator')->user();

        // Filtro de mês (padrão: mês atual)
        $month = $request->get('month', now()->format('Y-m'));

        // Calcular dados do banco de horas apenas para o colaborador logado
        $bankHoursData = $this->calculateCollaboratorBankHoursCLT($collaborator, $month);

        // Se for requisição AJAX, retornar apenas os dados necessários
        if ($request->ajax()) {
            return response()->json([
                'html' => [
                    'summary' => view('auth.system-for-employees.comp-time-employees.partials.summary', compact('bankHoursData'))->render(),
                    'details' => view('auth.system-for-employees.comp-time-employees.partials.details', compact('bankHoursData'))->render(),
                ]
            ]);
        }

        return view('auth.system-for-employees.comp-time-employees.index', compact(
            'bankHoursData',
            'month',
            'collaborator'
        ));
    }

    /**
     * Calcula o banco de horas do colaborador seguindo a CLT (44h semanais)
     */
    private function calculateCollaboratorBankHoursCLT($collaborator, $month)
    {
        // Configurar período de análise (mês completo)
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        // Verificar se o colaborador tem jornada de trabalho definida
        if (!$collaborator->workHours) {
            return $this->getEmptyBankHoursData($startDate, $endDate);
        }

        // Buscar todos os registros do período
        $timeTrackings = TimeTrackingModel::where('collaborator_id', $collaborator->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('status', '!=', 'ausente')
            ->orderBy('date')
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            });

        // Calcular limite semanal baseado na jornada individual do colaborador
        $weeklyLimitMinutes = $this->calculateCollaboratorWeeklyLimitMinutes($collaborator);

        $totalBankBalance = 0;
        $totalWorkedMinutes = 0;
        $recentDays = [];

        // Encontrar o primeiro domingo do período para começar as semanas
        $currentDate = $startDate->copy();
        while ($currentDate->dayOfWeek !== Carbon::SUNDAY) {
            $currentDate->subDay();
        }

        // Processar semana por semana (Domingo a Sábado) para cálculo do saldo CLT
        while ($currentDate <= $endDate) {
            $weekStart = $currentDate->copy();
            $weekEnd = $currentDate->copy()->addDays(6);

            $weekWorkedMinutes = 0;
            $weekExpectedMinutes = 0;

            // Processar cada dia da semana
            for ($i = 0; $i < 7; $i++) {
                $dayDate = $weekStart->copy()->addDays($i);

                // Só processar se estiver dentro do mês selecionado
                if ($dayDate->between($startDate, $endDate)) {
                    $dayKey = $dayDate->format('Y-m-d');
                    $tracking = $timeTrackings->get($dayKey);

                    $expectedMinutes = $this->getExpectedDailyMinutes($collaborator, $dayDate);
                    $workedMinutes = 0;

                    if ($tracking && $tracking->status !== 'ausente') {
                        // Calcular minutos trabalhados
                        // Período manhã
                        if ($tracking->entry_time_1 && $tracking->return_time_1) {
                            $start = Carbon::parse($tracking->entry_time_1);
                            $end = Carbon::parse($tracking->return_time_1);
                            $workedMinutes += $start->diffInMinutes($end);
                        }

                        // Período tarde
                        if ($tracking->entry_time_2 && $tracking->return_time_2) {
                            $start = Carbon::parse($tracking->entry_time_2);
                            $end = Carbon::parse($tracking->return_time_2);
                            $workedMinutes += $start->diffInMinutes($end);
                        }
                    }

                    $weekWorkedMinutes += $workedMinutes;
                    $weekExpectedMinutes += $expectedMinutes;

                    // Armazenar dados diários
                    $recentDays[] = [
                        'date' => $dayDate,
                        'tracking' => $tracking,
                        'worked_minutes' => $workedMinutes,
                        'expected_minutes' => $expectedMinutes,
                        'difference_minutes' => $workedMinutes - $expectedMinutes,
                    ];
                }
            }

            // Calcular saldo semanal considerando o limite CLT
            $weekBalance = min($weekWorkedMinutes, $weeklyLimitMinutes) - $weekExpectedMinutes;
            $totalBankBalance += $weekBalance;
            $totalWorkedMinutes += $weekWorkedMinutes;

            $currentDate = $weekEnd->copy()->addDay();
        }

        // Calcular total de minutos esperados
        $totalExpectedMinutes = collect($recentDays)->sum('expected_minutes');

        // Calcular média diária esperada
        $totalDaysWithExpectation = collect($recentDays)->where('expected_minutes', '>', 0)->count();
        $standardDailyMinutes = $totalDaysWithExpectation > 0 ?
            round($totalExpectedMinutes / $totalDaysWithExpectation) : 0;

        // Ordenar dias por data decrescente (mais recentes primeiro)
        $recentDays = collect($recentDays)->sortByDesc('date')->values()->all();

        return [
            'bank_balance_minutes' => $totalBankBalance,
            'total_worked_minutes' => $totalWorkedMinutes,
            'total_standard_minutes' => $totalExpectedMinutes,
            'work_days_count' => count($recentDays),
            'work_days' => $recentDays,
            'standard_daily_minutes' => $standardDailyMinutes,
            'period_start' => $startDate,
            'period_end' => $endDate,
        ];
    }

    /**
     * Calcula o limite semanal de minutos baseado na jornada do colaborador
     */
    private function calculateCollaboratorWeeklyLimitMinutes($collaborator)
    {
        if (!$collaborator->workHours) {
            return 44 * 60; // Padrão CLT: 44h = 2640 minutos
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
            return 0;
        }

        $workHours = $collaborator->workHours;
        $dayOfWeek = strtolower($date->locale('en')->dayName);

        // Verificar se é dia útil para este colaborador
        $dayActiveField = $dayOfWeek . '_active';
        if (!$workHours->$dayActiveField) {
            return 0; // Não trabalha neste dia
        }

        // Calcular minutos do dia baseado nos horários configurados da jornada
        $expectedMinutes = 0;

        // Período manhã (entry_1 -> exit_1)
        $entry1Field = $dayOfWeek . '_entry_1';
        $exit1Field = $dayOfWeek . '_exit_1';

        if ($workHours->$entry1Field && $workHours->$exit1Field) {
            $start = Carbon::parse($workHours->$entry1Field);
            $end = Carbon::parse($workHours->$exit1Field);

            // Para horários noturnos (saída no dia seguinte)
            if ($end->lessThan($start)) {
                $end->addDay();
            }

            $expectedMinutes += $start->diffInMinutes($end);
        }

        // Período tarde (entry_2 -> exit_2)
        $entry2Field = $dayOfWeek . '_entry_2';
        $exit2Field = $dayOfWeek . '_exit_2';

        if ($workHours->$entry2Field && $workHours->$exit2Field) {
            $start = Carbon::parse($workHours->$entry2Field);
            $end = Carbon::parse($workHours->$exit2Field);

            // Para horários noturnos (saída no dia seguinte)
            if ($end->lessThan($start)) {
                $end->addDay();
            }

            $expectedMinutes += $start->diffInMinutes($end);
        }

        return $expectedMinutes;
    }

    /**
     * Retorna dados vazios quando não há jornada definida
     */
    private function getEmptyBankHoursData($startDate, $endDate)
    {
        return [
            'bank_balance_minutes' => 0,
            'total_worked_minutes' => 0,
            'total_standard_minutes' => 0,
            'work_days_count' => 0,
            'work_days' => [],
            'standard_daily_minutes' => 0,
            'period_start' => $startDate,
            'period_end' => $endDate,
        ];
    }

    /**
     * Formata minutos para horas (HH:MM)
     */
    public static function formatMinutesToHours($minutes)
    {
        $hours = floor(abs($minutes) / 60);
        $mins = abs($minutes) % 60;
        $sign = $minutes < 0 ? '-' : '';
        return sprintf('%s%02d:%02d', $sign, $hours, $mins);
    }
}
