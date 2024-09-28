@extends('layouts.shop') <!-- Kế thừa từ layout chung -->
@section('content')
    <script src="{{ asset('js/confirmed-checkout.js') }}"></script>
    <!-- HTML giao diện checkout ở đây -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Checkout</h1>
        <!-- Breadcrumbs và nội dung checkout -->
    </div>

    <div class="container-fluid py-5">
        <div class="container py-5">
            <h1 class="mb-4">Billing details</h1>
            <form action="#" id="checkoutForm">
                <!-- Nội dung form checkout -->
                <div class="row g-4 text-center align-items-center justify-content-center pt-4">
                    <button type="button" id="placeOrderBtn" class="btn border-secondary py-3 px-4 text-uppercase w-100 text-primary">Place Order</button>
                </div>
            </form>

            <!-- Thông báo thành công -->
            <div id="orderSuccess" style="display: none;">
                <h2>Your order has been placed successfully!</h2>
                <p>Thank you for your order. We will deliver the goods to you as soon as possible.</p>
            </div>
        </div>
    </div>
@endsection
