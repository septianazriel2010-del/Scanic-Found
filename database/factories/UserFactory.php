<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => User::ROLE_STUDENT,
            'remember_token' => \Illuminate\Support\Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => User::ROLE_ADMIN]);
    }

    public function staff(): static
    {
        return $this->state(fn () => ['role' => User::ROLE_STAFF]);
    }

    public function teacher(): static
    {
        return $this->state(fn () => ['role' => User::ROLE_TEACHER]);
    }
}
