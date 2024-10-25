<?php

use Laravel\Dusk\Browser;

test('Change the quality of cart item', function () {
    $cartKey = $this->option('cartKey');
    $newAmount = $this->option('newAmount');
    $oldAmount = $this->option('oldAmount');
    // Get the cart item from the session
    $sessionCart = session()->get('cart');
    // Make sure the cart item quantity matches the old quantity
    $this->assertEquals($sessionCart[$cartKey], $oldAmount);
    // Emulate the form submission
    $this->browse(function (Browser $browser) use ($cartKey, $newAmount) {
        $browser->visit('/cart')
        ->type("@quantity_$cartKey",$newAmount) // Type the new quantity
        ->press("@quantity_btn_$cartKey") // Press the update button
        ->assertSee('Cart updated successfully'); // Check for success message
    });
    // Make sure the cart item quantity is changed to the new quantity
    $this->assertEquals($sessionCart[$cartKey], $newAmount);
});
