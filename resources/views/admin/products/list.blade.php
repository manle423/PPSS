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
        <div class="table-controls">
        </div>
        <table class="product-table">
            <thead>
                <tr>
                    <th>Product Id</th>
                    <th>Product Name</th>
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
                        <td>{{ $product->category->name }}</td> <!-- Hiển thị tên category -->
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
