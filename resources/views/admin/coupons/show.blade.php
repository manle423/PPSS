@extends('layouts.admin')

@section('content')
<!-- Custom Styles for Coupon Details -->
<link href="{{ asset('assets/vendor/css/coupon.css') }}" rel="stylesheet">

<div class="container-fluid pt-4 px-4">
    <div class="row g-4 justify-content-center">
        <div class="col-xl-5 col-lg-7 col-md-9">
            <div class="card shadow-lg border-0">
                <div class="card-header text-center bg-primary text-white py-3">
                    <h3 class="mb-0">Coupon Details</h3>
                </div>
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
                <div class="card-body p-4">
                    @if($coupon)
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th scope="row">Coupon Code:</th>
                                <td><span class="text-muted">{{ $coupon->code }}</span></td>
                            </tr>
                            <tr>
                                <th scope="row">Discount Value:</th>
                                <td><span class="text-muted">{{ $coupon->discount_value }}</span></td>
                            </tr>
                            <tr>
                                <th scope="row">Min Order Value:</th>
                                <td><span class="text-muted">{{ $coupon->min_order_value }}</span></td>
                            </tr>
                            <tr>
                                <th scope="row">Max Discount Value:</th>
                                <td><span class="text-muted">{{ $coupon->max_discount }}</span></td>
                            </tr>
                            <tr>
                                <th scope="row">Start Date:</th>
                                <td><span class="text-muted">{{ $coupon->start_date }}</span></td>
                            </tr>
                            <tr>
                                <th scope="row">End Date:</th>
                                <td><span class="text-muted">{{ $coupon->end_date }}</span></td>
                            </tr>
                            <tr>
                                <th scope="row">Status:</th>
                                <td>
                                    <span class="badge {{ $coupon->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $coupon->status == 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    @else
                    <div class="alert alert-warning text-center">
                        No coupon details found.
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-light d-flex justify-content-center py-3">
                  
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
