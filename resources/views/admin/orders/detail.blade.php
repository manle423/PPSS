@extends('layouts.admin')
@section('content')
    <link href="{{ asset('assets/vendor/css/orderdetail.css') }}" rel="stylesheet">

    <div class="order-details-container">
        <h2>Order Details</h2>

        <div class="order-info">
            <p><strong>Order code:</strong> {{ $order->order_code }}</p>
            <p><strong>Ordered date:</strong> {{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y H:i') }}</p>
            <p><strong>Name:</strong> {{ $order->user->full_name ?? $order->guest_name }}</p>
            <p><strong>Address:</strong> 
                {{ $order->shippingAddress->address_line_1 }}
                @if($order->shippingAddress->address_line_2)
                    , {{ $order->shippingAddress->address_line_2 }}
                @endif
                , {{ $order->shippingAddress->ward->name ?? 'Unknown Ward' }}
                , {{ $order->shippingAddress->district->name ?? 'Unknown District' }}
                , {{ $order->shippingAddress->province->name ?? 'Unknown Province' }}
            </p>
            <p><strong>Shipping method:</strong> {{ $order->shippingMethod->name ?? 'N/A' }}</p>
            <p><strong>Payment method:</strong> {{ ucfirst($order->payment_method) }}</p>
            <p><strong>Coupon:</strong> {{ $order->coupon->code ?? 'N/A' }}</p>
            <p><strong>Subtotal:</strong> {{ number_format($order->total_price, 2) }}</p>
            <p><strong>Shipping Fee:</strong> {{ number_format($order->shipping_fee, 2) ?? 0 }}</p>
            @if($order->discount_value > 0)
                <p><strong>Discount:</strong> {{ number_format($order->discount_value, 2) }}</p>
            @endif
            <p><strong>Total price:</strong> {{ number_format($order->final_price, 2) }}</p>
            <p><strong>Order Status:</strong> {{ ucfirst($order->status) }}</p>
            
            @if($order->status === 'PENDING')
                <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-danger">Cancel Order</button>
                </form>
            @endif
        </div>

        <table class="order-items">
            <thead>
                <tr>
                    <th>Product Image</th>
                    <th>Product name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderItems as $orderItem)
                    <tr>
                        <td><img src="{{ $orderItem->item->image_url }}" alt="{{ $orderItem->item->name }}" width="50"></td>
                        <td>{{ $orderItem->item->name }}</td>
                        <td>{{ $orderItem->quantity }}</td>
                        <td>{{ number_format($orderItem->price, 2) }}</td>
                        <td>{{ number_format($orderItem->quantity * $orderItem->price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
