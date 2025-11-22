<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'relationship',
        'relationship_notes',
        'is_primary_contact',
    ];

    protected $casts = [
        'is_primary_contact' => 'boolean',
    ];

    /**
     * Get the user that owns the guardian profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the patients under this guardian's care.
     */
    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    /**
     * Get the emergencies reported by this guardian.
     */
    public function emergencies()
    {
        return $this->hasMany(Emergency::class);
    }
}
