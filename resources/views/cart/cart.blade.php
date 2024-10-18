@extends('layouts.shop')
@section('content')

    <!-- Cart Page Start -->
    <div class="container-fluid py-5">
         <div class="container py-5">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                        <th>Variant</th>
                        <th>In Stock</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        @foreach ($cartItems as $item)
                        @php
                            $variantId = $item->variant ? strval($item->variant->id) : '';
                            $cartKey = $item->product->id . '-' . $variantId;
                            $amount = $sessionCart[$cartKey] ?? 0;
                        @endphp
                            <x-cart-item-new :item="$item" :cartKey="$cartKey" :amount="$amount"  /> 
                        @endforeach
                        
                    </tbody>
                </table>
            </div>
             <div class="row g-4 justify-content-end">
                <div class="col-8"></div>
                <div class="container-fluid">
                    <div class="bg-light rounded">
                        <div class="p-4">
                            
                            <div class="d-flex justify-content-between mb-4">
                                <h4 class="mb-0 me-4">Subtotal:</h4>
                                <p class="mb-0">VND: {{$subtotal}}</p>
                            </div>
                            
                        </div>
                        
                        <form action="{{ route('checkout.index')}}" method="GET">
                            <button
                                class="btn border-secondary rounded-pill px-4 py-3 text-primary text-uppercase mb-4 ms-4"
                                type="submit">
                                Proceed Checkout
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div> 
    </div> 
    
    <!-- Cart Page End -->
@endsection()