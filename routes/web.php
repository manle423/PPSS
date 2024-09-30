<?php

use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;

// Redirect
Route::get('/', function () {
    return redirect(route('home'));
});

// Routes for admin va shop
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect(route('admin.dashboard'));
    });
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/change-password', [AdminController::class, 'changePass'])->name('changePassword');

    Route::prefix('/categories')->group(function () {
        Route::get('/', [AdminCategoryController::class, 'list'])->name('category.list');
        Route::get('/create', [AdminCategoryController::class, 'create'])->name('category.create');
        Route::post('/store', [AdminCategoryController::class, 'store'])->name('category.store');
        Route::get('/edit/{id}', [AdminCategoryController::class, 'edit'])->name('category.edit');
        Route::post('/update/{id}', [AdminCategoryController::class, 'update'])->name('category.update');
        Route::get('/delete/{id}', [AdminCategoryController::class, 'delete'])->name('category.delete');
    });

    Route::prefix('/products')->group(function () {
        Route::get('/', [AdminProductController::class, 'list'])->name('products.list');
        Route::get('/create', [AdminProductController::class, 'create'])->name('products.create');
        Route::post('/store', [AdminProductController::class, 'store'])->name('products.store');
        Route::get('/edit/{id}', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::post('/update/{id}', [AdminProductController::class, 'update'])->name('products.update');
        Route::post('/delete/{id}', [AdminProductController::class, 'destroy'])->name('products.delete');
    });

    Route::prefix('/orders')->group(function () {
        Route::get('/', [AdminOrderController::class, 'list'])->name('orders.list');
    });
    // Route::resource('products', ShopProductController::class)->except(['show'])->names([
    //     'create' => 'shop.addPro',      // Route cho form thêm sản phẩm
    //     'store' => 'shop.storePro',      // Route để lưu sản phẩm
    //     'edit' => 'shop.editPro',        // Route cho form chỉnh sửa sản phẩm
    //     'update' => 'shop.updatePro',    // Route để cập nhật sản phẩm
    //     'destroy' => 'shop.deletePro',   // Route để xóa sản phẩm
    //     'index' => 'shop.listPro',       // Route để danh sách sản phẩm
    // ]);
});

// CHo người chưa đăng nhập
Route::middleware('guest')->group(function () {
    // Login Routes...
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);

    // Registration Routes...
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);

    // Password Reset Routes...
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Routes for authenticated users (logged in)
Route::middleware('auth')->group(function () {
    // Password Confirmation Routes...
    Route::get('password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
    Route::post('password/confirm', [ConfirmPasswordController::class, 'confirm']);

    // Email Verification Routes...
    Route::get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

    // Logout Route
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('logout', function () {
        return redirect('/home');
    });
});


// Cho người mua (chưa đăng nhập hoặc đã đăng nhập)
Route::middleware('buyerOrGuest')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/shop', [HomeController::class, 'shop'])->name('shop');
    Route::get('/shop-detail', [HomeController::class, 'shopDetail'])->name('shop-detail');
    Route::get('/cart', [HomeController::class, 'cart'])->name('cart');
    Route::get('/checkout', [HomeController::class, 'checkout'])->name('checkout');
    Route::get('/contact', [HomeController::class, 'checkout'])->name('contact');

    //Routes for products
    Route::get('/products', [ProductController::class, 'index'])->name('product.index');
    Route::get('products/{product}', [ProductController::class, 'show'])->name('product.show');

    // Routes for cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{product}', [CartController::class, 'store'])->name('cart.store');
    Route::delete('/cart/{product}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::patch('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/{cartKey}', [CartController::class, 'updateSession'])->name('cart.updateSession');
});

// Not found page
Route::get('/404', function () {
    return view('errors.404');
})->name('404');

//Route cho ProductController
// Route::middleware('checkoutBuyer')->group(function () {
//     Route::get('/products', [ProductController::class, 'index']); // Lấy danh sách sản phẩm
//     Route::post('/products', [ProductController::class, 'store']); // Thêm sản phẩm
//     Route::put('/products/{id}', [ProductController::class, 'update']); // Cập nhật sản phẩm
//     Route::delete('/products/{id}', [ProductController::class, 'destroy']); // Xóa sản phẩm
// });

//Route payment checkout
// Route::get('/confirmed-checkout', [OrderController::class, 'showCheckoutPage'])->name('confirmed-checkout');
Route::post('/checkout/momo', [PaymentController::class, 'momo'])->name('checkout.momo');
Route::post('/checkout/paypal', [PaymentController::class, 'paypal'])->name('checkout.paypal');
Route::post('/checkout/bank', [PaymentController::class, 'bank'])->name('checkout.bank');
Route::post('/checkout/cash', [PaymentController::class, 'cash'])->name('checkout.cash');

// route dat hang
Route::post('/checkout/place-order', [PaymentController::class, 'placeOrder'])->name('placeOrder');

Route::get('/order-success', function () {
    return view('order-success');
})->name('orderSuccess');