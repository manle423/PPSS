<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
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
