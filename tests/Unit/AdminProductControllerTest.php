<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use App\Models\User;

class AdminProductControllerTest extends TestCase
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

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testListProducts()
    {
        Product::factory()->count(5)->create();

        $response = $this->get(route('admin.products.list'));

        $response->assertStatus(200);
        $response->assertViewHas('products');
    }

    public function testCreateProduct()
    {
        $category = Category::factory()->create();

        $response = $this->post(route('admin.products.store'), [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'category_id' => $category->id,
            'price' => 100,
            'stock_quantity' => 10,
            'weight' => 1.5,
            'length' => 10,
            'width' => 5,
            'height' => 2,
        ]);

        $response->assertRedirect(route('admin.products.create'));
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }

    public function testUpdateProduct()
    {
        $product = Product::factory()->create();

        $response = $this->put(route('admin.products.update', $product->id), [
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'category_id' => $product->category_id,
            'price' => 150,
            'stock_quantity' => 20,
            'weight' => 2.0,
            'length' => 12,
            'width' => 6,
            'height' => 3,
        ]);

        $response->assertRedirect(route('admin.products.list'));
        $this->assertDatabaseHas('products', ['name' => 'Updated Product']);
    }

    public function testDeleteProduct()
    {
        $product = Product::factory()->create();

        $response = $this->post(route('admin.products.delete', $product->id));

        $response->assertRedirect(route('admin.products.list'));
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function testBulkDeleteProducts()
    {
        $products = Product::factory()->count(3)->create();

        $response = $this->post(route('admin.products.bulk-action'), [
            'action' => 'delete',
            'product_ids' => $products->pluck('id')->toArray(),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Selected products have been deleted.');

        foreach ($products as $product) {
            $this->assertSoftDeleted('products', ['id' => $product->id]);
        }
    }

    public function testBulkDiscountProducts()
    {
        $products = Product::factory()->count(3)->create(['price' => 100]);

        $response = $this->post(route('admin.products.bulk-action'), [
            'action' => 'discount',
            'product_ids' => $products->pluck('id')->toArray(),
            'discount_percentage' => 10,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Bulk discount applied successfully.');

        foreach ($products as $product) {
            $this->assertDatabaseHas('products', ['id' => $product->id, 'price' => 90]);
        }
    }
}
