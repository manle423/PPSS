@php
    use Carbon\Carbon;
@endphp
@extends('layouts.shop')

@section('content')
    <div class="container mt-5">
        <div class="alert alert-success mt-5">
            <h4 class="alert-heading">Order Success!</h4>
            <p>Your transaction was completed successfully.</p>
            <hr>
            <p class="mb-0">Thank you for your purchase.</p>
        </div>

        <div class="alert alert-info mt-3">
            <p class="mb-0">An order confirmation with details has been sent to your email address. Please check your inbox.</p>
        </div>

        <h4>Order Details</h4>
        <p><strong>Order Code:</strong> {{ $order->order_code }}</p>
        <p><strong>Order Date:</strong> {{ Carbon::parse($order->order_date)->format('d/m/Y H:i') }}</p>

        <h5>Shipping Information</h5>
        @if($shippingAddress)
            <p>{{ $shippingAddress->full_name }}<br>
               {{ $shippingAddress->address_line_1 }},
               @if($shippingAddress->address_line_2)
                   {{ $shippingAddress->address_line_2 }},
               @endif
               {{ $shippingAddress->ward->name }}, {{ $shippingAddress->district->name }}, {{ $shippingAddress->province->name }}</p>
        @endif

        <p><strong>Shipping Method:</strong> {{ $shippingMethod->name ?? 'N/A' }}</p>

        <h5>Order Items</h5>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orderItems as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>{{ $item['price'] }}</td>
                        <td>{{ $item['total'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            <strong>Subtotal:</strong> {{ number_format($order->total_price, 2) }}
        </div>

        @if($order->discount_value > 0)
            <div class="mt-1">
                <strong>Discount:</strong> {{ number_format($order->discount_value, 2) }}
            </div>
        @endif

        <div class="mt-1">
            <strong>Total Price:</strong> {{ number_format($order->final_price, 2) }}
        </div>

        <div class="mt-3">
            <strong>Order Status:</strong> {{ ucfirst($order->status) }}
        </div>

        <div class="mt-3">
            <strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}
        </div>

        <a href="{{ route('home') }}" class="btn btn-primary mt-3">Continue Shopping</a>
        @if($orderType == 'order')
            <a href="{{ route('user.order-history', 'PENDING') }}" class="btn btn-primary mt-3">Your Order History</a>
        @endif
    </div>  
@endsection
