<?php

namespace App\Models;

use App\Enums\TimeTrackingStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TimeTrackingModel extends Model
{
    protected $table = 'time_tracking';

    protected $fillable = [
        'collaborator_id',
        'date',
        'entry_time_1',
        'entry_time_1_observation',
        'return_time_1',
        'return_time_1_observation',
        'entry_time_2',
        'entry_time_2_observation',
        'return_time_2',
        'return_time_2_observation',
        'observations',
        'status',
        'total_hours_worked',
        'action',
    ];

    protected $casts = [
        'date' => 'date',
        'entry_time_1' => 'datetime:H:i',
        'return_time_1' => 'datetime:H:i',
        'entry_time_2' => 'datetime:H:i',
        'return_time_2' => 'datetime:H:i',
        'status' => TimeTrackingStatusEnum::class,
    ];

    public function collaborator(): BelongsTo
    {
        return $this->belongsTo(CollaboratorModel::class, 'collaborator_id');
    }

    public function calculateWorkedHours(): int
    {
        $totalMinutes = 0;

        if ($this->entry_time_1 && $this->return_time_1) {
            $morning = Carbon::parse($this->entry_time_1)->diffInMinutes(Carbon::parse($this->return_time_1));
            $totalMinutes += $morning;
        }

        if ($this->entry_time_2 && $this->return_time_2) {
            $afternoon = Carbon::parse($this->entry_time_2)->diffInMinutes(Carbon::parse($this->return_time_2));
            $totalMinutes += $afternoon;
        }

        return $totalMinutes;
    }

    public function getFormattedWorkedHoursAttribute(): string
    {
        if (!$this->total_hours_worked) {
            return '00:00';
        }

        $hours = intval($this->total_hours_worked / 60);
        $minutes = $this->total_hours_worked % 60;

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    public function isComplete(): bool
    {
        return !is_null($this->entry_time_1) &&
               !is_null($this->return_time_1) &&
               !is_null($this->entry_time_2) &&
               !is_null($this->return_time_2);
    }

    public function updateStatus(): void
    {
        if ($this->isComplete()) {
            $this->status = TimeTrackingStatusEnum::COMPLETO;
            $this->total_hours_worked = $this->calculateWorkedHours();
        } elseif ($this->entry_time_1 || $this->return_time_1 || $this->entry_time_2 || $this->return_time_2) {
            $this->status = TimeTrackingStatusEnum::INCOMPLETO;
            $this->total_hours_worked = $this->calculateWorkedHours();
        } else {
            $this->status = TimeTrackingStatusEnum::AUSENTE;
            $this->total_hours_worked = 0;
        }
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeByCollaborator($query, $collaboratorId)
    {
        return $query->where('collaborator_id', $collaboratorId);
    }

    public function scopeCurrentMonth($query)
    {
        return $query->whereMonth('date', now()->month)
                    ->whereYear('date', now()->year);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($timeTracking) {
            $timeTracking->updateStatus();
        });
    }
}
