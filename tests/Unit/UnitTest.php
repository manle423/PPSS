<?php

use App\Services\ShippingService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ShippingServiceTest extends TestCase
{
    public function testCalculateShippingFee()
    {
        // Mock the HTTP response
        Http::fake([
            'https://online-gateway.ghn.vn/*' => Http::response(['shipping_fee' => 5000], 200),
        ]);

        // Create an instance of the ShippingService
        $shippingService = new ShippingService();

        // Define test data
        $toDistrictId = 1566;
        $toWardCode = 510101;
        $weight = 10;

        // Call the calculateShippingFee method
        $result = $shippingService->calculateShippingFee($toDistrictId, $toWardCode, $weight);

        // Assert that the response contains the expected shipping_fee
        $this->assertEquals(5000, $result['shipping_fee']);
    }
    public function testCalculateShippingFeeWithFalseData()
    {
        // Mock the HTTP response
        Http::fake([
            'https://online-gateway.ghn.vn/*' => Http::response(['error' => 'Invalid request'], 400),
        ]);

        // Create an instance of the ShippingService
        $shippingService = new ShippingService();

        // Define false test data
        $toDistrictId = 'invalid_district_id'; // Incorrect data type
        $toWardCode = 'invalid_ward_code'; // Incorrect data
        $weight = 'not_numeric'; // Incorrect data type

        // Call the calculateShippingFee method with false data
        $result = $shippingService->calculateShippingFee($toDistrictId, $toWardCode, $weight);

        // Assert that the response contains an error message and has a status code of 400
        $this->assertArrayHasKey('error', $result);
        //$this->assertEquals(Http::response($status=400), Http::response());
    }
    
}