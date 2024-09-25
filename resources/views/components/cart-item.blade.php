@props(['item'])

<tr>
    <td>{{ $item->product->name }}</td>

    {{-- Check if the item has a variant before accessing variant_name --}}

    <td>{{ optional($item->variant)->variant_name?? '' }}</td>
    <td>{{optional($item->variant)->stock_quantity ?? $item->product->stock_quantity}}</td>
 
    {{-- <td>{{ number_format($item->product->stock_quantity) }}</td> --}}
    
    <td>
        <form action="{{ route('cart.update', $item) }}" method="POST">
            @csrf
            @method('PATCH')
            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                style="width: 60px;" max="{{ $item->product->stock_quantity }}" />
            <button class="btn btn-primary btn-sm">Update</button>
        </form>
    </td>
    
    <td>${{ number_format($item->product->price, 2) }}</td>
    
    <td>${{ number_format($item->quantity * $item->product->price, 2) }}</td>
    
    <td>
        <form action="{{ route('cart.destroy', $item) }}" method="POST">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger btn-sm">Remove</button>
        </form>
    </td>
</tr>
