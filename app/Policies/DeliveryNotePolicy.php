<?php

namespace App\Policies;

use App\Models\DeliveryNote;
use App\Models\User;

class DeliveryNotePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, DeliveryNote $deliveryNote): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, DeliveryNote $deliveryNote): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, DeliveryNote $deliveryNote): bool
    {
        return $user->role === 'admin';
    }

    public function restore(User $user, DeliveryNote $deliveryNote): bool
    {
        return $user->role === 'admin';
    }

    public function forceDelete(User $user, DeliveryNote $deliveryNote): bool
    {
        return $user->role === 'admin';
    }
}
