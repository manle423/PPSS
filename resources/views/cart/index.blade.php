@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Your Cart</h1>
        
        @if ($cartItems->isEmpty())
            <p>Your cart is empty.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>In Stock</th>
                        <th>Quantity</th>
                        
                        <th>Price</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cartItems as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ number_format($item->product->stock_quantity) }}</td>
                            <td>
                                <form action="{{ route('cart.update', $item) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                        style="width: 60px;" max={{$item->product->stock_quantity}} />
                                    <button  class="btn btn-primary btn-sm">Update</button>
                                </form>
                            </td>
                            
                            <td>${{ number_format($item->product->price, 2) }}</td>
                            <td>${{ number_format($item->quantity * $item->product->price, 2) }}</td>
                            <td>
                                <form action="{{ route('cart.destroy', $item) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <a href="{{ route('product.index') }}" class="btn btn-primary">Continue Shopping</a>
    </div>
@endsection
