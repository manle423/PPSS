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
use App\Models\District;
use App\Models\Ward;
use App\Services\ProfileService;

class CheckoutController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function success(Request $request)
    {
        $orderType = session('order_type');
        $orderId = session($orderType == 'order' ? 'order_id' : 'guest_order_id');

        if (!$orderId) {
            return redirect()->route('home')->with('error', 'Order not found.');
        }

        if ($orderType == 'order') {
            $order = Order::with(['orderItems.item', 'shippingAddress.district', 'shippingAddress.province', 'shippingAddress.ward', 'shippingMethod'])
                ->findOrFail($orderId);
            $shippingAddress = $order->shippingAddress;
        } else {
            $order = GuestOrder::with(['orderItems.item', 'shippingMethod'])
                ->findOrFail($orderId);
            $guestAddress = json_decode($order->guest_address, true);
            
            $district = District::find($guestAddress['district_id']);
            $province = Province::find($guestAddress['province_id']);
            $ward = Ward::find($guestAddress['ward_id']);
            $shippingAddress = (object) [
                'full_name' => $order->guest_name,
                'address_line_1' => $guestAddress['address_line_1'],
                'address_line_2' => $guestAddress['address_line_2'] ?? null,
                'district' => $district,
                'province' => $province,
                'ward' => $ward,
            ];
        }

        $orderItems = $order->orderItems->map(function ($item) {
            return [
                'name' => $item->item->name,
                'quantity' => $item->quantity,
                'price' => number_format($item->price, 2),
                'total' => number_format($item->quantity * $item->price, 2),
            ];
        });

        $shippingMethod = $order->shippingMethod ?? 'N/A';
        return view('checkout.success', compact('order', 'orderItems', 'shippingAddress', 'shippingMethod', 'orderType'));
    }

    public function index()
    {
        $sessionCart = session()->get('cart', []);
        $subtotal = session()->get('subtotal');
        $cartItems = session()->get('cartItems');

        // Kiểm tra xem giỏ hàng có trống không
        if (empty($sessionCart) || empty($cartItems)) {
            return Redirect::route('home')->with('info', 'Your cart is empty. Please add some items before checking out.');
        }

        $totalAmount = $subtotal;

        $user = Auth::user();
        $addresses = [];
        $provinces = Province::with('districts.wards')->orderBy('name', 'asc')->get();

        if ($user) {
            $addresses = $user->addresses()->orderBy('is_default', 'desc')->get();
        }

        return view('checkout.index', compact('sessionCart', 'subtotal', 'cartItems', 'totalAmount', 'user', 'addresses', 'provinces'));
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

            // Handle new address creation if necessary
            if ($user && $request->input('new_address')) {
                $addressId = $this->createNewAddress($request, $user);
            }

            // Create order
            DB::beginTransaction();
            $order = $this->createOrder($request, $user, $addressId);
            
            // Store order type and ID in session
            if ($user) {
                $request->session()->put('order_type', 'order');
                $request->session()->put('order_id', $order->id);
            } else {
                $request->session()->put('order_type', 'guest_order');
                $request->session()->put('guest_order_id', $order->id);
            }
            
            DB::commit();

            // Process payment based on selected method
            $paymentMethod = $request->input('payment_method');

            switch ($paymentMethod) {
                case 'paypal':
                    return redirect()->route('paypal.process');
                case 'vnpay':
                    return redirect()->route('vnpay.process');
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
                'ward_id' => 'required|exists:wards,id',
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
                    'ward_id' => 'required|exists:wards,id',
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
            if ($user) {
                // Create Order for authenticated user
                $order = Order::create([
                    'user_id' => $user->id,
                    'shipping_address_id' => $addressId,
                    'order_date' => now(),
                    'shipping_method_id' => 1, // Just for now
                    'payment_method' => $request->input('payment_method'),
                    'total_price' => $subtotal,
                    'discount_value' => 0,
                    'final_price' => $subtotal,
                    'status' => 'pending'
                ]);

                // Create OrderItems for authenticated user
                foreach ($cartItems as $item) {
                    $variantId = $item->variant ? strval($item->variant->id) : '';
                    $cartKey = $item->product->id . '-' . $variantId;
                    $quantity = $sessionCart[$cartKey] ?? 0;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'item_id' => $item->product->id,
                        'variant_id' => $item->variant ? $item->variant->id : null,
                        'quantity' => $quantity,
                        'price' => $item->variant ? $item->variant->variant_price : $item->product->price,
                    ]);
                }

                // Clear cart for authenticated user
                Cart::where('user_id', $user->id)->delete();

                return $order;
            } else {
                // Create GuestOrder for non-authenticated user
                $guestOrder = GuestOrder::create([
                    'guest_name' => $request->input('full_name'),
                    'guest_email' => $request->input('email'),
                    'guest_phone_number' => $request->input('phone_number'),
                    'guest_address' => json_encode([
                        'address_line_1' => $request->input('address_line_1'),
                        'address_line_2' => $request->input('address_line_2'),
                        'district_id' => $request->input('district_id'),
                        'province_id' => $request->input('province_id'),
                        'ward_id' => $request->input('ward_id'),
                    ], JSON_UNESCAPED_UNICODE),
                    'status' => 'pending',
                    'order_date' => now(),
                    'shipping_method_id' => 1, // Just for now
                    'payment_method' => $request->input('payment_method'),
                    'total_price' => $subtotal,
                    'discount_value' => 0, // Just for now
                    'final_price' => $subtotal,
                    'digital_signature' => '' // Just for now
                ]);

                // Create OrderItems for guest
                foreach ($cartItems as $item) {
                    $variantId = $item->variant ? strval($item->variant->id) : '';
                    $cartKey = $item->product->id . '-' . $variantId;
                    $quantity = $sessionCart[$cartKey] ?? 0;
                    OrderItem::create([
                        'guest_order_id' => $guestOrder->id,
                        'item_id' => $item->product->id,
                        'variant_id' => $item->variant ? $item->variant->id : null,
                        'quantity' => $quantity,
                        'price' => $item->variant ? $item->variant->variant_price : $item->product->price,
                    ]);
                }
                return $guestOrder;
            }
        } finally {
            // Clear the session cart in both cases
            session()->forget(['cart', 'cartItems', 'subtotal']);
        }
    }

    private function createNewAddress(Request $request, $user)
    {
        $validatedData = $this->profileService->validateAddressData($request);
        $addressData = $validatedData;

        $existingAddressCount = $this->profileService->getExistingAddressCount();

        if ($existingAddressCount === 0) {
            $addressData['is_default'] = true;
            $this->profileService->resetOtherDefaultAddresses();
        } else {
            $addressData['is_default'] = false;
        }

        $address = $user->addresses()->create($addressData);

        if ($addressData['is_default']) {
            $this->profileService->updateUserDefaultAddress($address->id);
        }
        return $address->id;
    }
}