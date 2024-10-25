<?php

use App\Models\Category;
use App\Models\Product;

test('Home page link works', function () {
    $response = $this->get('/home');

    $response->assertStatus(200);
});

test('Home page link shows latest products of all categories', function () {
    $randomProducts = Product::latest()->limit(8)->get();
    $response = $this->withSession(['latestProductsAll' => $randomProducts])->get('/home');
    // Check if see all products
    foreach ($randomProducts as $product) {
        // Check if the product is visible
        $response->assertSee($product->name, "Product name not visible")
            ->assertSee($product->description, "Product description not visible");
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

test('Home page link shows latest products of each category', function () {
    // Get 3 random categories
    $categories = Category::inRandomOrder()->limit(3)->get();
    // Get the 8 latest products of each category
    $latestProductsCategories = [];
    $index = 0;
    foreach ($categories as $category) {
        $latestProductsCategories[$index] = $category->products()->latest()->limit(8)->get();
        $index += 1;
    }
    $response = $this->withSession(['latestProductsCategories' => $latestProductsCategories])->get('/home');
    // Check if see the category names
    $index = 0;
    foreach ($categories as $category) {
        $response->assertSee($category->name);
        // Check if see the latest products of each category
        foreach ($latestProductsCategories[$index] as $product) {
            // Check if the product is visible
            $response->assertSee($product->name, "Product name not visible")
                ->assertSee($product->description, "Product description not visible");
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

test('Home page link shows most popular products of all categories', function () {
    $popularProducts = Product::withCount('orders')
        ->orderBy('orders_count', 'desc')->limit(8)->get();
    $response = $this->withSession(['popularProducts' => $popularProducts])->get('/home');
    // Check if see all products
    foreach ($popularProducts as $product) {
        // Check if the product is visible
        $response->assertSee($product->name, "Product name not visible")
            ->assertSee($product->description, "Product description not visible");
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
    $popularProductsCategories = Category::inRandomOrder()->limit(3)->get();;
    $index = 0;
    foreach ($popularProductsCategories as $category) {
        $popularProductsCategories[$index] = $category->products()
            ->withCount('orders')
            ->orderBy('orders_count', 'desc')->limit(8)->get();
        $index += 1;
    }
    $response = $this->withSession(['popularProductsCategories' => $popularProductsCategories])->get('/home');
    $index = 0;
    // Check if see all products
    foreach ($popularProductsCategories as $category) {
        //$response->assertSee($category->name);
        // Check if see the most popular products of each category
        foreach ($popularProductsCategories[$index] as $product) {
            // Check if the product is visible
            $response->assertSee($product->name, "Product name not visible")
                ->assertSee($product->description, "Product description not visible");
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
