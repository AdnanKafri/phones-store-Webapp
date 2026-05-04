<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Catalog\CategoryController as ApiCategoryController;
use App\Http\Controllers\Api\V1\Catalog\ProductController as ApiProductController;
use App\Http\Controllers\Api\V1\Listings\ListingController;
use App\Http\Controllers\Api\V1\Notifications\NotificationController as ApiNotificationController;
use App\Http\Controllers\Api\V1\GeneralController;
use App\Http\Controllers\Api\V1\DeviceRequestController as ApiDeviceRequestController;
use App\Http\Controllers\Api\V1\Profile\DashboardController as ApiDashboardController;
use App\Http\Controllers\Api\V1\Orders\OrderController;
use App\Http\Controllers\Api\V1\Orders\SalesOrderController;
use App\Http\Controllers\Api\V1\Profile\ProfileController as ApiProfileController;
use App\Http\Controllers\Api\V1\Wallet\WalletController as ApiWalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('api.v1.auth.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me'])->name('api.v1.auth.me');
        Route::post('logout', [AuthController::class, 'logout'])->name('api.v1.auth.logout');
    });
});

Route::get('home', [GeneralController::class, 'home'])->name('api.v1.home');
Route::get('search', [GeneralController::class, 'search'])->name('api.v1.search');

Route::get('products', [ApiProductController::class, 'index'])->name('api.v1.products.index');
Route::get('products/{product}', [ApiProductController::class, 'show'])->name('api.v1.products.show');
Route::get('categories', [ApiCategoryController::class, 'index'])->name('api.v1.categories.index');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [ApiProfileController::class, 'show'])->name('api.v1.me.show');
    Route::patch('me', [ApiProfileController::class, 'update'])->name('api.v1.me.update');
    Route::delete('me', [ApiProfileController::class, 'destroy'])->name('api.v1.me.destroy');
    Route::get('me/dashboard', [ApiDashboardController::class, 'index'])->name('api.v1.me.dashboard');

    Route::prefix('me')->name('api.v1.me.')->group(function () {
        Route::get('listings', [ListingController::class, 'index'])->name('listings.index');
        Route::post('listings', [ListingController::class, 'store'])->name('listings.store');
        Route::post('listings/{product}/update', [ListingController::class, 'update'])->name('listings.update');
        Route::delete('listings/{product}', [ListingController::class, 'destroy'])->name('listings.destroy');
    });

    Route::get('notifications', [ApiNotificationController::class, 'index'])->name('api.v1.notifications.index');
    Route::post('notifications/read-all', [ApiNotificationController::class, 'markAllAsRead'])->name('api.v1.notifications.read-all');
    Route::post('notifications/{id}/read', [ApiNotificationController::class, 'markAsRead'])->name('api.v1.notifications.read');

    Route::get('device-requests', [ApiDeviceRequestController::class, 'index'])->name('api.v1.device-requests.index');
    Route::post('device-requests', [ApiDeviceRequestController::class, 'store'])->name('api.v1.device-requests.store');
    Route::post('device-requests/{deviceRequest}/offer', [ApiDeviceRequestController::class, 'offer'])->name('api.v1.device-requests.offer');

    Route::get('wallet', [ApiWalletController::class, 'show'])->name('api.v1.wallet.show');
    Route::get('wallet/transactions', [ApiWalletController::class, 'transactions'])->name('api.v1.wallet.transactions');
    Route::get('wallet/recharge-requests', [ApiWalletController::class, 'rechargeRequests'])->name('api.v1.wallet.recharge-requests.index');
    Route::post('wallet/recharge-requests', [ApiWalletController::class, 'storeRechargeRequest'])->name('api.v1.wallet.recharge-requests.store');

    Route::get('orders', [OrderController::class, 'index'])->name('api.v1.orders.index');
    Route::post('orders', [OrderController::class, 'store'])->name('api.v1.orders.store');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('api.v1.orders.show');

    Route::prefix('sales')->name('api.v1.sales.')->group(function () {
        Route::get('orders', [SalesOrderController::class, 'index'])->name('orders.index');
        Route::post('orders/{order}/approve', [SalesOrderController::class, 'approve'])->name('orders.approve');
        Route::post('orders/{order}/reject', [SalesOrderController::class, 'reject'])->name('orders.reject');
    });
});
