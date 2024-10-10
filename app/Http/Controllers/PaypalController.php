<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\GuestOrder;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Facades\Log;

class PaypalController extends Controller
{
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
                            "value" => $total_amount
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

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            $orderType = session('order_type');
            $orderId = session($orderType == 'order' ? 'order_id' : 'guest_order_id');

            if ($orderId) {
                if ($orderType == 'order') {
                    $order = Order::findOrFail($orderId);
                    $order->status = Order::STATUS['pending'];
                } else {
                    $order = GuestOrder::findOrFail($orderId);
                    $order->status = 'PENDING';
                }
                $order->save();
            }
            return redirect()->route('checkout.success')->with('success', 'Transaction complete.');
        } else {
            return redirect()->route('checkout.index')->withErrors('error', 'Order not found.');
        }
    }

    public function cancel()
    {
        return redirect()->route('checkout.index')->withErrors('error', 'You have canceled the transaction.');
    }
}
