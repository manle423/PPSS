<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\GuestOrder;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CheckoutController;
use App\Models\Cart;

class PaypalController extends Controller
{
    protected $checkoutController;

    public function __construct(CheckoutController $checkoutController)
    {
        $this->checkoutController = $checkoutController;
    }

    public function create()
    {
        return view("payments.success");
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

            Log::info('Processing PayPal payment for ' . $orderType . ': ' . $order->id);

            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $paypalToken = $provider->getAccessToken();

            if (!$paypalToken) {
                Log::error('Failed to get PayPal access token');
                return redirect()->route('checkout.index')->withErrors('error', 'Failed to connect to PayPal. Please try again.');
            }

            $total_amount = number_format($order->total_price, 2, '.', '');
            Log::info('Order total amount: ' . $total_amount);

            // Lấy tỉ giá hối đoái ngoại tệ từ API
            $api_key = env('YOUR_API_KEY');
            $target_currency = 'VND';
            $url = "https://openexchangerates.org/api/latest.json?app_id=" . $api_key;
            $response = file_get_contents($url);
            $data = json_decode($response, true);
            
            // Kiểm tra tìm được tỉ giá hối đoái ngoại tệ từ API hay không
            if ($data) {
                $exchange_rate = $data['rates'][$target_currency];
            } else {
                // Gán cứng nếu không tìm được
                $exchange_rate = 24000;
            }
            // Chuyển đổi giá cả từ VND sang USD
            $total_amount_usd = number_format($total_amount / $exchange_rate, 2, '.', '');
            
            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => route('paypal.success'),
                    "cancel_url" => route('paypal.cancel'),
                ],
                "purchase_units" => [
                    0 => [
                        "amount" => [
                            "currency_code" => "USD",
                            "value" => $total_amount_usd
                        ]
                    ]
                ]
            ]);

            Log::info('PayPal createOrder response: ' . json_encode($response));

            if (isset($response['id']) && $response['id'] != null) {
                foreach ($response['links'] as $links) {
                    if ($links['rel'] == 'approve') {
                        Log::info('Redirecting to PayPal approval URL: ' . $links['href']);
                        return redirect()->away($links['href']);
                    }
                }
                Log::error('PayPal approval URL not found in response');
                return redirect()->route('checkout.index')->withErrors('error', 'PayPal approval link not found. Please try again.');
            } else {
                Log::error('PayPal createOrder failed: ' . ($response['message'] ?? 'Unknown error'));
                return redirect()->route('checkout.index')->withErrors('error', $response['message'] ?? 'Failed to create PayPal order. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('Exception in PayPal process: ' . $e->getMessage());
            return redirect()->route('checkout.index')->withErrors('error', 'An error occurred while processing your payment. Please try again.');
        }
    }

    public function success(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);

        $orderType = session('order_type');
        $orderId = session($orderType == 'order' ? 'order_id' : 'guest_order_id');

        if (!$orderId) {
            return redirect()->route('checkout.index')->withErrors('error', 'Order not found.');
        }

        if ($orderType == 'order') {
            $order = Order::findOrFail($orderId);
        } else {
            $order = GuestOrder::findOrFail($orderId);
        }
        // dd($response);
        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            // dd('thành công');
            // Thanh toán thành công
            $order->status = $orderType == 'order' ? Order::STATUS['pending'] : 'PENDING';
            $order->save();

            // Gửi email xác nhận đơn hàng
            $this->checkoutController->sendOrderConfirmationEmail($order, $orderType);

            // Xóa cart sau khi thanh toán thành công
            if ($orderType == 'order') {
                Cart::where('user_id', $order->user_id)->delete();
            }
            session()->forget(['cart', 'cartItems', 'subtotal']);

            return redirect()->route('checkout.success')->with('success', 'Transaction complete.');
        } else {
            // dd('Thất bại');
            // Thanh toán thất bại
            $order->status = $orderType == 'order' ? Order::STATUS['canceled'] : 'CANCELED';
            $order->save();

            $errorMessage = 'Payment failed. ';

            // Xử lý các trường hợp lỗi cụ thể
            if (isset($response['name']) && $response['name'] == 'UNPROCESSABLE_ENTITY') {
                switch ($response['details'][0]['issue'] ?? '') {
                    case 'INSTRUMENT_DECLINED':
                        $errorMessage .= 'Your payment method was declined. Please try a different payment method.';
                        break;
                    case 'PAYER_CANNOT_PAY':
                        $errorMessage .= 'There was an issue with your PayPal account. Please check your account or try a different payment method.';
                        break;
                    case 'PAYER_ACCOUNT_RESTRICTED':
                        $errorMessage .= 'Your PayPal account is restricted. Please contact PayPal support.';
                        break;
                    case 'PAYER_ACCOUNT_LOCKED_OR_CLOSED':
                        $errorMessage .= 'Your PayPal account is locked or closed. Please contact PayPal support.';
                        break;
                    case 'PAYEE_ACCOUNT_RESTRICTED':
                        $errorMessage .= 'There is an issue with the merchant\'s PayPal account. Please try again later or use a different payment method.';
                        break;
                    case 'CURRENCY_NOT_SUPPORTED_FOR_COUNTRY':
                        $errorMessage .= 'The currency is not supported for your country. Please try a different payment method.';
                        break;
                    default:
                        $errorMessage .= 'An unexpected error occurred. Please try again or use a different payment method.';
                }
            } else {
                $errorMessage .= $response['message'] ?? 'Please try again or use a different payment method.';
            }

            // Log the error for debugging
            Log::error('PayPal payment failed: ' . json_encode($response));

            return redirect()->route('checkout.index')->withErrors('error', $errorMessage);
        }
    }

    public function cancel()
    {
        return redirect()->route('checkout.index')->withErrors('error', 'You have canceled the transaction.');
    }
}
