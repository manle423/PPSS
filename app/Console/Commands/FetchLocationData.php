<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class FetchLocationData extends Command
{
    protected $signature = 'fetch:location-data';
    protected $description = 'Fetch location data from GHN API and save to a JSON file';

    public function handle()
    {
        $apiToken = env('GHN_TOKEN');

        // Fetch provinces
        $provincesResponse = Http::withHeaders([
            'Token' => $apiToken,
            'Content-Type' => 'application/json',
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/province');

        $provinces = $provincesResponse->json()['data'] ?? [];

        // Fetch districts
        $districtsResponse = Http::withHeaders([
            'Token' => $apiToken,
            'Content-Type' => 'application/json',
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/district');

        $districts = $districtsResponse->json()['data'] ?? [];

        // Fetch wards for each district
        $wards = [];
        foreach ($districts as $district) {
            $wardsResponse = Http::withHeaders([
                'Token' => $apiToken,
                'Content-Type' => 'application/json',
            ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/ward', [
                'district_id' => $district['DistrictID'],
            ]);

            $wardsData = $wardsResponse->json()['data'] ?? [];
            $wards = array_merge($wards, $wardsData);
        }

        // Save to JSON file with unescaped Unicode
        $locationData = [
            'provinces' => $provinces,
            'districts' => $districts,
            'wards' => $wards,
        ];

        File::put(database_path('data/locations.json'), json_encode($locationData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info('Location data fetched and saved to database/data/locations.json');
    }
}