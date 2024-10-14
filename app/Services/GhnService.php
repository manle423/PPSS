<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GhnService
{
    protected $baseUrl = 'https://online-gateway.ghn.vn/shiip/public-api/';
    protected $token;

    public function __construct()
    {
        $this->token = config('services.ghn.token');
    }

    protected function request($endpoint, $method = 'GET', $data = [])
    {
        $response = Http::withHeaders([
            'Token' => $this->token,
            'Content-Type' => 'application/json',
        ])->$method($this->baseUrl . $endpoint, $data);

        return $response->json();
    }

    public function getProvinces()
    {
        return $this->request('master-data/province');
    }

    public function getDistricts($provinceId)
    {
        return $this->request('master-data/district', 'GET', ['province_id' => $provinceId]);
    }

    public function getWards($districtId)
    {
        return $this->request('master-data/ward', 'GET', ['district_id' => $districtId]);
    }

    public function calculateShippingFee($data)
    {
        return $this->request('v2/shipping-order/fee', 'POST', $data);
    }

    public function createOrder($data)
    {
        return $this->request('v2/shipping-order/create', 'POST', $data);
    }
}