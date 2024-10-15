<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Province;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CouponUsage;
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
use Illuminate\Support\Facades\Mail;
use App\Services\OrderService;
use App\Services\CouponService;

class CheckoutController extends Controller
{
    protected $profileService;
    protected $orderService;
    protected $couponService;

    public function __construct(ProfileService $profileService, OrderService $orderService, CouponService $couponService)
    {
        $this->profileService = $profileService;
        $this->orderService = $orderService;
        $this->couponService = $couponService;
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

        // Record coupon usage if applicable
        $couponCode = session()->get('couponCode');
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            // Mark the coupon as used by the user
            CouponUsage::create([
                'user_id' => Auth::id(),
                'coupon_id' => $coupon->id,
                'order_id' => $orderId, // You need to have an order ID available here
            ]);

            // Reset the coupon usage state
            session()->forget('couponCode');
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
        $usedCoupon = session()->get('usedCoupon');
        $couponCode = session()->get('couponCode');
        $oldSubtotal = session()->get('oldSubtotal');

        // Kiểm tra xem giỏ hàng có trống không
        if (empty($sessionCart) || empty($cartItems)) {
            return Redirect::route('home')->with('info', 'Your cart is empty. Please add some items before checking out.');
        }

        $user = Auth::user();
        $addresses = [];
        $provinces = Province::with('districts.wards')->orderBy('name', 'asc')->get();

        if ($user) {
            $addresses = $user->addresses()->orderBy('is_default', 'desc')->get();
        }

        return view('checkout.index', compact(
            'sessionCart',
            'subtotal',
            'cartItems',
            'oldSubtotal',
            'user',
            'addresses',
            'provinces',
            'usedCoupon',
            'couponCode'
        ));
    }

    public function process(Request $request)
    {
        try {
            $user = Auth::user();
            $addressId = $request->input('selected_address_id');

            $validator = $this->validateCheckoutData($request, $user);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            if ($user) {
                $hasAddresses = $user->addresses()->exists();
                if (!$hasAddresses || $request->input('new_address')) {
                    $addressId = $this->createNewAddress($request, $user);
                }
            }

            DB::beginTransaction();
            $cartItems = session()->get('cartItems');
            $sessionCart = session()->get('cart', []);
            $subtotal = session()->get('subtotal');
            $oldSubtotal = session()->get('oldSubtotal', $subtotal);
            $couponCode = session()->get('couponCode');
            $discountValue = $this->couponService->calculateDiscount($couponCode, $oldSubtotal);
            $finalPrice = $oldSubtotal - $discountValue;
            $totalPrice = $couponCode ? $subtotal : $oldSubtotal;

            $order = $this->orderService->createOrder($request, $user, $addressId, $cartItems, $sessionCart, $totalPrice, $discountValue, $finalPrice);

            $orderType = $user ? 'order' : 'guest_order';
            $request->session()->put('order_type', $orderType);
            $request->session()->put($orderType . '_id', $order->id);

            DB::commit();

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
        // dd($request->all());
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
            // Kiểm tra xem người dùng đã có địa chỉ nào chưa
            $hasAddresses = $user->addresses()->exists();

            if (!$hasAddresses) {
                // Nếu chưa có địa chỉ, yêu cầu nhập thông tin địa chỉ mới
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
                // Nếu đã có địa chỉ, cho phép chọn địa chỉ hiện có hoặc tạo mới
                $rules['new_address'] = 'sometimes|boolean';
                
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
        }

        $messages = [
            'selected_address_id.required' => 'Please select an existing address or create a new one.',
            'selected_address_id.exists' => 'The selected address is invalid.',
        ];

        return Validator::make($request->all(), $rules, $messages);
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

    public function sendOrderConfirmationEmail($order, $orderType)
    {
        $email = $orderType === 'order' ? $order->user->email : $order->guest_email;
        Mail::to($email)->send(new OrderConfirmation($order, $orderType));
    }
}