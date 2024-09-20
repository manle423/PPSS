<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
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
            'name' => $this->faker->word,
            'category_id' => $this->faker->numberBetween(1, 10), // Adjust range as needed
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 10, 100), // Min 10, Max 100
            'stock_quantity' => $this->faker->numberBetween(0, 100),
        ];
    }
}
