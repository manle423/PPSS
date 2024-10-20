@php
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Http;

    function getLocationName($type, $id, $districtId = null) {
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

@extends('layouts.shop')

@section('content')
    <div class="container">
        <h1 class="my-4 text-center">Your order history</h1>

        <!-- Thanh điều hướng trạng thái -->
        <ul class="nav nav-pills mb-4 justify-content-center">
            <li class="nav-item">
                <a class="nav-link {{ $status == 'PENDING' ? 'active' : '' }}"
                    href="{{ route('user.order-history', 'PENDING') }}">Pending</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status == 'SHIPPING' ? 'active' : '' }}"
                    href="{{ route('user.order-history', 'SHIPPING') }}">Shipping</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status == 'COMPLETED' ? 'active' : '' }}"
                    href="{{ route('user.order-history', 'COMPLETED') }}">Completed</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status == 'CANCELED' ? 'active' : '' }}"
                    href="{{ route('user.order-history', 'CANCELED') }}">Canceled</a>
            </li>
        </ul>
 
        <!-- Kiểm tra nếu không có đơn hàng -->
        @if ($orders->isEmpty())
            <p class="text-center">There are no orders in status {{ $status }}.</p>
        @else
            <!-- Hiển thị bảng đơn hàng -->
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Order Code</th>
                        <th>Delivery Address</th>
                        <th>Shipping Method</th>
                        <th>Payment Method</th>
                        <th>Total Price</th>
                        <th>Discount</th>
                        <th>Final Price</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    
                    @foreach ($orders as $order)
                        <tr>
                            
                            <td>{{ $order->order_code }}</td>
                            <td>
                                {{ $order->shippingAddress->full_name ?? 'N/A' }}<br>
                                {{ $order->shippingAddress->address_line_1 ?? 'N/A' }}<br>
                                {{ getLocationName('ward', $order->shippingAddress->ward_id, $order->shippingAddress->district_id) ?? 'N/A' }}, 
                                {{ getLocationName('district', $order->shippingAddress->district_id) ?? 'N/A' }}, 
                                {{ getLocationName('province', $order->shippingAddress->province_id) ?? 'N/A' }}
                            </td>
                            <td>{{ $order->shippingMethod->name ?? 'N/A' }}</td>
                            <td>{{ $order->payment_method }}</td>
                            <td>{{ number_format($order->total_price, 2) }} VNĐ</td>
                            <td>{{ number_format($order->discount_value, 2) }} VNĐ</td>
                            <td>{{ number_format($order->final_price, 2) }} VNĐ</td>
                            <td>{{ $order->status }}</td>
                            <td>{{ Carbon::parse($order->order_date)->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('order.show', $order) }}" class="btn btn-info btn-sm">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Phân trang -->
            <div class="d-flex justify-content-center">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection
