<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;

test('Index product page link works', function () {
    $response = $this->get('/shop');

    $response->assertStatus(200)
        ->assertViewIs('product.shop')
        ->assertSee('Shop'); // Assuming 'Products' text is present on the page
});

test("Index product page display sorting options", function () {
    $response = $response = $this->get('/shop');
    $response->assertStatus(200)
        ->assertViewIs('product.shop')
        // Check if the form is viewable
        ->assertSee('Sort by')->assertSee('<form id="sortForm" action="' . route('product.index') . '" method="GET"', false)
        ->assertSee('<label for="sort">Sort by:</label>', false)
        ->assertSee('<select id="sort" name="sort" class="border-0 form-select-sm bg-light me-3"', false)
        ->assertSee('<option value="none"', false)
        ->assertSee('<option value="latest"', false)
        ->assertSee('<option value="asc"', false)
        ->assertSee('<option value="desc"', false);
});

test("Index product page display sorting results", function () {
    $sortType = ['asc', 'desc', 'latest'];
    $response = $response = $this->get('/shop?sort=' . array_rand($sortType));
    $response->assertStatus(200)
        ->assertViewIs('product.shop');
    $products = $response->original->getData()['products']; // Get the products from the response
    foreach ($products as $product) {
        // Check if the product is visible
        $response->assertSee($product->name, "Product name not visible")
            ->assertSee($product->description, "Product description not visible");
        // Check if the product price is visible with proper format
        if ($product->variants->count() == 0) {
            $response->assertSee(number_format($product->price, 0, '.', ','),"Product price not visible");
        } else if ($product->variants->count() == 1) {
            $response->assertSee(number_format($product->variants[0]->variant_price, 0, '.', ','),"Product price (1 variant) not visible");
        } else {
            $response->assertSee(number_format($product->variants->min('variant_price'), 0, '.', ','),"Product price (2+ variant) not visible");
            $response->assertSee(number_format($product->variants->max('variant_price'), 0, '.', ','),"Product price (2+ variant) not visible");
        }
    }
});

test("Index product page display category list", function () {
    $response = $this->get('/shop');
    $response->assertStatus(200)
        ->assertViewIs('product.shop')
        ->assertSee('Categories');
    $categories = $response->original->getData()['categories']; // Get the categories from the response
    foreach ($categories as $category) {
        $this->assertNotNull($category, "Category is null");
        // Check if the category name and number is visible
        $response->assertSee($category->name);
        $response->assertSee($category->products->count());
    }
});

test("Index product page display product of 2 categories", function () {
    // Get two random categories from the database
    $categories = Category::inRandomOrder()->limit(2)->get();

    // Extract category IDs to pass to the URL
    $categoryIds = $categories->pluck('id')->toArray();
    $categoryQuery = http_build_query(array('categories' => $categoryIds));
    // Simulate a request to the index method with the selected categories
    $response = $this->get('/shop?categories' . $categoryQuery);
    // Assert that the response is successful
    $response->assertStatus(200)
        ->assertViewIs('product.shop')
        ->assertSee('Shop') // Assuming 'Shop' text is present on the page
        ->assertViewHas('products');

    $products = $response->original->getData()['products']; // Get the products from the response
    // Check if the products are visible and match the categories
    foreach ($products as $product) {
        // Check if the product is visible
        $response->assertSee($product->name, "Product name not visible")
            ->assertSee($product->description, "Product description not visible");
        // Check if the product price is visible with proper format
        if ($product->variants->count() == 0) {
            $response->assertSee(number_format($product->price, 0, '.', ','),"Product price not visible");
        } else if ($product->variants->count() == 1) {
            $response->assertSee(number_format($product->variants[0]->variant_price, 0, '.', ','),"Product price (1 variant) not visible");
        } else {
            $response->assertSee(number_format($product->variants->min('variant_price'), 0, '.', ','),"Product price (2+ variant) not visible");
            $response->assertSee(number_format($product->variants->max('variant_price'), 0, '.', ','),"Product price (2+ variant) not visible");
        }
        // Check if the product matches the categories
        $categoryMatch = false;
        foreach ($categories as $category) {
            if ($product->category_id === $category->id) {
                $categoryMatch = true;
                break;
            }
        }

        $this->assertTrue($categoryMatch, "Product category does not match any of the selected categories");
    }
});

