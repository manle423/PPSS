<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Http\Controllers\Controller;
use App\Models\CouponUsage;
use Illuminate\Http\Request;

use function Laravel\Prompts\alert;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }



    public function useCoupon(Request $request)
    {
        // Check if the user is logged in
        $userId = auth()->id();
        if (!$userId) {
            return redirect()->back()->withErrors(['coupon_error' => 'Please login to use coupon']);
        }

        $sessionCoupon = session()->get('couponCode');
        $subtotal = session()->get('subtotal');
        $oldSubtotal = session()->get('oldSubtotal');
        if ($sessionCoupon) {
            $subtotal = session()->get('oldSubtotal');
        } else {
            $subtotal = session()->get('subtotal');
        }

        $code = $request->input('coupon_code');
        // Reset, not using the code
        if ($code == '') {
            session()->put('subtotal', $oldSubtotal);
            session()->forget('couponCode');
            return redirect()->back();
        }
        $coupon = Coupon::where('code', $code)->first();
        if ($coupon) {
            // Check if the coupon has already been used by the user
            $couponUsage = CouponUsage::where('user_id', $userId)
                ->where('coupon_id', $coupon->id)
                ->exists();

            if ($couponUsage) {
                return redirect()->back()->withErrors(['coupon_error' => 'Coupon has already been used.']); // Prevent duplicate usage
            }
            if ($coupon->is_valid($subtotal)) {
                // Coupon is valid, you can proceed with using it
                $subtractValue = $subtotal * $coupon->discount_value;
                $subtotal = $subtotal - ($subtractValue > $coupon->max_discount ? $coupon->max_discount : $subtractValue);
                // Store the old and new subtotal in the session
                session()->put('subtotal', $subtotal);
                session()->put('oldSubtotal', $oldSubtotal);
                session()->put('usedCoupon', true);
                session()->put('couponCode', $code);
                // Redirect back to the checkout page with the new subtotal and used coupon
                return redirect()->back();
            } else {
                return redirect()->back()->withErrors(['coupon_error' => 'Invalid coupon code']); // Coupon is invalid based on validation rules
            }
        } else {
            return redirect()->back()->withErrors(['coupon_error' => 'Coupon code not exist']); // Coupon with the provided code doesn't exist
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coupon $coupon)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coupon $coupon)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        //
    }
}
