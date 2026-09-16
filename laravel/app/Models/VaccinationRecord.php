<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VaccinationRecord extends Model
{
    use HasFactory;

    protected $table = 'vaccination_records';

    protected $fillable = [
        'child_id',
        'hospital_id',
        'vaccine_id',
        'appointment_id',
        'dose_number',
        'batch_number',
        'administered_by',
        'administered_at',
        'notes',
    ];

    protected $casts = [
        'dose_number' => 'integer',
        'administered_at' => 'datetime',
    ];

    // --- Child ---
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
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

    // --- Appointment ---
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
