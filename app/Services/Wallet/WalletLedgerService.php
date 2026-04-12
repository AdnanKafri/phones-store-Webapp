<?php

namespace App\Services\Wallet;

use App\Models\Order;
use App\Models\PaymentRequest;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

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

    public function depositForRechargeRequest(PaymentRequest $paymentRequest): mixed
    {
        return $paymentRequest->user->deposit(
            $paymentRequest->amount,
            'deposit',
            "Wallet Recharge (Ref: {$paymentRequest->id})"
        );
    }

    public function adminRecharge(User $user, float $amount, ?string $notes = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $notes) {
            $balanceBefore = $user->wallet_balance;
            $user->wallet_balance += $amount;
            $user->save();

            return WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'credit',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $user->wallet_balance,
                'reason' => 'Admin Recharge',
                'description' => $notes ?: 'Manual recharge by admin',
            ]);
        });
    }
}
