@extends('layouts.admin')

@section('content')
<div class="container-fluid pt-4 px-4 d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="row g-4 w-100 justify-content-center">
        <div class="col-sm-12 col-xl-8">
            <div class="bg-light rounded h-100 p-4">
                <h6 class="mb-4 text-center">Edit Coupon</h6>

                <!-- Hiển thị lỗi -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.coupon.update', $coupon->id) }}">
                    @csrf
                    @method('POST')
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Code:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="code" name="code" value="{{ old('code', $coupon->code) }}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Discount value:</label>
                        <div class="col-sm-10">
                            <input type="number" step="0.01" class="form-control" id="discount_value" name="discount_value" value="{{ old('discount_value', $coupon->discount_value) }}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Max discount:</label>
                        <div class="col-sm-10">
                            <input type="number" step="0.01" class="form-control" id="max_discount" name="max_discount" value="{{ old('max_discount', $coupon->max_discount) }}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Min order value:</label>
                        <div class="col-sm-10">
                            <input type="number" step="0.01" class="form-control" id="min_order_value" name="min_order_value" value="{{ old('min_order_value', $coupon->min_order_value) }}" required>
                        </div>
                    </div>
                   
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Start date:</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', $coupon->start_date) }}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">End date:</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date', $coupon->end_date) }}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Status:</label>
                        <div class="col-sm-10">
                            <div>
                                <input type="radio" id="active" name="status" value="1" {{ old('status', $coupon->status) == 1 ? 'checked' : '' }}>
                                <label for="active">Active</label>
                            </div>
                            <div>
                                <input type="radio" id="inactive" name="status" value="0" {{ old('status', $coupon->status) == 0 ? 'checked' : '' }}>
                                <label for="inactive">Inactive</label>
                            </div>
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
