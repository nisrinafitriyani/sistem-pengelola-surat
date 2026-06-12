<?php

namespace App\Policies;

use App\Models\Quotation;
use App\Models\User;

class QuotationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Quotation $quotation): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Quotation $quotation): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Quotation $quotation): bool
    {
        return $user->role === 'admin';
    }

    public function restore(User $user, Quotation $quotation): bool
    {
        return $user->role === 'admin';
    }

    public function forceDelete(User $user, Quotation $quotation): bool
    {
        return $user->role === 'admin';
    }
}
