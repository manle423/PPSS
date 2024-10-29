<?php
// Take a random user

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\FeatureTestService;
uses(RefreshDatabase::class);



test('Cart page link works', function () {
    FeatureTestService::initiateData();
    $response = $this->get('/cart');
    $response->assertStatus(200)
        ->assertSee('Product')->assertSee('Variant')
        ->assertSee('In Stock')->assertSee('Quantity')
        ->assertSee('Total')->assertSee('Actions')
        ->assertSee('Subtotal');
});

test("Cart page showing cart content of guest (from session only)", function () {
    FeatureTestService::initiateData();
    $sessionCart = [];
    // Get a list of random products
    $products = Product::inRandomOrder()->take(5)->get();
    // Add the products with their associated variants to the session
    foreach ($products as $product) {
        $variant = ProductVariant::where('product_id', $product->id)->inRandomOrder()->first();
        $cartKey = $product->id . "-" . ($variant ? $variant->id : '');
        $sessionCart[$cartKey] = random_int(1, ($variant ? $variant->stock_quantity : $product->stock_quantity));
    }
    $response = $this->withSession(['cart' => $sessionCart])->get('/cart');
    $subtotal = 0.0;
    $response->assertStatus(200);
    // Check if the cart items are visible
    dump("Items in cart: " . count($sessionCart));
    foreach ($sessionCart as $cartKey => $amount) {
        // Get the product and variant from the cart item
        list($productId, $variantId) = explode('-', $cartKey);
        $product = Product::find($productId);
        $variant = $variantId ? ProductVariant::find($variantId) : null;

        $totalPrice = $amount * ($variant ? $variant->variant_price : $product->price);
        $subtotal += $totalPrice;

        $response->assertSee($product->name)
            ->assertSee($variant ? $variant->variant_name : '')
            ->assertSee(number_format($variant ? $variant->variant_price : $product->product_price, 0, ".", ","))
            ->assertSee(strval($amount))
            ->assertSee(number_format($totalPrice, 0, ".", ","));
    }
    $response->assertSee(number_format($subtotal, 0, ".", ","));
    $response->assertSee('Proceed Checkout');
});

// test("Cart page guest cart item quantity can be changed", function () {
//     $sessionCart = [];
//     // Get a list of random products
//     $products = Product::inRandomOrder()->take(2)->get();
//     // Add the products with their associated variants to the session
//     foreach ($products as $product) {
//         $variant = ProductVariant::where('product_id', $product->id)->inRandomOrder()->first();
//         $cartKey = $product->id . "-" . ($variant ? $variant->id : '');
//         $sessionCart[$cartKey] = random_int(1, ($variant ? $variant->stock_quantity : $product->stock_quantity));
//     }

//     // Put the sessionCart into cache (to send into Dusk test)
//     Cache::put('sessionCart', $sessionCart);

//     // Check the quantity input of the cart items
//     foreach ($sessionCart as $cartKey => $amount) {
//         // Get the product and variant from the cart item
//         list($productId, $variantId) = explode('-', $cartKey);
//         $product = Product::find($productId);
//         $variant = $variantId ? ProductVariant::find($variantId) : null;
//         $newAmount = random_int(1, ($variant ? $variant->stock_quantity : $product->stock_quantity));
        
//         // Put the variants into cache (to send into Dusk test)
//         Cache::put('cartKey', $cartKey, 10);
//         Cache::put('newAmount', $newAmount,10);
//         Cache::put('oldAmount', $amount,10);
        
//         // Trigger the Dusk test for changing quantity
//         exec("php artisan dusk --group=CartTest", $output, $exitCode);

//         // Check the exit code to determine the success of the Dusk test
//         if ($exitCode === 0) {
//             echo "Dusk test was successful\n";
//         } else {
//             echo "Dusk test failed\n";
//             // Optionally, you can also output the Dusk test output for further inspection
//              echo implode("\n", $output);
//         }

        
//     }
//     $response = $this->withSession(['cart' => $sessionCart])->get('/cart');
//     $response->assertStatus(200);
// });

test("Cart page showing cart content of user (from database)", function () {
    // Get a user from the database
    FeatureTestService::initiateData();
    $user = User::where('role',"BUYER")->first();
    // Get the cart items of the user
    $cartItems = Cart::where('user_id', $user->id);
    $subtotal = 0.0;
    $response = $this->actingAs($user)->get('/cart');
    $response->assertStatus(200);
    // Check if the cart items are visible
    foreach ($cartItems as $cartItem) {
        $totalPrice = $cartItem->amount * ($cartItem->variant? $cartItem->variant->variant_price : $cartItem->product->price);
        $subtotal += $totalPrice;
        $variantId = $cartItem->variant? strval($cartItem->variant->id) : '';
        $response->assertSee($cartItem->product->name)
        ->assertSee($cartItem->variant? $cartItem->variant->variant_name : '')
        ->assertSee(number_format($cartItem->variant? $cartItem->variant->variant_price : $cartItem->product->price, 0, ".", ","))
        ->assertSee(strval($cartItem->amount))
        ->assertSee(number_format($totalPrice, 0, ".", ","));
    }
    $response->assertSee(number_format($subtotal, 0, ".", ","));
    $response->assertSee('Proceed Checkout');
});