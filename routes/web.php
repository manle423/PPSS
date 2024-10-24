<?php

use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminCustomerController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VnPayController;
use Illuminate\Support\Facades\Route;

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
    Route::get('/change-password', [AdminController::class, 'changePass'])->name('change-password');
  
    Route::get('/shop', [AdminController::class, 'showInfo'])->name('shop');
    Route::get('/update-shop', [AdminController::class, 'edit'])->name('shop-info');
    Route::post('/update-shop', [AdminController::class, 'update'])->name('update-shop-info');
    //
  //  Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ForgotPasswordController::class, 'reset'])->name('password.update');


    Route::prefix('/categories')->group(function () {
        Route::get('/', [AdminCategoryController::class, 'list'])->name('category.list');
        Route::get('/create', [AdminCategoryController::class, 'create'])->name('category.create');
        Route::post('/store', [AdminCategoryController::class, 'store'])->name('category.store');
        Route::get('/edit/{id}', [AdminCategoryController::class, 'edit'])->name('category.edit');
        Route::post('/update/{id}', [AdminCategoryController::class, 'update'])->name('category.update');
        Route::post('/delete/{id}', [AdminCategoryController::class, 'delete'])->name('category.delete');
        Route::post('/import', [AdminCategoryController::class, 'import'])->name('category.import');
        Route::get('/export-template', [AdminCategoryController::class, 'exportTemplate'])->name('category.export.template');
        Route::post('/bulk-action', [AdminCategoryController::class, 'bulkAction'])->name('category.bulk-action');
    });
    Route::prefix('/coupons')->group(function () {
        Route::get('/', [AdminCouponController::class, 'list'])->name('coupon.list');
        Route::get('/create', [AdminCouponController::class, 'create'])->name('coupon.create');
        Route::post('/store', [AdminCouponController::class, 'store'])->name('coupon.store');
        Route::get('/detail/{id}', [AdminCouponController::class, 'detail'])->name('coupon.detail');
        Route::get('/edit/{id}', [AdminCouponController::class, 'edit'])->name('coupon.edit');
        Route::post('/update/{id}', [AdminCouponController::class, 'update'])->name('coupon.update');
        Route::post('/delete/{id}', [AdminCouponController::class, 'delete'])->name('coupon.delete');
        Route::post('/import', [AdminCouponController::class, 'import'])->name('coupon.import');
        Route::get('/export-template', [AdminCouponController::class, 'exportTemplate'])->name('coupon.export.template');
        Route::post('/bulk-action', [AdminCouponController::class, 'bulkAction'])->name('coupon.bulk-action');
    });

    Route::prefix('/products')->group(function () {
        // product
        Route::get('/', [AdminProductController::class, 'list'])->name('products.list');
        Route::get('/create', [AdminProductController::class, 'create'])->name('products.create');
        Route::get('/filter', [AdminProductController::class, 'filter'])->name('products.filter');
        Route::post('/store', [AdminProductController::class, 'store'])->name('products.store');
        Route::get('/edit/{id}', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::put('/update/{id}', [AdminProductController::class, 'update'])->name('products.update');
        Route::post('/delete/{id}', [AdminProductController::class, 'destroy'])->name('products.delete');
        Route::get('/sale/{id}', [AdminProductController::class, 'sale'])->name('products.sale');
       
        Route::post('/search', [AdminProductController::class, 'search'])->name('products.search');
        // variant
        Route::delete('/variants/{id}', [AdminProductController::class, 'destroyVariant'])->name('products.variants.destroy');

        Route::post('/import', [AdminProductController::class, 'import'])->name('products.import');
        Route::get('/export-template', [AdminProductController::class, 'exportTemplate'])->name('products.export.template');

        Route::post('/bulk-action', [AdminProductController::class, 'bulkAction'])->name('products.bulk-action');
    });

    Route::prefix('/orders')->group(function () {
        Route::get('/', [AdminOrderController::class, 'list'])->name('orders.list');
        Route::get('/{id}', [AdminOrderController::class, 'show'])->name('orders.detail');
        Route::get('/guest-order/{id}', [AdminOrderController::class, 'detailGuestOrder'])->name('orders.detail-guest-order');
        Route::patch('/{order}/cancel', [AdminOrderController::class, 'cancelOrder'])->name('orders.cancel');
    });
    Route::prefix('/customers')->group(function () {
        Route::get('/', [AdminCustomerController::class, 'list'])->name('customers.list');
        Route::get('/edit/{id}', [AdminCustomerController::class, 'edit'])->name('customers.edit');
        Route::get('/detail/{id}', [AdminCustomerController::class, 'detail'])->name('customers.detail');
        // Route::post('/update/{id}', [AdminCustomerController::class, 'update'])->name('customers.update');
        Route::post('/delete/{id}', [AdminCustomerController::class, 'delete'])->name('customers.delete');
        Route::get('/orders/{id}', [AdminCustomerController::class, 'orders'])->name('customers.orders');
    });

    //Change password
    Route::get('/reset-pass', [AdminController::class, 'changePass'])->name('password.reset');
    Route::post('/reset-pass', [AdminController::class, 'setPass'])->name('password.update');
    
});
// Cho người chưa đăng nhập
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
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->middleware('auth')->name('verification.notice');

    // Logout Route
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('logout', function () {
        return redirect('/home');
    });

    // Routes for order
    Route::prefix('/order')->group(function () {
        Route::get('/history', [OrderController::class, 'history'])->name('order.history');
        Route::get('/show/{order}', [OrderController::class, 'show'])->name('order.show');
        Route::patch('/{order}/cancel', [OrderController::class, 'cancelOrder'])->name('order.cancel');
    });

    Route::prefix('/profile')->group(function () {
        Route::get('/', [ProfileController::class, 'viewProfile'])->name('user.profile');
        Route::post('/add-address', [ProfileController::class, 'addAddress'])->name('user.add-address');
        Route::delete('/delete-address/{id}', [ProfileController::class, 'deleteAddress'])->name('user.delete-address');
        Route::post('/edit-address/{id}', [ProfileController::class, 'editAddress'])->name('user.edit-address');
        Route::get('/address/{id}', [ProfileController::class, 'getAddress'])->name('user.get-address');
        Route::post('/update-info', [ProfileController::class, 'updateUserInfo'])->name('user.update-info');
    });

    Route::get('/order-history/{status}', [OrderController::class, 'getOrdersByStatus'])->name('user.order-history');
});

