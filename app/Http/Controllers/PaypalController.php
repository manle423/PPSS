<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalController extends Controller
{
    public function process(Request $request)
    {
        $order = Order::findOrFail($request->order_id);

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();

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
                        "value" => number_format($order->total_amount, 2, '.', '')
                    ]
                ]
            ]
        ]);

        if (isset($response['id']) && $response['id'] != null) {
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);
                }
            }
            return redirect()->route('checkout.index')->with('error', 'Something went wrong.');
        } else {
            return redirect()->route('checkout.index')->with('error', $response['message'] ?? 'Something went wrong.');
        }
    }

    public function success(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            // Update order status
            return redirect()->route('checkout.success')->with('success', 'Transaction complete.');
        } else {
            return redirect()->route('checkout.index')->with('error', $response['message'] ?? 'Something went wrong.');
        }
    }

    public function cancel()
    {
        return redirect()->route('checkout.index')->with('error', 'You have canceled the transaction.');
    }
}
