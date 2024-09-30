@extends('layouts.app')
@section('content')
    <div class="container">
        <h1>Your Cart</h1>

        <!-- Search Form -->
        <form action="{{ route('cart.index') }}" method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search products in cart..."
                    value="{{ request('search') }}">
                <div class="input-group-append">
                    <button class="btn btn-outline-primary" type="submit">Search</button>
                </div>
            </div>
            <!-- Category Dropdown -->
            <div class="form-group">
                <select name="category" class="form-control">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>



        @if ($cartItems == [])
            <p>Your cart is empty.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Variant</th>
                        <th>In Stock</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($cartItems as $item)
                        @php

                            $cartKey = $item->product->id . '-' . ($item->variant ? $item->variant->id : '');
                            $amount = $sessionCart[$cartKey] ?? 0;
                        @endphp
                        <x-cart-item :item="$item" :cartKey="$cartKey" :amount="$amount"  />
                    @endforeach

                    {{-- @guest
                        @foreach (session()->get('cart', []) as $cartKey => $amount)
                            @php
                                [$productId, $variantId] = explode('-', $cartKey);
                                
                            @endphp
                            <x-cart-item-guest :cartKey="$cartKey" :productId="$productId" :variantId="$variantId" :amount="$amount" />
                        @endforeach
                    @endguest --}}
                </tbody>
            </table>
        @endif

        <a href="{{ route('product.index') }}" class="btn btn-primary">Continue Shopping</a>
    </div>
@endsection
