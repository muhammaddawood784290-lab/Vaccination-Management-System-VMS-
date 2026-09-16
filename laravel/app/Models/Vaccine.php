<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vaccine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'doses',
        'age_range',
        'type',
        'manufacturer',
        'description',
        'status',
    ];

    protected $casts = [
        'doses' => 'integer',
    ];

    // --- Vaccine Inventory (hospital stocks) ---
    public function inventory(): HasMany
    {
        return $this->hasMany(VaccineInventory::class);
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
