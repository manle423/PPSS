<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .alert {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .alert-heading {
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>

    @php
        use Illuminate\Support\Facades\Http;

        function getLocationName($type, $id, $districtId = null)
        {
            $apiToken = env('GHN_TOKEN');
            $baseUrl = 'https://online-gateway.ghn.vn/shiip/public-api/master-data/';

            $response = Http::withHeaders([
                'Token' => $apiToken,
                'Content-Type' => 'application/json',
            ])->get($baseUrl . $type, $type === 'ward' ? ['district_id' => $districtId] : []);

            $data = $response->json()['data'] ?? [];

            if ($type === 'province') {
                $item = collect($data)->firstWhere('ProvinceID', $id);
                return $item ? $item['ProvinceName'] : 'Unknown Province';
            } elseif ($type === 'district') {
                $item = collect($data)->firstWhere('DistrictID', $id);
                return $item ? $item['DistrictName'] : 'Unknown District';
            } elseif ($type === 'ward') {
                $item = collect($data)->firstWhere('WardCode', $id);
                return $item ? $item['WardName'] : 'Unknown Ward';
            }

            return 'Unknown';
        }
    @endphp

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
        {{-- @dd($order->shippingAddress) --}}
        <h5>Shipping Information</h5>
        @if ($orderType === 'order')
            @php
                App\Http\Controllers\ProfileController::decryptAddress($order->shippingAddress)
            @endphp
            <p>{{ $order->shippingAddress->full_name }}<br>
                {{ $order->shippingAddress->address_line_1 }},
                @if ($order->shippingAddress->address_line_2)
                    {{ $order->shippingAddress->address_line_2 }},
                @endif
                {{ getLocationName('ward', $order->shippingAddress->ward_id, $order->shippingAddress->district_id) }},
                {{ getLocationName('district', $order->shippingAddress->district_id) }},
                {{ getLocationName('province', $order->shippingAddress->province_id) }}
            </p>
        @else
            @php
                $guestAddress = json_decode($order->guest_address, true);
                $guestAddress = App\Http\Controllers\ProfileController::decryptAddressData($guestAddress)
            @endphp
            <p>{{ $order->guest_name }}<br>
                {{ $guestAddress['address_line_1'] }},
                @if (isset($guestAddress['address_line_2']) && $guestAddress['address_line_2'])
                    {{ $guestAddress['address_line_2'] }},
                @endif
                {{ getLocationName('ward', $guestAddress['ward_id'], $guestAddress['district_id']) }},
                {{ getLocationName('district', $guestAddress['district_id']) }},
                {{ getLocationName('province', $guestAddress['province_id']) }}
            </p>
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

        @if ($order->discount_value > 0)
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
