<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['admin', 'employee', 'client']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'admin',
        ]);
    }

    public function employee(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'employee',
        ]);
    }

    public function client(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'client',
        ]);
    }
}