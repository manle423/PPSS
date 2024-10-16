@extends('layouts.admin')

@section('content')
<link href="{{ asset('assets/vendor/css/change-pass.css') }}" rel="stylesheet">
<h2>Reset Your Password</h2>

@if (session('status'))
    <p class="status-message">{{ session('status') }}</p>
@endif

@if ($errors->any())
    <ul class="error-list">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form class="change-pass"method="POST" action="{{ route('admin.password.update') }}">
    @csrf

    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
    </div>

    <div class="form-group">
        <label for="password">New Password:</label>
        <input type="password" id="password" name="password" required>
    </div>

    <div class="form-group">
        <label for="password_confirmation">Confirm Password:</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required>
    </div>

    <button type="submit" class="btn-submit">Reset Password</button>
</form>
@endsection
