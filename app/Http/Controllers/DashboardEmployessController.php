<?php

namespace App\Http\Controllers;

use App\Models\TimeTrackingModel;
use App\Models\SolicitationModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardEmployessController extends Controller
{
    public function index()
    {
        $collaborator = Auth::guard('collaborator')->user();
        $today = Carbon::today();
        $currentMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $metrics = [
            'today_records' => $this->getTodayRecords($collaborator, $today),
            'month_statistics' => $this->getMonthStatistics($collaborator, $currentMonth, $endOfMonth),
            'bank_hours' => $this->getCurrentBankHours($collaborator, $currentMonth, $endOfMonth),
            'recent_solicitations' => $this->getRecentSolicitations($collaborator),
            'punctuality' => $this->getPunctualityMetrics($collaborator, $currentMonth, $endOfMonth),
        ];

        return view('auth.system-for-employees.dashboard-employees.index', compact('collaborator', 'metrics'));
    }

    private function getTodayRecords($collaborator, $today)
    {
        $record = TimeTrackingModel::where('collaborator_id', $collaborator->id)
            ->whereDate('date', $today)
            ->first();

        return [
            'record' => $record,
            'has_entry_1' => $record && $record->entry_time_1,
            'has_return_1' => $record && $record->return_time_1,
            'has_entry_2' => $record && $record->entry_time_2,
            'has_return_2' => $record && $record->return_time_2,
            'status' => $record ? $record->status->value : null,
            'total_hours' => $record && $record->total_hours_worked ? round($record->total_hours_worked / 60, 1) : 0,
        ];
    }

    private function getMonthStatistics($collaborator, $startDate, $endDate)
    {
        $records = TimeTrackingModel::where('collaborator_id', $collaborator->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $totalDays = $records->count();
        $presentDays = $records->whereIn('status', ['completo', 'incompleto'])->count();
        $absentDays = $records->where('status', 'ausente')->count();
        $totalWorkedMinutes = $records->sum('total_hours_worked');

        $workingDays = $this->getWorkingDaysInMonth($startDate, $endDate);
        $attendanceRate = $workingDays > 0 ? round(($presentDays / $workingDays) * 100, 1) : 0;

        return [
            'total_days' => $totalDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'attendance_rate' => $attendanceRate,
            'total_worked_hours' => round($totalWorkedMinutes / 60, 1),
            'average_daily_hours' => $presentDays > 0 ? round($totalWorkedMinutes / 60 / $presentDays, 1) : 0,
        ];
    }

    private function getCurrentBankHours($collaborator, $startDate, $endDate)
    {
        if (!$collaborator->workHours) {
            return [
                'balance_minutes' => 0,
                'balance_formatted' => '00:00',
                'is_positive' => true,
            ];
        }

        $timeTrackings = TimeTrackingModel::where('collaborator_id', $collaborator->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('status', '!=', 'ausente')
            ->orderBy('date')
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            });

        $totalBankBalance = 0;
        $currentDate = $startDate->copy();

        while ($currentDate->dayOfWeek !== Carbon::SUNDAY) {
            $currentDate->subDay();
        }

        while ($currentDate <= $endDate) {
            $weekStart = $currentDate->copy();
            $weekEnd = $currentDate->copy()->addDays(6);

            $weekWorkedMinutes = 0;
            $weekExpectedMinutes = 0;

            for ($i = 0; $i < 7; $i++) {
                $dayDate = $weekStart->copy()->addDays($i);

                if ($dayDate->between($startDate, $endDate)) {
                    $dayKey = $dayDate->format('Y-m-d');
                    $tracking = $timeTrackings->get($dayKey);

                    $expectedMinutes = $this->getExpectedDailyMinutes($collaborator, $dayDate);
                    $workedMinutes = 0;

                    if ($tracking && $tracking->status !== 'ausente') {
                        if ($tracking->entry_time_1 && $tracking->return_time_1) {
                            $start = Carbon::parse($tracking->entry_time_1);
                            $end = Carbon::parse($tracking->return_time_1);
                            $workedMinutes += $start->diffInMinutes($end);
                        }

                        if ($tracking->entry_time_2 && $tracking->return_time_2) {
                            $start = Carbon::parse($tracking->entry_time_2);
                            $end = Carbon::parse($tracking->return_time_2);
                            $workedMinutes += $start->diffInMinutes($end);
                        }
                    }

                    $weekWorkedMinutes += $workedMinutes;
                    $weekExpectedMinutes += $expectedMinutes;
                }
            }

            $weekBalance = $weekWorkedMinutes - $weekExpectedMinutes;
            
            $cltWeeklyLimitMinutes = 44 * 60;
            $maxWeeklyOvertimeMinutes = 10 * 60;

            if ($weekWorkedMinutes > $cltWeeklyLimitMinutes) {
                $overtimeMinutes = $weekWorkedMinutes - $weekExpectedMinutes;
                $weekBalance = min($overtimeMinutes, $maxWeeklyOvertimeMinutes);
            }

            $totalBankBalance += $weekBalance;
            $currentDate = $weekEnd->copy()->addDay();
        }

        return [
            'balance_minutes' => $totalBankBalance,
            'balance_formatted' => $this->formatMinutesToHours($totalBankBalance),
            'is_positive' => $totalBankBalance >= 0,
        ];
    }

    private function getRecentSolicitations($collaborator)
    {
        return SolicitationModel::where('collaborator_id', $collaborator->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    private function getPunctualityMetrics($collaborator, $startDate, $endDate)
    {
        if (!$collaborator->workHours) {
            return [
                'on_time' => 0,
                'late' => 0,
                'punctuality_rate' => 0,
            ];
        }

        $records = TimeTrackingModel::where('collaborator_id', $collaborator->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereNotNull('entry_time_1')
            ->get();

        $onTime = 0;
        $late = 0;

        foreach ($records as $record) {
            $date = Carbon::parse($record->date);
            $dayOfWeek = strtolower($date->locale('en')->dayName);
            $expectedEntry = $collaborator->workHours->{$dayOfWeek . '_entry_1'};

            if (!$expectedEntry) continue;

            $actualEntry = Carbon::parse($record->entry_time_1);
            $expectedEntryTime = Carbon::parse($expectedEntry);

            if ($actualEntry->lte($expectedEntryTime->addMinutes(10))) {
                $onTime++;
            } else {
                $late++;
            }
        }

        $totalChecked = $onTime + $late;
        $punctualityRate = $totalChecked > 0 ? round(($onTime / $totalChecked) * 100, 1) : 0;

        return [
            'on_time' => $onTime,
            'late' => $late,
            'punctuality_rate' => $punctualityRate,
        ];
    }

    private function getExpectedDailyMinutes($collaborator, Carbon $date)
    {
        if (!$collaborator->workHours) {
            return 0;
        }

        $workHours = $collaborator->workHours;
        $dayOfWeek = strtolower($date->locale('en')->dayName);

        $dayActiveField = $dayOfWeek . '_active';
        if (!$workHours->$dayActiveField) {
            return 0;
        }

        $expectedMinutes = 0;

        $entry1Field = $dayOfWeek . '_entry_1';
        $exit1Field = $dayOfWeek . '_exit_1';

        if ($workHours->$entry1Field && $workHours->$exit1Field) {
            $start = Carbon::parse($workHours->$entry1Field);
            $end = Carbon::parse($workHours->$exit1Field);

            if ($end->lessThan($start)) {
                $end->addDay();
            }

            $expectedMinutes += $start->diffInMinutes($end);
        }

        $entry2Field = $dayOfWeek . '_entry_2';
        $exit2Field = $dayOfWeek . '_exit_2';

        if ($workHours->$entry2Field && $workHours->$exit2Field) {
            $start = Carbon::parse($workHours->$entry2Field);
            $end = Carbon::parse($workHours->$exit2Field);

            if ($end->lessThan($start)) {
                $end->addDay();
            }

            $expectedMinutes += $start->diffInMinutes($end);
        }

        return $expectedMinutes;
    }

    private function getWorkingDaysInMonth($startDate, $endDate)
    {
        $count = 0;
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            if (!$current->isWeekend()) {
                $count++;
            }
            $current->addDay();
        }

        return $count;
    }

    private function formatMinutesToHours($minutes)
    {
        $hours = floor(abs($minutes) / 60);
        $mins = abs($minutes) % 60;
        $sign = $minutes < 0 ? '-' : '+';
        return sprintf('%s%d:%02d', $sign, $hours, $mins);
    }
}
