@extends('layouts.app')

@section('content')
    <div class="container">

        {{-- Show product details --}}
        <h1>{{ $product->name }}</h1>
        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
        <p>{{ $product->description }}</p>
        <p>Price: ${{ $product->price }}</p>
        <p>Stock: {{ $product->stock_quantity }}</p>
        <p>Category: {{ $product->category_id }}</p>

        {{-- Amount to add to cart --}}
        <form action="{{ route('cart.store', $product->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="amount">Amount:</label>
                <input type="number" id="amount" name="amount" min="1" value="1" max="{{ $product->stock_quantity }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Add to Cart</button>
            <a href="{{ route('product.index') }}" class="btn btn-secondary">Back to products</a>
        </form>
    </div>
@endsection
