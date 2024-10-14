@extends('layouts.admin')
@section('content')
    <link href="{{ asset('assets/vendor/css/orderdetail.css') }}" rel="stylesheet">

    <div class="order-details-container">
        <h2>Order Details</h2>

        <div class="order-info">
            <p><strong>Order code:</strong> {{ $order->order_code }}</p>
            <p><strong>Ordered date:</strong> {{ $order->order_date }}</p>
            <p><strong>Name:</strong> {{ $order->user->full_name }}</p>
            <p><strong>Address:</strong> {{ $order->shippingAddress->full_address }}, {{ $order->shippingAddress->ward->name }}, {{ $order->shippingAddress->ward->district->name }}, {{ $order->shippingAddress->ward->district->province->name }}</p>
            <p><strong>Shipping method:</strong> {{ $order->shippingMethod->name ?? 'N/A' }}</p>
            <p><strong>Payment method:</strong> {{ $order->payment_method }}</p>
            <p><strong>Coupon:</strong> {{ $order->coupon->code ?? 'N/A' }}</p>
            <p><strong>Total price:</strong> {{ $order->total_price }}</p>
            <p><strong>Discount value:</strong> {{ $order->discount_value }}</p>
            <p><strong>Final price:</strong> {{ $order->final_price }}</p>
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
                        <td>{{ $orderItem->price }}</td>
                        <td>{{ $orderItem->quantity * $orderItem->price }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
