<?php

namespace App\Models;

use App\Enums\Role;
use App\Enums\UserStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'status' => UserStatus::class,
            'last_login_at' => 'datetime',
        ];
    }

    public function hasRole(Role|string|array $roles): bool
    {
        if (empty($this->role)) {
            return false;
        }

        $roleValue = is_string($this->role) ? $this->role : $this->role->value;
        $rolesArray = is_array($roles) ? $roles : [$roles];

        foreach ($rolesArray as $r) {
            $compareValue = $r instanceof Role ? $r->value : (string) $r;
            if ($roleValue === $compareValue) {
                return true;
            }
        }

        return false;
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::Active && $this->role !== null;
    }

    public function assignRole(Role $role): void
    {
        $this->forceFill(['role' => $role])->save();
    }

    public function activate(): void
    {
        $this->forceFill(['status' => UserStatus::Active])->save();
    }

    public function deactivate(): void
    {
        $this->forceFill(['status' => UserStatus::Inactive])->save();
    }

    public function recordLogin(): void
    {
        $this->forceFill(['last_login_at' => now()])->save();
    }
}
