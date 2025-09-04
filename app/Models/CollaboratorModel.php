<?php

namespace App\Models;

use App\Enums\CollaboratorStatusEnum;
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
        'status',
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
        'position_id' => 'integer',
        'status' => CollaboratorStatusEnum::class,
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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
     * Automatically hash the password when setting it.
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Clean CPF when setting it (remove non-numeric characters).
     */
    public function setCpfAttribute($value)
    {
        $this->attributes['cpf'] = preg_replace('/\D/', '', $value);
    }

    /**
     * Clean ZIP code when setting it (remove non-numeric characters).
     */
    public function setZipCodeAttribute($value)
    {
        $this->attributes['zip_code'] = preg_replace('/\D/', '', $value);
    }

    /**
     * Clean phone when setting it (remove non-numeric characters).
     */
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = preg_replace('/\D/', '', $value);
    }
}
