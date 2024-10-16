<?php

namespace App\Services;

use App\Models\Coupon;

class CouponService
{
    public function calculateDiscount($couponCode, $subtotal)
    {
        $coupon = Coupon::where('code', $couponCode)->first();
        if ($coupon && $coupon->is_valid($subtotal)) {
            $discount = $subtotal * $coupon->discount_value;
            return min($discount, $coupon->max_discount);
        }
        return 0;
    }
}