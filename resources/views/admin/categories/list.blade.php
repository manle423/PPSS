@extends('layouts.admin')
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Category List</h2>
            <div>
                <a href="{{ route('admin.category.create') }}" class="btn btn-primary">Add Category</a>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">Import Categories</button>
                <button type="button" class="btn btn-danger" id="bulkDeleteBtn">Bulk Delete</button>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('admin.category.list') }}" method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by name or description" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>

        <form id="bulkActionForm" action="{{ route('admin.category.bulk-action') }}" method="POST">
            @csrf
            <input type="hidden" name="action" value="delete">
            <table class="brand-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td><input type="checkbox" name="ids[]" value="{{ $category->id }}" class="category-checkbox"></td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->description }}</td>
                            <td class="d-flex">
                                <a href="{{ route('admin.category.edit', $category->id) }}" class="btn btn-sm  mr-2"><i class="fas fa-edit"></i> Edit</a>
                                <form action="{{ route('admin.category.delete', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?')">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="btn btn-sm "><i class="fas fa-trash"></i> Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </form>

        <div class="table-info">
            <span>Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} of {{ $categories->total() }} entries</span>
        </div>
        <div class="pagination">
            {{ $categories->links() }}
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Categories</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.category.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Excel File</label>
                            <input type="file" class="form-control" id="file" name="file" required>
                        </div>
                        <div class="mb-3">
                            <a href="{{ route('admin.category.export.template') }}" class="btn btn-secondary">Download Template</a>
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

    <script>
        document.getElementById('select-all').addEventListener('change', function() {
            var checkboxes = document.getElementsByClassName('category-checkbox');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        });

        document.getElementById('bulkDeleteBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to delete the selected categories?')) {
                document.getElementById('bulkActionForm').submit();
            }
        });
    </script>
@endsection
