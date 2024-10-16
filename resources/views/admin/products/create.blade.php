@extends('layouts.admin')

@section('content')
    <link href="{{ asset('assets/vendor/css/product.css') }}" rel="stylesheet">
    <div class="product-container">
        <h2 class="product-form-title">Create New Product</h2>
        
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
            
            <!-- Product Name -->
            <div class="input-group">
                <label for="name">Product Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            </div>

            <!-- Category -->
            <div class="input-group">
                <label for="category">Category</label>
                <select id="category" name="category_id" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Description -->
            <div class="input-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
            </div>

            <!-- Image Upload -->
            <div class="input-group">
                <label class="file-input">File Input</label>
                <input class="form-control" type="file" id="image" name="image">
            </div>

            <!-- Price -->
            <div class="input-group">
                <label for="price">Price ($)</label>
                <input type="number" id="price" name="price" step="0.01" value="{{ old('price') }}" required>
            </div>

            <!-- Stock Quantity -->
            <div class="input-group">
                <label for="stock">Stock Quantity</label>
                <input type="number" id="stock" name="stock_quantity" value="{{ old('stock_quantity') }}" required>
            </div>

            <!-- Dimensions -->
            <h3>Product Dimensions</h3>
            <div class="input-group">
                <label for="weight">Weight (kg)</label>
                <input type="number" id="weight" name="weight" step="0.01" value="{{ old('weight') }}">
            </div>
            <div class="input-group">
                <label for="length">Length (cm)</label>
                <input type="number" id="length" name="length" step="0.01" value="{{ old('length') }}">
            </div>
            <div class="input-group">
                <label for="width">Width (cm)</label>
                <input type="number" id="width" name="width" step="0.01" value="{{ old('width') }}">
            </div>
            <div class="input-group">
                <label for="height">Height (cm)</label>
                <input type="number" id="height" name="height" step="0.01" value="{{ old('height') }}">
            </div>

            <!-- Product Variants -->
            <div id="variants-container">
                <h3>Product Variants</h3>
                <button type="button" class="btn btn-primary mb-3" id="add-variant-button">Add Variant</button>
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
                @if(old('variants'))
                    @foreach(old('variants') as $index => $variant)
                        <div class="variant-group card mt-3">
                            <div class="card-body">
                                <button type="button" class="btn btn-danger remove-variant-button">Remove</button>

                                <!-- Variant Name -->
                                <div class="input-group mt-2">
                                    <label for="variant_name">Variant Name</label>
                                    <input type="text" name="variants[{{ $index }}][variant_name]" value="{{ $variant['variant_name'] }}">
                                </div>

                                <!-- Variant Price -->
                                <div class="input-group mt-2">
                                    <label for="variant_price">Variant Price ($)</label>
                                    <input type="number" name="variants[{{ $index }}][variant_price]" step="0.01" value="{{ $variant['variant_price'] }}">
                                </div>

                                <!-- Variant Stock Quantity -->
                                <div class="input-group mt-2">
                                    <label for="variant_stock">Stock Quantity</label>
                                    <input type="number" name="variants[{{ $index }}][stock_quantity]" value="{{ $variant['stock_quantity'] }}">
                                </div>

                                <!-- Variant Dimensions -->
                                <div class="input-group mt-2">
                                    <label for="variant_weight">Weight (kg)</label>
                                    <input type="number" name="variants[{{ $index }}][weight]" step="0.01" value="{{ $variant['weight'] }}">
                                </div>
                                <div class="input-group mt-2">
                                    <label for="variant_length">Length (cm)</label>
                                    <input type="number" name="variants[{{ $index }}][length]" step="0.01" value="{{ $variant['length'] }}">
                                </div>
                                <div class="input-group mt-2">
                                    <label for="variant_width">Width (cm)</label>
                                    <input type="number" name="variants[{{ $index }}][width]" step="0.01" value="{{ $variant['width'] }}">
                                </div>
                                <div class="input-group mt-2">
                                    <label for="variant_height">Height (cm)</label>
                                    <input type="number" name="variants[{{ $index }}][height]" step="0.01" value="{{ $variant['height'] }}">
                                </div>

                                <!-- Variant Expiration Date -->
                                <div class="input-group mt-2">
                                    <label for="variant_exp_date">Expiration Date</label>
                                    <input type="date" name="variants[{{ $index }}][exp_date]" value="{{ $variant['exp_date'] }}">
                                </div>

                                <!-- Variant Image -->
                                <div class="input-group mt-2">
                                    <label for="variant_image">Variant Image</label>
                                    <input type="file" name="variants[{{ $index }}][variant_image]" accept="image/*">
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            {{-- <div id="measurements-container">
                <h3>Measurements</h3>
                <div class="input-group">
                    <label for="weight">Weight (g)</label>
                    <input type="number" id="weight" name="weight" value="" min="0" max="2147483647" >
                </div>
                <div class="input-group">
                    <label for="stock">Length (cm)</label>
                    <input type="number" id="length" name="length" value="" min="0" max="2147483647" >
                </div>
                <div class="input-group">
                    <label for="stock">Width (cm)</label>
                    <input type="number" id="width" name="width" value="" min="0" max="2147483647" >
                </div>
                <div class="input-group">
                    <label for="stock">Height (cm)</label>
                    <input type="number" id="height" name="height" value="" min="0" max="2147483647" >
                </div>
            </div> --}}
            <div  style="text-align:center;"> 
            <button type="submit" class="submit-button" style="margin-top:15px;">Add Product</button>
            <a href="{{ route('admin.products.list') }}" class="btn btn-primary" style="margin-left:10px; background-color:yellow;" >Cancel</a>
            </div>
        </form>
    </div>

    <script src="{{ asset('assets/js/product.js') }}"></script>
@endsection
