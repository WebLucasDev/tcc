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
        $collaboratorId = $request->get('collaborator_id');
        $month = $request->get('month', now()->format('Y-m'));

        $allCollaborators = CollaboratorModel::where('status', CollaboratorStatusEnum::ACTIVE)
            ->orderBy('name')
            ->get();

        $compTimeData = $this->calculateCompTimeDataCLT($collaboratorId, $month);

        $summary = $this->calculateSummary($compTimeData);

        $breadcrumbs = [
            ['label' => 'Gestão de Ponto', 'url' => null],
            ['label' => 'Banco de Horas', 'url' => null],
        ];

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => [
                    'summary' => view('auth.time-management.comp-time.partials.summary', compact('compTimeData', 'summary'))->render(),
                    'table' => view('auth.time-management.comp-time.partials.table', compact('compTimeData'))->render(),
                ],
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

    private function calculateCompTimeDataCLT($collaboratorId = null, $month = null)
    {
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $collaboratorsQuery = CollaboratorModel::with('workHours')
            ->where('status', CollaboratorStatusEnum::ACTIVE);

        if ($collaboratorId) {
            $collaboratorsQuery->where('id', $collaboratorId);
        }

        $collaborators = $collaboratorsQuery->orderBy('name')->get();

        $compTimeData = [];

        foreach ($collaborators as $collaborator) {
            $bankHours = $this->calculateCollaboratorBankHoursCLT($collaborator, $startDate, $endDate);

            $compTimeData[] = [
                'collaborator' => $collaborator,
                'bank_hours' => $bankHours,
            ];
        }

        return $compTimeData;
    }

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
            'total_expected_minutes' => 0,
        ];

        if (is_array($compTimeData) && count($compTimeData) > 0) {
            $summary['total_collaborators'] = count($compTimeData);

            foreach ($compTimeData as $data) {
                $balance = $data['bank_hours']['bank_balance_minutes'] ?? 0;
                $worked = $data['bank_hours']['total_worked_minutes'] ?? 0;

                $summary['total_worked_minutes'] += $worked;

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
    }

    private function calculateCollaboratorBankHoursCLT($collaborator, $startDate, $endDate)
    {
        if (! $collaborator->workHours) {
            return $this->getEmptyBankHoursData($startDate, $endDate);
        }

        $timeTrackings = TimeTrackingModel::where('collaborator_id', $collaborator->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get()
            ->keyBy(function ($item) {
                return $item->date->format('Y-m-d');
            });

        $collaboratorWeeklyMinutes = $collaborator->workHours->total_weekly_hours * 60;

        $totalBankBalance = 0;
        $totalWorkedMinutes = 0;
        $totalWeeksAnalyzed = 0;
        $weeklyDetails = [];
        $recentDays = [];

        $currentDate = $startDate->copy();
        while ($currentDate->dayOfWeek !== Carbon::SUNDAY) {
            $currentDate->subDay();
        }

        while ($currentDate <= $endDate) {
            $weekStart = $currentDate->copy();
            $weekEnd = $currentDate->copy()->addDays(6);

            if ($weekEnd >= $startDate && $weekStart <= $endDate) {
                $weekWorkedMinutes = 0;
                $weekExpectedMinutes = 0;
                $weekDays = [];

                for ($dayOffset = 0; $dayOffset < 7; $dayOffset++) {
                    $day = $weekStart->copy()->addDays($dayOffset);

                    if ($day >= $startDate && $day <= $endDate) {
                        $dayKey = $day->format('Y-m-d');
                        $tracking = $timeTrackings->get($dayKey);

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
                            'day_name' => $day->locale('pt_BR')->dayName,
                        ];

                        if ($tracking || $expectedDailyMinutes > 0) {
                            $recentDays[] = [
                                'date' => $day->copy(),
                                'tracking' => $tracking,
                                'worked_minutes' => $dayWorkedMinutes,
                                'expected_minutes' => $expectedDailyMinutes,
                                'difference_minutes' => $dayWorkedMinutes - $expectedDailyMinutes,
                                'status' => $tracking ? $tracking->status : 'ausente',
                                'is_weekend' => $day->isWeekend(),
                                'day_name' => $day->locale('pt_BR')->dayName,
                            ];
                        }
                    }
                }

                $weekBankBalance = 0;

                if ($weekExpectedMinutes > 0 || $weekWorkedMinutes > 0) {
                    $weekBankBalance = $weekWorkedMinutes - $weekExpectedMinutes;

                    $cltWeeklyLimitMinutes = 44 * 60;
                    $maxWeeklyOvertimeMinutes = 10 * 60;

                    if ($weekWorkedMinutes > $cltWeeklyLimitMinutes) {
                        $overtimeMinutes = $weekWorkedMinutes - $weekExpectedMinutes;
                        $weekBankBalance = min($overtimeMinutes, $maxWeeklyOvertimeMinutes);
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
                    'limit_minutes' => $collaboratorWeeklyMinutes,
                    'bank_balance' => $weekBankBalance,
                    'days' => $weekDays,
                ];
            }

            $currentDate->addWeek();
        }

        $totalExpectedMinutes = 0;
        foreach ($weeklyDetails as $week) {
            $totalExpectedMinutes += $week['expected_minutes'] ?? 0;
        }

        $totalDaysWithExpectation = collect($recentDays)->where('expected_minutes', '>', 0)->count();
        $standardDailyMinutes = $totalDaysWithExpectation > 0 ?
            round($totalExpectedMinutes / $totalDaysWithExpectation) :
            $this->calculateStandardDailyMinutes($collaborator);

        return [
            'period_start' => $startDate,
            'period_end' => $endDate,
            'total_weeks_analyzed' => $totalWeeksAnalyzed,
            'total_worked_minutes' => $totalWorkedMinutes,
            'weekly_limit_minutes' => $collaboratorWeeklyMinutes,
            'bank_balance_minutes' => $totalBankBalance,
            'weekly_details' => $weeklyDetails,
            'work_days' => collect($recentDays)->sortByDesc('date')->values()->toArray(),
            'work_days_count' => collect($recentDays)->count(),
            'total_standard_minutes' => $totalExpectedMinutes,
            'standard_daily_minutes' => $standardDailyMinutes,
            'collaborator_weekly_minutes' => $collaboratorWeeklyMinutes,
        ];
    }

    private function calculateStandardDailyMinutes($collaborator)
    {
        if (! $collaborator->workHours) {
            return 528;
        }

        $weeklyMinutes = $collaborator->workHours->total_weekly_hours * 60;
        $activeDays = count($collaborator->workHours->getActiveDays());

        return $activeDays > 0 ? round($weeklyMinutes / $activeDays) : 0;
    }

    private function getExpectedDailyMinutes($collaborator, Carbon $date)
    {
        if (! $collaborator->workHours) {
            return $date->isWeekend() ? 0 : 528;
        }

        $workHours = $collaborator->workHours;
        $dayOfWeek = strtolower($date->format('l'));

        if (! $workHours->{$dayOfWeek.'_active'}) {
            return 0;
        }

        $totalMinutes = 0;

        if ($workHours->{$dayOfWeek.'_entry_1'} && $workHours->{$dayOfWeek.'_exit_1'}) {
            $entry1 = Carbon::parse($workHours->{$dayOfWeek.'_entry_1'});
            $exit1 = Carbon::parse($workHours->{$dayOfWeek.'_exit_1'});

            if ($exit1->lessThan($entry1)) {
                $exit1->addDay();
            }

            $totalMinutes += $entry1->diffInMinutes($exit1);
        }

        if ($workHours->{$dayOfWeek.'_entry_2'} && $workHours->{$dayOfWeek.'_exit_2'}) {
            $entry2 = Carbon::parse($workHours->{$dayOfWeek.'_entry_2'});
            $exit2 = Carbon::parse($workHours->{$dayOfWeek.'_exit_2'});

            if ($exit2->lessThan($entry2)) {
                $exit2->addDay();
            }

            $totalMinutes += $entry2->diffInMinutes($exit2);
        }

        return $totalMinutes;
    }

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
            'collaborator_weekly_minutes' => 0,
        ];
    }

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

        return $isNegative ? '-'.$formatted : $formatted;
    }
}
