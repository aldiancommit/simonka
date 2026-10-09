<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\JadwalKegiatan;
use App\Models\User;

class JadwalKegiatanPolicy
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
    public function view(User $user, JadwalKegiatan $jadwalKegiatan): bool
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
    public function update(User $user, JadwalKegiatan $jadwalKegiatan): bool
    {
        return $user->hasRole([Role::Admin, Role::Sekretariat]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JadwalKegiatan $jadwalKegiatan): bool
    {
        return $user->hasRole(Role::Admin);
    }
}
