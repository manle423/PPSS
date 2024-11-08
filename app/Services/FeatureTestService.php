<?php
namespace App\Services;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StoreInfo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

uses(RefreshDatabase::class);

class FeatureTestService
{
    public static function initiateData()
    {
        // Create test data
        $user = User::factory()->create();
        // Categories
        $categories = Category::factory()->count(3)->create();
        foreach ($categories as $category) {
            // Products within each category
            $products = Product::factory()->count(5)->create([
                'category_id' => $category->id,
            ]);
            foreach ($products as $product) {
                // Product variants within each product
                ProductVariant::factory()->count(3)->create([
                    'product_id' => $product->id,
                ]);
            }
        }
        // Cart items of user
        $cart = Cart::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);
        // Put cart items in session
        $sessionCart = [];
        foreach ($cart as $item) {
            $cartKey = $item->product->id. '-'.($item->variant_id? $item->variant_id : '');
            $sessionCart[$cartKey] = $item->quantity;
        }
        // Total price for the cart
        $subtotal = 0;
        // Loop through session cart items to create cart items
        foreach ($sessionCart as $cartKey => $amount) {
            list($productId, $variantId) = explode('-', $cartKey);

            $product = Product::find($productId);
            $variant = $variantId ? ProductVariant::find($variantId) : null;

            if ($product) {
                $cartItems[] = (object)[
                    'product' => $product,
                    'variant' => $variant,
                    'quantity' => $amount,
                    
                ];
                $subtotal += $amount * ($variant ? $variant->variant_price : $product->price);
            }
        }
        // Save the subtotal to session
        session()->put('subtotal', $subtotal);
        session()->put('cart', $sessionCart);
        session()->put('cartItems', $cartItems);
        // Store information
        StoreInfo::create([
            'name'  => 'Test Store',
            'description' => 'Lorem Ipsum',
            'phone'  => '1234567890',
            'email' => 'test@example.com',
            'address' => 'Test Address',
        ]);
        // Seed the database with location data
        Artisan::call('db:seed', ['--class' => 'LocationSeeder']);
    }
}