// Cho người mua (chưa đăng nhập hoặc đã đăng nhập)
Route::middleware('buyerOrGuest')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/about-us', [HomeController::class, 'aboutUs'])->name('about-us');
    Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy-policy');

    Route::prefix('/order')->group(function () {
        Route::get('/search', [OrderController::class, 'searchForm'])->name('order.search');
        Route::post('/search', [OrderController::class, 'search'])->name('order.search.post');
        Route::post('/verify', [OrderController::class, 'verifyAndShowOrder'])->name('order.verify');
    });

    //Routes for products
    Route::get('/shop', [ProductController::class, 'index'])->name('product.index');
    Route::get('/shop/{product}', [ProductController::class, 'show'])->name('product.show');

    // Routes for cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{product}', [CartController::class, 'store'])->name('cart.store');
    Route::delete('/cart/{cartKey}/{product}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/cart/delete/{cartKey}', [CartController::class, 'destroySession'])->name('cart.destroy-session');
    Route::patch('/cart/update/{cartKey}/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::patch('/cart/updateSession/{cartKey}', [CartController::class, 'updateSession'])->name('cart.update-session');

    Route::prefix('/checkout')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/process', [CheckoutController::class, 'process'])->name('checkout.process');
        Route::get('/success', [CheckoutController::class, 'success'])->name('checkout.success');
        Route::get('/coupon', [CouponController::class, 'useCoupon'])->name('checkout.coupon');
        Route::post('/calculate-shipping-fee', [CheckoutController::class, 'calculateShippingFee'])->name('checkout.calculate.shipping.fee');
    });

    Route::prefix('paypal')->group(function () {
        Route::get('create', [PaypalController::class, 'create'])->name('paypal.create');
        Route::get('process', [PaypalController::class, 'process'])->name('paypal.process');
        Route::get('success', [PaypalController::class, 'success'])->name('paypal.success');
        Route::get('cancel', [PaypalController::class, 'cancel'])->name('paypal.cancel');
    });

    Route::prefix('vnpay')->group(function () {
        Route::get('/process', [VnPayController::class, 'process'])->name('vnpay.process');
        Route::get('/return', [VnPayController::class, 'return'])->name('vnpay.return');
    });
});

// Not found page
Route::get('/404', function () {
    return view('errors.404');
})->name('404');

Route::get('/api/provinces', [LocationController::class, 'getProvinces'])->name('api.provinces');
Route::get('/api/districts/{province_id}', [LocationController::class, 'getDistricts'])->name('api.districts');
Route::get('/api/wards/{district_id}', [LocationController::class, 'getWards'])->name('api.wards');

Route::get('/api/province/{province_id}', [LocationController::class, 'getProvinceName'])->name('api.province.name');
Route::get('/api/district/{district_id}', [LocationController::class, 'getDistrictName'])->name('api.district.name');
Route::get('/api/ward/{ward_id}', [LocationController::class, 'getWardName'])->name('api.ward.name');



