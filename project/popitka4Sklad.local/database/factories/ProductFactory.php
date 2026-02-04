<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Manufacturer;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->optional()->paragraph(),
            'category_id' => Category::factory(),
            'manufacturer_id' => Manufacturer::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}