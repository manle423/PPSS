<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get all categories for the dropdown
        $categories = Category::all();

        // Start with a base query for products
        $query = Product::query();

        // Search by product name or description
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        // Filter by category if selected
        // if ($categoryId = $request->input('category')) {
        //     $query->where('category_id', $categoryId);
        // }
        // Filter by category if selected
        if ($request->has('categories')) {
            $query->whereIn('category_id', $request->input('categories'));
        }

        // Filter by price range
        if ($minPrice = $request->input('min_price')) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice = $request->input('max_price')) {
            $query->where('price', '<=', $maxPrice);
        }

        // Paginate the results or get them all
        $products = $query->paginate(9);

        //return view('product.index', compact('products', 'categories'));
        return view('product.shop', compact('products', 'categories'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Get all variants of the product
        $query = ProductVariant::query();
        $query->where('product_id', $product->id);
        $variants = $query->get();
        return view('product.show', ['product' => $product, 'variants' => $variants]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
