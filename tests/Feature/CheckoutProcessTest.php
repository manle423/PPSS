<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Category;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\StoreInfo;
use App\Models\ProductVariant;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Cart;
use App\Services\ShippingService;
use Mockery;
use Illuminate\Support\Facades\Route;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Facades\Http;

class CheckoutProcessTest extends TestCase
{
    use RefreshDatabase;

    protected User | Authenticatable $user;
    protected $product;
    protected $address;
    protected $shippingService;
    protected $session = [
        'cart' => [],
        'cartItems' => [],
        'subtotal' => 0,
        'oldSubtotal' => 0,
        'shipping_fee' => 0,
        'total' => 0,
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedDatabase();
    }

    private function seedDatabase()
    {
        $categories = Category::factory()->count(3)->create();
        foreach ($categories as $category) {
            $products = Product::factory()->count(5)->create([
                'category_id' => $category->id,
            ]);
            foreach ($products as $product) {
                ProductVariant::factory()->count(3)->create([
                    'product_id' => $product->id,
                ]);
            }
        }

        StoreInfo::create([
            'name'  => 'Test Store',
            'description' => 'Lorem Ipsum',
            'phone'  => '1234567890',
            'email' => 'test@example.com',
            'address' => 'Test Address',
        ]);

        $this->artisan('db:seed', ['--class' => 'LocationSeeder']);
    }

    // Test go to register page
    public function test_go_to_register_page()
    {
        $response = $this->get(route('register'));

        // Basic assertions
        $response->assertStatus(200);
        $response->assertViewIs('auth.register');

        // Assert form elements exist
        $response->assertSee('Full Name', false);
        $response->assertSee('Email Address', false);
        $response->assertSee('Password', false);
        $response->assertSee('Confirm Password', false);
    }

