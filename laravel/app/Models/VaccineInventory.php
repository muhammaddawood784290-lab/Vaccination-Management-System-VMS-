<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VaccineInventory extends Model
{
    use HasFactory;

    protected $table = 'vaccine_inventory';

    protected $fillable = [
        'hospital_id',
        'vaccine_id',
        'available',
        'capacity',
        'batch_number',
        'expiry_date',
        'last_updated',
    ];

    protected $casts = [
        'available' => 'integer',
        'capacity' => 'integer',
        'expiry_date' => 'date',
        'last_updated' => 'datetime',
    ];

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
}
