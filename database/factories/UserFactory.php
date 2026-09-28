<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
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
            'phone' => '+880 17'.fake()->numerify('##-######'),
            'city' => fake()->randomElement(['Dhaka', 'Chattogram', 'Sylhet']),
            'address' => fake()->streetAddress(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password123'),
            'is_admin' => false,
            'status' => 'active',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * An administrator account.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
            'status' => 'active',
        ]);
    }

    /**
     * A deactivated customer account.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
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
