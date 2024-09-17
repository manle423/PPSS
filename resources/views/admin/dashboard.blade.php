{{-- trang thống kê của admin --}}
@extends('layouts.admin')

@section('content')
@php
    $user = auth()->user()->role;
@endphp

@if($user == 'ADMIN')
    <h1>Dashboard for admin</h1>
@else
    <h1>You are not authorized to access this page</h1>
@endif
@endsection