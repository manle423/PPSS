
@extends('layouts.shop')
@section('content')
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Shop Detail</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Shop Detail</li>
        </ol>
    </div>
    <!-- Single Page Header End -->


    <!-- Single Product Start -->
    <div class="container-fluid py-5 mt-5">
        <div class="container py-5">
            <div class="row g-4 mb-5">
                {{-- Search bar--}}
                <div class="col-lg-4 col-xl-3">
                    <form class="row g-4" action="{{ route('product.index') }}" method="GET">
                        @csrf
                        <div class="col-lg-12">
                            <div class="mb-3 row g-4" style="display: flex;justify-content:space-between;">
                                <div class="input-group w-100 mx-auto d-flex">
                                    <input type="search" name="search" class="form-control p-3"
                                        placeholder="Search products..." aria-describedby="search-icon-1"
                                        value="{{ request('search') }}">
                                    <button type="submit" id="search-icon-1" class="input-group-text p-3"><i
                                            class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </div>
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
                                <input type="number" class="form-control" id="minPrice" name="min_price" min="0"
                                    max="500" value="{{ request('min_price') ?? 0 }}">
                                <label for="maxPrice">Max Price:</label>
                                <input type="number" class="form-control" id="maxPrice" name="max_price" min="0"
                                    max="500" value="{{ request('max_price') ?? 500 }}">
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
                                <img src="{{ asset('assets/vendor/img/banner-dog.png') }}" class="img-fluid w-100 rounded"
                                    alt="">
                                <div class="position-absolute" style="top: 50%; right: 10px; transform: translateY(-50%);">
                                    <h3 class="text-secondary fw-bold">Happy <br> Dog <br> Banner</h3>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                {{-- Product Info --}}
                <div class="col-lg-8 col-xl-9">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="border rounded">
                                <a href="#">
                                    <img src="{{ asset('assets/vendor/img/food-item.jpg') }}" class="img-fluid rounded"
                                        alt="Image">
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <h4 class="fw-bold mb-3">{{ $product->name }}</h4>
                            <p class="mb-3">{{ $product->category->name }}</p>
                            <h5 class="fw-bold mb-3"><span id="product-price">{{ $product->price }} đ</span></h5>
                            {{-- <p class="mb-3">Warranty period: 1 year</p> --}}

                            {{-- Product variants --}}
                            @if ($variants && !$variants->isEmpty())
                                <p class="mb-3">Variants:</p>
                                @foreach ($variants as $key => $variant)
                                    <div class="form-check mb-4">
                                        <input type="radio" class="form-check-input" id="variant-{{ $variant->id }}"
                                            name="variant_id_radio" value="{{ $variant->id }}"
                                            data-price="{{ $variant->variant_price }}"
                                            data-stock-quantity="{{ $variant->stock_quantity }}"
                                             {{ $key === 0 ? 'checked' : '' }}>
                                        <label class="form-check-label"
                                            for="variant-{{ $variant->id }}">{{ $variant->variant_name }}
                                             </label>
                                    </div>
                                @endforeach
                            @endif
                            
                            <form action="{{ route('cart.store', $product->id) }}" method="POST"
                                @if ($variants && !$variants->isEmpty()) onsubmit="return validateForm()" @endif>
                                @csrf
                                {{-- Amount to add to cart --}}
                                <div class="form-group mb-4">
                                    <label for="amount">Amount:</label>
                                    <input type="number" id="amount" name="amount" min="1" value="1"
                                        max="{{ $product->stock_quantity }}" required>
                                </div>
                                <p class="mb-4" id="product-stock-quantity">Stock: {{ $product->stock_quantity }}</p>
                                {{-- Hidden input to store selected variant --}}
                                <input type="hidden" id="variant-id" name="variant_id" value="">
                                <div class="btn border border-secondary rounded-pill px-4 py-2 mb-4 text-primary">
                                    <button type="submit" class="fa fa-shopping-bag text-primary"
                                        style="border:none;background:none;">
                                        Add to Cart
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-12">
                            <nav>
                                <div class="nav nav-tabs mb-3">
                                    <button class="nav-link active border-white border-bottom-0" type="button"
                                        role="tab" id="nav-about-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-about" aria-controls="nav-about"
                                        aria-selected="true">Description</button>
                                    {{-- <button class="nav-link border-white border-bottom-0" type="button" role="tab"
                                            id="nav-mission-tab" data-bs-toggle="tab" data-bs-target="#nav-mission"
                                            aria-controls="nav-mission" aria-selected="false">Reviews</button> --}}
                                </div>
                            </nav>
                            <div class="tab-content mb-5">
                                <div class="tab-pane active" id="nav-about" role="tabpanel"
                                    aria-labelledby="nav-about-tab">
                                    {{ $product->description }}
                                </div>
                                {{-- <div class="tab-pane" id="nav-mission" role="tabpanel" aria-labelledby="nav-mission-tab">
                                        <div class="d-flex">
                                            <img src="{{ asset('assets/vendor/img/avatar.jpg')}}" class="img-fluid rounded-circle p-3" style="width: 100px; height: 100px;" alt="">
                                            <div class="">
                                                <p class="mb-2" style="font-size: 14px;">April 12, 2024</p>
                                                <div class="d-flex justify-content-between">
                                                    <h5>Jason Smith</h5>
                                                    <div class="d-flex mb-3">
                                                        <i class="fa fa-star text-secondary"></i>
                                                        <i class="fa fa-star text-secondary"></i>
                                                        <i class="fa fa-star text-secondary"></i>
                                                        <i class="fa fa-star text-secondary"></i>
                                                        <i class="fa fa-star"></i>
                                                    </div>
                                                </div>
                                                <p>The generated Lorem Ipsum is therefore always free from repetition injected humour, or non-characteristic 
                                                    words etc. Susp endisse ultricies nisi vel quam suscipit </p>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <img src="{{ asset('assets/vendor/img/avatar.jpg')}}" class="img-fluid rounded-circle p-3" style="width: 100px; height: 100px;" alt="">
                                            <div class="">
                                                <p class="mb-2" style="font-size: 14px;">April 12, 2024</p>
                                                <div class="d-flex justify-content-between">
                                                    <h5>Sam Peters</h5>
                                                    <div class="d-flex mb-3">
                                                        <i class="fa fa-star text-secondary"></i>
                                                        <i class="fa fa-star text-secondary"></i>
                                                        <i class="fa fa-star text-secondary"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                    </div>
                                                </div>
                                                <p class="text-dark">The generated Lorem Ipsum is therefore always free from repetition injected humour, or non-characteristic 
                                                    words etc. Susp endisse ultricies nisi vel quam suscipit </p>
                                            </div>
                                        </div>
                                    </div> --}}
                                {{-- <div class="tab-pane" id="nav-vision" role="tabpanel">
                                    <p class="text-dark">Tempor erat elitr rebum at clita. Diam dolor diam ipsum et tempor
                                        sit. Aliqu diam
                                        amet diam et eos labore. 3</p>
                                    <p class="mb-0">Diam dolor diam ipsum et tempor sit. Aliqu diam amet diam et eos
                                        labore.
                                        Clita erat ipsum et lorem et sit</p>
                                </div> --}}
                            </div>
                        </div>
                        {{-- <form action="#">
                                <h4 class="mb-5 fw-bold">Leave a Reply</h4>
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <div class="border-bottom rounded">
                                            <input type="text" class="form-control border-0 me-4" placeholder="Yur Name *">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="border-bottom rounded">
                                            <input type="email" class="form-control border-0" placeholder="Your Email *">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="border-bottom rounded my-4">
                                            <textarea name="" id="" class="form-control border-0" cols="30" rows="8" placeholder="Your Review *" spellcheck="false"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="d-flex justify-content-between py-3 mb-5">
                                            <div class="d-flex align-items-center">
                                                <p class="mb-0 me-3">Please rate:</p>
                                                <div class="d-flex align-items-center" style="font-size: 12px;">
                                                    <i class="fa fa-star text-muted"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                </div>
                                            </div>
                                            <a href="#" class="btn border border-secondary text-primary rounded-pill px-4 py-3"> Post Comment</a>
                                        </div>
                                    </div>
                                </div>
                            </form> --}}
                    </div>
                </div>

            </div>
            <h1 class="fw-bold mb-0">Related products</h1>
            <div class="vesitable">
                <div class="owl-carousel vegetable-carousel justify-content-center">
                    <div class="border border-primary rounded position-relative vesitable-item">
                        <div class="vesitable-img">
                            <img src="{{ asset('assets/vendor/img/featur-1.jpg') }}" class="img-fluid w-100 rounded-top"
                                alt="">
                        </div>
                        <div class="text-white bg-primary px-3 py-1 rounded position-absolute"
                            style="top: 10px; right: 10px;">Toys</div>
                        <div class="p-4 pb-0 rounded-bottom">
                            <h4>Parsely</h4>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                            <div class="d-flex justify-content-between flex-lg-wrap">
                                <p class="text-dark fs-5 fw-bold">$4.99 / kg</p>
                                <a href="#"
                                    class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary"><i
                                        class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                            </div>
                        </div>
                    </div>
                    <div class="border border-primary rounded position-relative vesitable-item">
                        <div class="vesitable-img">
                            <img src="{{ asset('assets/vendor/img/featur-1.jpg') }}" class="img-fluid w-100 rounded-top"
                                alt="">
                        </div>
                        <div class="text-white bg-primary px-3 py-1 rounded position-absolute"
                            style="top: 10px; right: 10px;">Toys</div>
                        <div class="p-4 pb-0 rounded-bottom">
                            <h4>Parsely</h4>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                            <div class="d-flex justify-content-between flex-lg-wrap">
                                <p class="text-dark fs-5 fw-bold">$4.99 / kg</p>
                                <a href="#"
                                    class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary"><i
                                        class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                            </div>
                        </div>
                    </div>
                    <div class="border border-primary rounded position-relative vesitable-item">
                        <div class="vesitable-img">
                            <img src="{{ asset('assets/vendor/img/featur-1.jpg') }}" class="img-fluid w-100 rounded-top"
                                alt="">
                        </div>
                        <div class="text-white bg-primary px-3 py-1 rounded position-absolute"
                            style="top: 10px; right: 10px;">Toys</div>
                        <div class="p-4 pb-0 rounded-bottom">
                            <h4>Parsely</h4>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                            <div class="d-flex justify-content-between flex-lg-wrap">
                                <p class="text-dark fs-5 fw-bold">$4.99 / kg</p>
                                <a href="#"
                                    class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary"><i
                                        class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                            </div>
                        </div>
                    </div>
                    <div class="border border-primary rounded position-relative vesitable-item">
                        <div class="vesitable-img">
                            <img src="{{ asset('assets/vendor/img/featur-1.jpg') }}" class="img-fluid w-100 rounded-top"
                                alt="">
                        </div>
                        <div class="text-white bg-primary px-3 py-1 rounded position-absolute"
                            style="top: 10px; right: 10px;">Toys</div>
                        <div class="p-4 pb-0 rounded-bottom">
                            <h4>Parsely</h4>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                            <div class="d-flex justify-content-between flex-lg-wrap">
                                <p class="text-dark fs-5 fw-bold">$4.99 / kg</p>
                                <a href="#"
                                    class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary"><i
                                        class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                            </div>
                        </div>
                    </div>
                    <div class="border border-primary rounded position-relative vesitable-item">
                        <div class="vesitable-img">
                            <img src="{{ asset('assets/vendor/img/featur-1.jpg') }}" class="img-fluid w-100 rounded-top"
                                alt="">
                        </div>
                        <div class="text-white bg-primary px-3 py-1 rounded position-absolute"
                            style="top: 10px; right: 10px;">Toys</div>
                        <div class="p-4 pb-0 rounded-bottom">
                            <h4>Parsely</h4>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                            <div class="d-flex justify-content-between flex-lg-wrap">
                                <p class="text-dark fs-5 fw-bold">$4.99 / kg</p>
                                <a href="#"
                                    class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary"><i
                                        class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                            </div>
                        </div>
                    </div>
                    <div class="border border-primary rounded position-relative vesitable-item">
                        <div class="vesitable-img">
                            <img src="{{ asset('assets/vendor/img/featur-1.jpg') }}" class="img-fluid w-100 rounded-top"
                                alt="">
                        </div>
                        <div class="text-white bg-primary px-3 py-1 rounded position-absolute"
                            style="top: 10px; right: 10px;">Toys</div>
                        <div class="p-4 pb-0 rounded-bottom">
                            <h4>Parsely</h4>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                            <div class="d-flex justify-content-between flex-lg-wrap">
                                <p class="text-dark fs-5 fw-bold">$4.99 / kg</p>
                                <a href="#"
                                    class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary"><i
                                        class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                            </div>
                        </div>
                    </div>
                    <div class="border border-primary rounded position-relative vesitable-item">
                        <div class="vesitable-img">
                            <img src="{{ asset('assets/vendor/img/featur-1.jpg') }}" class="img-fluid w-100 rounded-top"
                                alt="">
                        </div>
                        <div class="text-white bg-primary px-3 py-1 rounded position-absolute"
                            style="top: 10px; right: 10px;">Toys</div>
                        <div class="p-4 pb-0 rounded-bottom">
                            <h4>Parsely</h4>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                            <div class="d-flex justify-content-between flex-lg-wrap">
                                <p class="text-dark fs-5 fw-bold">$4.99 / kg</p>
                                <a href="#"
                                    class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary"><i
                                        class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                            </div>
                        </div>
                    </div>
                    <div class="border border-primary rounded position-relative vesitable-item">
                        <div class="vesitable-img">
                            <img src="{{ asset('assets/vendor/img/featur-1.jpg') }}" class="img-fluid w-100 rounded-top"
                                alt="">
                        </div>
                        <div class="text-white bg-primary px-3 py-1 rounded position-absolute"
                            style="top: 10px; right: 10px;">Toys</div>
                        <div class="p-4 pb-0 rounded-bottom">
                            <h4>Parsely</h4>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                            <div class="d-flex justify-content-between flex-lg-wrap">
                                <p class="text-dark fs-5 fw-bold">$4.99 / kg</p>
                                <a href="#"
                                    class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary"><i
                                        class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Single Product End -->
    {{-- JavaScript to dynamically update price and selected variant --}}
    <script>
        // Make sure a variant is selected before adding it to cart
        function validateForm() {
            // Get the hidden input element
            var variantIdInput = document.getElementById('variant-id');

            // Check if the value is empty or null
            if (variantIdInput.value.trim() === '') {
                // Prompt the user with an alert
                alert('Please select a variant before adding to cart.');
                return false; // Prevent form submission
            }


            // Return true to allow the form to submit
            return true;
        }

        // Dynamically update price and selected variant
        document.addEventListener('DOMContentLoaded', function() {
            const variantInputs = document.querySelectorAll('input[name="variant_id_radio"]');
            const priceElement = document.getElementById('product-price');
            const hiddenVariantInput = document.getElementById('variant-id');
            const stockQuantityElement = document.getElementById('product-stock-quantity');

            function updatePriceAndVariant() {
                const selectedVariant = document.querySelector('input[name="variant_id_radio"]:checked');

                if (selectedVariant) {
                    const selectedPrice = selectedVariant.getAttribute('data-price');
                    const selectedStockQuantity = selectedVariant.getAttribute('data-stock-quantity');
                    const selectedVariantId = selectedVariant.value;

                    // Update the price element with the selected variant's price
                    priceElement.textContent = selectedPrice + " đ";

                    // Update the hidden input with the selected variant's ID
                    hiddenVariantInput.value = selectedVariantId;

                    //Update the stock quantity element with the selected variant's
                    stockQuantityElement.textContent = `Stock: ${selectedStockQuantity}`;

                    // Update the maximum amount to add to cart as the stock quantity
                    document.getElementById('amount').setAttribute('max', selectedStockQuantity);

                    // Log the selected variant ID (for debugging purposes)
                    // console.log('Selected variant ID:', hiddenVariantInput.value);
                }
            }

            // Run the function on page load to handle pre-selected variant
            updatePriceAndVariant();

            // Listen for changes in the variant selection
            variantInputs.forEach(function(input) {
                input.addEventListener('change', updatePriceAndVariant);
            });
        });
    </script>
@endsection()
