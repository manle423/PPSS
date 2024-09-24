@extends('layouts.shop-page')
@section('content')
<link href="{{ asset('assets/vendor/css/product.css') }}" rel="stylesheet">
<div class="product-table-container">
    <h1>Product List</h1>
    @if (session('success'))
                   <div class="alert alert-success">
                         {{ session('success') }}
                   </div>
                @endif
    <table class="product-table">
        <thead>
            <tr>
                <th>Product Id</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Description</th>
                <th>Price ($)</th>
                <th>Stock Quantity</th>
                <th>Created_at</th>
                <th>Updated_at</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{$product->id}}</td>
                    <td>{{$product->name}}</td>
                    <td>{{$product->category->name}}</td> <!-- Hiển thị tên category -->
                    <td>{{$product->description}}</td>
                    <td>{{$product->price}}</td>
                    <td>{{$product->stock_quantity}}</td>
                    <td>{{$product->created_at}}</td>
                    <td>{{$product->updated_at}}</td>
                    <td> <a href="{{ route('shop.editPro', $product->id) }}">Edit</a> || 
                    <form action="{{ route('shop.deletePro', $product->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="border:none; background:none; color:red; cursor:pointer;">Delete</button>
                   </form></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Thêm phân trang -->
    <div class="pagination">
        {{ $products->links() }} 
    </div>
</div>
@endsection
