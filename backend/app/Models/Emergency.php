<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emergency extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'guardian_id',
        'doctor_id',
        'description',
        'status',
        'priority',
        'location',
        'response_notes',
        'acknowledged_at',
        'resolved_at',
        'notification_sent',
    ];

    protected $casts = [
        'acknowledged_at' => 'datetime',
        'resolved_at' => 'datetime',
        'notification_sent' => 'boolean',
    ];

    /**
     * Get the patient for the emergency.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the guardian who reported the emergency.
     */
    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }

    /**
     * Get the doctor assigned to the emergency.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
