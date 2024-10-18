@extends('layouts.admin')
@section('content')
<style>
    .inactive-coupon {
        background-color: #f8d7da; 
        color: #721c24; 
    }
    .custom-dropdown {
        position: relative;
        display: inline-block;
    }
    .custom-dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
    }
    .custom-dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }
    .custom-dropdown-content a:hover {background-color: #f1f1f1}
    .custom-dropdown:hover .custom-dropdown-content {
        display: block;
    }
</style>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Coupons List</h2>
            <div>
                <a href="{{ route('admin.coupon.create') }}" class="btn btn-primary">Create coupon</a>
                <div class="custom-dropdown">
                    <button class="btn btn-secondary">More Actions</button>
                    <div class="custom-dropdown-content">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#importModal">Import Coupons</a>
                        <a href="#" id="bulkActivateBtn">Bulk Activate</a>
                        <a href="#" id="bulkDeactivateBtn">Bulk Deactivate</a>
                        <a href="#" id="bulkDeleteBtn">Bulk Delete</a>
                    </div>
                </div>
            </div>
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

        <form action="{{ route('admin.coupon.list') }}" method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by code" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>

        <form action="{{ route('admin.coupon.bulk-action') }}" method="POST" id="bulkActionForm">
            @csrf
            <input type="hidden" name="action" id="bulkAction">
            <table class="brand-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
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
                        <tr class="{{ $coupon->status == 0 ? 'inactive-coupon' : '' }}">
                            <td><input type="checkbox" name="ids[]" value="{{ $coupon->id }}" class="coupon-checkbox"></td>
                            <td>{{ $coupon->code }}</td>
                            <td>{{ $coupon->discount_value }}</td>
                            <td>{{ $coupon->min_order_value }}</td>
                            <td>{{ $coupon->max_discount }}</td>
                            <td>{{ $coupon->start_date }}</td>
                            <td>{{ $coupon->end_date }}</td>
                            <td>{{ $coupon->status == 1 ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <a href="{{ route('admin.coupon.detail', $coupon->id) }}">
                                    <i class="fas fa-eye"></i>
                                </a> ||
                                <a href="{{ route('admin.coupon.edit', $coupon->id) }}">
                                    <i class="fas fa-edit"></i>
                                </a> ||
                                <form action="{{ route('admin.coupon.delete', $coupon->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="btn btn-sm" onclick="return confirm('Are you sure you want to delete this coupon?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </form>

        <div class="table-info">
            <span>
                Showing {{ $coupons->firstItem() }} to {{ $coupons->lastItem() }} of {{ $coupons->total() }} entries
            </span>
        </div>
        <div class="pagination">
            {{ $coupons->links() }}
        </div>

        <!-- Import Modal -->
        <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Coupons</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.coupon.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="file" class="form-label">Excel File</label>
                                <input type="file" class="form-control" id="file" name="file" required>
                            </div>
                            <div class="mb-3">
                                <a href="{{ route('admin.coupon.export.template') }}" class="btn btn-secondary">Download Template</a>
                            </div>
                            <div class="alert alert-info">
                                <h6>Import Notes:</h6>
                                <ul>
                                    <li>The 'code' must be unique. Duplicate codes will be skipped.</li>
                                    <li>'discount_value' should be a decimal between 0 and 1 (e.g., 0.1 for 10% discount).</li>
                                    <li>'min_order_value' and 'max_discount' should be in VND.</li>
                                    <li>Dates should be in the format YYYY-MM-DD.</li>
                                    <li>'status' should be 1 for Active or 0 for Inactive.</li>
                                    <li>All fields are required.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Import</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('select-all').addEventListener('change', function() {
                var checkboxes = document.getElementsByClassName('coupon-checkbox');
                for (var checkbox of checkboxes) {
                    checkbox.checked = this.checked;
                }
            });

            function performBulkAction(action, confirmMessage) {
                if (confirm(confirmMessage)) {
                    document.getElementById('bulkAction').value = action;
                    document.getElementById('bulkActionForm').submit();
                }
            }

            document.getElementById('bulkActivateBtn').addEventListener('click', function(e) {
                e.preventDefault();
                performBulkAction('activate', 'Are you sure you want to activate the selected coupons?');
            });

            document.getElementById('bulkDeactivateBtn').addEventListener('click', function(e) {
                e.preventDefault();
                performBulkAction('deactivate', 'Are you sure you want to deactivate the selected coupons?');
            });

            document.getElementById('bulkDeleteBtn').addEventListener('click', function(e) {
                e.preventDefault();
                performBulkAction('delete', 'Are you sure you want to delete the selected coupons?');
            });
        });
    </script>
@endsection
