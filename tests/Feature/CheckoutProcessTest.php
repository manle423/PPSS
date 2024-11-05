<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\StoreInfo;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckoutProcessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        StoreInfo::create([
            'name'  => 'Test Store',
            'description' => 'Lorem Ipsum',
            'phone'  => '1234567890',
            'email' => 'test@example.com',
            'address' => 'Test Address',
        ]);
        
        // Seed the database with location data
        $this->artisan('db:seed', ['--class' => 'LocationSeeder']);
    }

    public function test_complete_checkout_process()
    {
        // Step 1: Register an account
        $user = $this->registerAccount();

        // Step 2: Add a product to the cart
        $product = $this->addToCart($user);

        // Step 3: Add a shipping address
        $address = $this->addShippingAddress($user);

        // Step 4: Process payment
        // $order = $this->processPayment($user, $product, $address);

        // // Step 5: Verify order creation
        // $this->assertOrderCreated($order, $user, $product, $address);
    }

    private function registerAccount()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    private function addToCart(User $user)
    {
        $product = Product::factory()->create();
        $this->post(route('cart.store', $product->id));
        return $product;
    }

    private function addShippingAddress(User $user)
    {
        $address = Address::factory()->create(['user_id' => $user->id]);
        $this->post(route('user.add-address'), $address->toArray());
        return $address;
    }

    private function processPayment(User $user, Product $product, Address $address)
    {
        $response = $this->post(route('checkout.process'), [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'address_id' => $address->id,
            'payment_method' => 'credit_card',
        ]);

        $response->assertStatus(200);
        return Order::where('user_id', $user->id)->first();
    }

    private function assertOrderCreated(Order $order, User $user, Product $product, Address $address)
    {
        $this->assertNotNull($order);
        $this->assertEquals($user->id, $order->user_id);
        $this->assertEquals($product->id, $order->product_id);
        $this->assertEquals($address->id, $order->address_id);
    }
}
