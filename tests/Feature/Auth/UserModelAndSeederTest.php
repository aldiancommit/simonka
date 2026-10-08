<?php

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;

test('1. Role and UserStatus enums have expected cases and labels', function () {
    expect(Role::Admin->value)->toBe('admin')
        ->and(Role::Pimpinan->value)->toBe('pimpinan')
        ->and(Role::Sekretariat->value)->toBe('sekretariat')
        ->and(Role::Admin->label())->toBe('Administrator')
        ->and(Role::Pimpinan->label())->toBe('Pimpinan')
        ->and(Role::Sekretariat->label())->toBe('Sekretariat');

    expect(UserStatus::Active->value)->toBe('active')
        ->and(UserStatus::Inactive->value)->toBe('inactive')
        ->and(UserStatus::Active->label())->toBe('Aktif')
        ->and(UserStatus::Inactive->label())->toBe('Nonaktif');

    // Ensure pending status does not exist in UserStatus
    $statusCases = array_map(fn ($case) => $case->value, UserStatus::cases());
    expect($statusCases)->not->toContain('pending');
});

test('2. role status and last_login_at are guarded from mass-assignment', function () {
    $user = User::create([
        'name' => 'Mass Assignment Test',
        'email' => 'mass_assign@example.com',
        'password' => 'SafePassword123!@#',
        'role' => 'admin',
        'status' => 'inactive',
        'last_login_at' => now(),
    ]);

    $user->refresh();

    // role should remain null because it is guarded from mass-assignment
    expect($user->role)->toBeNull()
        ->and($user->status)->toBe(UserStatus::Active) // default from migration
        ->and($user->last_login_at)->toBeNull();
});

test('3. user explicit domain methods manage role, status, and login activity properly', function () {
    $user = User::factory()->create([
        'role' => null,
        'status' => UserStatus::Active,
    ]);

    // isActive should return false if role is null
    expect($user->isActive())->toBeFalse();

    // Assign role
    $user->assignRole(Role::Admin);
    $user->refresh();

    expect($user->role)->toBe(Role::Admin)
        ->and($user->hasRole(Role::Admin))->toBeTrue()
        ->and($user->hasRole('admin'))->toBeTrue()
        ->and($user->hasRole(['pimpinan', 'admin']))->toBeTrue()
        ->and($user->hasRole('sekretariat'))->toBeFalse()
        ->and($user->isActive())->toBeTrue();

    // Deactivate
    $user->deactivate();
    $user->refresh();

    expect($user->status)->toBe(UserStatus::Inactive)
        ->and($user->isActive())->toBeFalse();

    // Activate
    $user->activate();
    $user->refresh();

    expect($user->status)->toBe(UserStatus::Active)
        ->and($user->isActive())->toBeTrue();

    // Record login
    expect($user->last_login_at)->toBeNull();
    $user->recordLogin();
    $user->refresh();

    expect($user->last_login_at)->not->toBeNull();
});

test('4. AdminUserSeeder fails with RuntimeException if env variables are missing or password is weak', function () {
    // 1. Missing env
    putenv('APP_ADMIN_EMAIL=');
    putenv('APP_ADMIN_PASSWORD=');
    unset($_ENV['APP_ADMIN_EMAIL'], $_ENV['APP_ADMIN_PASSWORD'], $_SERVER['APP_ADMIN_EMAIL'], $_SERVER['APP_ADMIN_PASSWORD']);

    expect(function () {
        $seeder = new AdminUserSeeder;
        $seeder->run();
    })->toThrow(RuntimeException::class, 'Variabel APP_ADMIN_EMAIL dan APP_ADMIN_PASSWORD wajib dikonfigurasi');

    // 2. Weak password (too short or missing required character types)
    putenv('APP_ADMIN_EMAIL=admin_test@simonka.palukota.go.id');
    putenv('APP_ADMIN_PASSWORD=short');
    $_ENV['APP_ADMIN_EMAIL'] = 'admin_test@simonka.palukota.go.id';
    $_ENV['APP_ADMIN_PASSWORD'] = 'short';

    expect(function () {
        $seeder = new AdminUserSeeder;
        $seeder->run();
    })->toThrow(RuntimeException::class);
});

test('5. AdminUserSeeder runs idempotently with valid env credentials', function () {
    putenv('APP_ADMIN_EMAIL=admin_seeder_test@simonka.palukota.go.id');
    putenv('APP_ADMIN_PASSWORD=AdminSecurePassword123!@#');
    putenv('APP_ADMIN_NAME=Admin SIMONKA Test');
    $_ENV['APP_ADMIN_EMAIL'] = 'admin_seeder_test@simonka.palukota.go.id';
    $_ENV['APP_ADMIN_PASSWORD'] = 'AdminSecurePassword123!@#';
    $_ENV['APP_ADMIN_NAME'] = 'Admin SIMONKA Test';
    $_SERVER['APP_ADMIN_EMAIL'] = 'admin_seeder_test@simonka.palukota.go.id';
    $_SERVER['APP_ADMIN_PASSWORD'] = 'AdminSecurePassword123!@#';
    $_SERVER['APP_ADMIN_NAME'] = 'Admin SIMONKA Test';

    $seeder = new AdminUserSeeder;

    // Run 1st time
    $seeder->run();

    expect(User::where('email', 'admin_seeder_test@simonka.palukota.go.id')->count())->toBe(1);
    $admin = User::where('email', 'admin_seeder_test@simonka.palukota.go.id')->first();
    expect($admin->role)->toBe(Role::Admin)
        ->and($admin->status)->toBe(UserStatus::Active)
        ->and($admin->name)->toBe('Admin SIMONKA Test');

    // Run 2nd time (idempotency check)
    $seeder->run();

    expect(User::where('email', 'admin_seeder_test@simonka.palukota.go.id')->count())->toBe(1);
});
