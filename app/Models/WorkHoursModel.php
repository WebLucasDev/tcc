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
        'monday_entry_1' => 'string',
        'monday_exit_1' => 'string',
        'monday_entry_2' => 'string',
        'monday_exit_2' => 'string',

        // Horários - Terça
        'tuesday_entry_1' => 'string',
        'tuesday_exit_1' => 'string',
        'tuesday_entry_2' => 'string',
        'tuesday_exit_2' => 'string',

        // Horários - Quarta
        'wednesday_entry_1' => 'string',
        'wednesday_exit_1' => 'string',
        'wednesday_entry_2' => 'string',
        'wednesday_exit_2' => 'string',

        // Horários - Quinta
        'thursday_entry_1' => 'string',
        'thursday_exit_1' => 'string',
        'thursday_entry_2' => 'string',
        'thursday_exit_2' => 'string',

        // Horários - Sexta
        'friday_entry_1' => 'string',
        'friday_exit_1' => 'string',
        'friday_entry_2' => 'string',
        'friday_exit_2' => 'string',

        // Horários - Sábado
        'saturday_entry_1' => 'string',
        'saturday_exit_1' => 'string',
        'saturday_entry_2' => 'string',
        'saturday_exit_2' => 'string',

        // Horários - Domingo
        'sunday_entry_1' => 'string',
        'sunday_exit_1' => 'string',
        'sunday_entry_2' => 'string',
        'sunday_exit_2' => 'string',

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
                    
                    // Para horários noturnos (saída no dia seguinte)
                    if ($exit1->lessThan($entry1)) {
                        $exit1->addDay();
                    }
                    
                    $totalMinutes += $entry1->diffInMinutes($exit1);
                }

                // Segundo turno
                if ($this->{$day . '_entry_2'} && $this->{$day . '_exit_2'}) {
                    $entry2 = Carbon::parse($this->{$day . '_entry_2'});
                    $exit2 = Carbon::parse($this->{$day . '_exit_2'});
                    
                    // Para horários noturnos (saída no dia seguinte)
                    if ($exit2->lessThan($entry2)) {
                        $exit2->addDay();
                    }
                    
                    $totalMinutes += $entry2->diffInMinutes($exit2);
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
            
            // Para horários noturnos (saída no dia seguinte)
            if ($exit1->lessThan($entry1)) {
                $exit1->addDay();
            }
            
            $totalMinutes += $entry1->diffInMinutes($exit1);
        }

        // Segundo turno
        if ($this->{$day . '_entry_2'} && $this->{$day . '_exit_2'}) {
            $entry2 = Carbon::parse($this->{$day . '_entry_2'});
            $exit2 = Carbon::parse($this->{$day . '_exit_2'});
            
            // Para horários noturnos (saída no dia seguinte)
            if ($exit2->lessThan($entry2)) {
                $exit2->addDay();
            }
            
            $totalMinutes += $entry2->diffInMinutes($exit2);
        }

        return round($totalMinutes / 60, 2); // Converter para horas com 2 casas decimais
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

    /**
     * Accessor para formatar horários de entrada/saída para campos time HTML.
     * Remove segundos se presentes e garante formato HH:MM
     */
    public function formatTimeForInput($value)
    {
        if (empty($value)) {
            return '';
        }

        // Se já está no formato correto HH:MM, retorna como está
        if (preg_match('/^\d{2}:\d{2}$/', $value)) {
            return $value;
        }

        // Se tem segundos (HH:MM:SS), remove os segundos
        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $value)) {
            return substr($value, 0, 5);
        }

        // Tentar parsear com Carbon para garantir formato correto
        try {
            return \Carbon\Carbon::parse($value)->format('H:i');
        } catch (\Exception $e) {
            return '';
        }
    }

    // Accessors para todos os campos de horário
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

    // Terça-feira
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

    // Quarta-feira
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

    // Quinta-feira
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

    // Sexta-feira
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

    // Sábado
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

    // Domingo
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
