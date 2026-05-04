<?php

namespace App\Http\Controllers\Api\V1\Profile;

use App\Http\Controllers\Api\ApiController;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends ApiController
{
    public function index(Request $request)
    {
        $user = $request->user();

        $totalListings = Product::where('user_id', $user->id)->count();
        $activeListings = Product::where('user_id', $user->id)->where('status', 'available')->count();
        
        $ordersCount = \App\Models\Order::where('user_id', $user->id)->count();
        $salesCount = \App\Models\Order::whereIn('product_id', $user->products()->pluck('id'))
            ->where('order_type', 'user')
            ->count();

        return $this->successResponse([
            'total_listings' => $totalListings,
            'active_listings' => $activeListings,
            'total_orders' => $ordersCount,
            'total_sales' => $salesCount,
            'wallet_balance' => (float) $user->wallet_balance,
        ], 'Dashboard stats retrieved successfully.');
    }
}
