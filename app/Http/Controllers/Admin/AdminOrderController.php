<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function __construct(){
        $this->middleware('admin');
    }
    public function list(){
        return view('admin.orders.list');
    }
}
