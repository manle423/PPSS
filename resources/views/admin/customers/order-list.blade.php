@extends('layouts.admin')
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
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
        <div class="table-controls">
        </div>
        <table class="brand-table">
            <thead>
                <tr>
                    <th>Order code</th>
                    <th>User name</th>
                    <th>Order date</th>
                    <th>Total price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
           @foreach($orders as $order)
                    <tr>
                        <td>{{$order->order_code}}</td>
                        <td>{{$username}}</td>
                        <td>{{$order->order_date}}</td>
                        <td>{{$order->total_price}}</td>
                        <td>{{$order->status}}</td>
                        <td>  
                        <a href=""> <i class="fas fa-eye"></i></a> || 
                        <a href=""><i class="fas fa-receipt" ></i> </a>||
                        <form action="" method="POST" style="display: inline-block;">
                         @csrf
                         @method('POST')
                         <button type="submit" class="btn btn-sm"  onclick="return confirm('Are you sure you want to delete this customer?')"> <i class="fas fa-trash"></i></button>
                        </form>
                    
                        </td>
                      
                @endforeach   
                    </tr>
              
            </tbody>
        </table>
        <div class="table-info">
            <span>Showing 1 to 5 of 5 entries</span>
        </div>
        <div class="pagination">
        {{ $orders->links() }}
        </div>
    </div>
@endsection
