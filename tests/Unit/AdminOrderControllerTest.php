<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User | Authenticatable $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'role' => 'ADMIN',
        ]);
        $this->actingAs($this->user);
    }

    public function testListOrders()
    {
        Order::factory()->count(5)->create();

        $response = $this->get(route('admin.orders.list'));

        $response->assertStatus(200);
        $response->assertViewHas('orders');
    }

    // public function testShowOrder()
    // {
    //     $order = Order::factory()->create();

    //     $response = $this->get(route('admin.orders.detail', $order->id));

    //     $response->assertStatus(200);
    //     // $response->assertViewHas('order', $order);
    // }

    public function testCancelOrder()
    {
        $order = Order::factory()->create(['status' => 'PENDING']);
        $product = Product::factory()->create(['stock_quantity' => 10]);
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'item_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->patch(route('admin.orders.cancel', $order->id));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Order has been cancelled successfully.');

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'CANCELED']);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock_quantity' => 12]);
    }
}