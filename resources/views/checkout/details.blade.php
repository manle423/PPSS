@extends('layouts.shop')

@section('content')
    <div class="container">
        <h1 class="my-4">Order Details - {{ $order->order_code }}</h1>

        <div class="row">
            <div class="col-md-6">
                <h3>Order Information</h3>
                <p><strong>Status:</strong> {{ $order->status }}</p>
                <p><strong>Order Date:</strong> {{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y H:i') }}</p>
                <p><strong>Payment Method:</strong> {{ $order->payment_method }}</p>
                <p><strong>Shipping Method:</strong> {{ $order->shippingMethod->name ?? 'N/A' }}</p>
            </div>
            <div class="col-md-6">
                <h3>Delivery Address</h3>
                <p>{{ $order->shippingAddress->full_name ?? 'N/A' }}</p>
                <p>{{ $order->shippingAddress->address_line_1 ?? 'N/A' }}</p>
                <p>{{ $order->shippingAddress->district->name ?? 'N/A' }}, {{ $order->shippingAddress->province->name ?? 'N/A' }}</p>
                <p>{{ $order->shippingAddress->phone_number ?? 'N/A' }}</p>
            </div>
        </div>

        <h3 class="mt-4">Order Items</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderItems as $item)
                    <tr>
                        <td>{{ $item->item->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price, 2) }} VNĐ</td>
                        <td>{{ number_format($item->quantity * $item->price, 2) }} VNĐ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="row justify-content-end">
            <div class="col-md-4">
                <table class="table table-bordered">
                    <tr>
                        <th>Total Price</th>
                        <td>{{ number_format($order->total_price, 2) }} VNĐ</td>
                    </tr>
                    <tr>
                        <th>Discount</th>
                        <td>{{ number_format($order->discount_value, 2) }} VNĐ</td>
                    </tr>
                    <tr>
                        <th>Final Price</th>
                        <td>{{ number_format($order->final_price, 2) }} VNĐ</td>
                    </tr>
                </table>
            </div>
        </div>

        <a href="{{ route('user.order-history', $order->status) }}" class="btn btn-secondary mt-3">Back to Order History</a>
    </div>
@endsection