<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\shop\ShopCateController;
use App\Http\Controllers\shop\ShopProductController;
use App\Http\Controllers\ProductController;

// Routes for admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect(route('admin.dashboard'));
    });
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/change-password', [AdminController::class, 'changePass'])->name('changePassword');
});

// Routes for guests (not logged in)
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

Route::get('/', function () {
    return redirect(route('home'));
});

// Home Route
// Route::get('/home', [HomeController::class, 'index'])->name('home');

// Routes for buyers and guests
Route::middleware('buyerOrGuest')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/shop', [HomeController::class, 'shop'])->name('shop');
    Route::get('/shop-detail', [HomeController::class, 'shopDetail'])->name('shop-detail');
    Route::get('/cart', [HomeController::class, 'cart'])->name('cart');
    Route::get('/checkout', [HomeController::class, 'checkout'])->name('checkout');
    Route::get('/contact', [HomeController::class, 'checkout'])->name('contact');
});

Route::get('/404', function () {
    return view('errors.404');
})->name('404');
//route for shop
Route::prefix('shop')->group(function () {
    Route::get('/page-home', [ShopCateController::class, 'index'])->name('shop.page');

    Route::get('/page-addcate', [ShopCateController::class, 'create'])->name('shop.addcate');

    Route::post('/page-addcate', [ShopCateController::class, 'store'])->name('shop.storecate');
    Route::get('/page-listcate',[ShopCateController::class, 'listCate'])->name('shop.listCate');
    Route::get('/page-editcate/{id}', [ShopCateController::class, 'editCate'])->name('shop.editCate');
    Route::post('/page-editcate/{id}', [ShopCateController::class, 'updateCate'])->name('shop.updateCate');
    Route::get('/page-deletecate/{id}', [ShopCateController::class, 'deleteCate'])->name('shop.deleteCate');

    Route::resource('products', ShopProductController::class)->except(['show'])->names([
        'create' => 'shop.addPro',      // Route cho form thêm sản phẩm
        'store' => 'shop.storePro',      // Route để lưu sản phẩm
        'edit' => 'shop.editPro',        // Route cho form chỉnh sửa sản phẩm
        'update' => 'shop.updatePro',    // Route để cập nhật sản phẩm
        'destroy' => 'shop.deletePro',   // Route để xóa sản phẩm
        'index' => 'shop.listPro',       // Route để danh sách sản phẩm
    ]);
    
});

