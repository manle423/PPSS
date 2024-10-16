<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\District;
use App\Models\Ward;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class ProvinceDistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $province_link = 'https://provinces.open-api.vn/api/';
            
            $response = Http::get($province_link);
            if ($response->successful()) {
                DB::beginTransaction();
                $data = $response->json();

                foreach ($data as $provinceData) {
                    $province_code = $provinceData['code'];
                    $province = Province::create([
                        'name' => $provinceData['name'],
                        'codename' => $provinceData['codename'],
                        'province_code' => $province_code,
                    ]);

                    $district_link = "https://provinces.open-api.vn/api/p/$province_code/?depth=2";
                    $district_response = Http::get($district_link);

                    if ($district_response->successful()) {
                        $district_data = $district_response->json()['districts'];
                        foreach ($district_data as $districtItem) {
                            $district = District::create([
                                'name' => $districtItem['name'],
                                'codename' => $districtItem['codename'],
                                'province_id' => $province->id,
                                'district_code' => $districtItem['code']
                            ]);

                            $ward_link = "https://provinces.open-api.vn/api/d/{$districtItem['code']}?depth=2";
                            $ward_response = Http::get($ward_link);

                            if ($ward_response->successful()) {
                                $ward_data = $ward_response->json()['wards'];
                                $add_wards = [];
                                foreach ($ward_data as $wardItem) {
                                    $add_wards[] = [
                                        'name' => $wardItem['name'],
                                        'codename' => $wardItem['codename'],
                                        'district_id' => $district->id
                                    ];
                                }
                                Ward::insert($add_wards);
                            } else {
                                throw new \Exception('Ward API request failed: ' . $ward_response->status());
                            }
                        }
                    } else {
                        throw new \Exception('District API request failed: ' . $district_response->status());
                    }
                }
                DB::commit();
            } else {
                throw new \Exception('Province API request failed: ' . $response->status());
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Error: ' . $e->getMessage());
        }
    }
}