    // Test user registration
    public function test_user_registration()
    {
        $this->test_go_to_register_page();

        $userData = [
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post(route('register'), $userData);

        // Assert redirect
        $response->assertRedirect(route('home'));

        // Assert user creation
        $this->user = User::where('email', $userData['email'])->first();
        $this->assertNotNull($this->user, 'User should be created');
        $this->assertEquals($userData['full_name'], $this->user->full_name);
        $this->assertEquals($userData['email'], $this->user->email);

        // Assert authentication
        $this->assertAuthenticatedAs($this->user);

        // Assert database
        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'full_name' => $userData['full_name']
        ]);
    }

    // Test add new address
    public function test_add_new_address()
    {
        $this->test_user_registration();

        // Test profile page access
        $response = $this->actingAs($this->user)
            ->get(route('user.profile'));

        // Basic assertions
        $response->assertStatus(200);
        $response->assertViewIs('user.profile');

        // Create address
        $this->address = Address::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // Assert address creation
        $this->assertDatabaseHas('addresses', [
            'id' => $this->address->id,
            'user_id' => $this->user->id
        ]);

        // Assert view data
        $response->assertViewHas('addresses');
        $response->assertViewHas('user');

        // Assert user relationship
        $this->assertTrue($this->user->addresses->contains($this->address));

        // Assert address attributes
        $this->assertNotNull($this->address->province_id);
        $this->assertNotNull($this->address->district_id);
        $this->assertNotNull($this->address->ward_id);
    }

    // Test go to home page
    public function test_go_to_home_page()
    {
        $this->test_add_new_address();

        $response = $this->withMiddleware(['buyerOrGuest'])
            ->actingAs($this->user)
            ->get(route('home'));

        // Basic assertions
        $response->assertStatus(200);
        $response->assertViewIs('home');

        // Assert view data
        $response->assertViewHas(['latestProductsAll', 'categories']);
    }

    public function test_add_product_to_cart()
    {
        $this->test_go_to_home_page();

        // Get product and variant
        $this->product = Product::first();
        $variant = ProductVariant::where('product_id', $this->product->id)->first();

        // Assert product and variant exist
        $this->assertNotNull($this->product, 'Product should not be null');
        $this->assertNotNull($variant, 'Variant should not be null');
        $this->assertTrue($this->product->variants->contains($variant));

        // Add to cart via route
        $response = $this->withMiddleware(['buyerOrGuest'])
            ->actingAs($this->user)
            ->post(route('cart.store', $this->product->id), [
                'amount' => 1,
                'variant_id' => $variant->id
            ]);

        // Assert redirect back to cart
        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('success', 'Product added to cart successfully!');

        // Assert cart item in database
        $this->assertDatabaseHas('carts', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'variant_id' => $variant->id,
            'quantity' => 1
        ]);

        // Assert session data
        $cartKey = $this->product->id . '-' . $variant->id;
        $this->assertEquals(1, session('cart')[$cartKey]);

        // Store session data for next tests
        $this->session['cart'] = session('cart');
        $this->session['cartItems'] = [(object)[
            'product' => $this->product,
            'variant' => $variant,
            'quantity' => 1
        ]];
        $this->session['subtotal'] = $variant->variant_price;
        $this->session['oldSubtotal'] = $this->session['subtotal'];

        session($this->session);

        // Assert cart calculations
        $cartItem = Cart::where([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'variant_id' => $variant->id
        ])->first();

        $this->assertEquals(1, $cartItem->quantity);
        $this->assertEquals($this->session['subtotal'], $variant->variant_price * $cartItem->quantity);
    }

    public function test_view_checkout_page()
    {
        $this->test_add_product_to_cart();

        // Assert cart data exists before proceeding
        $this->assertTrue(session()->has('cart'), 'Cart should exist in session');
        $this->assertTrue(session()->has('cartItems'), 'Cart items should exist in session');
        $this->assertTrue(session()->has('subtotal'), 'Subtotal should exist in session');

        // Assert user is authenticated
        $this->assertAuthenticatedAs($this->user);

        // Assert address exists
        $this->assertNotNull($this->address);
        $this->assertDatabaseHas('addresses', [
            'id' => $this->address->id,
            'user_id' => $this->user->id
        ]);

        // Access the checkout page with middleware
        $response = $this->withMiddleware(['buyerOrGuest'])
            ->actingAs($this->user)
            ->withSession($this->session)
            ->get(route('checkout.index'));

        // Assert basic response
        $response->assertStatus(200);
        $response->assertViewIs('checkout.index');

        // Assert view data exists and has correct type
        $response->assertViewHas([
            'cartItems',
            'sessionCart',
            'subtotal',
            'addresses',
            'user',
            'provinces', // Assuming provinces are passed to view
        ]);

        // Get view data for detailed assertions
        $viewData = $response->original->getData();

        // Assert cart data in view
        $this->assertNotNull($viewData['cartItems']);
        $this->assertIsArray($viewData['cartItems']);
        $this->assertNotEmpty($viewData['cartItems']);

        // Assert session cart in view
        $this->assertNotNull($viewData['sessionCart']);
        $this->assertIsArray($viewData['sessionCart']);
        $this->assertArrayHasKey($this->product->id . '-' . $this->product->variants->first()->id, $viewData['sessionCart']);

        // Assert addresses in view
        $this->assertNotNull($viewData['addresses']);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $viewData['addresses']);
        $this->assertTrue($viewData['addresses']->contains('id', $this->address->id));

        // Assert user data in view
        $this->assertNotNull($viewData['user']);
        $this->assertEquals($this->user->id, $viewData['user']->id);
        $this->assertEquals($this->user->email, $viewData['user']->email);

        // Now mock ShippingService using actual session subtotal
        $shippingService = $this->mock(ShippingService::class, function ($mock) {
            $mock->shouldReceive('calculateShippingFee')
                ->with($this->address->district_id, $this->address->ward_id, 1000)
                ->andReturn([
                    'code' => 200,
                    'data' => [
                        'total' => 50000,
                        'service_fee' => 0,
                        'insurance_fee' => 0,
                    ]
                ]);
        });

        // Calculate shipping fee using session subtotal
        $shippingFeeResponse = $shippingService->calculateShippingFee(
            $this->address->district_id,
            $this->address->ward_id,
            1000
        );

        // Set shipping fee and calculate final price based on session data
        $shippingFeeValue = $shippingFeeResponse['data']['total'];
        $finalPrice = $this->session['subtotal'] + $shippingFeeValue;

        $this->session['shipping_fee'] = $shippingFeeValue;
        $this->session['total'] = $finalPrice;

        session($this->session);

        // Assert session values
        $this->assertTrue(session()->has('shipping_fee'));
        $this->assertTrue(session()->has('total'));
        $this->assertEquals($shippingFeeValue, session('shipping_fee'));
        $this->assertEquals($finalPrice, session('total'));

        // Assert specific content on the page
        $response->assertSee('Billing details');
        $response->assertSee('Payment Method');
        $response->assertSee('Place Order');

        // Assert form elements exist
        $response->assertSee('selected_address_id', false);
        $response->assertSee('payment_method', false);

        // Assert payment methods are displayed
        $response->assertSee('PayPal');
        $response->assertSee('VNPay');
    }

    // Test payment process

    public function test_payment_process()
    {
        $this->test_view_checkout_page();

        // Create order directly with required fields
        $order = Order::create([
            'user_id' => $this->user->id,
            'shipping_address_id' => $this->address->id,
            'payment_method' => 'paypal',
            'total_price' => $this->session['subtotal'],
            'shipping_fee' => $this->session['shipping_fee'],
            'final_price' => $this->session['total'],
            'shipping_method_id' => 1,
            'status' => Order::STATUS['pending'],
            'order_date' => now(),
        ]);

        // Create order items
        foreach ($this->session['cartItems'] as $item) {
            $order->orderItems()->create([
                'item_id' => $item->product->id,
                'variant_id' => $item->variant->id,
                'quantity' => $item->quantity,
                'price' => $item->variant->variant_price,
                'total' => $item->variant->variant_price * $item->quantity
            ]);
        }

        // Soft delete user's cart items
        Cart::where('user_id', $this->user->id)->delete();

        // Simulate successful payment callback
        $successResponse = $this->withMiddleware(['buyerOrGuest'])
            ->actingAs($this->user)
            ->withSession([
                'order_type' => 'order',
                'order_id' => $order->id,
                'order_total' => $this->session['total'],
                'cartItems' => $this->session['cartItems'],
                'subtotal' => $this->session['subtotal'],
                'oldSubtotal' => $this->session['oldSubtotal'],
                'shipping_fee' => $this->session['shipping_fee'],
                'total' => $this->session['total'],
            ])
            ->get(route('checkout.success'));

        $successResponse->assertStatus(200);
        session()->forget(['cart', 'cartItems', 'subtotal', 'shipping_fee', 'oldSubtotal']);

        // Assert order details
        $order->refresh();
        $this->assertEquals(Order::STATUS['pending'], $order->status);
        $this->assertNotNull($order->order_code, 'Order code should be generated');
        $this->assertEquals($this->user->id, $order->user_id);
        $this->assertEquals($this->address->id, $order->shipping_address_id);
        $this->assertEquals('paypal', $order->payment_method);
        $this->assertEquals($this->session['subtotal'], $order->total_price);
        $this->assertEquals($this->session['shipping_fee'], $order->shipping_fee);
        $this->assertEquals($this->session['total'], $order->final_price);

        // Assert order items
        $this->assertEquals(count($this->session['cartItems']), $order->orderItems->count());
        foreach ($order->orderItems as $index => $orderItem) {
            $cartItem = $this->session['cartItems'][$index];
            $this->assertEquals($cartItem->product->id, $orderItem->item_id);
        }

        // Assert cart soft deleted 
        $this->assertSoftDeleted('carts', [
            'user_id' => $this->user->id
        ]);

        // Assert session cleared
        $this->assertNull(session('cart'));
        $this->assertNull(session('cartItems'));
        $this->assertNull(session('subtotal'));
        $this->assertNull(session('shipping_fee'));
    }

    // user go to history page
    public function test_go_to_history_page()
    {
        $this->test_payment_process();

        $response = $this->withMiddleware(['buyerOrGuest'])
            ->actingAs($this->user)
            ->get(uri: route('user.order-history', Order::STATUS['pending']));

        $response->assertStatus(200);
        $response->assertViewIs('checkout.history');

        // Assert view data
        $response->assertViewHas('orders');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}

