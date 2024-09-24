<?php

namespace App\Http\Controllers\Shop;

use App\Models\Product;
use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShopProductController extends Controller
{
    public function index()
    {
        $products = Product::whereNull('deleted_at')->paginate(10);
        return view('shop-page.list-product', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('shop-page.add-product', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $existingProduct = Product::where('name', $request->name)
                                  ->where('category_id', $request->category_id)
                                  ->whereNull('deleted_at')
                                  ->first();

        if ($existingProduct) {
            return redirect()->back()->withErrors(['name' => 'This product already exists in the selected category.']);
        }

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'stock_quantity' => $request->stock_quantity,
            'created_at' => now(),
        ]);

        return redirect()->route('products.create')->with('success', 'Product created successfully.');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('shop-page.edit-product', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($id);
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'stock_quantity' => $request->stock_quantity,
            'updated_at' => now(),
        ]);

        return redirect()->route('shop.listPro')->with('success', 'Product updated successfully!');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('shop.listPro')->with('success', 'Product deleted successfully.');
    }
}
