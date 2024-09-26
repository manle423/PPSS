<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminProductController extends Controller
{
    public function list()
    {
        $products = Product::whereNull('deleted_at')->paginate(10);
        return view('admin.products.list', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'variants.*.variant_name' => 'nullable|string|max:255',
            'variants.*.variant_price' => 'nullable|numeric|min:0',
            'variants.*.stock_quantity' => 'nullable|integer|min:0',
            'variants.*.exp_date' => 'nullable|date',
        ]);

        DB::beginTransaction();

        try {
            // Check if product is already exists
            $existingProduct = Product::where('name', $request->name)
                ->where('category_id', $request->category_id)
                ->whereNull('deleted_at')
                ->first();

            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'price' => $request->price,
                'stock_quantity' => $request->stock_quantity,
                'created_at' => now(),
            ]);

            if ($existingProduct) {
                throw new \Exception('This product already exists in the selected category.');
            }

            if ($request->has('variants')) {
                foreach ($request->variants as $variant) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'variant_name' => $variant['variant_name'],
                        'variant_price' => $variant['variant_price'],
                        'stock_quantity' => $variant['stock_quantity'],
                        'exp_date' => $variant['exp_date'],
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.products.create')->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            // dd($e);
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
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

        return redirect()->route('admin.products.list')->with('success', 'Product updated successfully!');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('admin.products.list')->with('success', 'Product deleted successfully.');
    }
}
