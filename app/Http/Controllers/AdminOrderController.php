<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function cancelOrder(Request $request, Order $order)
    {
        if ($order->status !== 'PENDING') {
            return back()->with('error', 'Only pending orders can be cancelled.');
        }

        // Update order status to CANCELLED
        $order->status = 'CANCELLED';
        $order->save();

        // Restore stock quantities
        foreach ($order->orderItems as $orderItem) {
            $product = $orderItem->item;
            $quantity = $orderItem->quantity;
            $variant = $orderItem->variant;

            if ($variant && property_exists($variant, 'stock_quantity')) {
                $variant->stock_quantity += $quantity;
                $variant->save();
            } elseif ($product && property_exists($product, 'stock_quantity')) {
                $product->stock_quantity += $quantity;
                $product->save();
            }
        }
        return back()->with('success', 'Order has been cancelled successfully.');
    }
}
