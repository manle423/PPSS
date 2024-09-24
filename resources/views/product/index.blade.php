@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Search Form -->
        <form action="{{ route('product.index') }}" method="GET" class="mb-4">
            <div class="input-group mb-2">
                <input type="text" name="search" class="form-control" placeholder="Search products..."
                    value="{{ request('search') }}">
                <div class="input-group-append">
                    <button class="btn btn-outline-primary" type="submit">Search</button>
                </div>
            </div>

            <!-- Category Dropdown -->
            <div class="form-group mb-2">
                <select name="category" class="form-control">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <!-- Product Listing -->
        <div>
            @foreach ($products as $product)
                <div class="card mb-3">
                    <div class="row no-gutters">
                        <div class="col-md-4">
                            <img src="{{ asset('storage/' . $product->image) }}" class="card-img" alt="{{ $product->name }}">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title">{{ $product->name }}</h5>
                                <p class="card-text">{{ $product->description }}</p>
                                <p class="card-text"><small class="text-muted">Price: ${{ $product->price }}</small></p>
                                <a href="{{ route('product.show', $product) }}" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pagination">
            {{ $products->links() }}
        </div>
    </div>
@endsection
