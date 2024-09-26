@extends('layouts.admin')
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Category List</h2>
            <a href="{{ route('admin.category.create') }}" class="btn btn-primary">Add Category</a>
        </div>
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
                    <th>Category Name</th>
                    <th>Description</th>
                    <th>Created at</th>
                    <th>Updated at</th>
                    <th>Deleted at</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->description }}</td>
                        <td>{{ $category->created_at }}</td>
                        <td>{{ $category->updated_at }}</td>
                        <td>{{ $category->deleted_at }}</td>
                        <td><a href="{{ route('admin.category.edit', $category->id) }}">Edit</a> || <a
                                href="{{ route('admin.category.delete', $category->id) }}">Delete</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="table-info">
            <span>Showing 1 to 5 of 5 entries</span>
        </div>
        <div class="pagination">
            {{ $categories->links() }}
        </div>
    </div>
@endsection