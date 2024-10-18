@extends('layouts.admin')

@section('content')
<link href="{{ asset('assets/vendor/css/customer.css') }}" rel="stylesheet">

<div class="customer-container">
    <h2>Customer Detail</h2>

    {{-- Hiển thị thông báo thành công nếu có --}}
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form class="customer-form">
        <div class="form-row">
            <div class="form-group">
                <label for="id">ID:</label>
                <input type="text" id="id" name="id" readonly value="{{ $user->id }}">
            </div>
            <div class="form-group">
                <label for="user_name">User Name:</label>
                <input type="text" id="user_name" name="user_name" value="{{ $user->user_name }}">
            </div>
            <div class="form-group">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" value="{{ $user->full_name }}">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="{{ $user->email }}">
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number:</label>
                <input type="tel" id="phone_number" name="phone_number" value="{{ $user->phone_number }}">
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="{{ $user->address }}">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="created_at">Created At:</label>
                <input type="text" id="created_at" name="created_at" readonly value="{{ $user->created_at }}">
            </div>
        </div>
    </form>

    <div class="form-actions">
        <form action="{{ route('admin.password.email') }}" method="POST" style="display:inline;">
            @csrf
            <input type="hidden" name="email" value="{{ $user->email }}">
            <button type="submit" class="btn btn-primary" style="background-color: blue; margin-right: 10px;">
                Send Reset Link
            </button>
        </form>

        <a href="{{ route('admin.customers.list') }}" class="btn btn-primary" style="background-color: yellow; margin-right: 10px;">
            Cancel
        </a>

        <a href="{{ route('admin.customers.orders', $user->id) }}">
            <button type="button" class="btn btn-primary" style="margin-left: 10px; background-color: green;">
                Customer Orders
            </button>
        </a>
    </div>
</div>
@endsection
