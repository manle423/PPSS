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

        {{-- Product variants --}}

        <a href="" class="btn btn-primary">Add to Cart</a>
   
    </div>
@endsection
