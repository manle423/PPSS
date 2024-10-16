@extends('layouts.admin')
@section('content')
<link href="{{ asset('assets/vendor/css/find.css') }}" rel="stylesheet">

<div class="container">
    <h2 class="text-center">You searched for: "{{ ucfirst($type) }}"</h2>

    <div class="popup" style="text-align: center; margin-top: 50px;">
        <p>Where do you want to go?</p>

        <div class="button-group">
            @if ($type == 'category')
                <a href="{{ route('admin.category.list') }}" class="btn btn-primary">Category List</a>
                <a href="{{ route('admin.category.create') }}" class="btn btn-success">Add Category</a>
            @elseif ($type == 'product')
                <a href="{{ route('admin.products.list') }}" class="btn btn-primary">Product List</a>
                <a href="{{ route('admin.products.create') }}" class="btn btn-success">Add Product</a>
            @elseif ($type == 'customer')
                <a href="{{ route('admin.customers.list') }}" class="btn btn-primary">Customer List</a>
                <a href="{{ route('admin.customers.create') }}" class="btn btn-success">Add Customer</a>
            @elseif ($type == 'order')
                <a href="{{ route('admin.order.list') }}" class="btn btn-primary">Order List</a>
                <a href="{{ route('admin.order.create') }}" class="btn btn-success">Add Order</a>
            @endif
        </div>
    </div>
</div>
@endsection
