@extends('layouts.admin')
@section('content')
    <link href="{{ asset('assets/vendor/css/product.css') }}" rel="stylesheet">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Product List</h2>

            <div>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Add Product</a>
                <div class="custom-dropdown">
                    <button class="btn btn-secondary">More Actions</button>
                    <div class="custom-dropdown-content">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#importModal">Import Products</a>
                        <a href="#" id="bulkDeleteBtn">Bulk Delete</a>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#bulkDiscountModal">Bulk Discount</a>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- Filter Form -->
        <form action="{{ route('admin.products.filter') }}" method="GET" id="filterForm" class="mb-4">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ request('name') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-control" id="category" name="category">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="price_min" class="form-label">Min Price</label>
                    <input type="number" class="form-control" id="price_min" name="price_min"
                        value="{{ request('price_min') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="price_max" class="form-label">Max Price</label>
                    <input type="number" class="form-control" id="price_max" name="price_max"
                        value="{{ request('price_max') }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="stock_min" class="form-label">Min Stock</label>
                    <input type="number" class="form-control" id="stock_min" name="stock_min"
                        value="{{ request('stock_min') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="stock_max" class="form-label">Max Stock</label>
                    <input type="number" class="form-control" id="stock_max" name="stock_max"
                        value="{{ request('stock_max') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="created_at_start" class="form-label">Created From</label>
                    <input type="date" class="form-control" id="created_at_start" name="created_at_start"
                        value="{{ request('created_at_start') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="created_at_end" class="form-label">Created To</label>
                    <input type="date" class="form-control" id="created_at_end" name="created_at_end"
                        value="{{ request('created_at_end') }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.products.list') }}" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>

        <!-- Bulk Discount Modal -->
        <div class="modal fade" id="bulkDiscountModal" tabindex="-1" aria-labelledby="bulkDiscountModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bulkDiscountModalLabel">Apply Bulk Discount</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.products.bulk-action') }}" method="POST" id="bulkDiscountForm">
                            @csrf
                            <div class="mb-3">
                                <label for="discount_percentage" class="form-label">Discount Percentage (%)</label>
                                <input type="number" class="form-control" id="discount_percentage"
                                    name="discount_percentage" min="0" max="100" step="0.01" required>
                                <small class="form-text text-muted">Enter a value between 0 and 100. For example, enter 50 for a 50% discount.</small>
                            </div>
                            <button type="submit" class="btn btn-primary">Apply Discount</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Import Modal -->
        <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Products</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.products.import') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="file" class="form-label">Choose Excel File</label>
                                <input type="file" name="file" class="form-control" id="file" required>
                            </div>
                            <div class="mb-3">
                                <a href="{{ route('admin.products.export.template') }}"
                                    class="btn btn-secondary">Download Template</a>
                            </div>
                            <div class="alert alert-info">
                                <h6>Import Instructions:</h6>
                                <ul>
                                    <li>Each row should represent either a product or a variant.</li>
                                    <li>For main products, fill all columns except 'variant_name' and 'exp_date'.</li>
                                    <li>For variants, 'product_name' must match an existing product.</li>
                                    <li>Variants require 'variant_name', 'price', 'stock_quantity', and other relevant
                                        fields.</li>
                                    <li>Ensure 'category' exists for main products.</li>
                                    <li>Price should be in Vietnamese Dong (VND).</li>
                                    <li>Weight should be in grams (g).</li>
                                    <li>Length, width, and height should be in centimeters (cm).</li>
                                    <li>Date format for 'exp_date' should be YYYY-MM-DD.</li>
                                    <li>Numeric fields (price, stock_quantity, weight, etc.) should not contain non-numeric
                                        characters.</li>
                                </ul>
                            </div>
                            <button type="submit" class="btn btn-primary">Import</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.products.bulk-action') }}" method="POST" id="bulkActionForm">
            @csrf
            <input type="hidden" name="action" id="bulkAction">
            <table class="product-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>Product Name</th>
                        <th>Image</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Price ($)</th>
                        <th>Stock Quantity</th>
                        <th>Created at</th>
                        <th>Updated at</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td><input type="checkbox" name="product_ids[]" value="{{ $product->id }}"
                                    class="product-checkbox"></td>
                            <td>{{ $product->name }}</td>
                            <td><img src="{{ $product->image }}" alt="{{ $product->name }}"
                                    style="width: 100px; height: auto;"></td>
                            <td>{{ $product->category->name }}</td>
                            <td>{{ Str::limit($product->description, 100) }}</td>
                            <td>{{ $product->price }}</td>
                            <td>{{ $product->stock_quantity }}</td>
                            <td>{{ $product->created_at }}</td>
                            <td>{{ $product->updated_at }}</td>
                            <td class="d-flex">
                                <a href="{{ route('admin.products.edit', $product->id) }}"
                                    class="btn btn-sm btn-primary mr-2"><i class="fas fa-edit"></i> Edit</a>
                                <button type="button" class="btn btn-sm btn-danger delete-product"
                                    data-id="{{ $product->id }}"><i class="fas fa-trash"></i> Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
        <div class="table-info">
            <span>Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }}
                entries</span>
        </div>
        <div class="pagination">
            {{ $products->links() }}
        </div>
    </div>

    <script>
        document.getElementById('selectAll').addEventListener('change', function() {
            var checkboxes = document.getElementsByClassName('product-checkbox');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        });

        document.getElementById('bulkDeleteBtn').addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete the selected products?')) {
                document.getElementById('bulkAction').value = 'delete';
                document.getElementById('bulkActionForm').submit();
            }
        });

        document.querySelectorAll('.delete-product').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this product?')) {
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('admin.products.delete', '') }}/' + this.dataset.id;
                    form.innerHTML = '@csrf @method('POST')';
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        // Thêm event listener cho form bulk discount
        document.getElementById('bulkDiscountForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var discountPercentage = document.getElementById('discount_percentage').value;
            var form = document.getElementById('bulkActionForm');
            form.action = "{{ route('admin.products.bulk-action') }}";
            document.getElementById('bulkAction').value = 'discount';
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'discount_percentage';
            input.value = discountPercentage;
            form.appendChild(input);
            form.submit();
        });

        // Add this to initialize dropdowns
        document.addEventListener('DOMContentLoaded', function() {
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
            var dropdownList = dropdownElementList.map(function(dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl)
            })
        });
    </script>
@endsection
