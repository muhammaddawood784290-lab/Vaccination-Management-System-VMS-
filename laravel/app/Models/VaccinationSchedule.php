<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VaccinationSchedule extends Model
{
    use HasFactory;

    protected $table = 'vaccination_schedules';

    protected $fillable = [
        'child_id',
        'vaccine_id',
        'dose_number',
        'target_age',
        'due_date',
        'status',
    ];

    protected $casts = [
        'dose_number' => 'integer',
        'due_date' => 'date',
    ];

    // --- Child ---
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    // --- Vaccine ---
    public function vaccine(): BelongsTo
    {
        return $this->belongsTo(Vaccine::class);
    }
}
