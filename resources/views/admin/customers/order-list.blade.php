@extends('layouts.admin')
@section('content')
<link href="{{ asset('assets/vendor/css/customer.css') }}" rel="stylesheet">
    <div class="admin-container">
        <div class="header-section d-flex justify-content-between align-items-center mb-3">
            <h2>Customer Orders</h2>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
       
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order code</th>
                    <th>User name</th>
                    <th>Order date</th>
                    <th>Total price</th>
                    <th>Status</th>
                   
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->order_code }}</td>
                        <td>{{ $username }}</td>
                        <td>{{ $order->order_date }}</td>
                        <td>{{ $order->total_price }}</td>
                        <td>{{ $order->status }}</td>
                     
                    </tr>
                @endforeach   
            </tbody>
          
        </table>
        <a href="{{ route('admin.customers.list') }}" class="btn btn-primary" style="margin:10px; background-color:yellow;" >Cancel</a>
        <div class="table-info">
            Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} entries
        </div>

        <div class="pagination">
            {{ $orders->links() }}
        </div>
    </div>
@endsection
