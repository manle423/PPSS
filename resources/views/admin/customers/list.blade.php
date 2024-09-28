@extends('layouts.admin')
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Customers List</h2>
           
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
                    <th>User name</th>
                    <th>Full name</th>
                    <th>Email</th>
                    <th>Phone number</th>
                    <th>Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->user_name}}</td>
                        <td>{{ $user->full_name}}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone_number}}</td>
                        <td>{{ $user->address}}</td>
                        <td>  
                        <a href="{{ route('admin.customers.detail', $user->id) }}"> <i class="fas fa-eye"></i></a> || 
                      
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="table-info">
            <span>Showing 1 to 5 of 5 entries</span>
        </div>
        <div class="pagination">
            {{ $users->links() }}
        </div>
    </div>
@endsection
