@extends('layouts.shop')
@section('content')
    <!-- Cart Page Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <form action="{{ route('checkout.index') }}" method="GET" id="cartForm">
                <div class="row">
                    <div class="col-12">
                        <div class="bg-light rounded p-4">

                            <div class="fw-bold mb-4 row text-center fw-bold bg-primary text-white py-2 rounded">
                                <div class="col-1">
                                    <input type="checkbox" id="selectAll">
                                </div class="">
                                <div class="col-2">Product</div>
                                <div class="col-2">Variant</div>
                                <div class="col-1">In Stock</div>
                                <div class="col-1">Quantity</div>
                                <div class="col-2">Price</div>
                                <div class="col-2">Total</div>
                                <div class="col-1">Actions</div>
                            </div>


                            @foreach ($cartItems as $item)
                                @php
                                    $variantId = $item->variant ? strval($item->variant->id) : '';
                                    $cartKey = $item->product->id . '-' . $variantId;
                                    $amount = $sessionCart[$cartKey] ?? 0;
                                @endphp
                                <div class="row text-start align-items-center mb-3">
                                    <div class="col-1">
                                        <input type="checkbox" name="selectedItems[]" value="{{ $item->product->id }}">
                                    </div>
                                    <div class="col-2">{{ $item->product->name }}</div>
                                    <div class="col-2">{{ $item->variant ? $item->variant->name : 'N/A' }}</div>
                                    <div class="col-1">{{ $item->product->in_stock ? 'Yes' : 'No' }}</div>
                                    <div class="col-1">
                                        <input type="number" value="{{ $amount }}" class="form-control text-center"
                                            readonly>
                                    </div>
                                    <div class="col-2">${{ $item->product->price }}</div>
                                    <div class="col-2">${{ $item->product->price * $amount }}</div>
                                    <div class="col-1">
                                    <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="g-4 justify-content-end">
                        <div class="offset-md-8">
                            <div class="bg-light rounded p-4">
                                <div class="d-flex justify-content-between mb-4">
                                    <h3 class="mb-0">Subtotal:</h3>
                                    <p class="mb-0 fs-3">${{ $subtotal }}</p>
                                </div>
                                <button class="btn btn-primary btn-block rounded-pill px-4 py-2 text-uppercase"
                                    type="submit" id="proceedCheckout">
                                    Proceed Checkout
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Cart Page End -->
    <script>
        document.getElementById('selectAll').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('input[name="selectedItems[]"]');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        });

        document.getElementById('proceedCheckout').addEventListener('click', function() {
            const selectedItems = document.querySelectorAll('input[name="selectedItems[]"]:checked');
            if (selectedItems.length === 0) {
                alert('Please select at least one product to proceed to checkout.');
            } else {
                // Nếu có sản phẩm được chọn, submit form để chuyển sang trang checkout
                document.getElementById('cartForm').submit();
            }
        });
    </script>
@endsection
