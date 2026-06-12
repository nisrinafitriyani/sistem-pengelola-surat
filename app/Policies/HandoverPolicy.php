<?php

namespace App\Policies;

use App\Models\Handover;
use App\Models\User;

class HandoverPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Handover $handover): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Handover $handover): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Handover $handover): bool
    {
        return $user->role === 'admin';
    }

    public function restore(User $user, Handover $handover): bool
    {
        return $user->role === 'admin';
    }

    public function forceDelete(User $user, Handover $handover): bool
    {
        return $user->role === 'admin';
    }
}
