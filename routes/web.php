<?php

use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminCustomerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;

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

    Route::prefix('/categories')->group(function () {
        Route::get('/', [AdminCategoryController::class, 'list'])->name('category.list');
        Route::get('/create', [AdminCategoryController::class, 'create'])->name('category.create');
        Route::post('/store', [AdminCategoryController::class, 'store'])->name('category.store');
        Route::get('/edit/{id}', [AdminCategoryController::class, 'edit'])->name('category.edit');
        Route::post('/update/{id}', [AdminCategoryController::class, 'update'])->name('category.update');
        Route::post('/delete/{id}', [AdminCategoryController::class, 'delete'])->name('category.delete');
    });
    Route::prefix('/coupons')->group(function () {
        Route::get('/', [AdminCouponController::class, 'list'])->name('coupon.list');
        Route::get('/create', [AdminCouponController::class, 'create'])->name('coupon.create');
        Route::post('/store', [AdminCouponController::class, 'store'])->name('coupon.store');
        Route::get('/detail/{id}', [AdminCouponController::class, 'detail'])->name('coupon.detail');
        Route::get('/edit/{id}', [AdminCouponController::class, 'edit'])->name('coupon.edit');
        Route::post('/update/{id}', [AdminCouponController::class, 'update'])->name('coupon.update');
        Route::post('/delete/{id}', [AdminCouponController::class, 'delete'])->name('coupon.delete');
    });

    Route::prefix('/products')->group(function () {
        // product
        Route::get('/', [AdminProductController::class, 'list'])->name('products.list');
        Route::get('/create', [AdminProductController::class, 'create'])->name('products.create');
        Route::post('/filter', [AdminProductController::class, 'filter'])->name('products.filter');
        Route::post('/store', [AdminProductController::class, 'store'])->name('products.store');
        Route::get('/edit/{id}', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::put('/update/{id}', [AdminProductController::class, 'update'])->name('products.update');
        Route::post('/delete/{id}', [AdminProductController::class, 'destroy'])->name('products.delete');

        // variant
        Route::delete('/variants/{id}', [AdminProductController::class, 'destroyVariant'])->name('products.variants.destroy');
    });

    Route::prefix('/orders')->group(function () {
        Route::get('/', [AdminOrderController::class, 'list'])->name('orders.list');
        Route::get('/{id}', [AdminOrderController::class, 'show'])->name('orders.detail');
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
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
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

    // Routes for order
    Route::get('history', [OrderController::class, 'history'])->name('order.history');

    Route::prefix('/profile')->group(function () {
        Route::get('/', [ProfileController::class, 'viewProfile'])->name('user.profile');
        Route::post('/add-address', [ProfileController::class, 'addAddress'])->name('user.add-address');
        Route::delete('/delete-address/{id}', [ProfileController::class, 'deleteAddress'])->name('user.delete-address');
        Route::post('/edit-address/{id}', [ProfileController::class, 'editAddress'])->name('user.edit-address');
        Route::get('/address/{id}', [ProfileController::class, 'getAddress'])->name('user.get-address');
        Route::post('/update-info', [ProfileController::class, 'updateUserInfo'])->name('user.update-info');
    });
});


// Cho người mua (chưa đăng nhập hoặc đã đăng nhập)
Route::middleware('buyerOrGuest')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    

    //Routes for products
    Route::get('/shop', [ProductController::class, 'index'])->name('product.index');
    Route::get('/shop/{product}', [ProductController::class, 'show'])->name('product.show');

    // Routes for cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{product}', [CartController::class, 'store'])->name('cart.store');
    Route::delete('/cart/{product}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::delete('/cart/delete/{cartKey}', [CartController::class, 'destroySession'])->name('cart.destroy-session');
    Route::patch('/cart/update/{cartKey}/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::patch('/cart/updateSession/{cartKey}', [CartController::class, 'updateSession'])->name('cart.update-session');

    Route::prefix('/checkout')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/process', [CheckoutController::class, 'process'])->name('checkout.process');
        Route::get('/success', [CheckoutController::class, 'success'])->name('checkout.success');
        Route::get('/coupon', [CouponController::class, 'useCoupon'])->name('checkout.coupon');
    });

    Route::prefix('paypal')->group(function () {
        Route::get('create', [PaypalController::class, 'create'])->name('paypal.create');
        Route::get('process', [PaypalController::class, 'process'])->name('paypal.process');
        Route::get('success', [PaypalController::class, 'success'])->name('paypal.success');
        Route::get('cancel', [PaypalController::class, 'cancel'])->name('paypal.cancel');
    });

    Route::prefix('vnpay')->group(function () {

    });
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

//Route payment paypal
// Route::get('paypal', [PaypalController::class, 'index'])->name('paypal');

// Route::post('paypal/payment', [PaypalController::class, 'payment'])->name('paypal.payment');
// Route::get('paypal/payment/success', [PaypalController::class, 'paymentSuccess'])->name('paypal.payment.success');
// Route::get('paypal/payment/cancel', [PaypalController::class, 'paymentCancel'])->name('paypal.payment.cancel');




// Route::get('/order-success', function () {
//     return view('payments.success');
// })->name('orderSuccess');
