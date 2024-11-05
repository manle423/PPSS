<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\District;
use App\Models\Ward;
use Illuminate\Support\Facades\File;

class LocationSeeder extends Seeder
{
    public function run()
    {
        $locations = json_decode(File::get(database_path('data/locations.json')), true);

        foreach ($locations['provinces'] as $province) {
            Province::create([
                'id' => $province['ProvinceID'],
                'name' => $province['ProvinceName'],
            ]);
        }

        foreach ($locations['districts'] as $district) {
            District::create([
                'id' => $district['DistrictID'],
                'name' => $district['DistrictName'],
                'province_id' => $district['ProvinceID'],
            ]);
        }

        foreach ($locations['wards'] as $ward) {
            Ward::create([
                'id' => $ward['WardCode'],
                'name' => $ward['WardName'],
                'district_id' => $ward['DistrictID'],
            ]);
        }
    }
}