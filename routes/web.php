<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/search', [\App\Http\Controllers\HomeController::class, 'search'])->name('search');
Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::get('/compare', [\App\Http\Controllers\ComparisonController::class, 'index'])->name('compare.index');
Route::get('/categories', [\App\Http\Controllers\CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [\App\Http\Controllers\CategoryController::class, 'show'])->name('categories.show');

// Dashboard Routes (Auth Required)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/my-listings', [\App\Http\Controllers\UserDashboardController::class, 'myListings'])->name('dashboard.my-listings');
    
    // Product Management (create route MUST come before show route)
    Route::get('/products/create', [\App\Http\Controllers\ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [\App\Http\Controllers\ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [\App\Http\Controllers\ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [\App\Http\Controllers\ProductController::class, 'update'])->name('products.update');
    Route::put('/products/{product}', [\App\Http\Controllers\ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [\App\Http\Controllers\ProductController::class, 'destroy'])->name('products.destroy');

    // Order Routes
    Route::post('/orders', [\App\Http\Controllers\OrderController::class, 'store'])->name('orders.store');
    Route::get('/dashboard/orders', [\App\Http\Controllers\OrderController::class, 'index'])->name('dashboard.orders');
    Route::get('/dashboard/sales', [\App\Http\Controllers\OrderController::class, 'sales'])->name('dashboard.sales');
    Route::put('/dashboard/sales/{order}', [\App\Http\Controllers\OrderController::class, 'updateStatus'])->name('dashboard.sales.update');
    Route::get('/orders/{order}/confirmation', [\App\Http\Controllers\OrderController::class, 'confirmation'])->name('orders.confirmation');
    
    // Wallet Routes
    Route::get('/dashboard/wallet', [\App\Http\Controllers\UserWalletController::class, 'index'])->name('wallet.index');
    Route::post('/dashboard/wallet/recharge', [\App\Http\Controllers\UserWalletController::class, 'recharge'])->name('wallet.recharge');
});

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders');
    Route::put('/orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'update'])->name('admin.orders.update');
    
    // Inventory Management
    Route::get('/inventory', [\App\Http\Controllers\Admin\ProductController::class, 'inventory'])->name('admin.inventory.index');

    // Device Requests
    Route::get('/device-requests', [\App\Http\Controllers\Admin\DeviceRequestController::class, 'index'])->name('admin.device-requests.index');
    Route::put('/device-requests/{deviceRequest}', [\App\Http\Controllers\Admin\DeviceRequestController::class, 'update'])->name('admin.device-requests.update');
    Route::delete('/device-requests/{deviceRequest}', [\App\Http\Controllers\Admin\DeviceRequestController::class, 'destroy'])->name('admin.device-requests.destroy');

    // Payment Requests (Wallet Recharges)
    Route::get('/payment-requests', [\App\Http\Controllers\Admin\PaymentRequestController::class, 'index'])->name('admin.payment-requests.index');
    Route::put('/payment-requests/{paymentRequest}', [\App\Http\Controllers\Admin\PaymentRequestController::class, 'update'])->name('admin.payment-requests.update');
});

// Product show route (must come AFTER /products/create)
Route::get('/products/{product}', [\App\Http\Controllers\ProductController::class, 'show'])->name('product.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');

    // Device Requests
    Route::get('/device-requests', [\App\Http\Controllers\DeviceRequestController::class, 'create'])->name('device-requests.create');
    Route::post('/device-requests', [\App\Http\Controllers\DeviceRequestController::class, 'store'])->name('device-requests.store');
    Route::post('/device-requests/{deviceRequest}/offer', [\App\Http\Controllers\DeviceRequestController::class, 'offer'])->name('device-requests.offer');
});

require __DIR__.'/auth.php';