test('Index product page display search result with right keyword', function () {
    $keyword = "itaqu";
    // Simulate a request to the show method with the keyword
    $response = $this->get('/shop?search=' . $keyword);
    // Assert that the response is successful
    $response->assertStatus(200)
        ->assertViewIs('product.shop')
        ->assertSee('Shop') // Assuming 'Products' text is present on the page
        ->assertSee($keyword) // Assuming 'a' is present in the search results
        ->assertViewHas('products');
    $products = $response->original->getData()['products']; // Get the products from the response

    foreach ($products as $product) {
        $this->assertTrue(
            stripos($product->name, $keyword) !== false || stripos($product->description, $keyword) !== false,
            "Keyword '$keyword' not found in product name or description"
        );
    }
});

test('Index product page display search result with wrong keyword', function () {
    $keyword = "###";
    // Simulate a request to the show method with the keyword
    $response = $this->get('/shop?search=' . $keyword);
    // Assert that the response is successful
    $response->assertStatus(200)
        ->assertViewIs('product.shop')
        ->assertSee('Shop') // Assuming 'Shop' text is present on the page
        ->assertViewHas('products',null);
});

test('Index product page display price range input',function(){
    $response = $this->get('/shop');
    // Assert that the price range form is there
    $response->assertStatus(200)
        ->assertSee('Price Range')
        ->assertSee('Min Price')->assertSee('Max Price')
        ->assertSee('<input type="number" class="form-control" id="minPrice" name="min_price" min="0" max="2147483647" value="">',false)
        ->assertSee('<input type="number" class="form-control" id="maxPrice" name="max_price" min="0" max="2147483647" value="">',false);
});

test('Index product page display search by price range results',function(){
    $minPrice = random_int(10000,999999);
    $maxPrice = random_int($minPrice,999999);
    // Simulate a request to the show method with the price range
    $response = $this->get('/shop?min_price='. $minPrice. '&max_price='. $maxPrice);
    // Assert that the response is successful
    $response->assertStatus(200)
        ->assertViewIs('product.shop')
        ->assertSee('Shop') // Assuming 'Shop' text is present on the page
        ->assertViewHas('products');
    $products = $response->original->getData()['products']; // Get the products from the response
    foreach ($products as $product) {
        $this->assertTrue(
            $product->price >= $minPrice && $product->price <= $maxPrice,
            "Product price out of range"
        );
    }
});


test('Product detail page link works', function () {

    // Get category form database
    $product = Product::all()->first();
    $variants = ProductVariant::where('product_id', $product->id)->get();

    // Simulate a request to the show method
    $response = $this->get('/shop/' . $product->id);

    // Assert that the response is successful
    $response->assertStatus(200)
        ->assertViewIs('product.shop-detail')
        ->assertViewHas('product', $product)
        ->assertViewHas('variants');
});

test('Product detail page display product details', function () {
    $product = Product::inRandomOrder()->first();
    $variants = ProductVariant::where('product_id', $product->id)->get();
    // Simulate a request to the show method
    $response = $this->get('/shop/' . $product->id);
    // Assert that the product details are visible
    $response->assertSee($product->name, "Product name not visible")
        ->assertSee($product->description, "Product description not visible")
        ->assertSee(number_format($product->price, 0, '.', ','),"Product price not visible");
    // Assert that the product variants are visible
    foreach($variants as $variant) {
        $response->assertSee(number_format($variant->variant_price, 0, '.', ','),"Product variant price not visible");
        $response->assertSee($variant->variant_name, "Product variant name not visible");
        $response->assertSee($variant->stock_quantity, "Product variant quantity not visible");
    }
});


