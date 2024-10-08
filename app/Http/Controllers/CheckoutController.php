<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Province;
use App\Models\Address;
use App\Models\Order;

class CheckoutController extends Controller
{
    public function index()
    {
        $sessionCart = session()->get('cart', []);
        $subtotal = session()->get('subtotal');
        $cartItems = session()->get('cartItems');
        $totalAmount = $subtotal;

        $user = Auth::user();
        $addresses = [];
        $provinces = [];

        if ($user) {
            $addresses = $user->addresses()->orderBy('is_default', 'desc')->get();
            $provinces = Province::with('districts')->orderBy('name', 'asc')->get();
        }

        return view('checkout.index', compact('sessionCart', 'subtotal', 'cartItems', 'totalAmount', 'user', 'addresses', 'provinces'));
    }

    public function process(Request $request)
    {
        dd($request->all());
        $user = Auth::user();

        // Validate input
        $validator = $this->validateCheckoutData($request, $user);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Create order
        $order = $this->createOrder($request, $user);

        // Process payment based on selected method
        $paymentMethod = $request->input('payment_method');

        switch ($paymentMethod) {
            case 'paypal':
                return redirect()->route('paypal.process', ['order_id' => $order->id]);
            case 'vnpay':
                return redirect()->route('vnpay.process', ['order_id' => $order->id]);
            default:
                return redirect()->back()->with('error', 'Invalid payment method selected.');
        }
    }

    private function validateCheckoutData(Request $request, $user)
    {
        // dd($request->all());
        $rules = [
            'payment_method' => 'required|in:paypal,vnpay',
            // Add other validation rules for address, etc.
        ];

        if (!$user) {
            $rules = array_merge($rules, [
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone_number' => 'required|string|max:15',
                'address_line_1' => 'required|string|max:255',
                'district_id' => 'required|exists:districts,id',
                'province_id' => 'required|exists:provinces,id',
            ]);
        } else {
            $rules = array_merge($rules, [
                'address_id' => 'required_without:new_address|exists:addresses,id',
                'new_address' => 'sometimes|boolean',
            ]);

            if ($request->input('new_address')) {
                $rules = array_merge($rules, [
                    'full_name' => 'required|string|max:255',
                    'phone_number' => 'required|string|max:15',
                    'address_line_1' => 'required|string|max:255',
                    'address_line_2' => 'nullable|string|max:255',
                    'district_id' => 'required|exists:districts,id',
                    'province_id' => 'required|exists:provinces,id',
                ]);
            }
        }

        return Validator::make($request->all(), $rules);
    }

    private function createOrder(Request $request, $user)
    {
        // Create order logic here
        // For now, let's just return a dummy order
        return new Order();
    }
}
