<?php

namespace App\Services\Orders;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderQueryService
{
    public function getUserOrders(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Order::where('user_id', $userId)
            ->with(['product', 'variant'])
            ->latest()
            ->paginate($perPage);
    }

    public function getSellerSalesOrders(int $sellerId, int $perPage = 10): LengthAwarePaginator
    {
        $productIds = Product::where('user_id', $sellerId)->pluck('id');

        return Order::whereIn('product_id', $productIds)
            ->where('order_type', 'user')
            ->with(['product', 'user'])
            ->latest()
            ->paginate($perPage);
    }

    public function getAdminOrders(?string $type = null, int $perPage = 20): LengthAwarePaginator
    {
        $query = Order::with(['user', 'product', 'variant'])->latest();

        if (! is_null($type)) {
            $query->where('order_type', $type);
        }

        return $query->paginate($perPage);
    }

    public function loadConfirmationOrder(Order $order): Order
    {
        return $order->load(['product.images', 'product.seller', 'user', 'variant']);
    }

    public function canViewConfirmation(Order $order, User $viewer): bool
    {
        return $order->user_id === $viewer->id || $viewer->role === 'admin';
    }
}
