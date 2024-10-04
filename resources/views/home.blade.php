{{-- trang chủ website --}}
@extends('layouts.shop')
@section('content')
    <!-- Featurs Section Start -->
    <div class="container-fluid py-5 mb-5 hero-header">
    </div>
    <div class="container-fluid fruite py-5">
        <div class="container py-4">
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="featurs-item text-center rounded bg-light p-4">

                        <i class="fas fa-car-side fa-3x" style="color: #81c408;"></i>

                        <div class="featurs-content text-center">
                            <h5 class="title-text">Free ship</h5>
                            <p class="mb-0">Orders over 500,000 VND</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="featurs-item text-center rounded bg-light p-4">

                        <i class="fas fa-user-shield fa-3x" style="color: #81c408;""></i>

                        <div class="featurs-content text-center">
                            <h5 class="title-text">Secure Payment</h5>
                            <p class="mb-0">100% Safe Guarantee</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="featurs-item text-center rounded bg-light p-4">

                        <i class="fas fa-exchange-alt fa-3x"style="color: #81c408;"></i>

                        <div class="featurs-content text-center">
                            <h5 class="title-text">Refund</h5>
                            <p class="mb-0">7 Days Money Back Guarantee </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="featurs-item text-center rounded bg-light p-4">

                        <i class="fa fa-phone-alt fa-3x" style="color: #81c408;"></i>


                        <div class="featurs-content text-center">
                            <h5 class="title-text">Support</h5>
                            <p class="mb-0">24/7 customer support</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Featurs Section End -->


    <!-- Fruits Shop Start-->
    <div class="container-fluid fruite py-5">
        {{-- Latest Products Section --}}
        <div class="container py-5">
            <div class="tab-class text-center">

                <div class="row g-4">
                    <div class="col-lg-4 text-start">
                        <h1>Our latest products</h1>
                    </div>
                    {{-- Latest Products Categories--}}
                    <div class="col-lg-8 text-end">
                        <ul class="nav nav-pills d-inline-flex text-center mb-5">
                            <li class="nav-item">
                                <a class="d-flex m-2 py-2 bg-light rounded-pill active" data-bs-toggle="pill"
                                    href="#tab-1">
                                    <span class="text-dark" style="width: 130px;text-align:center">All Products</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="d-flex py-2 m-2 bg-light rounded-pill" data-bs-toggle="pill" href="#tab-2">
                                    <span class="text-dark" style="width: 130px;text-align:center">{{$categories[0]->name}}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="d-flex m-2 py-2 bg-light rounded-pill" data-bs-toggle="pill" href="#tab-3">
                                    <span class="text-dark" style="width: 130px; text-align:center">{{$categories[1]->name}}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="d-flex m-2 py-2 bg-light rounded-pill" data-bs-toggle="pill" href="#tab-4">
                                    <span class="text-dark" style="width: 130px;text-align:center">{{$categories[2]->name}}</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>
                {{-- Latest Products Content (stored in multiple tabs)--}}
                <div class="tab-content">
                    <div id="tab-1" class="tab-pane fade show p-0 active">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="row g-4">
                                    @foreach ($latestProductsAll as $product)
                                        <div class="col-md-6 col-lg-4 col-xl-3">
                                            <div class="rounded position-relative fruite-item">
                                                <div class="fruite-img">
                                                    <img src="{{ asset('assets/vendor/img/toys.jpg') }}"
                                                        class="img-fluid w-100 rounded-top" alt="">
                                                </div>
                                                <div
                                                    class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                    style="top: 10px; left: 10px;">
                                                    {{ $product->category->name }}</div>
                                                <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                    <h4><a  href="{{ route('product.show', $product) }}">{{ $product->name }}</a></h4>
                                                    <p>{{ $product->description }}</p>
                                                    <div class="d-flex justify-content-between flex-lg-wrap">
                                                        @if ($product->variants->count() == 0)
                                                            <p class="text-dark fs-5 fw-bold mb-0">{{ $product->price }} đ
                                                            </p>
                                                        @elseif ($product->variants->count() == 1)
                                                            <p class="text-dark fs-5 fw-bold mb-0">
                                                                {{ $product->variants[0]->variant_price }} đ</p>
                                                            {{-- Show price in format (lowest variant price) - (highest variant price) --}}
                                                        @else
                                                            <p class="text-dark fs-5 fw-bold mb-0">
                                                                {{ $product->variants->min('variant_price') }} -
                                                                {{ $product->variants->max('variant_price') }} đ</p>
                                                        @endif
                                                        <a href="{{ route('product.show', $product) }}"
                                                            class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                                                class="fa fa-shopping-bag me-2 text-primary"></i> Add to
                                                            cart</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="tab-2" class="tab-pane fade show p-0 inactive">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="row g-4">
                                    @foreach ($latestProductsCategories[0] as $product)
                                        <div class="col-md-6 col-lg-4 col-xl-3">
                                            <div class="rounded position-relative fruite-item">
                                                <div class="fruite-img">
                                                    <img src="{{ asset('assets/vendor/img/toys.jpg') }}"
                                                        class="img-fluid w-100 rounded-top" alt="">
                                                </div>
                                                <div
                                                    class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                    style="top: 10px; left: 10px;">
                                                    {{ $product->category->name }}</div>
                                                <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                    <h4><a  href="{{ route('product.show', $product) }}">{{ $product->name }}</a></h4>
                                                    <p>{{ $product->description }}</p>
                                                    <div class="d-flex justify-content-between flex-lg-wrap">
                                                        @if ($product->variants->count() == 0)
                                                            <p class="text-dark fs-5 fw-bold mb-0">{{ $product->price }} đ
                                                            </p>
                                                        @elseif ($product->variants->count() == 1)
                                                            <p class="text-dark fs-5 fw-bold mb-0">
                                                                {{ $product->variants[0]->variant_price }} đ</p>
                                                            {{-- Show price in format (lowest variant price) - (highest variant price) --}}
                                                        @else
                                                            <p class="text-dark fs-5 fw-bold mb-0">
                                                                {{ $product->variants->min('variant_price') }} -
                                                                {{ $product->variants->max('variant_price') }} đ</p>
                                                        @endif
                                                        <a href="{{ route('product.show', $product) }}"
                                                            class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                                                class="fa fa-shopping-bag me-2 text-primary"></i> Add to
                                                            cart</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="tab-3" class="tab-pane fade show p-0 inactive">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="row g-4">
                                    @foreach ($latestProductsCategories[1] as $product)
                                        <div class="col-md-6 col-lg-4 col-xl-3">
                                            <div class="rounded position-relative fruite-item">
                                                <div class="fruite-img">
                                                    <img src="{{ asset('assets/vendor/img/toys.jpg') }}"
                                                        class="img-fluid w-100 rounded-top" alt="">
                                                </div>
                                                <div
                                                    class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                    style="top: 10px; left: 10px;">
                                                    {{ $product->category->name }}</div>
                                                <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                    <h4><a  href="{{ route('product.show', $product) }}">{{ $product->name }}</a></h4>
                                                    <p>{{ $product->description }}</p>
                                                    <div class="d-flex justify-content-between flex-lg-wrap">
                                                        @if ($product->variants->count() == 0)
                                                            <p class="text-dark fs-5 fw-bold mb-0">{{ $product->price }} đ
                                                            </p>
                                                        @elseif ($product->variants->count() == 1)
                                                            <p class="text-dark fs-5 fw-bold mb-0">
                                                                {{ $product->variants[0]->variant_price }} đ</p>
                                                            {{-- Show price in format (lowest variant price) - (highest variant price) --}}
                                                        @else
                                                            <p class="text-dark fs-5 fw-bold mb-0">
                                                                {{ $product->variants->min('variant_price') }} -
                                                                {{ $product->variants->max('variant_price') }} đ</p>
                                                        @endif
                                                        <a href="{{ route('product.show', $product) }}"
                                                            class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                                                class="fa fa-shopping-bag me-2 text-primary"></i> Add to
                                                            cart</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="tab-4" class="tab-pane fade show p-0 inactive">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="row g-4">
                                    @foreach ($latestProductsCategories[2] as $product)
                                        <div class="col-md-6 col-lg-4 col-xl-3">
                                            <div class="rounded position-relative fruite-item">
                                                <div class="fruite-img">
                                                    <img src="{{ asset('assets/vendor/img/toys.jpg') }}"
                                                        class="img-fluid w-100 rounded-top" alt="">
                                                </div>
                                                <div
                                                    class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                    style="top: 10px; left: 10px;">
                                                    {{ $product->category->name }}</div>
                                                <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                    <h4><a  href="{{ route('product.show', $product) }}">{{ $product->name }}</a></h4>
                                                    <p>{{ $product->description }}</p>
                                                    <div class="d-flex justify-content-between flex-lg-wrap">
                                                        @if ($product->variants->count() == 0)
                                                            <p class="text-dark fs-5 fw-bold mb-0">{{ $product->price }} đ
                                                            </p>
                                                        @elseif ($product->variants->count() == 1)
                                                            <p class="text-dark fs-5 fw-bold mb-0">
                                                                {{ $product->variants[0]->variant_price }} đ</p>
                                                            {{-- Show price in format (lowest variant price) - (highest variant price) --}}
                                                        @else
                                                            <p class="text-dark fs-5 fw-bold mb-0">
                                                                {{ $product->variants->min('variant_price') }} -
                                                                {{ $product->variants->max('variant_price') }} đ</p>
                                                        @endif
                                                        <a href="{{ route('product.show', $product) }}"
                                                            class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                                                class="fa fa-shopping-bag me-2 text-primary"></i> Add to
                                                            cart</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- Fact Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="bg-light p-5 rounded">
                <div class="row g-4 justify-content-center">
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter bg-white rounded p-5">
                            <i class="fa fa-users text-secondary"></i>
                            <h4>satisfied customers</h4>
                            <h1>1963</h1>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter bg-white rounded p-5">
                            <i class="fa fa-users text-secondary"></i>
                            <h4>quality of service</h4>
                            <h1>99%</h1>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter bg-white rounded p-5">
                            <i class="fa fa-users text-secondary"></i>
                            <h4>quality certificates</h4>
                            <h1>33</h1>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Fact Start -->


    <!-- Tastimonial End -->
@endsection
