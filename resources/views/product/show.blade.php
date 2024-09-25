@extends('layouts.app')

@section('content')
    <div class="container">

        {{-- Show product details --}}
        <h1>{{ $product->name }}</h1>
        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
        <p>{{ $product->description }}</p>

        {{-- Price of product or selected variant --}}
        <p>Price: $<span id="product-price">{{ $product->price }}</span></p>

        <p>Stock: {{ $product->stock_quantity }}</p>
        <p>Category: {{ $product->category->name }}</p>

        {{-- Choose product variant --}}
        @if ($variants !== null)
            <h5>Variants:</h5>
            @foreach ($variants as $variant)
                <div class="form-check">
                    <input type="radio" class="form-check-input" id="variant-{{ $variant->id }}" name="variant_id_radio"
                        value="{{ $variant->id }}" data-price="{{ $variant->variant_price }}">
                    <label class="form-check-label" for="variant-{{ $variant->id }}">{{ $variant->variant_name }} -
                        ${{ $variant->variant_price }}</label>
                </div>
            @endforeach
        @endif

        {{-- Amount to add to cart --}}
        <form action="{{ route('cart.store', $product->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="amount">Amount:</label>
                <input type="number" id="amount" name="amount" min="1" value="1"
                    max="{{ $product->stock_quantity }}" required>
            </div>

            {{-- Hidden input to store selected variant --}}
            <input type="hidden" id="variant-id" name="variant_id" value="">

            <button type="submit" class="btn btn-primary">Add to Cart</button>
            <a href="{{ route('product.index') }}" class="btn btn-secondary">Back to products</a>
        </form>

    </div>

    {{-- JavaScript to dynamically update price and selected variant --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const variantInputs = document.querySelectorAll('input[name="variant_id_radio"]');
            const priceElement = document.getElementById('product-price');
            const hiddenVariantInput = document.getElementById('variant-id');

            function updatePriceAndVariant() {
                const selectedVariant = document.querySelector('input[name="variant_id_radio"]:checked');

                if (selectedVariant) {
                    const selectedPrice = selectedVariant.getAttribute('data-price');
                    const selectedVariantId = selectedVariant.value;

                    // Update the price element with the selected variant's price
                    priceElement.textContent = selectedPrice;

                    // Update the hidden input with the selected variant's ID
                    hiddenVariantInput.value = selectedVariantId;

                    // Log the selected variant ID (for debugging purposes)
                    console.log('Selected variant ID:', hiddenVariantInput.value);
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
@endsection
