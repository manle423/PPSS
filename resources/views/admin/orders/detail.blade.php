@extends('layouts.admin')
@section('content')
<link href="{{ asset('assets/vendor/css/orderdetail.css') }}" rel="stylesheet">

<div class="order-details-container">
        <h2>Chi Tiết Đơn Hàng</h2>

        <!-- Thông tin chung của đơn hàng -->
        <div class="order-info">
            <p><strong>Order code:</strong> #123456</p>
            <p><strong>Ordered date:</strong> 22/09/2024</p>
            <p><strong>Name:</strong> Nguyễn Văn A</p>
            <p><strong>Address:</strong> 123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh</p>
            <p><strong>Shipping method:</strong> Express delivery</p>
            <p><strong>Payment method:</strong> PayPal</p>
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

        <!-- Tổng tiền đơn hàng -->
        <div class="order-total">
            <p><strong>Cupon:</strong> 10%</p>
            <p><strong>Price:</strong> 330000</p>
            <p><strong>Total:</strong> 297000</p>
            <p><strong>Status:</strong><span class="status completed">Hoàn thành</span> </p>
        </div>
    </div>

    @endsection