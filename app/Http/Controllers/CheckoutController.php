<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Province;
use App\Models\Address;
use App\Models\Cart;
use App\Models\GuestOrder;
use App\Models\Order;
use App\Models\OrderItem;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class CheckoutController extends Controller
{
    public function success(Request $request)
    {
        $orderId = session()->get('order_id');
        if (!$orderId) {
            return redirect()->route('home')->with('error', 'Order not found.');
        }

        $order = Order::with(['orderItems.item', 'shippingAddress', 'shippingMethod'])
                  ->findOrFail($orderId);

        $orderItems = $order->orderItems->map(function ($item) {
            return [
                'name' => $item->item->name,
                'quantity' => $item->quantity,
                'price' => number_format($item->price, 2),
                'total' => number_format($item->quantity * $item->price, 2),
            ];
        });

        $shippingAddress = $order->shippingAddress;
        $shippingMethod = $order->shippingMethod;

        // Reset the coupon usage state
        session()->forget('couponCode');

        return view('checkout.success', compact('order', 'orderItems', 'shippingAddress', 'shippingMethod'));
    }

    public function index()
    {
        $sessionCart = session()->get('cart', []);
        $subtotal = session()->get('subtotal');
        $cartItems = session()->get('cartItems');
        $usedCoupon = session()->get('usedCoupon');
        $couponCode = session()->get('couponCode');

        // Kiểm tra xem giỏ hàng có trống không
        if (empty($sessionCart) || empty($cartItems)) {
            return Redirect::route('home')->with('info', 'Your cart is empty. Please add some items before checking out.');
        }

        $totalAmount = $subtotal;

        $user = Auth::user();
        $addresses = [];
        $provinces = Province::with('districts')->orderBy('name', 'asc')->get();

        if ($user) {
            $addresses = $user->addresses()->orderBy('is_default', 'desc')->get();
        }


        return view('checkout.index', compact('sessionCart', 
        'subtotal', 'cartItems', 'totalAmount', 'user', 
        'addresses', 'provinces','usedCoupon','couponCode'));
    }

    public function process(Request $request)
    {
        try {
            $user = Auth::user();
            $addressId = $request->input('selected_address_id');

            // Validate input
            $validator = $this->validateCheckoutData($request, $user);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Create order
            DB::beginTransaction();
            $order = $this->createOrder($request, $user, $addressId);
            $request->session()->put('order_id', $order->id);
            DB::commit();

            // Process payment based on selected method
            $paymentMethod = $request->input('payment_method');

            switch ($paymentMethod) {
                case 'paypal':
                    return redirect()->route('paypal.process', ['order_id' => $order->id]);
                case 'vnpay':
                    return redirect()->route('vnpay.process', ['order_id' => $order->id]);
                default:
                    throw new Exception('Invalid payment method selected.');
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Checkout process failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred during checkout. Please try again.');
        }
    }

    private function validateCheckoutData(Request $request, $user)
    {
        $rules = [
            'payment_method' => 'required|in:paypal,vnpay',
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
            } else {
                $rules['selected_address_id'] = 'required|exists:addresses,id';
            }
        }

        $messages = [
            'selected_address_id.required' => 'Please select an existing address or create a new one.',
            'selected_address_id.exists' => 'The selected address is invalid.',
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    private function createOrder(Request $request, $user, $addressId)
    {
        try {
            $cartItems = session()->get('cartItems');
            $sessionCart = session()->get('cart', []);
            $subtotal = session()->get('subtotal');
            $orderData = [
                'order_date' => now(),
                'shipping_method_id' => 1, // Just for now
                'payment_method' => $request->input('payment_method'),
                'total_price' => $subtotal,
                'discount_value' => 0,
                'final_price' => $subtotal, // total_price - discount_value, just for now
            ];

            if ($user) {
                $orderData['user_id'] = $user->id;
                $orderData['shipping_address_id'] = $addressId;
            } else {
                // Create GuestOrder
                $guestOrder = GuestOrder::create([
                    'guest_email' => $request->input('email'),
                    'guest_phone_number' => $request->input('phone_number'),
                    'guest_address' => json_encode([
                        'full_name' => $request->input('full_name'),
                        'address_line_1' => $request->input('address_line_1'),
                        'address_line_2' => $request->input('address_line_2'),
                        'district_id' => $request->input('district_id'),
                        'province_id' => $request->input('province_id'),
                    ]),
                    'status' => 'pending',
                    'order_date' => now(),
                    'shipping_method_id' => $request->input('shipping_method_id'),
                    'payment_method' => $request->input('payment_method'),
                    'total_price' => $subtotal,
                    'discount_value' => 0,
                    'final_price' => $subtotal,
                ]);

                $orderData['guest_order_id'] = $guestOrder->id;
            }

            $order = Order::create($orderData);

            // Create OrderItems
            foreach ($cartItems as $item) {
                $variantId = $item->variant ? strval($item->variant->id) : '';
                $cartKey = $item->product->id . '-' . $variantId;
                $quantity = $sessionCart[$cartKey] ?? 0;

                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $item->product->id,
                    'quantity' => $quantity,
                    'price' => $item->variant ? $item->variant->variant_price : $item->product->price,
                ]);
            }

            // // Clear the cart after creating the order
            // session()->forget(['cart', 'cartItems', 'subtotal']);

            // // Delete cart items from database for authenticated users
            // if ($user) {
            //     Cart::where('user_id', $user->id)->delete();
            // }

            return $order;
        } catch (Exception $e) {
            Log::error('Order creation failed: ' . $e->getMessage());
            throw $e; // Re-throw the exception to be caught in the main try-catch block
        }
    }
}
