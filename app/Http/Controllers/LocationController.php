<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LocationController extends Controller
{
    protected $apiToken;

    public function __construct()
    {
        $this->apiToken = env('GHN_TOKEN');
    }

    public function getProvinces()
    {
        $response = Http::withHeaders([
            'Token' => $this->apiToken,
            'Content-Type' => 'application/json',
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/province');
        return response()->json($response->json());
    }

    public function getDistricts($province_id)
    {
        $response = Http::withHeaders([
            'Token' => $this->apiToken,
            'Content-Type' => 'application/json',
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/district', [
            'province_id' => $province_id,
        ]);

        return response()->json($response->json());
    }

    public function getWards($district_id)
    {
        $response = Http::withHeaders([
            'Token' => $this->apiToken,
            'Content-Type' => 'application/json',
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/ward', [
            'district_id' => $district_id,
        ]);

        return response()->json($response->json());
    }

    public function getProvinceName($province_id)
    {
        $response = Http::withHeaders([
            'Token' => $this->apiToken,
            'Content-Type' => 'application/json',
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/province');
        $provinces = $response->json()['data'] ?? [];

        $province = collect($provinces)->firstWhere('ProvinceID', $province_id);
        if ($province) {
            return response()->json(['name' => $province['ProvinceName']]);
        }

        return response()->json(['name' => 'Province not found'], 404);
    }

    public function getDistrictName($district_id)
    {
        $response = Http::withHeaders([
            'Token' => $this->apiToken,
            'Content-Type' => 'application/json',
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/district');
        $districts = $response->json()['data'] ?? [];
        $district = collect($districts)->firstWhere('DistrictID', $district_id);
        
        if ($district) {
            // dd($district);
            return response()->json(['name' => $district['DistrictName']]);
        }

        return response()->json(['name' => 'District not found'], 404);
    }

    public function getWardName($ward_id, Request $request)
    {
        $district_id = $request->query('district_id');

        if (!$district_id) {
            return response()->json(['error' => 'District ID is required'], 400);
        }

        $response = Http::withHeaders([
            'Token' => $this->apiToken,
            'Content-Type' => 'application/json',
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/ward', [
            'district_id' => $district_id
        ]);

        $wards = $response->json()['data'] ?? [];

        $ward = collect($wards)->firstWhere('WardCode', $ward_id);

        if ($ward) {
            return response()->json(['name' => $ward['WardName']]);
        }

        return response()->json(['name' => 'Ward not found'], 404);
    }
}
