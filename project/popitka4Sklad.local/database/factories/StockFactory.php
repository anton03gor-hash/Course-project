<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'warehouse_id' => Warehouse::factory(),
            'quantity' => $this->faker->randomFloat(2, 0, 1000),
            'position' => $this->faker->optional()->bothify('Стеллаж ##-Полка #'),
            'last_update' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function withQuantity(float $quantity): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => $quantity,
        ]);
    }
}