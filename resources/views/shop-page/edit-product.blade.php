@extends('layouts.admin')

@section('content')
<link href="{{ asset('assets/vendor/css/product.css') }}" rel="stylesheet">
<div class="product-container">
    <h2 class="product-form-title">Edit Product</h2>
    
    <!-- Display errors -->
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

    <form action="{{ route('shop.updatePro', $product->id) }}" method="post" class="new-product-form">
        @csrf
        @method('PUT')

        <div class="input-group">
            <label for="id">Product ID</label>
            <input type="text" id="id" name="id" readonly value="{{ $product->id }}">
        </div>

        <div class="input-group">
            <label for="name">Product Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required>
        </div>

        <div class="input-group">
            <label for="category">Category</label>
            <select id="category" name="category_id" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $category->id == $product->category_id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="input-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4" required>{{ old('description', $product->description) }}</textarea>
        </div>

        <div class="input-group">
            <label for="price">Price ($)</label>
            <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" step="0.01" required>
        </div>

        <div class="input-group">
            <label for="stock">Stock Quantity</label>
            <input type="number" id="stock" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required>
        </div>

        <button type="submit" class="submit-button">Update Product</button>
    </form>
</div>
@endsection
