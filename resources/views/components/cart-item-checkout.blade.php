@props(['item', 'cartKey', 'amount'])
<tr>
    <th scope="row">
        <img src="{{ $item->product->image }}" 
             alt="{{ $item->product->name }}" 
             class="img-fluid rounded" 
             style="max-width: 80px; max-height: 80px; object-fit: cover;">
    </th>
    <th class="py-5" scope="row">
        {{ $item->product->name }}
    </th>
    <td class="py-5" scope="row">
        {{ optional($item->variant)->variant_name ?? '' }}
    </td>
    <td class="py-5" scope="row">
        {{ number_format((optional($item->variant)->variant_price ?? $item->product->price),0,'.',',')}} đ
    </td>
    <td class="py-5" scope="row">
        @php
            $sessionCart = session()->get('cart', []);
        @endphp
        {{ $sessionCart[$cartKey] }}
    </td>
    <td class="py-5" scope="row">
        {{ number_format($item->quantity * (optional($item->variant)->variant_price ?? $item->product->price), 0,'.',',') }} đ
    </td>
</tr>
