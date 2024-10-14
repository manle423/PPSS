<?php

namespace App\Http\Controllers;

use App\Services\GhnService;
use Illuminate\Http\Request;

class GhnController extends Controller
{
    protected $ghnService;

    public function __construct(GhnService $ghnService)
    {
        $this->ghnService = $ghnService;
    }

    public function getProvinces()
    {
        $provinces = $this->ghnService->getProvinces();
        return response()->json($provinces);
    }

    public function getDistricts($provinceId)
    {
        $districts = $this->ghnService->getDistricts($provinceId);
        return response()->json($districts);
    }

    public function getWards($districtId)
    {
        $wards = $this->ghnService->getWards($districtId);
        return response()->json($wards);
    }

    public function calculateShippingFee(Request $request)
    {
        $data = $request->validate([
            'to_district_id' => 'required|integer',
            'to_ward_code' => 'required|string',
            'weight' => 'required|integer',
        ]);

        $shippingFee = $this->ghnService->calculateShippingFee($data);
        return response()->json($shippingFee);
    }

    public function createOrder(Request $request)
    {
        $data = $request->validate([
            // Add validation rules for order creation
        ]);

        $order = $this->ghnService->createOrder($data);
        return response()->json($order);
    }
}
