<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Wallet\WalletLedgerService;
use App\Services\Wallet\WalletQueryService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(
        private WalletQueryService $walletQueryService,
        private WalletLedgerService $walletLedgerService,
    ) {
    }

    public function index(Request $request)
    {
        $users = $this->walletQueryService->getWalletUsers($request->input('search'));

        return view('admin.wallets.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('walletTransactions');
        $transactions = $this->walletQueryService->getAdminUserTransactions($user);

        return view('admin.wallets.show', compact('user', 'transactions'));
    }

    public function recharge(Request $request, User $user)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:255',
        ]);

        $this->walletLedgerService->adminRecharge(
            $user,
            (float) $validated['amount'],
            $validated['notes'] ?? null,
        );

        return back()->with('success', 'Wallet recharged successfully.');
    }
}
