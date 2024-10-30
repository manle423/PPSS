<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::inRandomOrder()->first();
        $variant = $product->variants ? $product->variants->random() : null;
        $maxCartAmount = $variant != null ? $variant->stock_quantity : $product->stock_quantity;
        
        return [
            //
            'product_id' => $product->id,
            'user_id' => User::where('role',"BUYER")->inRandomOrder()->first()->id,
            'quantity' => $this->faker->numberBetween(1, $maxCartAmount),
            'variant_id' => $variant->id,
        ];
    }
}
