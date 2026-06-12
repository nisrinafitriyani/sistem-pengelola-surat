<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Client $client): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Client $client): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->role === 'admin';
    }

    public function restore(User $user, Client $client): bool
    {
        return $user->role === 'admin';
    }

    public function forceDelete(User $user, Client $client): bool
    {
        return $user->role === 'admin';
    }
}
