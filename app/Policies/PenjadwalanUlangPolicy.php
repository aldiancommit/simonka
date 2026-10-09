<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\PenjadwalanUlang;
use App\Models\User;

class PenjadwalanUlangPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isActive();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PenjadwalanUlang $penjadwalanUlang): bool
    {
        return $user->isActive();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole([Role::Admin, Role::Sekretariat]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PenjadwalanUlang $penjadwalanUlang): bool
    {
        return $user->hasRole([Role::Admin, Role::Pimpinan, Role::Sekretariat]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PenjadwalanUlang $penjadwalanUlang): bool
    {
        return $user->hasRole(Role::Admin);
    }
}
