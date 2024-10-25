<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Create a new user or use an existing one
            'full_name' => $this->faker->name,
            'phone_number' => $this->faker->phoneNumber,
            'address_line_1' => $this->faker->streetAddress,
            'address_line_2' => $this->faker->secondaryAddress,
            'province_id' => $this->faker->numberBetween(1, 100), // Adjust range as needed
            'district_id' => $this->faker->numberBetween(1, 100), // Adjust range as needed
            'ward_id' => $this->faker->numberBetween(1, 100), // Adjust range as needed
            'is_default' => $this->faker->boolean,
        ];
    }
}