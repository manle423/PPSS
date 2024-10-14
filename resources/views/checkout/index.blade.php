<script src="https://www.paypal.com/sdk/js?client-id={{ env('PAYPAL_SANDBOX_CLIENT_ID') }}"></script>
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
            <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
                @csrf
                <div class="row g-5">
                    <div class="col-md-12 col-lg-6 col-xl-5">
                        @if (Auth::check())
                            @if ($addresses->isNotEmpty())
                                <div class="form-group mb-3">
                                    <label for="address_id">Select Address</label>
                                    <select name="address_id" id="address_id" class="form-control">
                                        <option value="">Select an address</option>
                                        @foreach ($addresses as $address)
                                            <option value="{{ $address->id }}">
                                                {{ $address->full_name }} - {{ $address->address_line_1 }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="selected_address_id" id="selected_address_id" value="">
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="new_address" name="new_address"
                                        value="1">
                                    <label class="form-check-label" for="new_address">
                                        Use a different address
                                    </label>
                                </div>
                                <div id="new_address_form" style="display: none;">
                                    <x-checkout.address-form :provinces="$provinces" />
                                </div>
                            @else
                                <div id="new_address_form">
                                    <x-checkout.address-form :provinces="$provinces" />
                                </div>
                            @endif
                        @else
                            <x-checkout.address-form :provinces="$provinces" />
                            <div class="form-group mb-3">
                                <label for="email">Email Address<sup>*</sup></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        @endif
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
                                                <p class="mb-0 text-dark">{{ $subtotal }} $</p>
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
                        <input type="hidden" name="total_amount" value="{{ $subtotal }}">
                        <div class="form-group mb-3">
                            <label for="payment_method">Payment Method</label>
                            <select name="payment_method" id="payment_method" class="form-control" required>
                                <option value="paypal">PayPal</option>
                                <option value="vnpay">VNPay</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Place Order</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Checkout Page End -->
    <script src="{{ asset('assets/js/checkout.js') }}"></script>
@endsection()
