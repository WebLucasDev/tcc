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

        $month = $request->get('month', now()->format('Y-m'));

        $bankHoursData = $this->calculateCollaboratorBankHoursCLT($collaborator, $month);

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

    private function calculateCollaboratorBankHoursCLT($collaborator, $month)
    {
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        if (!$collaborator->workHours) {
            return $this->getEmptyBankHoursData($startDate, $endDate);
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
        $totalWorkedMinutes = 0;
        $recentDays = [];

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

                    $recentDays[] = [
                        'date' => $dayDate,
                        'tracking' => $tracking,
                        'worked_minutes' => $workedMinutes,
                        'expected_minutes' => $expectedMinutes,
                        'difference_minutes' => $workedMinutes - $expectedMinutes,
                    ];
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
            $totalWorkedMinutes += $weekWorkedMinutes;

            $currentDate = $weekEnd->copy()->addDay();
        }

        $totalExpectedMinutes = collect($recentDays)->sum('expected_minutes');

        $totalDaysWithExpectation = collect($recentDays)->where('expected_minutes', '>', 0)->count();
        $standardDailyMinutes = $totalDaysWithExpectation > 0 ?
            round($totalExpectedMinutes / $totalDaysWithExpectation) : 0;

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

    public static function formatMinutesToHours($minutes)
    {
        $hours = floor(abs($minutes) / 60);
        $mins = abs($minutes) % 60;
        $sign = $minutes < 0 ? '-' : '';
        return sprintf('%s%02d:%02d', $sign, $hours, $mins);
    }
}
