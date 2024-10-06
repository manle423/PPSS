@extends('layouts.admin')
@section('content')
<link href="{{ asset('assets/vendor/css/orderdetail.css') }}" rel="stylesheet">

<div class="order-details-container">
    <h2>Chi Tiết Đơn Hàng</h2>

    <!-- Thông tin chi tiết của đơn hàng -->
    <div class="order-info">
        <p><strong>Order code:</strong> {{ $order->order_code }} </p>
        <p><strong>Ordered date:</strong> {{ $order->order_date }}</p>
        <p><strong>Name:</strong> {{ $order->user->full_name }} </p>
        <p><strong>Address:</strong> {{ $order->shipping_address }}</p>
        <p><strong>Shipping method:</strong> Express delivery</p>
        <p><strong>Payment method:</strong> {{ $order->payment_method }}</p>
        <p><strong>Coupon:</strong> {{ $order->coupon->discount_value }}</p>
        <p><strong>Total price:</strong> {{ $order->total_price }}</p>
        <p><strong>Discount value:</strong> {{ $order->discount_value }}</p>
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
            <tr>
                <td>Image</td>
                <td>Sand</td>
                <td>Yellow</td>
                <td>2</td>
                <td>60000</td>
                <td>55000</td>
                <td>110000</td>
            </tr>
            <tr>
                <td>Image</td>
                <td>Sand</td>
                <td>Yellow</td>
                <td>2</td>
                <td>60000</td>
                <td>55000</td>
                <td>110000</td>
            </tr>
            <tr>
                <td>Image</td>
                <td>Sand</td>
                <td>Yellow</td>
                <td>2</td>
                <td>60000</td>
                <td>55000</td>
                <td>110000</td>
            </tr>
        </tbody>
    </table>
</div>

@endsection
