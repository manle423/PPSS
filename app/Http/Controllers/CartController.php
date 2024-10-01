<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Laravel\Prompts\alert;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Start with a base query for products
        $query = Cart::query();

        // Get all categories for the dropdown
        $categories = Category::all();

        $sessionCart = session()->get('cart', []);
        $subtotal = 0.0;
        // Search by product name or description
        if ($search = $request->input('search')) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category if selected
        if ($categoryId = $request->input('category')) {
            $query->whereHas('product', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        $cartItems = [];

        // Filter cart items by the authenticated user if logged in
        if (Auth::check()) {
            $query->where('user_id', Auth::id());
            $cartItems = $query->with('product')->get();
         

            foreach ($cartItems as $item) {
                $subtotal += $item->quantity * $item->product->price;
            }
        }

        // Get cart items from session if the user is not logged in

        else if (!Auth::check()) {


            // Loop through session cart items to create cart items
            foreach ($sessionCart as $cartKey => $amount) {
                list($productId, $variantId) = explode('-', $cartKey);

                $product = Product::find($productId);
                $variant = $variantId ? ProductVariant::find($variantId) : null;

                if ($product) {
                    $cartItems[] = (object)[
                        'product' => $product,
                        'variant' => $variant,
                        'quantity' => $amount
                    ];
                }
            }
        }

        return view('cart.cart', compact('cartItems', 'categories', 'sessionCart','subtotal'));
        //return view('cart.index', compact('cartItems', 'categories','sessionCart'));
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
    public function store(Request $request, $productId)
    {

        // Find the product
        $product = Product::findOrFail($productId);

        // Validate the incoming request
        $request->validate([
            'amount' => 'required|integer|min:1|max:' . $product->stock_quantity,
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);
        // Get the product variant (if there is one)
        $variantId = $request->input('variant_id');

        if ($variantId) {
            $variant = ProductVariant::findOrFail($variantId);
            // Check if the selected variant is available
            if ($variant->stock_quantity < $request->input('amount')) {
                return redirect()->route('cart.index')->with('error', 'Selected variant is out of stock!');
            }
        }
        // Check if the user is logged in
        if (Auth::check()) {
            // Check if the product is already in the user's cart
            $cartItem = Cart::where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->first();

            if ($cartItem) {
                // Update the quantity if the item is already in the cart
                $cartItem->quantity += $request->input('amount');
                $cartItem->save();
            } else {
                // Create a new cart item
                $cart = Cart::create([
                    'user_id' => Auth::id(),
                    'product_id' => $productId,
                    'quantity' => $request->input('amount'),
                    'added_at' => now(),
                    'variant_id' => $variantId,
                ]);
            }
        } else {
            // Get the cart items from the session, if any
            $sessionCart = session()->get('cart', []);

            $cartKey = $productId . '-' . $variantId;

            if (array_key_exists($cartKey, $sessionCart)) {
                // Update quantity in session cart
                $sessionCart[$cartKey] += $request->input('amount');
            } else {
                // Add new item to session cart
                $sessionCart[$cartKey] = $request->input('amount');
            }

            // Update the session with the modified cart
            session()->put('cart', $sessionCart);
        }
        // Redirect back with a success message
        return redirect()->route('cart.index')->with('success', 'Product added to cart successfully!');
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, $id)
    {
        // Find the cart item by its ID
        $cartItem = Cart::findOrFail($id);

        // Validate the quantity input
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Update the quantity
        $cartItem->quantity = $request->input('quantity');
        $cartItem->save();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Cart updated successfully.');
    }


    /**
     * Update the specified resource in session.
     */
    public function updateSession(Request $request, $cartKey)
    {
        // Get the cart item from the session
        $sessionCart = session()->get('cart', []);

        // Validate the quantity input
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $sessionCart[$cartKey],
        ]);

        // Update the quantity in session cart
        $sessionCart[$cartKey] = $request->input('quantity');

        // Update the session with the modified cart
        session()->put('cart', $sessionCart);
        // Redirect back with a success message
        return redirect()->back()->with('success', 'Cart updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the cart item by its ID and delete it
        $cartItem = Cart::findOrFail($id);
        $cartItem->delete();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Item removed from the cart.');
    }
}
