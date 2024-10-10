@extends('layouts.shop')
@section('content')
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Shop</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <!-- <li class="breadcrumb-item"><a href="#">Home</a></li>
                                                <li class="breadcrumb-item"><a href="#">Pages</a></li>
                                                <li class="breadcrumb-item active text-white">Shop</li> -->
        </ol>
    </div>
    <!-- Single Page Header End -->


    <!-- Fruits Shop Start-->
    <div class="container-fluid fruite py-5">
        <div class="container py-5">
            <form class="row g-4" action="{{ route('product.index') }}" method="GET">
                @csrf
                <div class="col-lg-12">
                    <div class="mb-3 row g-4" style="display: flex;justify-content:space-between;">
                        <div class="col-xl-3">
                            <div class="input-group w-100 mx-auto d-flex">
                                <input type="search" name="search" class="form-control p-3"
                                    placeholder="Search products..." aria-describedby="search-icon-1"
                                    value="{{ request('search') }}">
                                <button type="submit" id="search-icon-1" class="input-group-text p-3"><i
                                        class="fa fa-search"></i></button>
                            </div>
                        </div>
                        <div class="col-xl-3" style="width:fit-content;">
                            <div class="bg-light ps-3 py-3 rounded d-flex justify-content-between mb-4">
                                <form id="sortForm" action="{{ route('product.index') }}" method="GET"
                                    style="margin-left:auto;width:150px;">
                                    @csrf
                                    <label for="sort">Sort by:</label>
                                    <select id="sort" name="sort" class="border-0 form-select-sm bg-light me-3"
                                        onchange="this.form.submit()">
                                        <option value="none" {{ request('sort') == 'none' ? 'selected' : '' }}>None
                                        </option>
                                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Price
                                            Increasing</option>
                                        <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Price
                                            Decreasing</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-lg-3">
                            <div class="row g-4">
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <h4>Categories</h4>
                                        <ul class="list-unstyled fruite-categorie">
                                            @foreach ($categories as $category)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="categories[]"
                                                        value="{{ $category->id }}" id="category{{ $category->id }}"
                                                        {{ is_array(request('categories')) && in_array($category->id, request('categories')) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="category{{ $category->id }}">
                                                        {{ $category->name }} ({{ $category->products->count() }})
                                                    </label>
                                                </div>
                                            @endforeach
                                        </ul>
                                        <!-- Additional hidden inputs to maintain category selection -->
                                        @if (count(request('categories', [])) > 0)
                                            @foreach (request('categories') as $selectedCategory)
                                                <input type="hidden" name="categories[]" value="{{ $selectedCategory }}">
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <h4 class="mb-2">Price Range</h4>
                                        <label for="minPrice">Min Price:</label>
                                        <input type="number" class="form-control" id="minPrice" name="min_price"
                                            min="0" max="500" value="{{ request('min_price') ?? 0 }}">
                                        <label for="maxPrice">Max Price:</label>
                                        <input type="number" class="form-control" id="maxPrice" name="max_price"
                                            min="0" max="500" value="{{ request('max_price') ?? 500 }}">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <h4>Additional</h4>

                                        <div class="mb-2">
                                            <input type="radio" class="me-2" id="Categories-3" name="Categories-1"
                                                value="Beverages">
                                            <label for="Categories-3"> Sales</label>
                                        </div>
                                        <div class="mb-2">
                                            <input type="radio" class="me-2" id="Categories-4" name="Categories-1"
                                                value="Beverages">
                                            <label for="Categories-4"> Discount</label>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="position-relative">
                                        <img src="{{ asset('assets/vendor/img/banner-dog.png') }}"
                                            class="img-fluid w-100 rounded" alt="">
                                        <div class="position-absolute"
                                            style="top: 50%; right: 10px; transform: translateY(-50%);">
                                            <h3 class="text-secondary fw-bold">Happy <br> Dog <br> Banner</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <!-- Product List -->
                            <div class="row g-4 justify-content-center">
                                @foreach ($products as $product)
                                    <div class="col-md-6 col-lg-6 col-xl-4">
                                        <div class="rounded position-relative fruite-item">
                                            <div class="fruite-img">
                                                <img src="{{ $product->image != null ? $product->image :asset('assets/vendor/img/food-item.jpg') }}"
                                                    class="img-fluid w-100 rounded-top" alt="">
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                style="top: 10px; left: 10px;">{{ $product->category->name }}</div>
                                            <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                <h4><a
                                                        href="{{ route('product.show', $product) }}">{{ $product->name }}</a>
                                                </h4>
                                                <p>{{ Str::words($product->description, 10) }}</p>
                                                <div class="d-flex justify-content-between flex-lg-wrap">
                                                    @if ($product->variants->count() == 0)
                                                        <p class="text-dark fs-5 fw-bold mb-0">{{ $product->price }} đ</p>
                                                    @elseif ($product->variants->count() == 1)
                                                        <p class="text-dark fs-5 fw-bold mb-0">{{ $product->variants[0]->variant_price }} đ</p>
                                                    {{--Show price in format (lowest variant price) - (highest variant price)--}}
                                                    @else
                                                    <p class="text-dark fs-5 fw-bold mb-0">{{ $product->variants->min('variant_price') }} -
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



                                <div class="col-12">
                                    <div class="pagination d-flex justify-content-center mt-5">
                                        {{ $products->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Fruits Shop End-->

    {{-- Script for updating the price input --}}
    <script>
        document.getElementById('rangeInput').addEventListener('input', function() {
            document.getElementById('minPrice').value = 0;
            document.getElementById('maxPrice').value = this.value;
        });
    </script>
@endsection
