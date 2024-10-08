<?php

namespace App\Http\Controllers;

use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Http\Request;

class PaypalController extends Controller
{
    public function createPaypal()
    {
        return view("payments.success");
    }

    public function processPaypal(Request $request)
    {
        // dd($request->all());
        // $item = $request;

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('processSuccess'),
                "cancel_url" => route('processCancel'),
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => "100.00"
                    ]
                ]
            ]
        ]);

        if (isset($response['id']) && $response['id'] != null) {

            // redirect to approve href
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);
                }
            }

            return redirect()
                ->route('paypal.create')
                ->with('error', 'Something went wrong.');

        } else {
            return redirect()
                ->route('paypal.create')
                ->with('error', $response['message'] ?? 'Something went wrong.');
        }
    }


    public function processSuccess(Request $request)
    {

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            return redirect()
                ->route('paypal.create')
                ->with('success', 'Transaction complete.');
        } else {
            return redirect()
                // fail transaction
                ->route('paypal.create')
                ->with('error', $response['message'] ?? 'Something went wrong.');
        }

    }

    public function processCancel(Request $request)
    {
        return redirect()
            ->route('paypal.create')
            ->with('error', $response['message'] ?? 'You have canceled the transaction.');
    }
}
