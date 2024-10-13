<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Http\Controllers\Controller;
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
        $sessionCoupon = session()->get('couponCode');
        $subtotal = session()->get('subtotal');
        $oldSubtotal = $subtotal;
        if ($sessionCoupon) {
            $subtotal = session()->get('oldSubtotal');
        }
        else {
            $subtotal = session()->get('subtotal');
        }
        
        $code = $request->input('coupon_code');
        // Reset, not using the code
        if ($code == '') {
            session()->put('subtotal',$oldSubtotal);
            return redirect()->back(); // Coupon with the provided code doesn't exist
        }
        $coupon = Coupon::where('code', $code)->first();
        if ($coupon) {
            if ($coupon->is_valid($subtotal)) {
                // Coupon is valid, you can proceed with using it
                $subtractValue = $subtotal * $coupon->discount_value;
                $subtotal = $subtotal - ($subtractValue > $coupon->max_discount ? $coupon->max_discount : $subtractValue);
                // Store the old and new subtotal in the session
                session()->put('subtotal',$subtotal);
                session()->put('oldSubtotal',$oldSubtotal);
                session()->put('usedCoupon',true);
                session()->put('couponCode',$code);
                // Redirect back to the checkout page with the new subtotal and used coupon
                return redirect()->back();
            } else {
                return redirect()->back()->withErrors(['coupon_error'=> 'Invalid code']); // Coupon is invalid based on validation rules
            }
        } else {
            return redirect()->back()->withErrors(['coupon_error'=> 'Code not exist']); // Coupon with the provided code doesn't exist
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
