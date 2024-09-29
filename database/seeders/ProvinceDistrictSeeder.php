<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\District;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class ProvinceDistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        try{
            $province_link = 'https://provinces.open-api.vn/api/';
            
            $response = Http::get($province_link);
            if($response->successful()){
                DB::beginTransaction();
                $data = $response->json();

                $id = 1;
                foreach ($data as $value) {
                    $province_code = $value['code'];
                    $province = Province::create([
                        'name' => $value['name'],
                        'codename' => $value['codename'],
                        'province_code'=>$province_code,
                    ]);
                    $district_link = "https://provinces.open-api.vn/api/p/$province_code/?depth=2";
                    $district_response = Http::get($district_link);

                    if($district_response->successful()){
                        $district_data = $district_response->json()['districts'];
                        $add_district = [];
                        foreach ($district_data as $item){
                            $add_district[] = 
                            [
                                'name' => $item['name'],
                                'codename' => $item['codename'],
                                'province_id'=> $id
                            ];
                        }
                        $province->districts()->createMany($add_district);
                    }
                    else{
                        throw new \Exception('District API request failed: ' . $response->status());
                    }
                    $id += 1;
                }
                DB::commit();
            }
            else{
                throw new \Exception('Province API request failed: ' . $response->status());
            }
        }catch(\Exception $e){
                DB::rollBack();
                throw new \Exception('Error: ' . $e->getMessage());
        }
      
    }
}
