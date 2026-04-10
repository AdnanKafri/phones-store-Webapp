<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Core Stats
        $totalUsers = User::where('role', 'user')->count();
        $totalProducts = Product::where('status', 'available')->count();
        $totalOrders = Order::count();

        // Financials
        // Platform Revenue = Inventory Orders Completed
        // Platform Revenue = Inventory Orders Completed/Shipped/Approved
        $platformRevenue = Order::where('order_type', 'inventory')
            ->whereIn('status', ['approved', 'shipping', 'completed'])
            ->sum('total_price');

        // Marketplace Volume = User Orders Completed
        // Marketplace Volume = User Orders Completed
        $marketplaceVolume = Order::where('order_type', 'user')
            ->whereIn('status', ['approved', 'shipping', 'completed'])
            ->sum('total_price');

        // Recent Orders
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers', 
            'totalProducts', 
            'totalOrders', 
            'platformRevenue', 
            'marketplaceVolume',
            'recentOrders'
        ));
    }
}
