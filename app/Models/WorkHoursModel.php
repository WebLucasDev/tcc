<?php

namespace App\Models;

use App\Enums\WorkHoursStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WorkHoursModel extends Model
{
    protected $table = 'work_hours';

    protected $fillable = [
        'name',
        'total_weekly_hours',
        'description',
        'status',

        'monday_active',
        'monday_entry_1',
        'monday_exit_1',
        'monday_entry_2',
        'monday_exit_2',

        'tuesday_active',
        'tuesday_entry_1',
        'tuesday_exit_1',
        'tuesday_entry_2',
        'tuesday_exit_2',

        'wednesday_active',
        'wednesday_entry_1',
        'wednesday_exit_1',
        'wednesday_entry_2',
        'wednesday_exit_2',

        'thursday_active',
        'thursday_entry_1',
        'thursday_exit_1',
        'thursday_entry_2',
        'thursday_exit_2',

        'friday_active',
        'friday_entry_1',
        'friday_exit_1',
        'friday_entry_2',
        'friday_exit_2',

        'saturday_active',
        'saturday_entry_1',
        'saturday_exit_1',
        'saturday_entry_2',
        'saturday_exit_2',

        'sunday_active',
        'sunday_entry_1',
        'sunday_exit_1',
        'sunday_entry_2',
        'sunday_exit_2',
    ];

    protected $casts = [
        'total_weekly_hours' => 'decimal:2',
        'status' => WorkHoursStatusEnum::class,

        'monday_active' => 'boolean',
        'tuesday_active' => 'boolean',
        'wednesday_active' => 'boolean',
        'thursday_active' => 'boolean',
        'friday_active' => 'boolean',
        'saturday_active' => 'boolean',
        'sunday_active' => 'boolean',

        'monday_entry_1' => 'string',
        'monday_exit_1' => 'string',
        'monday_entry_2' => 'string',
        'monday_exit_2' => 'string',

        'tuesday_entry_1' => 'string',
        'tuesday_exit_1' => 'string',
        'tuesday_entry_2' => 'string',
        'tuesday_exit_2' => 'string',

        'wednesday_entry_1' => 'string',
        'wednesday_exit_1' => 'string',
        'wednesday_entry_2' => 'string',
        'wednesday_exit_2' => 'string',

        'thursday_entry_1' => 'string',
        'thursday_exit_1' => 'string',
        'thursday_entry_2' => 'string',
        'thursday_exit_2' => 'string',

        'friday_entry_1' => 'string',
        'friday_exit_1' => 'string',
        'friday_entry_2' => 'string',
        'friday_exit_2' => 'string',

        'saturday_entry_1' => 'string',
        'saturday_exit_1' => 'string',
        'saturday_entry_2' => 'string',
        'saturday_exit_2' => 'string',

        'sunday_entry_1' => 'string',
        'sunday_exit_1' => 'string',
        'sunday_entry_2' => 'string',
        'sunday_exit_2' => 'string',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function collaborators()
    {
        return $this->hasMany(CollaboratorModel::class, 'work_hours_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', WorkHoursStatusEnum::ACTIVE);
    }

    public function calculateWeeklyHours()
    {
        $totalMinutes = 0;
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($days as $day) {
            if ($this->{$day . '_active'}) {

                if ($this->{$day . '_entry_1'} && $this->{$day . '_exit_1'}) {
                    $entry1 = Carbon::parse($this->{$day . '_entry_1'});
                    $exit1 = Carbon::parse($this->{$day . '_exit_1'});

                    if ($exit1->lessThan($entry1)) {
                        $exit1->addDay();
                    }

                    $totalMinutes += $entry1->diffInMinutes($exit1);
                }

                if ($this->{$day . '_entry_2'} && $this->{$day . '_exit_2'}) {
                    $entry2 = Carbon::parse($this->{$day . '_entry_2'});
                    $exit2 = Carbon::parse($this->{$day . '_exit_2'});

                    if ($exit2->lessThan($entry2)) {
                        $exit2->addDay();
                    }

                    $totalMinutes += $entry2->diffInMinutes($exit2);
                }
            }
        }

        return round($totalMinutes / 60, 2);
    }


    public function getDailyHours($day)
    {
        if (!$this->{$day . '_active'}) {
            return 0;
        }

        $totalMinutes = 0;

        if ($this->{$day . '_entry_1'} && $this->{$day . '_exit_1'}) {
            $entry1 = Carbon::parse($this->{$day . '_entry_1'});
            $exit1 = Carbon::parse($this->{$day . '_exit_1'});

            if ($exit1->lessThan($entry1)) {
                $exit1->addDay();
            }

            $totalMinutes += $entry1->diffInMinutes($exit1);
        }

        if ($this->{$day . '_entry_2'} && $this->{$day . '_exit_2'}) {
            $entry2 = Carbon::parse($this->{$day . '_entry_2'});
            $exit2 = Carbon::parse($this->{$day . '_exit_2'});

            if ($exit2->lessThan($entry2)) {
                $exit2->addDay();
            }

            $totalMinutes += $entry2->diffInMinutes($exit2);
        }

        return round($totalMinutes / 60, 2);
    }

    public function getActiveDays()
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $activeDays = [];

        foreach ($days as $day) {
            if ($this->{$day . '_active'}) {
                $activeDays[] = $day;
            }
        }

        return $activeDays;
    }

    public function getInactiveDays()
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $inactiveDays = [];

        foreach ($days as $day) {
            if (!$this->{$day . '_active'}) {
                $inactiveDays[] = $day;
            }
        }

        return $inactiveDays;
    }

    public static function getDayNameInPortuguese($day)
    {
        $dayNames = [
            'monday' => 'Segunda-feira',
            'tuesday' => 'Terça-feira',
            'wednesday' => 'Quarta-feira',
            'thursday' => 'Quinta-feira',
            'friday' => 'Sexta-feira',
            'saturday' => 'Sábado',
            'sunday' => 'Domingo',
        ];

        return $dayNames[$day] ?? $day;
    }

    protected static function booted()
    {
        static::saving(function ($workHour) {
            $workHour->total_weekly_hours = $workHour->calculateWeeklyHours();
        });
    }

    public function formatHoursToTime($hours)
    {
        $totalMinutes = $hours * 60;
        $hoursFormatted = intval($totalMinutes / 60);
        $minutesFormatted = $totalMinutes % 60;

        return sprintf('%02d:%02d', $hoursFormatted, $minutesFormatted);
    }

    public function getFormattedWeeklyHours()
    {
        return $this->formatHoursToTime($this->total_weekly_hours);
    }

    public function formatTimeForInput($value)
    {
        if (empty($value)) {
            return '';
        }

        if (preg_match('/^\d{2}:\d{2}$/', $value)) {
            return $value;
        }

        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $value)) {
            return substr($value, 0, 5);
        }

        try {
            return \Carbon\Carbon::parse($value)->format('H:i');
        } catch (\Exception $e) {
            return '';
        }
    }

    public function getMondayEntry1Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getMondayExit1Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getMondayEntry2Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getMondayExit2Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getTuesdayEntry1Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getTuesdayExit1Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getTuesdayEntry2Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getTuesdayExit2Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getWednesdayEntry1Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getWednesdayExit1Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getWednesdayEntry2Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getWednesdayExit2Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getThursdayEntry1Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getThursdayExit1Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getThursdayEntry2Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getThursdayExit2Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getFridayEntry1Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getFridayExit1Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getFridayEntry2Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getFridayExit2Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getSaturdayEntry1Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getSaturdayExit1Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getSaturdayEntry2Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getSaturdayExit2Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getSundayEntry1Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getSundayExit1Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getSundayEntry2Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }

    public function getSundayExit2Attribute($value)
    {
        return $this->formatTimeForInput($value);
    }
}
