<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guardian_id',
        'doctor_id',
        'blood_type',
        'allergies',
        'medical_history',
        'current_medications',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    /**
     * Get the user that owns the patient profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the guardian of the patient.
     */
    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }

    /**
     * Get the doctor assigned to the patient.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the appointments for the patient.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the home visits for the patient.
     */
    public function homeVisits()
    {
        return $this->hasMany(HomeVisit::class);
    }

    /**
     * Get the prescriptions for the patient.
     */
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    /**
     * Get the emergencies for the patient.
     */
    public function emergencies()
    {
        return $this->hasMany(Emergency::class);
    }
}
