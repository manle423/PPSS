<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ShippingService
{
    protected $apiKey;
    protected $shopId;

    public function __construct()
    {
        $this->apiKey = env('GHN_API_KEY');
        $this->shopId = env('GHN_SHOP_ID');
    }

    public function calculateShippingFee($address)
    {
        $response = Http::withHeaders([
            'Token' => $this->apiKey,
        ])->post('https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee', [
            'from_district_id' => 1,// your_shop_district_id,
            'service_id' => 53320, // Example service ID
            'to_district_id' => $address['district_id'],
            'to_ward_code' => $address['ward_id'],
            'weight' => 1000, // Example weight in grams
            'insurance_value' => 1000000, // Example insurance value
        ]);

        return $response->json();
    }
}