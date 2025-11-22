<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'medication_id',
        'appointment_id',
        'dosage',
        'frequency',
        'duration_days',
        'instructions',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the patient for the prescription.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor who prescribed the medication.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the medication.
     */
    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }

    /**
     * Get the appointment associated with this prescription.
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
