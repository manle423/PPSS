@extends('layouts.app')

@section('content')
    <div class="container">
        <div>
            @foreach ($products as $product)
            <div class="card mb-3">
                <div class="row no-gutters">
                    <div class="col-md-4">
                        <img src="{{ asset('storage/'. $product->image) }}" class="card-img" alt="...">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title">{{$product->name}}</h5>
                            <p class="card-text">{{$product->description}}</p>
                            <p class="card-text"><small class="text-muted">Price: ${{$product->price}}</small></p>
                            <a href="{{route('product.show', $product)}}" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <br>
        {{-- <div >
            Latest products: {{$latestProducts}}
        </div> --}}
        {{--Pagination--}}
        <div class="pagination">
            {{$products->links()}}
        </div>
    </div>
    
@endsection