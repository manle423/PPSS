<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_code' => $this->faker->unique()->regexify('HD[0-9]{8}'),
            'user_id' => User::inRandomOrder()->first()->id,
            'guest_order_id' => null,
            'status' => $this->faker->randomElement(['pending', 'completed', 'canceled']),
            'order_date' => $this->faker->dateTimeThisYear(),
            'shipping_address' => $this->faker->address,
            'shipping_method_id' => 1,
            'payment_method' => $this->faker->randomElement(['CREDIT_CARD', 'PAYPAL', 'VNPAY']),
            'promotion_id' => null,
            'coupon_id' => null,
            'total_price' => $this->faker->randomFloat(2, 10, 1000),
            'discount_value' => $this->faker->randomFloat(2, 1, 50),
            'final_price' => $this->faker->randomFloat(2, 5, 1000),
        ];
    }
}
