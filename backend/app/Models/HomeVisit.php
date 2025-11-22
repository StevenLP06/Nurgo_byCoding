<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'visit_date',
        'estimated_duration_minutes',
        'address',
        'status',
        'reason',
        'notes',
        'findings',
        'notification_sent',
    ];

    protected $casts = [
        'visit_date' => 'datetime',
        'notification_sent' => 'boolean',
    ];

    /**
     * Get the patient for the home visit.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor for the home visit.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
