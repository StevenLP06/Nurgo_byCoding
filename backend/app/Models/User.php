<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
        'birth_date',
        'gender',
        'address',
        'document_type',
        'document_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['role_name'];

    /**
     * Get the role name accessor.
     */
    public function getRoleNameAttribute()
    {
        return $this->role ? $this->role->name : null;
    }

    /**
     * Get the role of the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the doctor profile if user is a doctor.
     */
    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    /**
     * Get the guardian profile if user is a guardian.
     */
    public function guardian()
    {
        return $this->hasOne(Guardian::class);
    }

    /**
     * Get the patient profile if user is a patient.
     */
    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is a doctor.
     */
    public function isDoctor()
    {
        return $this->hasRole('doctor');
    }

    /**
     * Check if user is a patient.
     */
    public function isPatient()
    {
        return $this->hasRole('patient');
    }

    /**
     * Check if user is a guardian.
     */
    public function isGuardian()
    {
        return $this->hasRole('guardian');
    }
}
