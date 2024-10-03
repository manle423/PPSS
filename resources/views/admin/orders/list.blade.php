@extends('layouts.admin')
@section('content')
<link href="{{ asset('assets/vendor/css/orderlist.css') }}" rel="stylesheet">

<div class="order-list-container">
        <h1>Danh sách đơn hàng</h1>
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order code</th>
                    <th>Client Name</th>
                    <th>Ordered date</th>
                    <th>Satus</th>
                    <th>Final Price ($)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->order_code }}</td>
                        <td>Tên khách hàng</td>
                        <td>{{ $order->created_date }}</td> <!-- Hiển thị tên category -->
                        @if($order->status=='PENDING')
                        <td><span class="status pending">Pending</span></td>
                        @elseif($order->status=='COMPLETED')
                        <td><span class="status completed">Completed</span></td>
                        @elseif($order->status=='CANCELED')
                        <td><span class="status canceled">Canceled</span></td>
                        @elseif($order->status=='SHIPPING')
                        <td><span class="status shipping">Shipping</span></td>
                        @endif
                        <td>{{ $order->final_price }}</td>
                        <td><button class="action-btn">Details</button></td>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
       
    </div>
    <style>
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    background: linear-gradient(to right, #f4f4f4, #e0e0e0);
    padding: 20px;
}

.order-list-container {
    max-width: 1200px;
    margin: 0 auto;
    background-color: #fff;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

h1 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
    font-size: 2em;
    letter-spacing: 0.05em;
}

.order-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.order-table th, 
.order-table td {
    padding: 15px 20px;
    border: 1px solid #ddd;
    text-align: left;
}

.order-table th {
    background-color: #007bff;
    color: #fff;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    font-size: 0.95em;
}

.order-table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

.order-table tbody tr:hover {
    background-color: #f1f1f1;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transform: scale(1.01);
}

.status {
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 0.85em;
    font-weight: bold;
    display: inline-block;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.status.pending {
    background-color: #ffc107;
    color: #fff;
}

.status.completed {
    background-color: #28a745;
    color: #fff;
}

.status.canceled {
    background-color: #dc3545;
    color: #fff;
}
.status.shipping {
    background-color: #679caa;
    color: #fff;
}

.action-btn {
    padding: 8px 16px;
    border: none;
    background-color: #007bff;
    color: white;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1em;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.action-btn:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .order-list-container {
        padding: 10px;
    }

    .order-table th, 
    .order-table td {
        padding: 10px;
        font-size: 0.9em;
    }

    h1 {
        font-size: 1.5em;
    }
}

    </style>
@endsection