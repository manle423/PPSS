@extends('layouts.admin')
@section('content')
    <link href="{{ asset('assets/vendor/css/orderlist.css') }}" rel="stylesheet">

    <div class="order-list-container">
        <h1>All Orders</h1>

        <form action="{{ route('admin.orders.list') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label for="order_code">Order Code:</label>
                    <input type="text" name="order_code" id="order_code" class="form-control" value="{{ request('order_code') }}" placeholder="Search by order code">
                </div>
                <div class="col-md-3">
                    <label for="start_date">Start Date:</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date">End Date:</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="customer_type">Customer Type:</label>
                    <select name="customer_type" id="customer_type" class="form-control">
                        <option value="">All</option>
                        <option value="registered" {{ request('customer_type') === 'registered' ? 'selected' : '' }}>Registered</option>
                        <option value="guest" {{ request('customer_type') === 'guest' ? 'selected' : '' }}>Guest</option>
                    </select>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-3">
                    <label for="sort">Sort By:</label>
                    <select name="sort" id="sort" class="form-control">
                        <option value="order_date" {{ request('sort') === 'order_date' ? 'selected' : '' }}>Order Date</option>
                        <option value="final_price" {{ request('sort') === 'final_price' ? 'selected' : '' }}>Total Price</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="direction">Sort Direction:</label>
                    <select name="direction" id="direction" class="form-control">
                        <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Descending</option>
                        <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Ascending</option>
                    </select>
                </div>
                <div class="col-md-3 mt-4">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </div>
        </form>

        <table class="order-table">
            <thead>
                <tr>
                    <th>Order code</th>
                    <th>Name / Phone</th>
                    <th>Ordered date</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->order_code ?? 'N/A' }}</td>
                        <td>
                            @if ($order instanceof \App\Models\Order)
                                {{ $order->user->full_name }}
                            @else
                                {{ $order->guest_name ?? 'N/A' }}
                            @endif
                        </td>
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
                        <td>{{ number_format($order->final_price, 0, ',', '.') }} VND</td>
                        <td>
                            <button class="action-btn">
                                <a href="{{ $order instanceof \App\Models\Order ? route('admin.orders.detail', $order->id) : route('admin.orders.detail-guest-order', $order->id) }}">
                                    Details
                                </a>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
@endsection
