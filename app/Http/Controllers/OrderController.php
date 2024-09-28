<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function showCheckoutPage()
    {
        // Hiển thị trang checkout
        return view('components.modal-confirmed-checkout');
    }

    public function store(Request $request)
    {
        $user = auth()->user(); // Giả sử người dùng đã đăng nhập

        // Tính tổng giá đơn hàng
        $totalPrice = 0;
        $products = $request->input('products');

        foreach ($products as $productData) {
            $product = Product::findOrFail($productData['product_id']);
            $totalPrice += $product->price * $productData['quantity'];

            // Kiểm tra tồn kho
            if ($product->stock < $productData['quantity']) {
                return response()->json(['error' => 'Không đủ hàng'], 400);
            }
        }

        // Gọi API tính phí giao hàng
        $shippingFee = $this->calculateShippingFee($request->input('address'));

        // Tạo đơn hàng
        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $totalPrice,
            'shipping_fee' => $shippingFee,
            'payment_method' => $request->input('payment_method'),
            'status' => 'pending',
        ]);

        // Lưu chi tiết sản phẩm
        foreach ($products as $productData) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productData['product_id'],
                'quantity' => $productData['quantity'],
                'price' => $product->price,
            ]);

            // Cập nhật tồn kho
            $product->update(['stock' => $product->stock - $productData['quantity']]);
        }

        return response()->json(['message' => 'Đặt hàng thành công', 'order_id' => $order->id], 201);
    }

    protected function calculateShippingFee($address)
    {
        // Gọi API của đơn vị giao hàng
        $apiUrl = env('SHIPPING_API_URL');
        $response = Http::post($apiUrl, [
            'address' => $address,
            'weight' => 1.0, // Giả định trọng lượng
        ]);

        if ($response->successful()) {
            return $response->json()['shipping_fee'];
        }

        return 0; // Trả về 0 nếu API lỗi
    }
    
    public function placeOrder(Request $request)
    {
        // Xử lý đơn hàng
        // Logic xử lý lưu đơn hàng vào database, gửi email xác nhận, v.v.
    }
}
