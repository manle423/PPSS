<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class CheckoutController extends Controller
{
    public function index()
    {
        // Cart stored in session
        $sessionCart = session()->get('cart', []);
        // Subtotal stored in session
        $subtotal = session()->get('subtotal');

        // CartItems stored in session
        $cartItems = session()->get('cartItems');

        return view('webshop.checkout', compact('sessionCart','subtotal', 'cartItems'));
    }


    public function __construct()
    {
        $this->middleware('buyerOrGuest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }
}
