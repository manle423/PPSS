@extends('layouts.admin')

@section('content')
<link href="{{ asset('assets/vendor/css/shop-info.css') }}" rel="stylesheet">
<div class="shop-information mt-5">
    <h2 class="text-center">Shop Information</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="shop-card">
        <div class="card-body">
            <h5 class="card-title">Shop Name</h5>
            <p class="card-text">{{ $storeInfo->name }}</p>
       <div>
            <h5 class="card-title">Description</h5>
            <p class="card-text">{{ $storeInfo->description }}</p>
            <a href="#" data-bs-toggle="modal" data-bs-target="#shopDetailsModal">More</a>
        </div>
            <h5 class="card-title">Why People Like Us</h5>
            <p class="card-text">{{ $storeInfo->footer_why_people_like_us }}</p>

            <h5 class="card-title">Address</h5>
            <p class="card-text">{{ $storeInfo->address }}</p>

            <h5 class="card-title">Contact</h5>
            <p class="card-text">{{ $storeInfo->phone }}</p>

            <h5 class="card-title">Email</h5>
            <p class="card-text">{{ $storeInfo->email }}</p>
            <h5 class="card-title">Team name</h5>
            <p class="card-text">{{ $storeInfo->team ?? 'N/A'  }}</p>
            <a href="{{ route('admin.dashboard') }}" class="return-button">Back to Dashboard</a>
        </div>
    </div>
</div>

<!-- Modal Structure -->
<div class="modal fade" id="shopDetailsModal" tabindex="-1" aria-labelledby="shopDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shopDetailsModalLabel">Shop Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5 class="card-title">Product Category</h5>
                <p class="card-text">{{ $storeInfo['product_category'] }}</p>

                <h5 class="card-title">Trusted Brands</h5>
                <p class="card-text">{{ $storeInfo['trusted'] }}</p>

                <h5 class="card-title">Quality</h5>
                <p class="card-text">{{ $storeInfo['quality'] }}</p>

                <h5 class="card-title">Pricing</h5>
                <p class="card-text">{{ $storeInfo['price'] }}</p>

                <h5 class="card-title">Delivery</h5>
                <p class="card-text">{{ $storeInfo['delivery'] }}</p>

                <h5 class="card-title">Thanks Message</h5>
                <p class="card-text">{{ $storeInfo['thanks'] }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection