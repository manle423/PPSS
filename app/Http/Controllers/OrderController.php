<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\GuestOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('buyerOrGuest');
    }

    public function searchForm()
    {
        return view('user.orders.search');
    }

    public function search(Request $request)
    {
        $request->validate([
            'order_code' => 'required|string',
            'email' => 'required|email',
        ]);

        $orderCode = $request->order_code;
        $email = $request->email;

        if (Str::startsWith($orderCode, 'GT')) {
            $order = GuestOrder::where('order_code', $orderCode)
                               ->where('guest_email', $email)
                               ->first();
        } else {
            $order = Order::where('order_code', $orderCode)
                          ->whereHas('user', function($query) use ($email) {
                              $query->where('email', $email);
                          })
                          ->first();
        }

        if (!$order) {
            return back()->withErrors(['error' => 'Không tìm thấy đơn hàng hoặc thông tin không chính xác.'])
                         ->withInput();
        }

        // Generate verification code
        $verificationCode = Str::random(6);
        $order->verification_code = $verificationCode;
        $order->save();

        // Send email with verification code
        $recipientEmail = $order instanceof GuestOrder ? $order->guest_email : $order->user->email;
        Mail::raw("Mã xác thực của bạn là: $verificationCode", function($message) use ($recipientEmail) {
            $message->to($recipientEmail)
                    ->subject('Mã xác thực đơn hàng');
        });

        return back()->with('success', 'Mã xác thực đã được gửi đến email của bạn. Vui lòng kiểm tra và nhập mã xác thực.')
                     ->withInput();
    }

    public function verifyAndShowOrder(Request $request)
    {
        $request->validate([
            'order_code' => 'required|string',
            'email' => 'required|email',
            'verification_code' => 'required|string',
        ]);

        $orderCode = $request->order_code;
        $email = $request->email;
        $verificationCode = $request->verification_code;

        if (Str::startsWith($orderCode, 'GT')) {
            $order = GuestOrder::where('order_code', $orderCode)
                               ->where('guest_email', $email)
                               ->where('verification_code', $verificationCode)
                               ->first();
        } else {
            $order = Order::where('order_code', $orderCode)
                          ->whereHas('user', function($query) use ($email) {
                              $query->where('email', $email);
                          })
                          ->where('verification_code', $verificationCode)
                          ->first();
        }

        if (!$order) {
            return back()->withErrors(['error' => 'Mã xác thực không chính xác hoặc đã hết hạn.'])
                         ->withInput();
        }

        // Clear verification code after successful verification
        $order->verification_code = null;
        $order->save();

        return view('user.orders.search', ['order' => $order])
               ->withInput($request->only(['order_code', 'email']));
    }

    public function getOrdersByStatus(Request $request, $status)
    {
        if (!in_array($status, ['PENDING','SHIPPING', 'COMPLETED', 'CANCELED'])) {
            return redirect()->back()->with('error', 'Trạng thái không hợp lệ.');
        }
        
        $orders = Auth::user()->orders()
            ->with(['shippingAddress.province', 'shippingAddress.district', 'shippingAddress.ward', 'shippingMethod'])
            ->where('status', $status)
            ->orderBy('order_date', 'desc')
            ->paginate(5);

        return view('checkout.history', compact('orders', 'status'));
    }

    public function show(Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['shippingAddress.province', 'shippingAddress.district', 'shippingAddress.ward', 'shippingMethod', 'orderItems.item']);

        return view('checkout.details', compact('order'));
    }
}
