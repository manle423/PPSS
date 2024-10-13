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

        // Cart stored in session
        $sessionCart = session()->get('cart', []);

        // Total price for the cart
        $subtotal = 0;

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
            // Check if the query is not empty
            if ($query->count()) {
                $cartItems = $query->with('product')->get();
                foreach ($cartItems as $item) {
                    $subtotal += $item->quantity * (optional($item->variant)->variant_price ?? $item->product->price);
                    $variantId = $item->variant ? strval($item->variant->id) : '';
                    $cartKey = $item->product->id . '-' . $variantId;
                    $sessionCart[$cartKey] = $item->quantity;
                }
                // Update the session with the cart items from the database
                session()->put('cart', $sessionCart);
            } else {
                // Create cart items on the database based on the session data
                foreach ($sessionCart as $cartKey => $amount) {
                    list($productId, $variantId) = explode('-', $cartKey);

                    $product = Product::find($productId);
                    $variant = $variantId ? ProductVariant::find($variantId) : null;

                    if ($product) {
                        Cart::create([
                            'user_id' => Auth::id(),
                            'product_id' => $product->id,
                            'variant_id' => $variantId,
                            'quantity' => $amount
                        ]);
                    }
                }
                $cartItems = $query->with('product')->get();
            }
        }

        // Get cart items from session 
        else {
            
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
                    $subtotal += $amount * ($variant ? $variant->variant_price : $product->price);
                }
            }
        }

        // Save the subtotal to session
        session()->put('subtotal', $subtotal);

        // Save cartItems to session
        session()->put('cartItems', $cartItems);

        return view('cart.cart', compact('cartItems', 'categories', 'sessionCart', 'subtotal'));
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
        
        // Get the cart items from the session, if any
        $sessionCart = session()->get('cart', []);

        // Validate the incoming request
        $request->validate([
            'amount' => 'required|integer|min:1|',
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
                // Create a new cart item in database
                $cart = Cart::create([
                    'user_id' => Auth::id(),
                    'product_id' => $productId,
                    'quantity' => $request->input('amount'),
                    'added_at' => now(),
                    'variant_id' => $variantId,
                ]);
            }
        }
        
        // Get the cart items from the session, if any
        $sessionCart = session()->get('cart', []);

        $cartKey = $productId . '-' . $variantId;

        if (array_key_exists($cartKey, $sessionCart)) {
            // Update quantity in session cart
            $sessionCart[$cartKey] += number_format($request->input('amount'));
        } else {
            // Add new item to session cart
            $sessionCart[$cartKey] = number_format($request->input('amount'));
        }
        // Update the session with the modified cart
        session()->put('cart', $sessionCart);

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
    public function update(Request $request, $cartKey, $id)
    {
 
        // Find the cart item by its ID
        $cartItem = Cart::findOrFail($id);

        // Validate the quantity input
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Check if the cart item id exists
        if ($cartItem) {
            // Update the quantity
            $cartItem->quantity = $request->input('quantity');
            $cartItem->save();
        }

        // Get the cart item from the session
        $sessionCart = session()->get('cart', []);

        // Update the quantity in session cart
        $sessionCart[$cartKey] = $request->input('quantity');

        // Update the session with the modified cart
        session()->put('cart', $sessionCart);
        

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
            'quantity' => 'required|integer|min:1',
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
    public function destroy($cartKey, $id)
    {
        // Find the cart item by its ID and delete it
        $cartItem = Cart::findOrFail($id);
        $cartItem->delete();

        // Get the cart item from the session
        $sessionCart = session()->get('cart', []);

        // Remove the item from session cart
        unset($sessionCart[$cartKey]);

        // Update the session with the modified cart
        session()->put('cart', $sessionCart);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Item removed from the cart.');
    }

    /**
     * Remove the specified resource from session.
     */
    public function destroySession($cartKey)
    {
        // Get the cart item from the session
        $sessionCart = session()->get('cart', []);

        // Remove the item from session cart
        unset($sessionCart[$cartKey]);

        // Update the session with the modified cart
        session()->put('cart', $sessionCart);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Item removed from the cart.');
    }
}