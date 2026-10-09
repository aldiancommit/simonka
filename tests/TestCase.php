<?php

namespace Tests;

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    protected function beforeRefreshingDatabase()
    {
        $this->ensureSafeTestDatabase();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->ensureSafeTestDatabase();
    }

    protected function ensureSafeTestDatabase(): void
    {
        $activeDb = config('database.connections.'.config('database.default').'.database');
        if ($activeDb !== 'simonka_test') {
            throw new RuntimeException(
                "SAFETY GUARD BLOCKED EXECUTION: Active database is '{$activeDb}'. Tests are strictly restricted to 'simonka_test' to prevent database mutation or data loss!"
            );
        }
    }

    public function actingAsRole(Role|string $role = Role::Admin, array $attributes = []): User
    {
        $roleEnum = is_string($role) ? Role::from($role) : $role;

        $user = User::factory()->create(array_merge([
            'role' => $roleEnum,
            'status' => UserStatus::Active,
            'email_verified_at' => now(),
        ], $attributes));

        $this->actingAs($user);

        return $user;
    }
}
