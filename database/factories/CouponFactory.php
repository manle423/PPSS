<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->lexify('COUPON????')),
            'discount_value' => $this->faker->randomFloat(2, 0, 0.5), // Random discount value between 0 and 100
            'min_order_value' => $this->faker->numberBetween(0, 1000000), // Random minimum order value
            'max_discount' => $this->faker->numberBetween(0, 500000), // Random maximum discount
            'start_date' => $this->faker->dateTimeBetween('-1 month', 'now'), // Random start date within the last month
            'end_date' => $this->faker->dateTimeBetween('now', '+1 month'), // Random end date within the next month
            'status' => $this->faker->boolean(80), // 80% chance of being active
        ];
    }
}
