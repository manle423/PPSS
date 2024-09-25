<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::inRandomOrder()->first()->id ?? Product::factory(), 
            'variant_name' => $this->faker->word,
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'variant_price' => $this->faker->randomFloat(2, 10, 100), // Min 10, Max 100
            'exp_date' => $this->faker->date(),
        ];
    }
}
