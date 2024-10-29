<?php
namespace App\Services;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StoreInfo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

class FeatureTestService
{
    public static function initiateData()
    {
        // Create test data
        $user = User::factory()->create();
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
    }
}
