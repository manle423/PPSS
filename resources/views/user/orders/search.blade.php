@extends('layouts.shop')

@section('content')
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-8 mt-5">
                <h2>Search Your Order</h2>
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

                <form action="{{ route('order.search.post') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="order_code">Order Code:</label>
                        <input type="text" class="form-control" id="order_code" name="order_code" value="{{ old('order_code') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>

                @if(session('success'))
                    <form action="{{ route('order.verify') }}" method="POST" class="mt-4">
                        @csrf
                        <input type="hidden" name="order_code" value="{{ old('order_code') }}">
                        <input type="hidden" name="email" value="{{ old('email') }}">
                        <div class="form-group">
                            <label for="verification_code">Verification Code:</label>
                            <input type="text" class="form-control" id="verification_code" name="verification_code" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Verify</button>
                    </form>
                @endif

                @if(isset($order))
                    <div class="mt-5">
                        <h3>Order Details</h3>
                        <p><strong>Order Code:</strong> {{ $order->order_code }}</p>
                        <p><strong>Status:</strong> {{ $order->status }}</p>
                        <p><strong>Order Date:</strong> {{ $order->order_date }}</p>
                        <p><strong>Total Price:</strong> {{ number_format($order->final_price, 0, ',', '.') }} VND</p>
                        
                        <h4>Order Items</h4>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                <tr>
                                    <td>{{ $item->item->name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->price, 0, ',', '.') }} VND</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
