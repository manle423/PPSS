@props(['item'])
<tr>
    <td>{{ $item->product->name }}</td>

    {{-- Check if the item has a variant before accessing variant_name --}}

    <td>{{ optional($item->variant)->variant_name ?? '' }}</td>

    <td>{{ optional($item->variant)->stock_quantity ?? $item->product->stock_quantity }}</td>

    {{-- <td>{{ number_format($item->product->stock_quantity) }}</td> --}}

    @guest
        {{-- Update the quantity of the cart stored in session --}}
        <script>
            function updateQuantity(productId, variantId) {
                var quantityInput = document.getElementById('quantityInput');
                var newQuantity = parseInt(quantityInput.value);

                if (newQuantity < 1 || newQuantity > {{ $item->product->stock_quantity }}) {
                    alert('Invalid quantity entered!');
                    return;
                }

                // Update the quantity of the cart item in the session directly
                // Assuming $item is stored in the session with the key 'cart'
                var sessionCart = {!! json_encode(session('cart')) !!};
                var cartKey = productId + '-' + variantId;

                // Update the quantity for the corresponding cart item
                sessionCart[cartKey] = newQuantity;

                // Update the session with the modified cart data
                sessionStorage.setItem('cart', JSON.stringify(sessionCart));

                // Update the displayed quantity in the input field
                quantityInput.value = newQuantity;
                quantityInput.textContent = newQuantity;

                alert("Quantity changed to " + quantityInput.value);
                
            }
            
        </script>
        <td>
            <input type="number" id="quantityInput" name="quantity" value="{{ $item->quantity }}" style="width: 60px;"
                max="{{ $item->product->stock_quantity }}" />
            <button class="btn btn-primary btn-sm"
                onclick="updateQuantity({{ $item->product->id }},{{ $item->variant->id }})">
                Update</button>
        </td>
    @endguest
    @auth
        {{-- Update the quantity of the cart in database --}}
        <td>
            <form action="{{ route('cart.update', $item) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" style="width: 60px;"
                    max="{{ $item->product->stock_quantity }}" />
                <button class="btn btn-primary btn-sm">Update</button>
            </form>
        </td>
    @endauth


    <td>${{ number_format($item->product->price, 2) }}</td>

    <td>${{ number_format($item->quantity * $item->product->price, 2) }}</td>

    @guest
        {{-- Delete the cart stored in session --}}
        <td>
            <script>
                function deleteCartItem(productId, variantId) {
                    // Delete the cart item in the session directly
                    // Assuming $item is stored in the session with the key 'cart'
                    var sessionCart = {!! json_encode(session('cart')) !!};
                    var cartKey = productId + '-' + variantId;

                    delete sessionCart[cartKey];

                    // Update the session with the modified cart data
                    sessionStorage.setItem('cart', JSON.stringify(sessionCart));
                }
            </script>
            <button class="btn btn-danger btn-sm"
                onclick="deleteCartItem({{ $item->product->id }},{{ $item->variant->id }})">Remove</button>
        </td>

    @endguest


    @auth
        {{-- Delete the cart in database --}}
        <td>
            <form action="{{ route('cart.destroy', $item) }}" method="POST">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm">Remove</button>
            </form>
        </td>
    @endauth


</tr>
