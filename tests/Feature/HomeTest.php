<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StoreInfo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\FeatureTestService;
uses(RefreshDatabase::class);



test('Home page link works', function () {
    FeatureTestService::initiateData();
    $response = $this->get('/home');

    $response->assertStatus(200);
});

test('Home page link shows latest products of all categories', function () {
    FeatureTestService::initiateData();
    $randomProducts = Product::latest()->limit(8)->get();
    $response = $this->withSession(['latestProductsAll' => $randomProducts])->get('/home');
    // Check if see all products
    foreach ($randomProducts as $product) {
        // Check if the view has the product
        $response->assertViewHas('latestProductsAll', function ($collection) use ($product){
            return $collection->contains($product);
        });
        // Check if the product is visible
        $response->assertSee($product->name, "Product name not visible")
            ->assertSee(mb_substr($product->description,0,10), "Product description not visible");
        // Check if the product price is visible with proper format
        if ($product->variants->count() == 0) {
            $response->assertSee(number_format($product->price, 0, '.', ','), "Product price not visible");
        } else if ($product->variants->count() == 1) {
            $response->assertSee(number_format($product->variants[0]->variant_price, 0, '.', ','), "Product price (1 variant) not visible");
        } else {
            $response->assertSee(number_format($product->variants->min('variant_price'), 0, '.', ','), "Product price (2+ variant) not visible");
            $response->assertSee(number_format($product->variants->max('variant_price'), 0, '.', ','), "Product price (2+ variant) not visible");
        }
        
    }
    $response->assertStatus(200)->assertSee("All");
});

// test('Home page link shows latest products of each category', function () {
//     initiateData();
//     // Get 3 random categories
//     $categories = Category::inRandomOrder()->limit(3)->get();
//     // Get the 8 latest products of each category
//     $latestProductsCategories = [];
//     $index = 0;
//     foreach ($categories as $category) {
//         $latestProductsCategories[$index] = $category->products()->latest()->limit(8)->get();
//         $index += 1;
//     }
//     $response = $this->withSession(['categories' => $categories,'latestProductsCategories' => $latestProductsCategories])->get('/home');
//     // Check if see the category names
//     $index = 0;
//     foreach ($categories as $category) {
//         $response->assertSee($category->name);
//         // Check if see the latest products of each category
//         foreach ($latestProductsCategories[$index] as $product) {
//             // Check if the product is visible
//             $response->assertSee($product->name, "Product name not visible")
//                 ->assertSee(mb_substr($product->description,0,10), "Product description not visible");
//             // Check if the product price is visible with proper format
//             if ($product->variants->count() == 0) {
//                 $response->assertSee(number_format($product->price, 0, '.', ','), "Product price not visible");
//             } else if ($product->variants->count() == 1) {
//                 $response->assertSee(number_format($product->variants[0]->variant_price, 0, '.', ','), "Product price (1 variant) not visible");
//             } else {
//                 $response->assertSee(number_format($product->variants->min('variant_price'), 0, '.', ','), "Product price (2+ variant) not visible");
//                 $response->assertSee(number_format($product->variants->max('variant_price'), 0, '.', ','), "Product price (2+ variant) not visible");
//             }
//         }
//         $index += 1;
//     }
//     $response->assertStatus(200);
// });

test('Home page link shows most popular products of all categories', function () {
    FeatureTestService::initiateData();
    $popularProducts = Product::withCount('orders')
        ->orderBy('orders_count', 'desc')->limit(8)->get();
    $response = $this->withSession(['popularProducts' => $popularProducts])->get('/home');
    // Check if see all products
    foreach ($popularProducts as $product) {
        // Check if the product is visible
        $response->assertSee($product->name, "Product name not visible")
            ->assertSee(mb_substr($product->description,0,10), "Product description not visible");
        // Check if the product price is visible with proper format
        if ($product->variants->count() == 0) {
            $response->assertSee(number_format($product->price, 0, '.', ','), "Product price not visible");
        } else if ($product->variants->count() == 1) {
            $response->assertSee(number_format($product->variants[0]->variant_price, 0, '.', ','), "Product price (1 variant) not visible");
        } else {
            $response->assertSee(number_format($product->variants->min('variant_price'), 0, '.', ','), "Product price (2+ variant) not visible");
            $response->assertSee(number_format($product->variants->max('variant_price'), 0, '.', ','), "Product price (2+ variant) not visible");
        }
    }
    $response->assertStatus(200)->assertSee("Most Popular");
});

test('Home page link shows most popular products of each category', function () {
    FeatureTestService::initiateData();
    $popularProductsCategories = Category::inRandomOrder()->limit(3)->get();;
    $index = 0;
    foreach ($popularProductsCategories as $category) {
        $popularProductsCategories[$index] = $category->products()
            ->withCount('orders')
            ->orderBy('orders_count', 'desc')->limit(8)->get();
        $index += 1;
    }
    $response = $this->get('/home');
    $index = 0;
    // Check if see all products
    foreach ($popularProductsCategories as $category) {
        //$response->assertSee($category->name);
        // Check if see the most popular products of each category
        foreach ($popularProductsCategories[$index] as $product) {
            // Check if the product is visible
            $response->assertSee($product->name, "Product name not visible")
                ->assertSee(mb_substr($product->description,0,10), "Product description not visible");
            // Check if the product price is visible with proper format
            if ($product->variants->count() == 0) {
                $response->assertSee(number_format($product->price, 0, '.', ','), "Product price not visible");
            } else if ($product->variants->count() == 1) {
                $response->assertSee(number_format($product->variants[0]->variant_price, 0, '.', ','), "Product price (1 variant) not visible");
            } else {
                $response->assertSee(number_format($product->variants->min('variant_price'), 0, '.', ','), "Product price (2+ variant) not visible");
                $response->assertSee(number_format($product->variants->max('variant_price'), 0, '.', ','), "Product price (2+ variant) not visible");
            }
        }
        $index += 1;
    }
    $response->assertStatus(200);
});
