@extends('layouts.shop')
@section('content')
<link href="{{ asset('assets/vendor/css/aboutus.css') }}" rel="stylesheet">

    <!-- About us Page Start -->
    <header>
        <h1>About Us</h1>
    </header>

    <section class="about-us">
        <h2>Welcome to Our Pet Store</h2>
        <p>{{ $info->description}}</p>
        
        <h3>Product Categories</h3>
        <p>{{$info->product_category}}</p>
        <h3>Trusted Brands</h3>
        <p>{{$info->trusted}}</p>
        <h3>Product Quality</h3>
       <p>{{$info->quality}}</p>
        <h3>Affordable Prices</h3>
        <p>{{$info->price}}</p>
        <h3>Fast and Affordable Delivery</h3>
        <p>{{$info->delivery}}</p>
       
       <p>{{$info->thanks}}</p>
    </section>


    <!-- About us Page End -->
@endsection()
