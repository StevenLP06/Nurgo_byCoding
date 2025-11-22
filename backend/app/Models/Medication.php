<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'dosage_info',
        'side_effects',
        'contraindications',
        'requires_prescription',
        'is_active',
    ];

    protected $casts = [
        'requires_prescription' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the prescriptions for this medication.
     */
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
}
