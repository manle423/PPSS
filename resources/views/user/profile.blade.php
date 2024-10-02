@extends('layouts.shop')

@section('content')
<link href="{{ asset('assets/vendor/css/profile.css') }}" rel="stylesheet">
    <div class="container-fluid py-5">
        <div class="row-profile">

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
            
            <div class="col-md-12">
                <h1 style="margin-top:60px; text-align:center;">Profile</h1>
                <div class="user-info-view">
                    <p><strong>Full Name:</strong> {{ $user->full_name ?? 'N/A' }}</p>
                    <p><strong>Username:</strong> {{ $user->username ?? 'N/A' }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Phone Number:</strong> {{ $user->phone_number ?? 'N/A' }}</p>
                    <p><strong>Address:</strong> {{ $user->address ?? 'N/A' }}</p>
                    <button class="btn btn-secondary btn-edit-user-info">Edit</button>
                </div>
                <div class="user-info-edit" style="display: none;">
                    <form action="{{ route('user.update-info') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="full_name"  style="font-weight: bold;">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="{{ $user->full_name }}" required>
                        </div>
                        <div class="form-group">
                            <label for="username" style="font-weight: bold;">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="{{ $user->username }}" required>
                        </div>
                        <div class="form-group" style="font-weight: bold;">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" disabled>
                        </div>
                        <div class="form-group" style="font-weight: bold;">
                            <label for="phone_number">Phone Number</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ $user->phone_number }}" required>
                        </div>
                        <div class="form-group" style="font-weight: bold;">
                            <label for="address">Address</label>
                            <input type="text" class="form-control" id="address" name="address" value="{{ $user->address }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary" style="margin-top:10px">Save</button>
                        <button type="button" class="btn btn-secondary btn-cancel-edit-user-info" style="margin-top:10px">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button id="show-address-form" class="btn btn-primary" style="margin-top:10px; margin-bottom:10px;">Add New Address</button>
                <div id="address-form-container" style="display: none;">
                    <x-profile.address-form :provinces="$provinces" />
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12">
                <h2>Addresses</h2>
                @if ($addresses->isEmpty())
                    <p>N/A</p>
                @else
                    @foreach ($addresses as $address)
                        <x-profile.address-card :address="$address" :provinces="$provinces" />
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    <script>
        var addAddressRoute = "{{ route('user.add-address') }}";
    </script>
    <script src="{{ asset('assets/js/profile.js') }}"></script>
@endsection