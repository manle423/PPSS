@php
    use Carbon\Carbon;
@endphp

@extends('layouts.shop')


@section('content')
    <div class="container">
        <h1 class="my-4 text-center">Your order history</h1>

        <!-- Thanh điều hướng trạng thái -->

        <ul class="nav nav-pills mb-4 justify-content-center">
            <li class="nav-item">
                <a class="nav-link {{ $status == 'PENDING' ? 'active' : '' }}"
                    href="{{ route('user.order-history', 'PENDING') }}">Pending</a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ $status == 'SHIPPING' ? 'active' : '' }}"
                    href="{{ route('user.order-history', 'SHIPPING') }}">Shipping</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status == 'COMPLETED' ? 'active' : '' }}"
                    href="{{ route('user.order-history', 'COMPLETED') }}">Completed</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status == 'CANCELED' ? 'active' : '' }}"
                    href="{{ route('user.order-history', 'CANCELED') }}">Canceled</a>
            </li>
        </ul>
 
        <!-- Kiểm tra nếu không có đơn hàng -->

        @if ($orders->isEmpty())
            <p class="text-center">There are no orders in status {{ $status }}.</p>
        @else
            <!-- Hiển thị bảng đơn hàng -->
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>Delivery Address</th>
                        <th>Payment Method</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            @auth    
                            <td>{{ $order->shipping_address }}</td>
                            @endauth
                            @guest
                        
                            @endguest
                            <td>{{ $order->payment_method }}</td>
                            <td>{{ number_format($order->total_price, 2) }} VNĐ</td>
                            <td>{{ $order->status }}</td>
                            <td>{{ Carbon::parse($order->order_date)->format('d/m/Y H:i') }}</td>
                            <td>
                                {{-- <a href="{{ route('order.details', $order->id) }}" class="btn btn-info btn-sm">Xem chi tiết</a> --}}
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        @endif
    </div>
@endsection
