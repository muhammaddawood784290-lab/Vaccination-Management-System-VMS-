<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VaccinationRecord;

class VaccinationRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, VaccinationRecord $record): bool
    {
        if ($user->isAdmin()) return true;
        if ($user->isHospital()) return $user->hospital_id === $record->hospital_id;
        if ($user->isParent()) {
            return $record->child->user_id === $user->id;
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->isHospital();
    }
}
