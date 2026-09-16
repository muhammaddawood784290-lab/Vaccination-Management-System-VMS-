<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'parent_id',
        'hospital_id',
        'vaccine_id',
        'dose',
        'appointment_date',
        'appointment_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'datetime:H:i',
    ];

    // --- Child ---
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    // --- Parent (user) ---
    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    // --- Hospital ---
    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    // --- Vaccine ---
    public function vaccine(): BelongsTo
    {
        return $this->belongsTo(Vaccine::class);
    }

    // --- Vaccination Record (result of this appointment) ---
    public function vaccinationRecord(): HasOne
    {
        return $this->hasOne(VaccinationRecord::class);
    }
}
