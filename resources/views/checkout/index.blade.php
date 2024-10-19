<script src="https://www.paypal.com/sdk/js?client-id={{ env('PAYPAL_SANDBOX_CLIENT_ID') }}"></script>
<script>
    window.GHNConfig = {
        token: "{{ env('GHN_TOKEN') }}",
        shopId: "{{ env('GHN_SHOP_ID') }}"
    };
</script>
@extends('layouts.shop')
@section('content')
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Checkout</h1>
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
                        <!-- Remove the address form here -->
                        <a href="#" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#shippingModal">
                            Select Shipping Address
                        </a>
                        @include('components.modal-shipping', ['addresses' => $addresses])
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
                                        <td class="py-3">
                                            <p class="mb-0 text-dark text-uppercase py-3">Subtotal</p>
                                        </td>
                                        <td class="py-3">
                                            <div class="py-3 border-bottom border-top">
                                                @if(session('coupon_discount', 0) > 0)
                                                    <p class="mb-0 text-muted text-decoration-line-through">{{ number_format(session('oldSubtotal', 0), 0, '.', ',') }} đ</p>
                                                @endif
                                                <p class="mb-0 text-dark" id="subtotal">{{ number_format(session('subtotal', 0), 0, '.', ',') }} đ</p>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="py-3">
                                            <p class="mb-0 text-dark text-uppercase py-3">Shipping Fee</p>
                                        </td>
                                        <td class="py-3">
                                            <div class="py-3 border-bottom border-top">
                                                <p class="mb-0 text-dark" id="shippingFee">{{ number_format(session('shipping_fee', 0), 0, '.', ',') }} đ</p>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="py-3">
                                            <p class="mb-0 text-dark text-uppercase py-3">Total</p>
                                        </td>
                                        <td class="py-3">
                                            <div class="py-3 border-bottom border-top">
                                                <p class="mb-0 text-dark" id="totalAmount">{{ number_format(session('total', session('subtotal', 0)), 0, '.', ',') }} đ</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
            <form action="{{ route('checkout.coupon') }}" method="get" id="coupon-form">
                @csrf
                <input type="hidden" name="subtotal" value="{{ isset($oldSubtotal) ? $oldSubtotal : $subtotal }}">
                <input type="hidden" name="address_id" value="{{ request('address_id') }}">
                <input type="hidden" name="new_address" value="{{ request('new_address') }}">
                <input type="hidden" name="new_full_name" value="{{ old('new_full_name') }}">
                <input type="hidden" name="new_phone_number" value="{{ old('new_phone_number') }}">
                <input type="hidden" name="new_province_id" value="{{ old('new_province_id') }}">
                <input type="hidden" name="new_district_id" value="{{ old('new_district_id') }}">
                <input type="hidden" name="new_ward_id" value="{{ old('new_ward_id') }}">
                <input type="hidden" name="new_address_line_1" value="{{ old('new_address_line_1') }}">
                <input type="hidden" name="new_address_line_2" value="{{ old('new_address_line_2') }}">

                <input type="text" class="border-1 rounded me-5 py-3 mb-4" placeholder="Coupon Code" id="coupon_code"
                    name='coupon_code' value="{{ $couponCode }}">

                <button class="btn border-secondary rounded-pill px-4 py-3 text-primary" type="submit">Apply
                    Coupon</button>
                @error('coupon_error')
                    <p class="text-danger" role="alert">
                        <strong>{{ $message }}</strong>
                    </p>
                @enderror
            </form>
        </div>
    </div>
    <!-- Checkout Page End -->

    <script src="{{ asset('assets/js/checkout.js') }}?v={{ time() }}"></script>
@endsection
