<?php

namespace App\Services\Wallet;

use App\Models\PaymentRequest;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WalletQueryService
{
    public function getUserTransactions(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return User::findOrFail($userId)
            ->walletTransactions()
            ->latest()
            ->paginate($perPage);
    }

    public function getUserRechargeRequests(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return PaymentRequest::where('user_id', $userId)
            ->where('type', 'deposit')
            ->latest()
            ->paginate($perPage);
    }

    public function getUserWalletSummary(User $user): array
    {
        return [
            'user_id' => $user->id,
            'balance' => (float) $user->wallet_balance,
            'transactions_count' => $user->walletTransactions()->count(),
            'recharge_requests_count' => $user->paymentRequests()->where('type', 'deposit')->count(),
            'pending_recharge_requests_count' => $user->paymentRequests()
                ->where('type', 'deposit')
                ->where('status', 'pending')
                ->count(),
        ];
    }

    public function getAdminRechargeRequests(int $perPage = 20): LengthAwarePaginator
    {
        return PaymentRequest::with('user')->latest()->paginate($perPage);
    }

    public function getWalletUsers(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = User::query();

        if ($search) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function getAdminUserTransactions(User $user, int $perPage = 20): LengthAwarePaginator
    {
        return $user->walletTransactions()->latest()->paginate($perPage);
    }
}
