<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


// test('Index product page', function () {
//     // Create test data
//     // $categories = Category::all();
//     // $products = Product::all();

//     // Simulate a request to the index method
//     $response = $this->get('/shop');

//     // Assert that the response is successful
//     $response->assertStatus(200)
//              ->assertViewIs('product.shop')
//              ->assertViewHas('products')
//              ->assertViewHas('categories');
// });

// test('Product detail page', function () {
//     // Get test data from database
//     $product = Product::all()->random();
//     $variants = ProductVariant::where('product_id', $product->id)->get();

//     // Simulate a request to the show method
//     $response = $this->get('/shop/' . $product->id);

//     // Assert that the response is successful
//     $response->assertStatus(200)
//              ->assertViewIs('product.shop-detail')
//              ->assertViewHas('product', $product)
//              ->assertViewHas('variants');
// });
