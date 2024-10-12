@extends('layouts.admin')
@section('content')
<link href="{{ asset('assets/vendor/css/orderdetail.css') }}" rel="stylesheet">

<div class="order-details-container">
    <h2>Guest order detail</h2>

    <!-- Thông tin chi tiết của đơn hàng -->
    <div class="order-info">
        <p><strong>Order code:</strong> {{ $order->orders->order_code }} </p>
        <p><strong>Ordered date:</strong> {{ $order->order_date }}</p>
        <p><strong>Name:</strong> {{ $order->orders->user->full_name }} </p>
        <p><strong>Phone number:</strong> {{ $order->guest_phone_number }} </p>
        <p><strong>Address:</strong> {{ $order->guest_address }}</p>
        <p><strong>Status:</strong> {{ $order->status }} </p>
        <p><strong>Order date:</strong> {{ $order->order_date }} </p>
        <p><strong>Shipping method:</strong> {{ $order->shipping_method_id }} </p>
        <p><strong>Payment method:</strong> {{ $order->payment_method }}</p>
        <p><strong>Promotion:</strong> {{ $order->promotion_id ?? 'N/A' }}</p>
        <p><strong>Coupon:</strong> {{ $order->coupon_id ?? 'N/A' }}</p>
        <p><strong>Total price:</strong> {{ $order->total_price ?? 'N/A' }}</p>
        <p><strong>Discount value:</strong> {{ $order->discount_value ?? 'N/A'}}</p>
        <p><strong>Final price:</strong> {{ $order->final_price }}</p>
    </div>

    <!-- Bảng hiển thị chi tiết các sản phẩm trong đơn hàng -->
    <table class="order-items">
        <thead>
            <tr>
                <th>Product Image</th>
                <th>Product name</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Promotion</th>
                <th>Last Price</th>
            </tr>
        </thead>
        <tbody>
            <!-- Giả sử bạn sẽ thay thế các giá trị này bằng dữ liệu thực -->
            
            @foreach ($order->orderItems as $orderItem)
            <tr>
    <td>{{ $orderItem->item->image }}</td>
    <td>{{ $orderItem->item->name }}</td>
    <td>{{ $orderItem->variant_id }}</td>
    <td>{{ $orderItem->quantity }}</td>
    <td>{{ $orderItem->item->price }}</td>
    <td>promotion</td>
    <td>{{ $order->final_price }}</td>
</tr>
            @endforeach

        </tbody>
    </table>
</div>

@endsection
