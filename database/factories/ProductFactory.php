<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => \App\Models\Category::factory(),
            'name' => fake()->words(3, true),
            'barcode' => fake()->unique()->ean13(),
            'cost_price' => fake()->randomFloat(2, 5, 50),
            'selling_price' => fake()->randomFloat(2, 60, 200),
            'stock' => fake()->numberBetween(10, 100),
            'image' => null,
            'description' => fake()->paragraph(),
        ];
    }
}
