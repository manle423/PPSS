@props(['item', 'cartKey', 'amount'])
<tr>
    <th scope="row">
        image here
    </th>
    <th scope="row">
        {{ $item->product->name }}
    </th>
    <td class="py-5">
        {{ optional($item->variant)->variant_name ?? '' }}
    </td>
    <td>
        @php
            $sessionCart = session()->get('cart', []);
        @endphp
        {{ $sessionCart[$cartKey] }}
    </td>
    <td class="py-5">
        {{ (optional($item->variant)->variant_price ?? $item->product->price)}}đ
    </td>
    <td class="py-5">
        {{ number_format($item->quantity * (optional($item->variant)->variant_price ?? $item->product->price), 2) }}đ
    </td>


</tr>
