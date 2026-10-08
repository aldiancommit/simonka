<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('APP_ADMIN_EMAIL');
        $password = env('APP_ADMIN_PASSWORD');
        $name = env('APP_ADMIN_NAME', 'Administrator SIMONKA');

        if (empty($email) || empty($password)) {
            throw new RuntimeException('Variabel APP_ADMIN_EMAIL dan APP_ADMIN_PASSWORD wajib dikonfigurasi di file environment sebelum menjalankan seeder.');
        }

        $validator = Validator::make(
            ['password' => $password],
            [
                'password' => [
                    'required',
                    'string',
                    Password::min(10)->letters()->mixedCase()->numbers()->symbols(),
                ],
            ]
        );

        if ($validator->fails()) {
            throw new RuntimeException('APP_ADMIN_PASSWORD tidak memenuhi kebijakan keamanan: '.implode(', ', $validator->errors()->all()));
        }

        $admin = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole(Role::Admin);
        $admin->activate();
    }
}
