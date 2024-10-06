@extends('layouts.admin')

@section('content')
    <link href="{{ asset('assets/vendor/css/product.css') }}" rel="stylesheet">
    <div class="product-container">
        <h2 class="product-form-title">Create New Product</h2>
        <!-- Hiển thị lỗi -->
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
        <form action="{{ route('admin.products.store') }}" enctype="multipart/form-data" method="post" class="new-product-form" onsubmit="removeEmptyVariants()">
            @csrf
            <div class="input-group">
                <label for="name">Product Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            </div>
            <div class="input-group">
                <label for="category">Category</label>
                <select id="category" name="category_id" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="input-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
            </div>
            <div class="input-group">
            <label class="file-input">File Input</h6>      
            <input class="form-control" type="file" id="image" name="image">            
            </div>
            <div class="input-group">
                <label for="price">Price ($)</label>
                <input type="number" id="price" name="price" step="0.01" value="{{ old('price') }}" required>
            </div>
            <div class="input-group">
                <label for="stock">Stock Quantity</label>
                <input type="number" id="stock" name="stock_quantity" value="{{ old('stock_quantity') }}" required>
            </div>

            <div id="variants-container">
                <h3>Product Variants</h3>
                <button type="button" class="btn btn-primary mb-3" id="add-variant-button">Add Variant</button>
                @if(old('variants'))
                    @foreach(old('variants') as $index => $variant)
                        <div class="variant-group card mt-3">
                            <div class="card-body">
                                <button type="button" class="btn btn-danger remove-variant-button">Remove</button>
                                <div class="input-group mt-2">
                                    <label for="variant_name">Variant Name</label>
                                    <input type="text" name="variants[{{ $index }}][variant_name]" value="{{ $variant['variant_name'] }}">
                                </div>
                                <div class="input-group mt-2">
                                    <label for="variant_price">Variant Price ($)</label>
                                    <input type="number" name="variants[{{ $index }}][variant_price]" step="0.01" value="{{ $variant['variant_price'] }}">
                                </div>
                                <div class="input-group mt-2">
                                    <label for="variant_stock">Stock Quantity</label>
                                    <input type="number" name="variants[{{ $index }}][stock_quantity]" value="{{ $variant['stock_quantity'] }}">
                                </div>
                                <div class="input-group mt-2">
                                    <label for="variant_exp_date">Expiration Date</label>
                                    <input type="date" name="variants[{{ $index }}][exp_date]" value="{{ $variant['exp_date'] }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="variant-group card mt-3" style="display: none;">
                        <div class="card-body">
                            <button type="button" class="btn btn-danger remove-variant-button">Remove</button>
                            <div class="input-group mt-2">
                                <label for="variant_name">Variant Name</label>
                                <input type="text" name="variants[0][variant_name]">
                            </div>
                            <div class="input-group mt-2">
                                <label for="variant_price">Variant Price ($)</label>
                                <input type="number" name="variants[0][variant_price]" step="0.01">
                            </div>
                            <div class="input-group mt-2">
                                <label for="variant_stock">Stock Quantity</label>
                                <input type="number" name="variants[0][stock_quantity]">
                            </div>
                            <div class="input-group mt-2">
                                <label for="variant_exp_date">Expiration Date</label>
                                <input type="date" name="variants[0][exp_date]">
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div  style="text-align:center;"> 
            <button type="submit" class="submit-button" style="margin-top:15px;">Add Product</button>
            <a href="{{ route('admin.products.list') }}" class="btn btn-primary" style="margin-left:10px; background-color:yellow;" >Cancel</a>
            </div>
        </form>
    </div>

    <script src="{{ asset('assets/js/product.js') }}"></script>
@endsection