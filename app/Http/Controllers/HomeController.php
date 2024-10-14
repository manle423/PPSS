<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
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
    public function index(Request $request)
    {
        // Start with a base query for products
        $query = Product::query();

        // Get 3 random categories
        $categories = Category::inRandomOrder()->limit(3)->get();

        // Filter products by selected category
        if ($request->filled('category_id')) {
            $categoryId = $request->get('category_id');
            $query->where('category_id', $categoryId);
        }

        // Get the 8 latest products
        $latestProductsAll = $query->latest()->limit(8)->get();

        // Get the 8 latest products of each category
        $latestProductsCategories = [];
        $index = 0;
        foreach ($categories as $category) {
            $latestProductsCategories[$index] = $category->products()->latest()->limit(8)->get();
            $index += 1;
        }

        // Get 8 most popular products
        $popularProducts = Product::query()
            ->withCount('orders')
            ->orderBy('orders_count', 'desc')->limit(8)->get();
        
        // Get 8 most popular products of each category
        $popularProductsCategories = [];
        $index = 0;
        foreach ($categories as $category) {
            $popularProductsCategories[$index] = $category->products()
                ->withCount('orders')
                ->orderBy('orders_count', 'desc')->limit(8)->get();
            $index += 1;
        }

        // Return the view with the data
        return view('home', compact('categories', 'latestProductsAll','latestProductsCategories','popularProducts','popularProductsCategories'));
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

    public function contact()
    {
        return view('webshop.contact');
    }

    public function aboutUs(){
        return view('webshop.about-us');
    }

    public function privacyPolicy(){
        return view('webshop.privacy-policy');
    }
}
