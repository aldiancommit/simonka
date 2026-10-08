<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('Password123!@#'),
            'role' => Role::Sekretariat,
            'status' => UserStatus::Active,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * State: Administrator.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::Admin,
            'status' => UserStatus::Active,
        ]);
    }

    /**
     * State: Pimpinan.
     */
    public function pimpinan(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::Pimpinan,
            'status' => UserStatus::Active,
        ]);
    }

    /**
     * State: Sekretariat.
     */
    public function sekretariat(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::Sekretariat,
            'status' => UserStatus::Active,
        ]);
    }

    /**
     * State: Inactive user.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserStatus::Inactive,
        ]);
    }

    /**
     * State: User without role.
     */
    public function unassignedRole(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => null,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
