<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CustomerRequestController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\PaymentRequestController;
use App\Http\Controllers\Admin\WalletController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('users', UserController::class);
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    
    // Product image management
    Route::delete('product-images/{image}', [ProductController::class, 'deleteImage'])->name('product-images.destroy');
    Route::post('product-images/{image}/set-primary', [ProductController::class, 'setPrimaryImage'])->name('product-images.set-primary');
    
    Route::resource('reviews', ReviewController::class);
    Route::resource('reports', ReportController::class);
    
    // New modules
    Route::resource('customer-requests', CustomerRequestController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::resource('transactions', TransactionController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::resource('payment-requests', PaymentRequestController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::get('wallets', [WalletController::class, 'index'])->name('wallets.index');
    Route::get('wallets/{user}', [WalletController::class, 'show'])->name('wallets.show');
    Route::post('wallets/{user}/recharge', [WalletController::class, 'recharge'])->name('wallets.recharge');
    Route::get('revenues', [\App\Http\Controllers\Admin\RevenueController::class, 'index'])->name('revenues.index');
});
