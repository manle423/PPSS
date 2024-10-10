<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class AdminOrderController extends Controller
{
    public function __construct(){
        $this->middleware('admin');
    }
    public function index() {
        dd("index");
    }

    public function list()
    {
        $orders = Order::paginate(perPage:10);
        if($orders == null) return view (view:'admin.orders.list');
        return view(view:'admin.orders.list', data:compact(var_name: 'orders'));
    }
    public function show($id){
        $order=Order::with('user')->findOrFail($id);
        return view('admin.orders.detail',compact('order'));
        
    }
}

