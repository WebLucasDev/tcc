<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PositionModel extends Model
{
    protected $table = 'positions';

    protected $fillable = [
        'name',
        'department_id',
    ];

    protected $casts = [
        'department_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(DepartmentModel::class, 'department_id');
    }

    public function collaborators()
    {
        return $this->hasMany(CollaboratorModel::class, 'position_id');
    }
}
