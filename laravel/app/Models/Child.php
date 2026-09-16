<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Child extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'date_of_birth',
        'gender',
        'blood_group',
        'relationship',
        'allergies',
        'notes',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    // --- Parent (user) ---
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // --- Vaccination Schedules ---
    public function vaccinationSchedules(): HasMany
    {
        return $this->hasMany(VaccinationSchedule::class);
    }

    // --- Appointments ---
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    // --- Vaccination Records ---
    public function vaccinationRecords(): HasMany
    {
        return $this->hasMany(VaccinationRecord::class);
    }
}
