<?php

namespace App\Policies;

use App\Models\Hospital;
use App\Models\User;

class HospitalPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Hospital $hospital): bool
    {
        if ($user->isAdmin()) return true;
        if ($user->isParent()) return $hospital->status === 'active';
        if ($user->isHospital()) return $user->hospital_id === $hospital->id;
        return false;
    }

    public function update(User $user, Hospital $hospital): bool
    {
        if ($user->isAdmin()) return true;
        if ($user->isHospital()) return $user->hospital_id === $hospital->id;
        return false;
    }

    public function approve(User $user): bool
    {
        return $user->isAdmin();
    }
}
