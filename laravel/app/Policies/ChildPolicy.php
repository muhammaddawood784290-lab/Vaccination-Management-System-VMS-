<?php

namespace App\Policies;

use App\Models\Child;
use App\Models\User;

class ChildPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->isAdmin()) return true;
        return false;
    }

    public function view(User $user, Child $child): bool
    {
        if ($user->isAdmin()) return true;
        return $user->isParent() && $user->id === $child->user_id;
    }

    public function create(User $user): bool
    {
        return $user->isParent();
    }

    public function update(User $user, Child $child): bool
    {
        if ($user->isAdmin()) return true;
        return $user->isParent() && $user->id === $child->user_id;
    }

    public function delete(User $user, Child $child): bool
    {
        if ($user->isAdmin()) return true;
        return $user->isParent() && $user->id === $child->user_id;
    }
}
