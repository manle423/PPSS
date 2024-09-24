@extends('layouts.shop-page')
@section('content')
<div class="container-fluid pt-4 px-4 d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="row g-4 w-100 justify-content-center">
        <div class="col-sm-12 col-xl-8">
            <div class="bg-light rounded h-100 p-4">
                <h6 class="mb-4 text-center">Edit Category</h6>
                <form method="POST" action="{{ route('shop.updateCate', $category->id) }}">
                    @csrf
                  
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Id:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="id" value="{{ $category->id }}" readonly required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Name:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="name" value="{{ $category->name }}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Description:</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="description" rows="3" required>{{ $category->description }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
