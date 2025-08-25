<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PositionModel extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'positions';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'department_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'department_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the department that owns this position.
     */
    public function department()
    {
        return $this->belongsTo(DepartmentModel::class, 'department_id');
    }

    /**
     * Get the collaborators for this position.
     */
    public function collaborators()
    {
        return $this->hasMany(CollaboratorModel::class, 'position_id');
    }
}
