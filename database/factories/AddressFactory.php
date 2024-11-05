<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use App\Models\Province;
use App\Models\District;
use App\Models\Ward;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition()
    {
        $province = Province::inRandomOrder()->first();
        $district = District::where('province_id', $province->id)->inRandomOrder()->first();
        $ward = Ward::where('district_id', $district->id)->inRandomOrder()->first();

        return [
            'user_id' => User::factory(),
            'full_name' => $this->faker->name,
            'phone_number' => $this->faker->phoneNumber,
            'address_line_1' => $this->faker->streetAddress,
            'address_line_2' => $this->faker->secondaryAddress,
            'province_id' => $province->id,
            'district_id' => $district->id,
            'ward_id' => $ward->id,
            'is_default' => $this->faker->boolean,
        ];
    }
}
