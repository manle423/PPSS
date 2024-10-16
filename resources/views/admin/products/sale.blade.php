@extends('layouts.admin')

@section('content')
<link href="{{ asset('assets/vendor/css/sale.css') }}" rel="stylesheet">

<div class="wrapper">
    <h1 class="page-title">Product Sales Report</h1>
    <a><button type="button"class="btn btn-secondary" style="background-color:royalblue;"data-bs-toggle="modal" data-bs-target="#filterModal">Seach</button></a>
     <!-- Modal -->
     <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
       <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter products</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="filterForm" action="{{route('admin.products.search')}}" method="POST">
                  @csrf
              
                    <div class="mb-3">
                        <label for="productName" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" placeholder="Date order">
                        <input type="hidden" value="{{$productId}}" name="id">
                    </div> 
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="filterForm" class="btn btn-secondary">Search</button>
            </div>
        </div>
    </div>
    </div>

    <div class="report-card">
        <div class="report-content">
            <h2 class="product-name">Product Name: {{ $productName }}</h2>
            <p class="product-id"><strong>Product ID:</strong> {{ $productId }}</p>

            <table>
                <thead>
                    <tr>
                        <th>Order Code</th>
                        <th>Variant ID</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Date</th>
                    </tr>
                </thead>

                @php
                    $sumQuantity = 0;
                    $totalRevenue = 0;
                @endphp

                <tbody>
                    @forelse ($productSales as $sale)
                        @php
                            $totalPrice = $sale->quantity * $sale->price;
                            $sumQuantity += $sale->quantity;
                            $totalRevenue += $totalPrice;
                        @endphp
                        <tr>
                            <td>{{ $sale->order ? $sale->order->order_code : 'N/A' }}</td>
                            <td>{{ $sale->variant_id }}</td>
                            <td>{{ $sale->quantity }}</td>
                            <td>{{ number_format($sale->price) }} đ</td>
                            <td>{{ number_format($totalPrice) }} đ</td>
                            <td>{{ $sale->created_at }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No sales data available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="summary">
                <p><strong>Total Quantity Sold:</strong> {{ $sumQuantity }}</p>
                <p><strong>Total Revenue:</strong> {{ number_format($totalRevenue) }} đ</p>
            </div>

        </div>
        
    </div>

    <div class="navigation">
        <button class="back-link" onclick="window.history.back()">Back to Product</button>
    </div>
</div>
@endsection
