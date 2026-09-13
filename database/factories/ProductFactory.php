<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'brand' => fake()->company(),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 20, 200),
            'stock' => fake()->numberBetween(5, 30),
            'image_path' => 'img/products/f1.jpg',
            'is_active' => true,
        ];
    }
}
