<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition()
    {
        $startDate = now();
        $endDate = now()->addDays(rand(1, 30));
        
        return [
            'code' => strtoupper($this->faker->unique()->bothify('???###')),
            'discount_value' => $this->faker->randomFloat(2, 0.1, 0.5),
            'min_order_value' => $this->faker->numberBetween(100000, 1000000),
            'max_discount' => $this->faker->numberBetween(50000, 200000),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $this->faker->boolean()
        ];
    }

    /**
     * Active coupon state
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 1,
                'end_date' => now()->addDays(10)
            ];
        });
    }

    /**
     * Inactive coupon state
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 0
            ];
        });
    }

    /**
     * Expired coupon state
     */
    public function expired()
    {
        return $this->state(function (array $attributes) {
            return [
                'end_date' => now()->subDays(1)
            ];
        });
    }
}