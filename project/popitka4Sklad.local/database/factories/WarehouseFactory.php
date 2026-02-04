<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WarehouseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' Склад',
            'latitude' => $this->faker->latitude(55.5, 55.9),
            'longitude' => $this->faker->longitude(37.3, 37.8),
            'country' => 'RU',
            'city' => $this->faker->city(),
            'street' => $this->faker->streetName(),
            'house_number' => $this->faker->buildingNumber(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}