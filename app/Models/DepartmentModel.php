<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentModel extends Model
{
    protected $table = 'departments';

    protected $fillable = [
        'name',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function positions()
    {
        return $this->hasMany(PositionModel::class, 'department_id');
    }

    public function collaborators()
    {
        return $this->hasManyThrough(
            CollaboratorModel::class,
            PositionModel::class,
            'department_id',
            'position_id',
            'id',
            'id'
        );
    }
}
