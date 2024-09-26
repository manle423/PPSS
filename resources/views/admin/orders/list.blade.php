@extends('layouts.admin')
@section('content')
<link href="{{ asset('assets/vendor/css/orderlist.css') }}" rel="stylesheet">

<div class="order-list-container">
        <h1>Danh sách đơn hàng</h1>
        <table class="order-table">
            <thead>
                <tr>
                    <th>Order code</th>
                    <th>Name</th>
                    <th>Ordered date</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>001</td>
                    <td>Nguyễn Văn A</td>
                    <td>20/09/2024</td>
                    <td><span class="status pending">Đang xử lý</span></td>
                    <td>1.000.000 VNĐ</td>
                    <td><button class="action-btn">Chi tiết</button></td>
                </tr>
                <tr>
                    <td>002</td>
                    <td>Trần Thị B</td>
                    <td>18/09/2024</td>
                    <td><span class="status completed">Hoàn thành</span></td>
                    <td>2.500.000 VNĐ</td>
                    <td><button class="action-btn">Chi tiết</button></td>
                </tr>
                <tr>
                    <td>003</td>
                    <td>Phạm Văn C</td>
                    <td>15/09/2024</td>
                    <td><span class="status canceled">Đã hủy</span></td>
                    <td>500.000 VNĐ</td>
                    <td><button class="action-btn">Chi tiết</button></td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection