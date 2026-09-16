<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'city',
        'hospital_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- Hospital (for hospital-role users) ---
    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    // --- Children (for parent-role users) ---
    public function children(): HasMany
    {
        return $this->hasMany(Child::class);
    }

    // --- Appointments (for parent-role users) ---
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'parent_id');
    }

    // --- Parent Requests (submitted by parent) ---
    public function parentRequests(): HasMany
    {
        return $this->hasMany(ParentRequest::class);
    }

    // --- Notifications ---
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    // --- Helper: check role ---
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isParent(): bool
    {
        return $this->role === 'parent';
    }

    public function isHospital(): bool
    {
        return $this->role === 'hospital';
    }
}
