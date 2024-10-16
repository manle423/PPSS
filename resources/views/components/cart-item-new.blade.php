@props(['item', 'cartKey', 'amount'])
<tr>
    <th scope="row">
        {{ $item->product->name }}
    </th>
    <td>
        <p class="mb-0 mt-4">{{ optional($item->variant)->variant_name ?? '' }}</p>
    </td>
    <td>
        <p class="mb-0 mt-4">{{ optional($item->variant)->stock_quantity ?? $item->product->stock_quantity }}</p>
    </td>
    <td>
        @auth
        <p class="mb-0 mt-4">
            <form action="{{ route('cart.update',  ['cartKey' => $cartKey, 'product' => $item]) }}" method="POST">
                @csrf
                @method('PATCH')
                
                @php
                    $sessionCart = session()->get('cart', []);
                    $newAmount = $sessionCart[$cartKey];
                @endphp
                <input type="number" id="quantity_{{ $cartKey }}" name="quantity" value="{{ $newAmount }}" style="width: 60px;"
                    max="{{ optional($item->variant)->stock_quantity ?? $item->product->stock_quantity }}" />
                <button class="btn btn-primary btn-sm">Update</button>
            </form>
        </p>
        @endauth
            
        @guest
            <form action="{{ route('cart.update-session', ['cartKey' => $cartKey]) }}" method="POST">
                @csrf
                @method('PATCH')
                @php
                    $sessionCart = session()->get('cart', []);
                    $newAmount = $sessionCart[$cartKey];
                @endphp
                <input type="number" id="quantity_{{ $cartKey }}" name="quantity" value="{{ $newAmount }}" style="width: 60px;"
                    max="{{optional($item->variant)->stock_quantity ?? $item->product->stock_quantity }}" />
                <button type="submit" class="btn btn-primary btn-sm">
                    Update
                </button>
            </form>
            <input type="hidden" id="itemQuantity_{{ $cartKey }}" value="{{ $item->quantity }}" />
        @endguest
    </td>
    <td>
        <p class="mb-0 mt-4">
        {{(optional($item->variant)->variant_price ?? $item->product->price)  }}đ
        </p>
    </td>
    <td>
        <p class="mb-0 mt-4">{{ number_format($item->quantity * (optional($item->variant)->variant_price ?? $item->product->price), 2) }}đ</p>
    </td>
    <td>
        @auth
            <form action="{{ route('cart.destroy',['cartKey' => $cartKey, 'product' => $item]) }}" method="POST">
                @csrf
                @method('DELETE')
                <button class="btn btn-md rounded-circle bg-light border mt-4">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        @endauth
        @guest
            <form action="{{ route('cart.destroy-session', $cartKey) }}" method="POST">
                @csrf
                <button class="btn btn-md rounded-circle bg-light border mt-4">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        @endguest
    </td>

</tr>
