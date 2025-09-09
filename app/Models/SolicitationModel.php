<?php

namespace App\Models;

use App\Enums\SolicitationStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitationModel extends Model
{
    protected $table = 'solicitations';

    protected $fillable = [
        'status',
        'old_time_start',
        'old_time_finish',
        'new_time_start',
        'new_time_finish',
        'reason',
        'admin_comment',
        'time_tracking_id',
        'collaborator_id'
    ];

    protected function casts(): array
    {
        return [
            'status' => SolicitationStatusEnum::class,
            'old_time_start' => 'datetime',
            'old_time_finish' => 'datetime',
            'new_time_start' => 'datetime',
            'new_time_finish' => 'datetime',
        ];
    }

    /**
     * Relacionamento com o registro de time tracking
     */
    public function timeTracking(): BelongsTo
    {
        return $this->belongsTo(TimeTrackingModel::class, 'time_tracking_id');
    }

    /**
     * Relacionamento com o colaborador que fez a solicitação
     */
    public function collaborator(): BelongsTo
    {
        return $this->belongsTo(CollaboratorModel::class, 'collaborator_id');
    }
}
