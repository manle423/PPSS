@extends('layouts.shop')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success</title>
    <script src="https://www.paypal.com/sdk/js?client-id={{ env('PAYPAL_SANDBOX_CLIENT_ID') }}"></script>
</head>

<body>
    <div class="container mt-5">
        <div class="alert alert-success">
            <h4 class="alert-heading">Order Success!</h4>
            <p>Your transaction was completed successfully.</p>
            <hr>
            <p class="mb-0">Thank you for your purchase.</p>
        </div>

        <h4>Order Details</h4>
        {{-- <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orderItems as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>{{ $item['price'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table> --}}

        <a href="{{ route('home') }}" class="btn btn-primary mt-3">Continue Shopping</a>
    </div>
</body>

</html>
