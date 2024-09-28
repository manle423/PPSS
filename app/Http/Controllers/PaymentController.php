<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function placeOrder(Request $request)
    {
        $payment_method = $request->payment_method;

        if ($payment_method == 'momo') {
            return redirect()->route('checkout.momo');
        } elseif ($payment_method == 'paypal') {
            return redirect()->route('checkout.paypal');
        } elseif ($payment_method == 'bank') {
            return redirect()->route('checkout.bank');
        } elseif ($payment_method == 'cash') {
            return redirect()->route('checkout.cash');
        }
    }
    
    public function momo(Request $request)
    {
        // Liên kết với API của Momo tại đây
        $api_url = 'https://test-payment.momo.vn/v2/gateway/api/create';

        $data = [
            'amount' => $request->total,
            // Các tham số cần thiết khác để gửi tới API Momo
        ];

        // Gửi yêu cầu tới API Momo
        $response = $this->callApi($api_url, $data);

        return redirect()->to($response->payUrl);
    }

    public function paypal(Request $request)
    {
        // Liên kết với API của PayPal tại đây
        $api_url = 'https://api.sandbox.paypal.com/v1/payments/payment';

        $data = [
            'intent' => 'sale',
            'payer' => [
                'payment_method' => 'paypal',
            ],
            // Các tham số cần thiết khác để gửi tới API PayPal
        ];

        // Gửi yêu cầu tới API PayPal
        $response = $this->callApi($api_url, $data);

        return redirect()->to($response->links[1]->href);  // Redirect đến trang thanh toán của PayPal
    }

    public function bank(Request $request)
    {
        // Xử lý thanh toán qua ngân hàng (API của ngân hàng)
        // Giả sử có API của ngân hàng tương tự
        $api_url = 'https://bankapi.vn/payment';

        $data = [
            'account_number' => $request->account_number,
            'amount' => $request->total,
            // Các thông tin cần thiết khác cho giao dịch
        ];

        // Gửi yêu cầu tới API của ngân hàng
        $response = $this->callApi($api_url, $data);

        // Giả sử có đường dẫn để redirect tới trang thanh toán của ngân hàng
        return redirect()->to($response->redirect_url);
    }

    public function cash(Request $request)
    {
        // Với phương thức tiền mặt, chỉ cần lưu đơn hàng và xác nhận
        // Xử lý logic để lưu đơn hàng vào database
        // Sau khi lưu thành công, hiển thị thông báo đã đặt hàng thành công
        return view('order.success');
    }

    // Hàm gọi API chung
    private function callApi($url, $data)
    {
        // Gửi yêu cầu HTTP POST tới API (dùng Curl hoặc Guzzle)
        $client = new \GuzzleHttp\Client();
        $response = $client->post($url, [
            'json' => $data
        ]);

        return json_decode($response->getBody());
    }
}
