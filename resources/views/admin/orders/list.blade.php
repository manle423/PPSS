@extends('layouts.admin')
@section('content')
    <link href="{{ asset('assets/vendor/css/orderlist.css') }}" rel="stylesheet">

    <div class="order-list-container">
        <h1>Orders list</h1>
        <select style="margin-bottom:10px;" onchange="window.location.href=this.value;">
        <option value="" selected disabled>-- Select Order Type --</option>
        <option value="{{ route('admin.orders.list') }}">Customer orders</option>
    <option value="{{ route('admin.orders.list-guest-orders') }}">Guest orders</option>
</select>
        <table class="order-table">
            <thead>
                <tr>
                    <th>Order code</th>
                    <th>Name</th>
                    <th>Ordered date</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->order_code }}</td>
                        <td>{{ $order->user->full_name }}</td>
                        <td>{{ $order->order_date }}</td>
                        <td>
                            @if ($order->status == 'PENDING')
                                <span class="status pending">Pending</span>
                            @elseif($order->status == 'COMPLETED')
                                <span class="status complete">Completed</span>
                            @elseif($order->status == 'CANCELED')
                                <span class="status canceled">Canceled</span>
                            @elseif($order->status == 'SHIPPING')
                                <span class="status shipping">Shipping</span>
                            @endif
                        </td>
                        <td>{{ $order->final_price }}</td>

                        <td><button class="action-btn"><a href="{{ route('admin.orders.detail', $order->id) }}">Details</a></button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
