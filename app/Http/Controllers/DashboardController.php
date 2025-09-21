<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CollaboratorModel;
use App\Models\TimeTrackingModel;
use App\Models\SolicitationModel;
use App\Models\DepartmentModel;
use App\Enums\CollaboratorStatusEnum;
use App\Enums\TimeTrackingStatusEnum;
use App\Enums\SolicitationStatusEnum;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Exibe o Dashboard com relatórios reais do sistema.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Filtros
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $department_id = $request->get('department_id');

        // Buscar dados para o dashboard
        $metrics = $this->getMetrics($month, $department_id);
        $departments = DepartmentModel::orderBy('name')->get();

        return view('auth.dashboard.index', compact(
            'user',
            'metrics',
            'departments',
            'month',
            'department_id'
        ));
    }

    /**
     * Coleta todas as métricas para o dashboard
     */
    private function getMetrics($month, $department_id = null)
    {
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        $today = Carbon::today();

        return [
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
                'month_name' => $startDate->locale('pt_BR')->monthName,
                'year' => $startDate->year
            ],
            'overview' => $this->getOverviewMetrics($startDate, $endDate, $department_id),
            'punctuality' => $this->getPunctualityMetrics($startDate, $endDate, $department_id),
            'recent_records' => $this->getRecentRecords($today, $department_id),
            'daily_attendance' => $this->getDailyAttendance($startDate, $endDate, $department_id),
            'top_performers' => $this->getTopPerformers($startDate, $endDate, $department_id),
            'alerts' => $this->getAlerts($startDate, $endDate, $department_id),
            'solicitations_summary' => $this->getSolicitationsSummary($startDate, $endDate, $department_id)
        ];
    }

    /**
     * Métricas gerais de visão geral
     */
    private function getOverviewMetrics($startDate, $endDate, $department_id = null)
    {
        $collaboratorsQuery = CollaboratorModel::where('status', CollaboratorStatusEnum::ACTIVE);

        if ($department_id) {
            $collaboratorsQuery->whereHas('position', function($q) use ($department_id) {
                $q->where('department_id', $department_id);
            });
        }

        $totalCollaborators = $collaboratorsQuery->count();

        // Registros do período
        $recordsQuery = TimeTrackingModel::whereBetween('date', [$startDate, $endDate])
            ->with('collaborator');

        if ($department_id) {
            $recordsQuery->whereHas('collaborator.position', function($q) use ($department_id) {
                $q->where('department_id', $department_id);
            });
        }

        $records = $recordsQuery->get();

        $totalRecords = $records->count();
        $completeRecords = $records->where('status', TimeTrackingStatusEnum::COMPLETO)->count();
        $incompleteRecords = $records->where('status', TimeTrackingStatusEnum::INCOMPLETO)->count();
        $absentRecords = $records->where('status', TimeTrackingStatusEnum::AUSENTE)->count();

        $totalWorkedMinutes = $records->sum('total_hours_worked');
        $averageWorkedHours = $totalRecords > 0 ? round($totalWorkedMinutes / 60 / $totalRecords, 1) : 0;

        return [
            'total_collaborators' => $totalCollaborators,
            'total_records' => $totalRecords,
            'complete_records' => $completeRecords,
            'incomplete_records' => $incompleteRecords,
            'absent_records' => $absentRecords,
            'total_worked_hours' => round($totalWorkedMinutes / 60, 1),
            'average_worked_hours' => $averageWorkedHours,
            'completion_rate' => $totalRecords > 0 ? round(($completeRecords / $totalRecords) * 100, 1) : 0
        ];
    }

    /**
     * Métricas de pontualidade
     */
    private function getPunctualityMetrics($startDate, $endDate, $department_id = null)
    {
        $recordsQuery = TimeTrackingModel::whereBetween('date', [$startDate, $endDate])
            ->whereNotNull('entry_time_1')
            ->with('collaborator.workHours');

        if ($department_id) {
            $recordsQuery->whereHas('collaborator.position', function($q) use ($department_id) {
                $q->where('department_id', $department_id);
            });
        }

        $records = $recordsQuery->get();

        $onTime = 0;
        $late = 0;
        $totalLateMinutes = 0;

        foreach ($records as $record) {
            if (!$record->collaborator || !$record->collaborator->workHours) continue;

            $date = Carbon::parse($record->date);
            $dayOfWeek = strtolower($date->format('l'));
            $expectedEntry = $record->collaborator->workHours->{$dayOfWeek . '_entry_1'};

            if (!$expectedEntry) continue;

            $actualEntry = Carbon::parse($record->entry_time_1);
            $expectedEntryTime = Carbon::parse($expectedEntry);

            // Tolerância de 10 minutos
            if ($actualEntry->lte($expectedEntryTime->addMinutes(10))) {
                $onTime++;
            } else {
                $late++;
                $lateMinutes = $expectedEntryTime->diffInMinutes($actualEntry);
                $totalLateMinutes += $lateMinutes;
            }
        }

        $totalChecked = $onTime + $late;
        $punctualityRate = $totalChecked > 0 ? round(($onTime / $totalChecked) * 100, 1) : 0;
        $averageLateMinutes = $late > 0 ? round($totalLateMinutes / $late, 1) : 0;

        return [
            'on_time' => $onTime,
            'late' => $late,
            'punctuality_rate' => $punctualityRate,
            'average_late_minutes' => $averageLateMinutes,
            'total_late_minutes' => $totalLateMinutes
        ];
    }

    /**
     * Registros recentes (hoje)
     */
    private function getRecentRecords($date, $department_id = null)
    {
        $query = TimeTrackingModel::whereDate('date', $date)
            ->with('collaborator')
            ->where(function($q) {
                $q->whereNotNull('entry_time_1')
                  ->orWhereNotNull('return_time_1')
                  ->orWhereNotNull('entry_time_2')
                  ->orWhereNotNull('return_time_2');
            });

        if ($department_id) {
            $query->whereHas('collaborator.position', function($q) use ($department_id) {
                $q->where('department_id', $department_id);
            });
        }

        return $query->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($record) {
                return [
                    'collaborator_name' => $record->collaborator->name ?? 'N/A',
                    'last_action' => $this->getLastAction($record),
                    'time' => $this->getLastActionTime($record),
                    'status' => $record->status->value,
                    'total_hours' => $record->total_hours_worked ? round($record->total_hours_worked / 60, 1) : 0
                ];
            });
    }

    /**
     * Presença diária do mês
     */
    private function getDailyAttendance($startDate, $endDate, $department_id = null)
    {
        $totalCollaborators = CollaboratorModel::where('status', CollaboratorStatusEnum::ACTIVE);

        if ($department_id) {
            $totalCollaborators->whereHas('position', function($q) use ($department_id) {
                $q->where('department_id', $department_id);
            });
        }

        $totalCollaborators = $totalCollaborators->count();

        $dailyData = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $recordsQuery = TimeTrackingModel::whereDate('date', $currentDate)
                ->whereIn('status', [TimeTrackingStatusEnum::COMPLETO, TimeTrackingStatusEnum::INCOMPLETO]);

            if ($department_id) {
                $recordsQuery->whereHas('collaborator.position', function($q) use ($department_id) {
                    $q->where('department_id', $department_id);
                });
            }

            $presentCount = $recordsQuery->count();
            $attendanceRate = $totalCollaborators > 0 ? round(($presentCount / $totalCollaborators) * 100, 1) : 0;

            $dailyData[] = [
                'date' => $currentDate->copy(),
                'day_name' => $currentDate->locale('pt_BR')->dayName,
                'present_count' => $presentCount,
                'attendance_rate' => $attendanceRate,
                'is_weekend' => $currentDate->isWeekend()
            ];

            $currentDate->addDay();
        }

        return $dailyData;
    }

    /**
     * Top performers do mês
     */
    private function getTopPerformers($startDate, $endDate, $department_id = null)
    {
        $query = TimeTrackingModel::whereBetween('date', [$startDate, $endDate])
            ->with('collaborator')
            ->selectRaw('collaborator_id,
                COUNT(*) as total_days,
                SUM(CASE WHEN status = "completo" THEN 1 ELSE 0 END) as complete_days,
                SUM(total_hours_worked) as total_minutes,
                AVG(total_hours_worked) as avg_minutes')
            ->groupBy('collaborator_id')
            ->having('total_days', '>', 0);

        if ($department_id) {
            $query->whereHas('collaborator.position', function($q) use ($department_id) {
                $q->where('department_id', $department_id);
            });
        }

        return $query->orderByDesc('complete_days')
            ->limit(5)
            ->get()
            ->map(function ($record) {
                $completionRate = $record->total_days > 0 ? round(($record->complete_days / $record->total_days) * 100, 1) : 0;

                return [
                    'name' => $record->collaborator->name ?? 'N/A',
                    'total_days' => $record->total_days,
                    'complete_days' => $record->complete_days,
                    'completion_rate' => $completionRate,
                    'total_hours' => round($record->total_minutes / 60, 1),
                    'avg_hours' => round($record->avg_minutes / 60, 1)
                ];
            });
    }

    /**
     * Alertas do sistema
     */
    private function getAlerts($startDate, $endDate, $department_id = null)
    {
        // Colaboradores com muitos atrasos
        $lateQuery = TimeTrackingModel::whereBetween('date', [$startDate, $endDate])
            ->with('collaborator.workHours')
            ->whereNotNull('entry_time_1');

        if ($department_id) {
            $lateQuery->whereHas('collaborator.position', function($q) use ($department_id) {
                $q->where('department_id', $department_id);
            });
        }

        $lateCollaborators = 0;
        foreach ($lateQuery->get()->groupBy('collaborator_id') as $records) {
            $lateCount = 0;
            foreach ($records as $record) {
                if ($this->isLateArrival($record)) {
                    $lateCount++;
                }
            }
            if ($lateCount >= 5) { // 5+ atrasos no mês
                $lateCollaborators++;
            }
        }

        // Colaboradores com muitas ausências
        $absentQuery = TimeTrackingModel::whereBetween('date', [$startDate, $endDate])
            ->where('status', TimeTrackingStatusEnum::AUSENTE);

        if ($department_id) {
            $absentQuery->whereHas('collaborator.position', function($q) use ($department_id) {
                $q->where('department_id', $department_id);
            });
        }

        $frequentAbsent = $absentQuery->selectRaw('collaborator_id, COUNT(*) as absent_count')
            ->groupBy('collaborator_id')
            ->having('absent_count', '>=', 3)
            ->count();

        // Solicitações pendentes
        $pendingSolicitations = SolicitationModel::where('status', SolicitationStatusEnum::PENDING)->count();

        return [
            'frequent_late' => $lateCollaborators,
            'frequent_absent' => $frequentAbsent,
            'pending_solicitations' => $pendingSolicitations
        ];
    }

    /**
     * Resumo das solicitações
     */
    private function getSolicitationsSummary($startDate, $endDate, $department_id = null)
    {
        $query = SolicitationModel::whereBetween('created_at', [$startDate, $endDate]);

        if ($department_id) {
            $query->whereHas('collaborator.position', function($q) use ($department_id) {
                $q->where('department_id', $department_id);
            });
        }

        $total = $query->count();
        $pending = $query->clone()->where('status', SolicitationStatusEnum::PENDING)->count();
        $approved = $query->clone()->where('status', SolicitationStatusEnum::APPROVED)->count();
        $rejected = $query->clone()->where('status', SolicitationStatusEnum::REJECTED)->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
            'approval_rate' => ($approved + $rejected) > 0 ? round(($approved / ($approved + $rejected)) * 100, 1) : 0
        ];
    }

    /**
     * Métodos auxiliares
     */
    private function getLastAction($record)
    {
        if ($record->return_time_2) return 'Saída Final';
        if ($record->entry_time_2) return 'Retorno';
        if ($record->return_time_1) return 'Saída Almoço';
        if ($record->entry_time_1) return 'Entrada';
        return 'N/A';
    }

    private function getLastActionTime($record)
    {
        if ($record->return_time_2) return Carbon::parse($record->return_time_2)->format('H:i');
        if ($record->entry_time_2) return Carbon::parse($record->entry_time_2)->format('H:i');
        if ($record->return_time_1) return Carbon::parse($record->return_time_1)->format('H:i');
        if ($record->entry_time_1) return Carbon::parse($record->entry_time_1)->format('H:i');
        return '--:--';
    }

    private function isLateArrival($record)
    {
        if (!$record->entry_time_1 || !$record->collaborator->workHours) {
            return false;
        }

        $dayOfWeek = strtolower(Carbon::parse($record->date)->format('l'));
        $expectedEntry = $record->collaborator->workHours->{$dayOfWeek . '_entry_1'};

        if (!$expectedEntry) return false;

        $actualEntry = Carbon::parse($record->entry_time_1);
        $expectedEntryTime = Carbon::parse($expectedEntry);

        return $actualEntry->gt($expectedEntryTime->addMinutes(10)); // 10 min de tolerância
    }
}
