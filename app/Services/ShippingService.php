<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ShippingService
{
    protected $apiKey;
    protected $shopId;
    protected $fromDistrictId = 1455;
    protected $fromWardCode = "21404";

    public function __construct()
    {
        $this->apiKey = env('GHN_TOKEN');
        $this->shopId = env('GHN_SHOP_ID');
    }

    public function calculateShippingFee($toDistrictId, $toWardCode, $weight = 1000)
    {
        // Chuyển đổi $toDistrictId thành số nguyên
        $toDistrictId = intval($toDistrictId);

        $response = Http::withHeaders([
            'Token' => $this->apiKey,
            'ShopId' => $this->shopId,
        ])->post('https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee', [
            "from_district_id" => $this->fromDistrictId,
            "from_ward_code" => $this->fromWardCode,
            "service_type_id" => 2,
            'to_district_id' => $toDistrictId,
            'to_ward_code' => $toWardCode,
            'weight' => $weight,
        ]);

        return $response->json();
    }
}
