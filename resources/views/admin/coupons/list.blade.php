@extends('layouts.admin')
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Coupons List</h2>
            <a href="{{ route('admin.coupon.create') }}" class="btn btn-primary">Create coupon</a>
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
        <div class="table-controls">
        </div>
        <table class="brand-table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Code</th>
                    <th>Discount value</th>
                    <th>Min order value</th>
                    <th>Max discount value</th>
                    <th>Start date</th>
                    <th>End date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($coupons as $coupon)
                    <tr>
                        <td>{{ $coupon->id }}</td>
                        <td>{{ $coupon->code }}</td>
                        <td>{{ $coupon->discount_value }}</td>
                        <td>{{ $coupon->min_order_value }}</td>
                        <td>{{ $coupon->max_discount }}</td>
                        <td>{{ $coupon->start_date }}</td>
                        <td>{{$coupon->end_date }}</td>
                        <td>{{$coupon->status }}</td>
                        <td>  
                        <a href="{{ route('admin.coupon.detail', $coupon->id) }}"> <i class="fas fa-eye"></i></a> || 
                        <a href="{{ route('admin.coupon.edit', $coupon->id) }}"> <i class="fas fa-edit"></i></a> || 
                        <form action="{{ route('admin.coupon.delete', $coupon->id) }}" method="POST" style="display: inline-block;">
                         @csrf
                         @method('POST')
                         <button type="submit" class="btn btn-sm"  onclick="return confirm('Are you sure you want to delete this category?')"> <i class="fas fa-trash"></i></button>
                        </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="table-info">
            <span>Showing 1 to 5 of 5 entries</span>
        </div>
        <div class="pagination">
            {{ $coupons->links() }}
        </div>
    </div>
@endsection