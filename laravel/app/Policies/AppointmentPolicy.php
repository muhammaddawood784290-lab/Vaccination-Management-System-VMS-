<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->isAdmin()) return true;
        if ($user->isParent()) return $user->id === $appointment->parent_id;
        if ($user->isHospital()) return $user->hospital_id === $appointment->hospital_id;
        return false;
    }

    public function create(User $user): bool
    {
        return $user->isParent();
    }

    public function update(User $user, Appointment $appointment): bool
    {
        if ($user->isAdmin()) return true;
        if ($user->isHospital()) return $user->hospital_id === $appointment->hospital_id;
        return false;
    }

    public function cancel(User $user, Appointment $appointment): bool
    {
        if ($user->isAdmin()) return true;
        if ($user->isParent()) {
            return $user->id === $appointment->parent_id
                && in_array($appointment->status, ['pending', 'approved']);
        }
        return false;
    }
}
