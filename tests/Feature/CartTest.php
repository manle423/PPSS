<?php
// Take a random user



use App\Models\Product;
use App\Models\ProductVariant;
use Laravel\Dusk\Browser;


test('Cart page link works', function () {
    $response = $this->get('/cart');
    $response->assertStatus(200)
        ->assertSee('Product')->assertSee('Variant')
        ->assertSee('In Stock')->assertSee('Quantity')
        ->assertSee('Total')->assertSee('Actions')
        ->assertSee('Subtotal');
});

test("Cart page showing cart content of guest (from session only)", function () {
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
});

test("Cart page guest cart item quantity can be changed", function () {
    $sessionCart = [];
    // Get a list of 3 random products
    $products = Product::inRandomOrder()->take(3)->get();
    // Add the products with their associated variants to the session
    foreach ($products as $product) {
        $variant = ProductVariant::where('product_id', $product->id)->inRandomOrder()->first();
        $cartKey = $product->id . "-" . ($variant ? $variant->id : '');
        $sessionCart[$cartKey] = random_int(1, ($variant ? $variant->stock_quantity : $product->stock_quantity));
    }
    // Check if the route can handle the session cart
    $response = $this->withSession(['cart' => $sessionCart])->get('/cart');
    $response->assertStatus(200);

    // Check the quantity input of the cart items
    foreach ($sessionCart as $cartKey => $amount) {
        // Get the product and variant from the cart item
        list($productId, $variantId) = explode('-', $cartKey);
        $product = Product::find($productId);
        $variant = $variantId ? ProductVariant::find($variantId) : null;
        $newAmount = random_int(1, ($variant ? $variant->stock_quantity : $product->stock_quantity));

        // Trigger the Dusk test for changing quantity
        exec("php artisan dusk --group=CartTest --cartKey=$cartKey --newAmount=$newAmount --oldAmount=$amount" );
    }
});
