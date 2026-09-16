<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Hospital extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'contact_person',
        'designation',
        'total_beds',
        'description',
        'status',
        'rating',
    ];

    protected $casts = [
        'total_beds' => 'integer',
        'rating' => 'float',
    ];

    // --- Users (hospital staff) ---
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'hospital_id');
    }

    // --- Vaccine Inventory ---
    public function vaccineInventory(): HasMany
    {
        return $this->hasMany(VaccineInventory::class);
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

    // --- Parent Requests ---
    public function parentRequests(): HasMany
    {
        return $this->hasMany(ParentRequest::class);
    }

    // --- Convenience: available vaccines ---
    public function availableVaccines(): HasManyThrough
    {
        return $this->hasManyThrough(Vaccine::class, VaccineInventory::class, 'hospital_id', 'id', 'id', 'vaccine_id');
    }
}
