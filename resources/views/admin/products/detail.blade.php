@extends('layouts.admin')
@section('content')
    <link href="{{ asset('assets/vendor/css/product-detail.css') }}" rel="stylesheet">
    
    <div class="container" ">
        <div class="product-detail" >
            <div class="product-image">
                <img src="{{ $product->image }}" alt="{{ $product->name }} alt="Product Image">
            </div>
            <div class="product-info" >
                <h1 class="product-title">Product Name :{{$product->name}}</h1>
                <p class="product-price">Price: {{$product->price}} VND</p>
                <h4 class="product-category">Category: {{$product->category->name}}</h4>
                <p class="product-description">
                {{$product->description}}
                </p>
                <h5 class="product-stock-quantity">Stock quantity: {{$product->stock_quantity}}</h5>
                <h6 class="product-weight">Weight: {{$product->weight}} (kg)</h6>
                <h6 class="product-height">Height: {{$product->height}} (cm)</h6>
                <h6 class="product-length">Length: {{$product->length}} (cm)</h6>
                <h6 class="product-width">Width: {{$product->width}} (cm)</h6>

                <div class="product-actions">
                    <a href="{{route('admin.products.list')}}"><button class="btn-add-to-cart">Back to list</button></a>
                    <a href="{{ route('admin.products.edit', $product->id) }}"><button class="btn-add-to-cart" style="background-color:green">Back to product</button></a>
                    <button class="btn-add-to-cart" style="background-color:blue;" data-bs-toggle="modal" data-bs-target="#detailModal">More</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Product Variants</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tbody>
                        @if($product_variants->isEmpty()) 
                        <h4>None</h4>
                        @else
                        @foreach($product_variants as $variant)
                        <tr>
                            <th style="width: 35%; font-size: 18px; color: red;">Variant ID:</th>
                            <td id="variant_id">{{$variant->id }}</td>
                        </tr>
                        <tr>
                            <th>Variant Name:</th>
                            <td>{{$variant->variant_name }}</td>
                        </tr>
                        <tr>
                            <th>Variant Price:</th>
                            <td>{{$variant->variant_price}} VND</td>
                        </tr>
                        <tr>
                            <th>Stock Quantity:</th>
                            <td>{{$variant->stock_quantity }}</td>
                        </tr>
                        <tr>
                            <th>Expiration Date:</th>
                            <td>{{$variant->exp_date}}</td>
                        </tr>
                        <tr>
                            <th>Weight:</th>
                            <td> {{$variant->weight}} (kg)</td>
                        </tr>
                        <tr>
                            <th>Height:</th>
                            <td>{{$variant->height}} (cm)</td>
                        </tr>
                        <tr>
                            <th>Length:</th>
                            <td>{{$variant->length}} (cm)</td>
                        </tr>
                        <tr>
                            <th>Width:</th>
                            <td>{{$variant->width}} (cm)</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



@endsection