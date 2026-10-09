<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Konsultasi;
use App\Models\User;

class KonsultasiPolicy
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
    public function view(User $user, Konsultasi $konsultasi): bool
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
    public function update(User $user, Konsultasi $konsultasi): bool
    {
        return $user->hasRole([Role::Admin, Role::Pimpinan, Role::Sekretariat]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Konsultasi $konsultasi): bool
    {
        return $user->hasRole(Role::Admin);
    }
}
