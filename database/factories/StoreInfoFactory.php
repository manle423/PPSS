<?php

namespace Database\Factories;

use App\Models\StoreInfo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StoreInfo>
 */
class StoreInfoFactory extends Factory
{
    protected $model = StoreInfo::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'description' => $this->faker->sentence,
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->email
        ];
    }
}