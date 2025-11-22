<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_date',
        'duration_minutes',
        'status',
        'type',
        'reason',
        'notes',
        'diagnosis',
        'created_by',
        'notification_sent',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'notification_sent' => 'boolean',
    ];

    /**
     * Get the patient for the appointment.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor for the appointment.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the user who created the appointment.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the prescriptions associated with this appointment.
     */
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
}
