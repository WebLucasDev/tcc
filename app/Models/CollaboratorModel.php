<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class CollaboratorModel extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     */
    protected $table = 'collaborators';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'admission_date',
        'phone',
        'zip_code',
        'street',
        'neighborhood',
        'number',
        'entry_time_1',
        'entry_time_2',
        'return_time_1',
        'return_time_2',
        'position_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'admission_date' => 'date',
        'entry_time_1' => 'datetime:H:i',
        'entry_time_2' => 'datetime:H:i',
        'return_time_1' => 'datetime:H:i',
        'return_time_2' => 'datetime:H:i',
        'position_id' => 'integer',
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Automatically hash the password when setting it.
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Get the department that owns the collaborator (via position).
     */
    public function department()
    {
        return $this->hasOneThrough(
            DepartmentModel::class,
            PositionModel::class,
            'id', // Foreign key on the positions table
            'id', // Foreign key on the departments table
            'position_id', // Local key on the collaborators table
            'department_id' // Local key on the positions table
        );
    }

    /**
     * Get the position that owns the collaborator.
     */
    public function position()
    {
        return $this->belongsTo(PositionModel::class, 'position_id');
    }

    /**
     * Get the time tracking records for the collaborator.
     */
    public function timeTrackings()
    {
        return $this->hasMany(TimeTrackingModel::class, 'collaborator_id');
    }

    /**
     * Format CPF for display.
     */
    public function getFormattedCpfAttribute()
    {
        $cpf = preg_replace('/\D/', '', $this->cpf);
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
    }

    /**
     * Format phone for display.
     */
    public function getFormattedPhoneAttribute()
    {
        if (!$this->phone) return null;

        $phone = preg_replace('/\D/', '', $this->phone);
        if (strlen($phone) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $phone);
        }
        return $this->phone;
    }

    /**
     * Format ZIP code for display.
     */
    public function getFormattedZipCodeAttribute()
    {
        if (!$this->zip_code) return null;

        $zipCode = preg_replace('/\D/', '', $this->zip_code);
        return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $zipCode);
    }

    /**
     * Get full address.
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->street,
            $this->number ? "nº {$this->number}" : null,
            $this->neighborhood,
            $this->formatted_zip_code,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Scope to filter by department (via position).
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->whereHas('position', function ($q) use ($departmentId) {
            $q->where('department_id', $departmentId);
        });
    }

    /**
     * Scope to filter by position.
     */
    public function scopeByPosition($query, $positionId)
    {
        return $query->where('position_id', $positionId);
    }

    /**
     * Scope to search by name or email.
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('cpf', 'like', "%{$term}%");
        });
    }
}
