<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Http\Controllers\Controller;
use App\Models\CouponUsage;
use Illuminate\Http\Request;

use function Laravel\Prompts\alert;

class CouponController extends Controller
{
    public function useCoupon(Request $request)
    {
        $userId = auth()->id();
        if (!$userId) {
            return redirect()->back()->withErrors(['coupon_error' => 'Please login to use coupon']);
        }

        $code = $request->input('coupon_code');
        $oldSubtotal = session()->get('oldSubtotal', session()->get('subtotal'));
        $subtotal = session()->get('subtotal');

        if ($code == '') {
            session()->put('subtotal', $oldSubtotal);
            session()->forget(['couponCode', 'usedCoupon']);
            return redirect()->back()->withInput();
        }

        $coupon = Coupon::where('code', $code)->first();
        if ($coupon) {
            $couponUsage = CouponUsage::where('user_id', $userId)
                ->where('coupon_id', $coupon->id)
                ->exists();

            if ($couponUsage) {
                return redirect()->back()->withErrors(['coupon_error' => 'Coupon has already been used.'])->withInput();
            }

            if ($coupon->is_valid($oldSubtotal)) {
                $discount = $oldSubtotal * $coupon->discount_value;
                $discount = min($discount, $coupon->max_discount);
                $newSubtotal = $oldSubtotal - $discount;

                session()->put('subtotal', $newSubtotal);
                session()->put('oldSubtotal', $oldSubtotal);
                session()->put('usedCoupon', true);
                session()->put('couponCode', $code);

                return redirect()->back()->withInput();
            } else {
                return redirect()->back()->withErrors(['coupon_error' => 'Invalid coupon code'])->withInput();
            }
        } else {
            return redirect()->back()->withErrors(['coupon_error' => 'Coupon code not exist'])->withInput();
        }
    }
}
