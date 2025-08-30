<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DepartmentModel extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'departments';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the positions for this department.
     */
    public function positions()
    {
        return $this->hasMany(PositionModel::class, 'department_id');
    }

    /**
     * Get the collaborators for this department (via positions).
     */
    public function collaborators()
    {
        return $this->hasManyThrough(
            CollaboratorModel::class,
            PositionModel::class,
            'department_id', // Foreign key on the positions table
            'position_id', // Foreign key on the collaborators table
            'id', // Local key on the departments table
            'id' // Local key on the positions table
        );
    }
}
