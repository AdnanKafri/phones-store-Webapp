<?php

namespace App\Services\Orders;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderQueryService
{
    public function getUserOrders(int $userId, int $perPage = 10, array $relations = ['product', 'variant']): LengthAwarePaginator
    {
        return Order::where('user_id', $userId)
            ->with($relations)
            ->latest()
            ->paginate($perPage);
    }

    public function getApiUserOrders(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->getUserOrders($userId, $perPage, $this->apiRelations());
    }

    public function getSellerSalesOrders(int $sellerId, int $perPage = 10, array $relations = ['product', 'user']): LengthAwarePaginator
    {
        $productIds = Product::where('user_id', $sellerId)->pluck('id');

        return Order::whereIn('product_id', $productIds)
            ->where('order_type', 'user')
            ->with($relations)
            ->latest()
            ->paginate($perPage);
    }

    public function getApiSellerSalesOrders(int $sellerId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->getSellerSalesOrders($sellerId, $perPage, $this->apiRelations());
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
        return $order->load(['product.category', 'product.images', 'product.seller', 'user', 'variant']);
    }

    public function loadApiOrder(Order $order): Order
    {
        return $order->load($this->apiRelations());
    }

    public function getApiOrderForBuyer(Order $order, int $buyerId): ?Order
    {
        if ($order->user_id !== $buyerId) {
            return null;
        }

        return $this->loadApiOrder($order);
    }

    public function getApiSalesOrderForSeller(Order $order, int $sellerId): ?Order
    {
        $order->loadMissing('product');

        if ($order->order_type !== 'user' || ! $order->product || $order->product->user_id !== $sellerId) {
            return null;
        }

        return $this->loadApiOrder($order);
    }

    public function canViewConfirmation(Order $order, User $viewer): bool
    {
        return $order->user_id === $viewer->id || $viewer->role === 'admin';
    }

    private function apiRelations(): array
    {
        return [
            'product.category',
            'product.images',
            'product.seller',
            'user',
            'variant',
        ];
    }
}
