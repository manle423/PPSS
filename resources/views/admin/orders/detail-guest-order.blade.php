@extends('layouts.admin')
@section('content')
    <link href="{{ asset('assets/vendor/css/orderdetail.css') }}" rel="stylesheet">

    <div class="order-details-container">
        <h2>Guest Order Details</h2>

        <div class="order-info">
            <p><strong>Order code:</strong> {{ $order->order_code }}</p>
            <p><strong>Ordered date:</strong> {{ $order->order_date }}</p>
            <p><strong>Name:</strong> {{ $order->guest_name }}</p>
            <p><strong>Phone number:</strong> {{ $order->guest_phone_number }}</p>
            <p><strong>Email:</strong> {{ $order->guest_email }}</p>
            <p><strong>Address:</strong>
                @php
                    $address = $order->guest_address;
                @endphp
                {{ $address['address_line_1'] }},
                @if ($address['address_line_2'])
                    {{ $address['address_line_2'] }},
                @endif
                {{ $address['ward_name'] }},
                {{ $address['district_name'] }},
                {{ $address['province_name'] }}
            </p>
            <p><strong>Shipping method:</strong> {{ $order->shippingMethod->name ?? 'N/A' }}</p>
            <p><strong>Payment method:</strong> {{ $order->payment_method }}</p>
            <p><strong>Coupon:</strong> {{ $order->coupon->code ?? 'N/A' }}</p>
            <p><strong>Total price:</strong> {{ number_format($order->total_price, 0, ',', '.') }} VND</p>
            <p><strong>Discount value:</strong> {{ number_format($order->discount_value, 0, ',', '.') }} VND</p>
            <p><strong>Final price:</strong> {{ number_format($order->final_price, 0, ',', '.') }} VND</p>
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
                        <td><img src="{{ $orderItem->item->image_url }}" alt="{{ $orderItem->item->name }}" width="50">
                        </td>
                        <td>{{ $orderItem->item->name }}</td>
                        <td>{{ $orderItem->quantity }}</td>
                        <td>{{ number_format($orderItem->price, 0, ',', '.') }} VND</td>
                        <td>{{ number_format($orderItem->quantity * $orderItem->price, 0, ',', '.') }} VND</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
