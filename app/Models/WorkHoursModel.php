<?php

namespace App\Models;

use App\Enums\WorkHoursStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class WorkHoursModel extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'work_hours';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'total_weekly_hours',
        'description',
        'status',

        // Segunda-feira
        'monday_active',
        'monday_entry_1',
        'monday_exit_1',
        'monday_entry_2',
        'monday_exit_2',

        // Terça-feira
        'tuesday_active',
        'tuesday_entry_1',
        'tuesday_exit_1',
        'tuesday_entry_2',
        'tuesday_exit_2',

        // Quarta-feira
        'wednesday_active',
        'wednesday_entry_1',
        'wednesday_exit_1',
        'wednesday_entry_2',
        'wednesday_exit_2',

        // Quinta-feira
        'thursday_active',
        'thursday_entry_1',
        'thursday_exit_1',
        'thursday_entry_2',
        'thursday_exit_2',

        // Sexta-feira
        'friday_active',
        'friday_entry_1',
        'friday_exit_1',
        'friday_entry_2',
        'friday_exit_2',

        // Sábado
        'saturday_active',
        'saturday_entry_1',
        'saturday_exit_1',
        'saturday_entry_2',
        'saturday_exit_2',

        // Domingo
        'sunday_active',
        'sunday_entry_1',
        'sunday_exit_1',
        'sunday_entry_2',
        'sunday_exit_2',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'total_weekly_hours' => 'decimal:2',
        'status' => WorkHoursStatusEnum::class,

        // Dias ativos
        'monday_active' => 'boolean',
        'tuesday_active' => 'boolean',
        'wednesday_active' => 'boolean',
        'thursday_active' => 'boolean',
        'friday_active' => 'boolean',
        'saturday_active' => 'boolean',
        'sunday_active' => 'boolean',

        // Horários - Segunda
        'monday_entry_1' => 'datetime:H:i',
        'monday_exit_1' => 'datetime:H:i',
        'monday_entry_2' => 'datetime:H:i',
        'monday_exit_2' => 'datetime:H:i',

        // Horários - Terça
        'tuesday_entry_1' => 'datetime:H:i',
        'tuesday_exit_1' => 'datetime:H:i',
        'tuesday_entry_2' => 'datetime:H:i',
        'tuesday_exit_2' => 'datetime:H:i',

        // Horários - Quarta
        'wednesday_entry_1' => 'datetime:H:i',
        'wednesday_exit_1' => 'datetime:H:i',
        'wednesday_entry_2' => 'datetime:H:i',
        'wednesday_exit_2' => 'datetime:H:i',

        // Horários - Quinta
        'thursday_entry_1' => 'datetime:H:i',
        'thursday_exit_1' => 'datetime:H:i',
        'thursday_entry_2' => 'datetime:H:i',
        'thursday_exit_2' => 'datetime:H:i',

        // Horários - Sexta
        'friday_entry_1' => 'datetime:H:i',
        'friday_exit_1' => 'datetime:H:i',
        'friday_entry_2' => 'datetime:H:i',
        'friday_exit_2' => 'datetime:H:i',

        // Horários - Sábado
        'saturday_entry_1' => 'datetime:H:i',
        'saturday_exit_1' => 'datetime:H:i',
        'saturday_entry_2' => 'datetime:H:i',
        'saturday_exit_2' => 'datetime:H:i',

        // Horários - Domingo
        'sunday_entry_1' => 'datetime:H:i',
        'sunday_exit_1' => 'datetime:H:i',
        'sunday_entry_2' => 'datetime:H:i',
        'sunday_exit_2' => 'datetime:H:i',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get collaborators that use this work hour schedule.
     */
    public function collaborators()
    {
        return $this->hasMany(CollaboratorModel::class, 'work_hours_id');
    }

    /**
     * Get all active work hour schedules.
     */
    public function scopeActive($query)
    {
        return $query->where('status', WorkHoursStatusEnum::ACTIVE);
    }

    /**
     * Calculate total weekly hours based on all configured days.
     */
    public function calculateWeeklyHours()
    {
        $totalMinutes = 0;
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($days as $day) {
            if ($this->{$day . '_active'}) {
                // Primeiro turno
                if ($this->{$day . '_entry_1'} && $this->{$day . '_exit_1'}) {
                    $entry1 = Carbon::parse($this->{$day . '_entry_1'});
                    $exit1 = Carbon::parse($this->{$day . '_exit_1'});
                    $totalMinutes += $exit1->diffInMinutes($entry1);
                }

                // Segundo turno
                if ($this->{$day . '_entry_2'} && $this->{$day . '_exit_2'}) {
                    $entry2 = Carbon::parse($this->{$day . '_entry_2'});
                    $exit2 = Carbon::parse($this->{$day . '_exit_2'});
                    $totalMinutes += $exit2->diffInMinutes($entry2);
                }
            }
        }

        return round($totalMinutes / 60, 2); // Converter para horas com 2 casas decimais
    }

    /**
     * Get daily hours for a specific day.
     */
    public function getDailyHours($day)
    {
        if (!$this->{$day . '_active'}) {
            return 0;
        }

        $totalMinutes = 0;

        // Primeiro turno
        if ($this->{$day . '_entry_1'} && $this->{$day . '_exit_1'}) {
            $entry1 = Carbon::parse($this->{$day . '_entry_1'});
            $exit1 = Carbon::parse($this->{$day . '_exit_1'});
            $totalMinutes += $exit1->diffInMinutes($entry1);
        }

        // Segundo turno
        if ($this->{$day . '_entry_2'} && $this->{$day . '_exit_2'}) {
            $entry2 = Carbon::parse($this->{$day . '_entry_2'});
            $exit2 = Carbon::parse($this->{$day . '_exit_2'});
            $totalMinutes += $exit2->diffInMinutes($entry2);
        }

        return round($totalMinutes / 60, 2);
    }

    /**
     * Get array of active days.
     */
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

    /**
     * Get array of inactive days (for adding rules functionality).
     */
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

    /**
     * Get day name in Portuguese.
     */
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

    /**
     * Update total weekly hours automatically before saving.
     */
    protected static function booted()
    {
        static::saving(function ($workHour) {
            $workHour->total_weekly_hours = $workHour->calculateWeeklyHours();
        });
    }

    /**
     * Format hours to HH:MM format.
     */
    public function formatHoursToTime($hours)
    {
        $totalMinutes = $hours * 60;
        $hoursFormatted = intval($totalMinutes / 60);
        $minutesFormatted = $totalMinutes % 60;

        return sprintf('%02d:%02d', $hoursFormatted, $minutesFormatted);
    }

    /**
     * Get weekly hours formatted as HH:MM.
     */
    public function getFormattedWeeklyHours()
    {
        return $this->formatHoursToTime($this->total_weekly_hours);
    }
}
