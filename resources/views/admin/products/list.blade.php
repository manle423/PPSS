@extends('layouts.admin')
@section('content')
<link href="{{ asset('assets/vendor/css/product.css') }}" rel="stylesheet">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Product List</h2>
          
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Add Product</a>
        </div>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <a><button type="button"class="submit-button" style="background-color:royalblue"data-bs-toggle="modal" data-bs-target="#filterModal">Filter</button></a>
        <!-- Modal -->
       <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
       <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter products</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="filterForm" action="{{route('admin.products.filter')}}" method="POST">
                  @csrf
                <!-- Tiêu chí lọc tên hàng -->
                    <div class="mb-3">
                        <label for="productName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="productName" name="name" placeholder="Product name">
                    </div>
                    <!-- Tiêu chí lọc đơn giá -->
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" class="form-control" id="price" name="price" step="0.01" placeholder="Price">
                    </div>
                    <!-- Tiêu chí lọc số lượng -->
                    <div class="mb-3">
                        <label for="stock" class="form-label">Stock quantity</label>
                        <input type="number" class="form-control" id="stock" name="stock_quantity" placeholder="Quantity">
                    </div>
                 
                   
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="filterForm" class="btn btn-secondary">Filter</button>
            </div>
        </div>
    </div>
</div>

        <div class="table-controls">
        </div>
        <table class="product-table">
            <thead>
                <tr>
                    <th>Product Id</th>
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
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->name }}</td>
                        <td><img src="{{ asset('img/products/' . $product->image) }}"></td>
                        <td>{{ $product->category->name }}</td> 
                        <td>{{ $product->description }}</td>
                        <td>{{ $product->price }}</td>
                        <td>{{ $product->stock_quantity }}</td>
                        <td>{{ $product->created_at }}</td>
                        <td>{{ $product->updated_at }}</td>
                        <td class="d-flex">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm  mr-2"><i class="fas fa-edit"></i> Edit</a>
                            <form action="{{ route('admin.products.delete', $product->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                @csrf
                                @method('POST')
                                <button type="submit" class="btn btn-sm "><i class="fas fa-trash"></i> Delete</button>
                            </form>
                           
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="table-info">
            <span>Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} entries</span>
        </div>
        <div class="pagination">
            {{ $products->links() }}
        </div>
    </div>
@endsection
