<?php

namespace App\Policies;

use App\Models\Approval;
use App\Models\User;

class ApprovalPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Approval $approval): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Approval $approval): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Approval $approval): bool
    {
        return $user->role === 'admin';
    }

    public function restore(User $user, Approval $approval): bool
    {
        return $user->role === 'admin';
    }

    public function forceDelete(User $user, Approval $approval): bool
    {
        return $user->role === 'admin';
    }
}
