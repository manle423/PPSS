@props(['item','cartKey','amount'])
<tr>
    <th scope="row">
        {{ $item->product->name }}
    </th>
    <td>
        <p class="mb-0 mt-4">{{  optional($item->variant)->variant_name ?? '' }}</p>
    </td>
    <td>
        <p class="mb-0 mt-4">{{ optional($item->variant)->stock_quantity ?? $item->product->stock_quantity }}</p>
    </td>
    <td>
        <p class="mb-0 mt-4">
            <form action="{{ route('cart.update', $item) }}" method="POST">
            @csrf
            @method('PATCH')
            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" style="width: 60px;"
                max="{{ $item->product->stock_quantity }}" />
            <button class="btn btn-primary btn-sm">Update</button>
        </form></p>
    </td>
    <td>
        ${{ number_format($item->product->price, 2) }}
    </td>
    <td>
        <p class="mb-0 mt-4">${{ number_format($item->quantity * $item->product->price, 2) }}</p>
    </td>
    <td>
        <form action="{{ route('cart.destroy', $item) }}" method="POST">
            @csrf
            @method('DELETE')
            <button class="btn btn-md rounded-circle bg-light border mt-4">
                <i class="fas fa-trash"></i>
            </button>
        </form>
        
    </td>

</tr>