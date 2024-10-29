<?php

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Cache;
use Laravel\Dusk\Browser;

test('Change the quality of cart item', function () {
   

    $cartKey = Cache::get('cartKey');
    $newAmount = Cache::get('newAmount');
    $oldAmount = Cache::get('oldAmount');
    
    $sessionCart = [];
    // Get a list of random products
    $products = Product::inRandomOrder()->take(5)->get();
    // Add the products with their associated variants to the session
    foreach ($products as $product) {
        $variant = ProductVariant::where('product_id', $product->id)->inRandomOrder()->first();
        $cartKey = $product->id . "-" . ($variant ? $variant->id : '');
        $sessionCart[$cartKey] = random_int(1, ($variant ? $variant->stock_quantity : $product->stock_quantity));
    }
    // Emulate the form submission
    $this->browse(function (Browser $browser) use ($cartKey, $newAmount) {
        $browser->visit('cart.update-session')
        ->type("@quantity_$cartKey",$newAmount) // Type the new quantity
        ->press("@quantity_btn_$cartKey") // Press the update button
        ->assertSee('Cart not updated successfully'); // Check for success message
    });

 
});
