<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\GuestOrder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AdminOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function list(Request $request)
    {
        $userOrdersQuery = Order::with('user');
        $guestOrdersQuery = GuestOrder::query();

        // Order code search
        if ($request->filled('order_code')) {
            $orderCode = $request->input('order_code');
            $userOrdersQuery->where('order_code', $orderCode);
            $guestOrdersQuery->where('order_code', $orderCode);
        }

        // Date range filter
        if ($request->filled(['start_date', 'end_date'])) {
            $userOrdersQuery->whereBetween('order_date', [$request->start_date, $request->end_date]);
            $guestOrdersQuery->whereBetween('order_date', [$request->start_date, $request->end_date]);
        }

        // Customer type filter
        if ($request->filled('customer_type')) {
            if ($request->customer_type === 'registered') {
                $guestOrdersQuery->whereRaw('1 = 0'); // No guest orders
            } elseif ($request->customer_type === 'guest') {
                $userOrdersQuery->whereRaw('1 = 0'); // No user orders
            }
        }

        // Sorting
        $sortField = $request->input('sort', 'order_date');
        $sortDirection = $request->input('direction', 'desc');

        $userOrders = $userOrdersQuery->get();
        $guestOrders = $guestOrdersQuery->get();

        // Combine and sort
        $allOrders = $userOrders->concat($guestOrders)->sortBy($sortField, SORT_REGULAR, $sortDirection === 'desc');

        // Pagination
        $perPage = 10;
        $page = $request->input('page', 1);
        $total = $allOrders->count();

        $paginatedOrders = new LengthAwarePaginator(
            $allOrders->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.orders.list', ['orders' => $paginatedOrders]);
    }

    public function show($id)
    {
        $order = Order::with(['user', 'shippingAddress.ward.district.province', 'orderItems.item'])->findOrFail($id);
        return view('admin.orders.detail', compact('order'));
    }

    public function detailGuestOrder($id)
    {
        $order = GuestOrder::with(['orders', 'orderItems.item'])->findOrFail($id);
        return view('admin.orders.detail-guest-order', compact('order'));
    }
}
