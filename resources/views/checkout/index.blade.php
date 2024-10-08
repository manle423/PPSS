@extends('layouts.shop')
@section('content')


    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Checkout</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Checkout</li>
        </ol>
    </div>
    <!-- Single Page Header End -->


    <!-- Checkout Page Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <h1 class="mb-4">Billing details</h1>
            <form action="#">
                <div class="row g-5">
                    <div class="col-md-12 col-lg-6 col-xl-5">

                        <div class="form-item">
                            <label class="form-label my-3">Full Name<sup>*</sup></label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-item">
                            <label class="form-label my-3">Address <sup>*</sup></label>
                            <input type="text" class="form-control" placeholder="House Number Street Name">
                        </div>

                        <div class="form-item">
                            <label class="form-label my-3">Mobile<sup>*</sup></label>
                            <input type="tel" class="form-control">
                        </div>
                        <div class="form-item">
                            <label class="form-label my-3">Email Address<sup>*</sup></label>
                            <input type="email" class="form-control">
                        </div>




                    </div>
                    <div class="col-md-12 col-lg-6 col-xl-7">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Products</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Variant</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- <tr>
                                        <th scope="row">
                                            <div class="d-flex align-items-center mt-2">
                                                <img src="img/vegetable-item-2.jpg" class="img-fluid rounded-circle"
                                                    style="width: 90px; height: 90px;" alt="">
                                            </div>
                                        </th>
                                        <td class="py-5">Awesome Brocoli</td>
                                        <td class="py-5">Green Brocoli</td>
                                        <td class="py-5">$69.00</td>
                                        <td class="py-5">2</td>
                                        <td class="py-5">$138.00</td>
                                    </tr> --}}
                                    @foreach ($cartItems as $item)
                                        @php
                                            $variantId = $item->variant ? strval($item->variant->id) : '';
                                            $cartKey = $item->product->id . '-' . $variantId;
                                            $amount = $sessionCart[$cartKey] ?? 0;
                                        @endphp
                                        <x-cart-item-checkout :item="$item" :cartKey="$cartKey" :amount="$amount" />
                                    @endforeach
                                    <tr>
                                        <th scope="row">
                                        </th>
                                        <td class="py-5"></td>
                                        <td class="py-5"></td>
                                        <td class="py-5">
                                            <p class="mb-0 text-dark py-3">Subtotal</p>
                                        </td>
                                        <td class="py-5">
                                            <div class="py-3 border-bottom border-top">
                                                <p class="mb-0 text-dark">{{ $subtotal }}đ</p>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th scope="row">
                                        </th>
                                        <td class="py-5">
                                            <p class="mb-0 text-dark py-4">Shipping</p>
                                        </td>
                                        <td colspan="3" class="py-5">
                                            <div class="form-check text-start">
                                                <input type="checkbox" class="form-check-input bg-primary border-0"
                                                    id="Shipping-1" name="Shipping-1" value="Shipping">
                                                <label class="form-check-label" for="Shipping-1">Free Shipping</label>
                                            </div>
                                            <div class="form-check text-start">
                                                <input type="checkbox" class="form-check-input bg-primary border-0"
                                                    id="Shipping-2" name="Shipping-1" value="Shipping">
                                                <label class="form-check-label" for="Shipping-2">Flat rate: $15.00</label>
                                            </div>
                                            <div class="form-check text-start">
                                                <input type="checkbox" class="form-check-input bg-primary border-0"
                                                    id="Shipping-3" name="Shipping-1" value="Shipping">
                                                <label class="form-check-label" for="Shipping-3">Local Pickup:
                                                    $8.00</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3">
                                            <p class="mb-0 text-dark text-uppercase py-3">TOTAL</p>
                                        </td>
                                        <td class="py-3">
                                            <div class="py-3 border-bottom border-top">
                                                <p class="mb-0 text-dark">$135.00</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div>
                            <input type="text" class="border-1 rounded me-5 py-3 mb-4" placeholder="Coupon Code">
                            <button class="btn border-secondary rounded-pill px-4 py-3 text-primary" type="button">Apply
                                Coupon</button>
                        </div>

                        {{-- payment method --}}
                        <h3 class="mb-4 mt-4">Payment Method</h3>
                        <div>
                            {{-- <form action="{{ route('placeOrder') }}" method="POST"> --}}
                                @csrf
                                <select name="payment_method" id="payment_method" class="form-select form-select-lg mb-3"
                                    aria-label=".form-select-lg example">
                                    <option value="cash">Cash</option>
                                    <option value="bank">Bank</option>
                                    <option value="paypal">Paypal</option>
                                    <option value="momo">Momo</option>
                                </select>
                        </div>
                        {{-- <button type="submit">Place Order</button> --}}
            </form>

            <div class="row g-4 text-center align-items-center justify-content-center pt-4">

                <button id="placeOrderBtn" type="button"
                    class="btn border-secondary py-3 px-4 text-uppercase w-100 text-primary">Place
                    Order</button>

            </div>
        </div>
    </div>
    </form>
    </div>
    </div>
    <!-- Checkout Page End -->
@endsection()
