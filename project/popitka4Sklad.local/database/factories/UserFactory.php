<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => $this->faker->firstName(),
            'surname' => $this->faker->lastName(),
            'fathername' => $this->faker->optional()->firstName(),
            'phone' => '7' . $this->faker->numerify('##########'),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'role_id' => Role::factory(),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::factory()->admin(),
        ]);
    }

    public function employee(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::factory()->employee(),
        ]);
    }

    public function client(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::factory()->client(),
        ]);
    }
}