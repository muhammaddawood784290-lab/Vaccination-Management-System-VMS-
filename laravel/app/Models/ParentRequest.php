<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentRequest extends Model
{
    use HasFactory;

    protected $table = 'parent_requests';

    protected $fillable = [
        'user_id',
        'type',
        'subject',
        'details',
        'hospital_id',
        'status',
        'admin_response',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    // --- User (parent who submitted) ---
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // --- Hospital (target hospital, optional) ---
    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }
}
