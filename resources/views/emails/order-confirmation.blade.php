<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; }
        .alert { background-color: #d4edda; border-color: #c3e6cb; color: #155724; padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; }
        .alert-heading { margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <div class="alert">
            <h4 class="alert-heading">Order Confirmation</h4>
            <p>Your order has been successfully placed.</p>
            <hr>
            <p class="mb-0">Thank you for your purchase.</p>
        </div>

        <h4>Order Details</h4>
        <p><strong>Order Code:</strong> {{ $order->order_code }}</p>
        <p><strong>Order Date:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>

        <h5>Shipping Information</h5>
        @if($orderType === 'order')
            <p>{{ $order->shippingAddress->full_name }}<br>
               {{ $order->shippingAddress->address_line_1 }},
               @if($order->shippingAddress->address_line_2)
                   {{ $order->shippingAddress->address_line_2 }},
               @endif
               {{ $order->shippingAddress->ward->name }}, {{ $order->shippingAddress->district->name }}, {{ $order->shippingAddress->province->name }}</p>
        @else
            @php
                $guestAddress = json_decode($order->guest_address, true);
                $ward = App\Models\Ward::find($guestAddress['ward_id']);
                $district = App\Models\District::find($guestAddress['district_id']);
                $province = App\Models\Province::find($guestAddress['province_id']);
            @endphp
            <p>{{ $order->guest_name }}<br>
               {{ $guestAddress['address_line_1'] }},
               @if(isset($guestAddress['address_line_2']) && $guestAddress['address_line_2'])
                   {{ $guestAddress['address_line_2'] }},
               @endif
               {{ $ward->name }}, {{ $district->name }}, {{ $province->name }}</p>
        @endif

        <p><strong>Shipping Method:</strong> {{ $order->shippingMethod->name ?? 'N/A' }}</p>

        <h5>Order Items</h5>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderItems as $item)
                    <tr>
                        <td>{{ $item->item->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price, 2) }}</td>
                        <td>{{ number_format($item->quantity * $item->price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            <strong>Subtotal:</strong> {{ number_format($order->total_price, 2) }}
        </div>

        @if($order->discount_value > 0)
            <div style="margin-top: 10px;">
                <strong>Discount:</strong> {{ number_format($order->discount_value, 2) }}
            </div>
        @endif

        <div style="margin-top: 10px;">
            <strong>Total Price:</strong> {{ number_format($order->final_price, 2) }}
        </div>

        <div style="margin-top: 20px;">
            <strong>Order Status:</strong> {{ ucfirst($order->status) }}
        </div>

        <div style="margin-top: 10px;">
            <strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}
        </div>

        <p style="margin-top: 20px;">Thank you for shopping with us!</p>
    </div>
</body>
</html>
