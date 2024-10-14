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

        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" class="new-product-form" enctype="multipart/form-data" onsubmit="removeEmptyVariants()">
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
                    @foreach ($categories as $category)
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
                <label class="file-input">Product Image</label>
                @if($product->image)
                    <div class="mb-2">
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" id="product-image-preview" style="max-width: 200px; max-height: 200px;">
                    </div>
                @endif
                <input class="form-control" type="file" id="image" name="image" accept="image/*" onchange="previewImage(this);">
            </div>
            <div class="input-group">
                <label for="price">Price ($)</label>
                <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}"
                    step="0.01" required>
            </div>

            <div class="input-group">
                <label for="stock">Stock Quantity</label>
                <input type="number" id="stock" name="stock_quantity"
                    value="{{ old('stock_quantity', $product->stock_quantity) }}" required>
            </div>

            <div id="variants-container">
                <h3>Product Variants</h3>
                <button type="button" class="btn btn-primary mb-3" id="add-variant-button">Add Variant</button>
                @foreach($product->variants as $index => $variant)
                    <div class="variant-group card mt-3">
                        <div class="card-body">
                            <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
                            <button type="button" class="btn btn-danger remove-variant-button">Remove</button>
                            <div class="input-group mt-2">
                                <label for="variant_name">Variant Name</label>
                                <input type="text" name="variants[{{ $index }}][variant_name]" value="{{ $variant->variant_name }}">
                            </div>
                            <div class="input-group mt-2">
                                <label for="variant_price">Variant Price ($)</label>
                                <input type="number" name="variants[{{ $index }}][variant_price]" step="0.01" value="{{ $variant->variant_price }}">
                            </div>
                            <div class="input-group mt-2">
                                <label for="variant_stock">Stock Quantity</label>
                                <input type="number" name="variants[{{ $index }}][stock_quantity]" value="{{ $variant->stock_quantity }}">
                            </div>
                            <div class="input-group mt-2">
                                <label for="variant_exp_date">Expiration Date</label>
                                <input type="date" name="variants[{{ $index }}][exp_date]" value="{{ $variant->exp_date }}">
                            </div>
                            <div class="input-group mt-2">
                                <label for="variant_image">Variant Image</label>
                                <input type="file" name="variants[{{ $index }}][variant_image]" accept="image/*">
                                @if($variant->image)
                                    <img src="{{ $variant->image }}" alt="{{ $variant->variant_name }}" style="max-width: 100px; max-height: 100px;">
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Template for new variants (hidden by default) -->
                <template id="variant-template">
                    <div class="variant-group card mt-3">
                        <div class="card-body">
                            <button type="button" class="btn btn-danger remove-variant-button">Remove</button>
                            <div class="input-group mt-2">
                                <label for="variant_name">Variant Name</label>
                                <input type="text" name="variants[__INDEX__][variant_name]">
                            </div>
                            <div class="input-group mt-2">
                                <label for="variant_price">Variant Price ($)</label>
                                <input type="number" name="variants[__INDEX__][variant_price]" step="0.01">
                            </div>
                            <div class="input-group mt-2">
                                <label for="variant_stock">Stock Quantity</label>
                                <input type="number" name="variants[__INDEX__][stock_quantity]">
                            </div>
                            <div class="input-group mt-2">
                                <label for="variant_exp_date">Expiration Date</label>
                                <input type="date" name="variants[__INDEX__][exp_date]">
                            </div>
                            <div class="input-group mt-2">
                                <label for="variant_image">Variant Image</label>
                                <input type="file" name="variants[__INDEX__][variant_image]" accept="image/*">
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <button type="submit" class="submit-button">Update Product</button>
            <a href="{{ route('admin.products.list') }}"><button type="button" class="submit-button" style="background-color:yellow; margin-right:10px; margin-left:10px;">Cancel</button></a>
        
            <a href="{{ route('admin.products.sale',$product->id) }}"><button type="button" class="submit-button" style="background-color:blue;">Revenue</button></a>
        </form>
    </div>

    <script src="{{ asset('assets/js/product.js') }}"></script>
@endsection