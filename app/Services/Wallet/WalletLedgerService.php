<?php

namespace App\Services\Wallet;

use App\Models\Order;
use App\Models\User;

class WalletLedgerService
{
    public function hasSufficientBalance(User $user, float $amount): bool
    {
        return $user->wallet_balance >= $amount;
    }

    public function withdrawForMarketplacePurchase(User $buyer, Order $order): mixed
    {
        return $buyer->withdraw(
            $order->total_price,
            'purchase',
            "شراء جهاز: {$order->product->brand} {$order->product->model} (طلب #{$order->id})"
        );
    }

    public function depositForMarketplaceSale(User $seller, Order $order): mixed
    {
        return $seller->deposit(
            $order->total_price,
            'sale',
            "بيع جهاز: {$order->product->brand} {$order->product->model} (طلب #{$order->id})"
        );
    }

    public function withdrawForInventoryPurchase(User $buyer, Order $order): mixed
    {
        return $buyer->withdraw(
            $order->total_price,
            'purchase',
            "شراء من المتجر: طلب #{$order->id} - {$order->product->brand} {$order->product->model}"
        );
    }
}
