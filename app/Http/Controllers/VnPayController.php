<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\GuestOrder;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CheckoutController;
use App\Models\Cart;

class VnPayController extends Controller
{
    protected $checkoutController;

    public function __construct(CheckoutController $checkoutController)
    {
        $this->checkoutController = $checkoutController;
    }

    public function process(Request $request)
    {
        try {
            $orderType = session('order_type');
            $orderId = session($orderType == 'order' ? 'order_id' : 'guest_order_id');

            if ($orderType == 'order') {
                $order = Order::findOrFail($orderId);
            } else {
                $order = GuestOrder::findOrFail($orderId);
            }

            Log::info('Processing VNPAY payment for ' . $orderType . ': ' . $order->id);
            // Lấy thông tin config từ file config vnpay.php
            $vnp_TmnCode = config('vnpay.vnp_TmnCode');
            $vnp_HashSecret = config('vnpay.vnp_HashSecret');
            $vnp_Url = config('vnpay.vnp_Url');
            $vnp_ReturnUrl = config('vnpay.vnp_Returnurl');

            $total_amount = $order->total_price * 100000;  // VNPAY yêu cầu số tiền phải nhân 100 (vì đơn vị tính là VND)
            Log::info('Order total amount: ' . $total_amount);
            $inputData = array(
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => $total_amount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $request->ip(),
                "vnp_Locale" => 'vn',
                "vnp_OrderInfo" => 'Payment for order ' . $order->order_code,
                "vnp_OrderType" => "billpayment",
                "vnp_ReturnUrl" => $vnp_ReturnUrl,
                "vnp_TxnRef" => $order->id,
            );

            // Kiểm tra nếu có mã ngân hàng
            if (isset($request->bankCode) && $request->bankCode != "") {
                $inputData['vnp_BankCode'] = $request->bankCode;
            }

            // Sắp xếp mảng dữ liệu input theo thứ tự key
            ksort($inputData);

            $query = "";
            $hashdata = "";

            foreach ($inputData as $key => $value) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            $vnp_Url .= "?" . $query;
            if (isset($vnp_HashSecret)) {
                $vnpSecureHash = hash_hmac('sha512', trim($hashdata, '&'), $vnp_HashSecret);
                $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
            }
            Log::info('Redirecting to VNPAY URL: ' . $vnp_Url);
            return redirect()->away($vnp_Url);
        } catch (\Exception $e) {
            Log::error('Exception in VNPAY process: ' . $e->getMessage());
            return redirect()->route('checkout.index')->withErrors('error', 'An error occurred while processing your payment. Please try again.');
        }
    }

    public function return(Request $request)
    {
        try {
            $vnp_SecureHash = $request->vnp_SecureHash;
            $inputData = $request->all();
            unset($inputData['vnp_SecureHash']);
            ksort($inputData);

            $hashData = "";
            foreach ($inputData as $key => $value) {
                $hashData .= urlencode($key) . "=" . urlencode($value) . '&';
            }
            $hashData = rtrim($hashData, '&');
            $secureHash = hash_hmac('sha512', $hashData, config('vnpay.vnp_HashSecret'));
            if ($secureHash === $vnp_SecureHash) {
                if ($request->vnp_ResponseCode == '00') {
                    // Thanh toán thành công
                    $orderType = session('order_type');
                    $orderId = session($orderType == 'order' ? 'order_id' : 'guest_order_id');
                    if ($orderId) {
                        if ($orderType == 'order') {
                            $order = Order::findOrFail($orderId)->with('shippingAddress');
                            $order->status = Order::STATUS['pending'];
                        } else {
                            $order = GuestOrder::findOrFail($orderId);
                            $order->status = 'PENDING';
                        }
                        $order->save();

                        // Gửi email xác nhận đơn hàng
                        $this->checkoutController->sendOrderConfirmationEmail($order, $orderType);

                        // Xóa cart sau khi thanh toán thành công
                        if ($orderType == 'order') {
                            Cart::where('user_id', $order->user_id)->delete();
                        }
                        session()->forget(['cart', 'cartItems', 'subtotal']);

                        return redirect()->route('checkout.success')->with('success', 'Transaction complete.');
                    }
                } else {
                    // Thanh toán thất bại
                    $orderType = session('order_type');
                    $orderId = session($orderType == 'order' ? 'order_id' : 'guest_order_id');

                    if ($orderId) {
                        if ($orderType == 'order') {
                            $order = Order::findOrFail($orderId);
                            $order->status = Order::STATUS['canceled'];
                        } else {
                            $order = GuestOrder::findOrFail($orderId);
                            $order->status = 'CANCELED';
                        }
                        $order->save();
                    }

                    $errorMessage = 'Payment failed. Error code: ' . $request->vnp_ResponseCode;
                    return redirect()->route('checkout.index')->withErrors('error', $errorMessage);
                }
            } else {
                return redirect()->route('checkout.index')->withErrors('error', 'Invalid payment data.');
            }
        } catch (\Exception $e) {
            Log::error('Exception in VNPAY return: ' . $e->getMessage());
            return redirect()->route('checkout.index')->withErrors('error', 'An error occurred. Please try again.');
        }
    }

    public function cancel()
    {
        return redirect()->route('checkout.index')->withErrors('error', 'You have canceled the transaction.');
    }
}
