<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('buyerOrGuest');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('webshop.home');
    }

    public function shop()
    {
        return view('webshop.shop');
    }

    public function shopDetail()
    {
        return view('webshop.shop-detail');
    }

    public function cart()
    {
        return view('webshop.cart');
    }

    public function checkout()
    {
        return view('webshop.checkout');
    }
}
