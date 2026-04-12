<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Catalog\ProductQueryService;

class UserDashboardController extends Controller
{
    public function __construct(
        private ProductQueryService $productQueryService,
    ) {
    }

    public function index()
    {
        $user = auth()->user();
        $totalListings = Product::where('user_id', $user->id)->count();
        $activeListings = Product::where('user_id', $user->id)->where('status', 'available')->count();
        
        $ordersCount = \App\Models\Order::where('user_id', $user->id)->count();
        $salesCount = \App\Models\Order::whereIn('product_id', $user->products()->pluck('id'))->where('order_type', 'user')->count();
        $walletBalance = $user->wallet_balance;
        $notifications = $user->unreadNotifications()->limit(5)->get();

        return view('dashboard.index', compact(
            'totalListings', 
            'activeListings', 
            'ordersCount', 
            'salesCount', 
            'walletBalance',
            'notifications'
        ));
    }

    public function myListings()
    {
        $products = $this->productQueryService->getUserListings(auth()->id());

        return view('dashboard.my-listings', compact('products'));
    }
}
