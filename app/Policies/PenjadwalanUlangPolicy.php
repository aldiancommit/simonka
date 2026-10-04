<?php

namespace App\Policies;

use App\Models\PenjadwalanUlang;
use App\Models\User;

class PenjadwalanUlangPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PenjadwalanUlang $penjadwalanUlang): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PenjadwalanUlang $penjadwalanUlang): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PenjadwalanUlang $penjadwalanUlang): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PenjadwalanUlang $penjadwalanUlang): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PenjadwalanUlang $penjadwalanUlang): bool
    {
        return false;
    }
}
