@extends('layouts.admin')

@section('content')

<link href="{{ asset('assets/vendor/css/customer.css') }}" rel="stylesheet">
<div class="customer-container">
    <h2>Customer detail</h2>
    <form class="customer-form">
        <div class="form-row">
            <div class="form-group">
                <label for="id">ID:</label>
                <input type="text" id="id" name="id" readonly value="{{$user->id}}">
            </div>
            <div class="form-group">
                <label for="user_name">User name:</label>
                <input type="text" id="user_name" name="user_name"  value="{{$user->user_name}}">
            </div>
            <div class="form-group">
                <label for="full_name">Full name:</label>
                <input type="text" id="full_name" name="full_name"   value="{{$user->full_name}}">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email"   value="{{$user->email}}">
            </div>
            <div class="form-group">
                <label for="phone_number">Phone number:</label>
                <input type="tel" id="phone_number" name="phone_number"  value="{{$user->phone_number}}">
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address"  value="{{$user->address}}">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="created_at">Created at:</label>
                <input type="text" id="created_at" name="created_at" readonly value="{{$user->created_at}}">
            </div>
        </div>

        <div class="form-actions">
        <a href="{{ route('admin.customers.list') }}" class="btn btn-primary" style="background-color: yellow; margin-right: 10px;">
         Cancel
        </a>

      
           <a href="{{ route('admin.customers.orders', $user->id) }}"><button type="button" class="btn btn-primary" style="margin-left:10px; background-color:green">Customer orders</button></a> 
        </div>
    </form>
</div>
@endsection
