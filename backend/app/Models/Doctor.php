<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialty',
        'license_number',
        'bio',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    /**
     * Get the user that owns the doctor profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the patients assigned to this doctor.
     */
    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    /**
     * Get the appointments for this doctor.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the home visits for this doctor.
     */
    public function homeVisits()
    {
        return $this->hasMany(HomeVisit::class);
    }

    /**
     * Get the prescriptions written by this doctor.
     */
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    /**
     * Get the emergencies assigned to this doctor.
     */
    public function emergencies()
    {
        return $this->hasMany(Emergency::class);
    }
}
