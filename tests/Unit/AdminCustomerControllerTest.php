<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminCustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'role' => 'ADMIN',
        ]);
        $this->actingAs($this->user);
    }

    public function testListCustomers()
    {
        User::factory()->count(5)->create(['role' => 'BUYER']);

        $response = $this->get(route('admin.customers.list'));

        $response->assertStatus(200);
        $response->assertViewHas('users');
    }

    // public function testEditCustomer()
    // {
    //     $customer = User::factory()->create(['role' => 'BUYER']);

    //     $response = $this->get(route('admin.customers.edit', $customer->id));

    //     $response->assertStatus(200);
    //     $response->assertViewHas('user', $customer);
    // }

    public function testDetailCustomer()
    {
        $customer = User::factory()->create(['role' => 'BUYER']);

        $response = $this->get(route('admin.customers.detail', $customer->id));

        $response->assertStatus(200);
        $response->assertViewHas('user', $customer);
    }

    public function testDeleteCustomer()
    {
        $customer = User::factory()->create(['role' => 'BUYER']);

        $response = $this->post(route('admin.customers.delete', $customer->id));

        $response->assertRedirect(route('admin.customers.list'));
        $this->assertSoftDeleted('users', ['id' => $customer->id]);
    }

    public function testCustomerOrders()
    {
        $customer = User::factory()->create(['role' => 'BUYER']);
        $address = Address::factory()->create(['user_id' => $customer->id]);
        $orders = Order::factory()->count(3)->create([
            'user_id' => $customer->id,
            'shipping_address_id' => $address->id,
        ]);

        $response = $this->get(route('admin.customers.orders', $customer->id));
        $response->assertStatus(200);
        $response->assertViewHas('orders', function ($viewOrders) use ($orders) {
            return $viewOrders->count() === $orders->count();
        });
    }
}