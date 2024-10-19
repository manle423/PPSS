<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\GuestOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class OrderService
{
    public function createOrder($request, $user, $addressId, $cartItems, $sessionCart, $subtotal, $discountValue, $finalPrice, $shippingFee)
    {
        if ($user) {
            $order = Order::create([
                'user_id' => $user->id,
                'shipping_address_id' => $addressId,
                'order_date' => now(),
                'shipping_method_id' => 1,
                'payment_method' => $request->input('payment_method'),
                'total_price' => $subtotal,
                'discount_value' => $discountValue,
                'final_price' => $finalPrice,
                'status' => 'pending',
                'shipping_fee' => $shippingFee
            ]);
            $this->createOrderItems($order->id, $cartItems, $sessionCart, 'order_id');
            return $order;
        } else {
            $guestOrder = GuestOrder::create([
                'guest_name' => $request->input('full_name'),
                'guest_email' => $request->input('email'),
                'guest_phone_number' => $request->input('phone_number'),
                // Encrypt the address
                'guest_address' => json_encode([
                    'address_line_1' => Crypt::encryptString($request->input('address_line_1')),
                    'address_line_2' => Crypt::encryptString($request->input('address_line_2')),
                    'district_id' => Crypt::encryptString($request->input('district_id')),
                    'province_id' => Crypt::encryptString($request->input('province_id')),
                    'ward_id' => Crypt::encryptString($request->input('ward_id')),
                ], JSON_UNESCAPED_UNICODE),
                'status' => 'pending',
                'order_date' => now(),
                'shipping_method_id' => 1,
                'payment_method' => $request->input('payment_method'),
                'total_price' => $subtotal,
                'discount_value' => $discountValue,
                'final_price' => $finalPrice,
                'shipping_fee' => $shippingFee,
                'digital_signature' => ''
            ]);

            $this->createOrderItems($guestOrder->id, $cartItems, $sessionCart, 'guest_order_id');
            return $guestOrder;
        }
    }

    private function createOrderItems($orderId, $cartItems, $sessionCart, $orderKey)
    {
        foreach ($cartItems as $item) {
            $variantId = $item->variant ? strval($item->variant->id) : '';
            $cartKey = $item->product->id . '-' . $variantId;
            $quantity = $sessionCart[$cartKey] ?? 0;

            OrderItem::create([
                $orderKey => $orderId,
                'item_id' => $item->product->id,
                'variant_id' => $item->variant ? $item->variant->id : null,
                'quantity' => $quantity,
                'price' => $item->variant ? $item->variant->variant_price : $item->product->price,
            ]);
        }
    }
}
