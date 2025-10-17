<?php

namespace App\Models;

use App\Enums\CollaboratorStatusEnum;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class CollaboratorModel extends Authenticatable
{
    protected $table = 'collaborators';

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
        'position_id',
        'work_hours_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'admission_date' => 'date',
        'position_id' => 'integer',
        'status' => CollaboratorStatusEnum::class,
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function position()
    {
        return $this->belongsTo(PositionModel::class, 'position_id');
    }

    public function workHours()
    {
        return $this->belongsTo(WorkHoursModel::class, 'work_hours_id');
    }

    public function timeTrackings()
    {
        return $this->hasMany(TimeTrackingModel::class, 'collaborator_id');
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function setCpfAttribute($value)
    {
        $this->attributes['cpf'] = preg_replace('/\D/', '', $value);
    }

    public function setZipCodeAttribute($value)
    {
        $this->attributes['zip_code'] = preg_replace('/\D/', '', $value);
    }

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = preg_replace('/\D/', '', $value);
    }
}
