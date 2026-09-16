<?php

namespace App\Policies;

use App\Models\ParentRequest;
use App\Models\User;

class ParentRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, ParentRequest $request): bool
    {
        if ($user->isAdmin()) return true;
        if ($user->isParent()) return $user->id === $request->user_id;
        return false;
    }

    public function create(User $user): bool
    {
        return $user->isParent();
    }

    public function update(User $user, ParentRequest $request): bool
    {
        return $user->isAdmin();
    }
}
