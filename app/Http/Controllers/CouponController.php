<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        $subtotal = $request->input('subtotal');
        $code = $request->input('coupon_code');
        $coupon = Coupon::where('code', $code)->first();

        if ($coupon) {
            if ($coupon->is_valid($subtotal)) {
                // Coupon is valid, you can proceed with using it
                $subtractValue = $subtotal * $coupon->discount_value;
                $newSubtotal = $subtotal - ($subtractValue > $coupon->max_discount ? $coupon->max_discount : $subtractValue);
                // Redirect back to the checkout page with the new subtotal
                return redirect()->route('checkout.index')->with('subtotal', $newSubtotal);
            } else {
                return redirect()->back()->with('subtotal',$subtotal)->withErrors(['coupon_error'=> 'Invalid code']); // Coupon is invalid based on validation rules
            }
        } else {
            return redirect()->back()->with('subtotal',$subtotal)->withErrors(['coupon_error'=> 'Code not exist']); // Coupon with the provided code doesn't exist
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
