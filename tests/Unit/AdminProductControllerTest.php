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
use App\Models\Address;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\Order;

class AdminProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User | Authenticatable $user;
    protected Product $product;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->user = User::factory()->create(['role' => 'ADMIN']);
        $this->actingAs($this->user);
        
        // Create test category
        $this->category = Category::factory()->create();
        
        // Create test product
        $this->product = Product::factory()->create([
            'category_id' => $this->category->id
        ]);
    }

    public function testListProducts()
    {
        Product::factory()->count(15)->create();

        $response = $this->get(route('admin.products.list'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.products.list');
        $response->assertViewHas('products');
        
        // Test pagination
        $products = $response->original->getData()['products'];
        $this->assertEquals(10, $products->perPage());
    }

    public function testProductDetail()
    {
        $variant = ProductVariant::factory()->create([
            'product_id' => $this->product->id
        ]);

        $response = $this->get(route('admin.products.detail', $this->product->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.products.detail');
        $response->assertViewHas(['product', 'product_variants']);
        $response->assertSee($this->product->name);
        $response->assertSee($variant->variant_name);
    }

    public function testCreateProductForm()
    {
        $response = $this->get(route('admin.products.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.products.create');
        $response->assertViewHas('categories');
    }

    public function testStoreProductWithValidData()
    {
        Storage::fake('cloudinary');
        $image = UploadedFile::fake()->image('product.jpg');

        $response = $this->post(route('admin.products.store'), [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'category_id' => $this->category->id,
            'price' => 100,
            'stock_quantity' => 10,
            'weight' => 1.5,
            'length' => 10,
            'width' => 5,
            'height' => 2,
            'image' => $image,
            'variants' => [
                [
                    'variant_name' => 'Test Variant',
                    'variant_price' => 90,
                    'stock_quantity' => 5,
                    'weight' => 1.0,
                    'length' => 8,
                    'width' => 4,
                    'height' => 1,
                    'exp_date' => now()->addDays(30)->toDateString(),
                    'variant_image' => $image
                ]
            ]
        ]);

        $response->assertRedirect(route('admin.products.create'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'category_id' => $this->category->id
        ]);
        
        $this->assertDatabaseHas('product_variants', [
            'variant_name' => 'Test Variant'
        ]);
    }

    public function testStoreProductValidation()
    {
        $response = $this->post(route('admin.products.store'), [
            'name' => '',
            'price' => -1,
            'stock_quantity' => -1,
            'category_id' => 999 // non-existent category
        ]);

        $response->assertSessionHasErrors([
            'category_id',
            'price',
            'stock_quantity'
        ]);
    }

    public function testStoreProductDuplicate()
    {
        $existingProduct = Product::factory()->create([
            'name' => 'Duplicate Product',
            'category_id' => $this->category->id
        ]);

        $response = $this->post(route('admin.products.store'), [
            'name' => 'Duplicate Product',
            'category_id' => $this->category->id,
            'price' => 100,
            'stock_quantity' => 10
        ]);

        $response->assertSessionHasErrors('error');
    }

    public function testUpdateProductWithValidData()
    {
        Storage::fake('cloudinary');
        $image = UploadedFile::fake()->image('updated.jpg');

        $response = $this->put(route('admin.products.update', $this->product->id), [
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'category_id' => $this->category->id,
            'price' => 150,
            'stock_quantity' => 20,
            'weight' => 2.0,
            'length' => 12,
            'width' => 6,
            'height' => 3,
            'image' => $image
        ]);

        $response->assertRedirect(route('admin.products.list'));
        $this->assertDatabaseHas('products', [
            'id' => $this->product->id,
            'name' => 'Updated Product'
        ]);
    }

    public function testFilterProducts()
    {
        // Create test products with specific conditions
        Product::factory()->create([
            'name' => 'Dog Food',
            'price' => 100,
            'stock_quantity' => 10,
            'created_at' => now()->subDays(5),
            'category_id' => $this->category->id
        ]);

        Product::factory()->create([
            'name' => 'Cat Food',
            'price' => 200,
            'stock_quantity' => 20,
            'created_at' => now()->subDays(3),
            'category_id' => $this->category->id
        ]);

        // Test filter with name only
        $response = $this->get('/admin/products/filter?name=Dog');
        $response->assertStatus(200);
        $response->assertViewIs('admin.products.list');
        $products = $response->original->getData()['products'];
        $this->assertTrue($products->contains('name', 'Dog Food'));
        $this->assertFalse($products->contains('name', 'Cat Food'));

        // Test filter with multiple parameters
        $response = $this->get('/admin/products/filter', [
            'name' => 'd',
            'category' => $this->category->id,
            'price_min' => 50,
            'price_max' => 150,
            'stock_min' => 5,
            'stock_max' => 15,
            'created_at_start' => now()->subDays(7)->toDateString(),
            'created_at_end' => now()->toDateString()
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('admin.products.list');
        $products = $response->original->getData()['products'];
        $this->assertTrue($products->contains('name', 'Dog Food'));

        // Test empty filters should return all products
        $response = $this->get('/admin/products/filter', [
            'name' => '',
            'category' => '',
            'price_min' => '',
            'price_max' => '',
            'stock_min' => '',
            'stock_max' => '',
            'created_at_start' => '',
            'created_at_end' => ''
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('admin.products.list');
        $products = $response->original->getData()['products'];
    }

    public function testFilterProductsPagination()
    {
        // Create 15 products
        Product::factory()->count(15)->create([
            'name' => 'Test Product',
            'category_id' => $this->category->id
        ]);

        $response = $this->get('/admin/products/filter', [
            'name' => 'Test'
        ]);

        $response->assertStatus(200);
        $products = $response->original->getData()['products'];
        
        // Verify pagination
        $this->assertEquals(10, $products->perPage());
        $this->assertTrue($products->hasMorePages());
    }

    public function testSearchProducts()
    {
        Product::factory()->create([
            'name' => 'Searchable Product'
        ]);

        // Thay đổi route từ search sang find
        $response = $this->get(route('admin.products.list', [
            'query' => 'Searchable'
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('admin.products.list');
        $response->assertSee('Searchable Product');
    }

    public function testImportProducts()
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('products.xlsx');

        Excel::shouldReceive('import')
            ->once()
            ->andReturn(new ProductsImport());

        $response = $this->post(route('admin.products.import'), [
            'file' => $file
        ]);

        $response->assertRedirect(route('admin.products.list'));
        $response->assertSessionHas('success');
    }

    public function testExportTemplate()
    {
        $response = $this->get(route('admin.products.export.template'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function testBulkDeleteProducts()
    {
        $products = Product::factory()->count(3)->create();

        $response = $this->post(route('admin.products.bulk-action'), [
            'action' => 'delete',
            'product_ids' => $products->pluck('id')->toArray()
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

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
            'discount_percentage' => 10
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        foreach ($products as $product) {
            $this->assertDatabaseHas('products', [
                'id' => $product->id,
                'price' => 90
            ]);
        }
    }

    public function testProductSales()
    {
        // have the province, district, ward first
        $this->artisan('db:seed', ['--class' => 'LocationSeeder']);
        // Create required ShippingAddress
        $address = Address::factory()->create(['user_id' => $this->user->id]);
        // Create required Order
        $order = Order::factory()->create(['shipping_address_id' => $address->id]);
        
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'item_id' => $this->product->id,
            'quantity' => 2,
            'price' => 100
        ]);

        $response = $this->get(route('admin.products.sale', $this->product->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.products.sale');
        $response->assertViewHas(['productName', 'productId', 'productSales']);
    }
}