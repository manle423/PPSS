@extends('layouts.admin')

@section('content')
<link href="{{ asset('assets/vendor/css/shop-info.css') }}" rel="stylesheet">

<div class="container mt-5">
    <h2>Shop information</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.shop-info') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group mb-3">
            <label>Shop name</label>
            <input type="text" name="name" class="form-control" value="{{ $storeInfo->name}}" required>
        </div>

        <div class="form-group mb-3">
            <label>Current logo</label><br>
            @if(isset($storeInfo['logo']))
                <img src="{{ asset('storage/' . $storeInfo->logo) }}"" width="150">
            @else
                <p>None</p>
            @endif
        </div>

        <div class="form-group mb-3">
            <label>New logo</label>
            <input type="file" name="logo" class="form-control-file">
        </div>

        <div class="form-group mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control">{{ $storeInfo->description }}</textarea>
        </div>

        <div class="form-group mb-3">
            <label>Product Categories</label>
            <textarea name="product_category" class="form-control">{{ $storeInfo->product_category }}</textarea>
        </div>

        <div class="form-group mb-3">
            <label>Trusted Brands</label>
            <textarea name="trusted" class="form-control">{{ $storeInfo->trusted }}</textarea>
        </div>

        <div class="form-group mb-3">
            <label>Product Quality</label>
            <textarea name="quality" class="form-control">{{ $storeInfo->quality }}</textarea>
        </div>

        <div class="form-group mb-3">
            <label>Affordable Prices</label>
            <textarea name="price" class="form-control">{{ $storeInfo->price }}</textarea>
        </div>

        <div class="form-group mb-3">
            <label>Fast and Affordable Delivery</label>
            <textarea name="delivery" class="form-control">{{ $storeInfo->delivery }}</textarea>
        </div>

        <div class="form-group mb-3">
            <label>Thanks</label>
            <textarea name="thanks" class="form-control">{{ $storeInfo->thanks }}</textarea>
        </div>

        <div class="form-group mb-3">
            <label>Why people like us</label>
            <textarea name="footer_why_people_like_us" class="form-control">{{ $storeInfo->footer_why_people_like_us }}</textarea>
        </div>

        <div class="form-group mb-3">
            <label>Address</label>
            <input type="text" name="address" class="form-control" value="{{ $storeInfo->address }}">
        </div>

        <div class="form-group mb-3">
            <label>Contact</label>
            <input type="text" name="phone" class="form-control" value="{{ $storeInfo->phone }}">
        </div>

        <div class="form-group mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ $storeInfo->email }}">
        </div>

        <div class="form-group mb-3">
            <label>Team name</label>
            <input type="text" name="team" class="form-control" value="{{ $storeInfo->team ?? 'N/A' }}">
        </div>

        <button type="submit" class="btn btn-primary mt-3">Update</button>
    </form>
</div>
@endsection
