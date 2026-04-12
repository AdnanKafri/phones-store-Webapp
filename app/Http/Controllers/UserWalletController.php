<?php

namespace App\Http\Controllers;

use App\Services\Wallet\RechargeRequestService;
use App\Services\Wallet\WalletQueryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserWalletController extends Controller
{
    public function __construct(
        private WalletQueryService $walletQueryService,
        private RechargeRequestService $rechargeRequestService,
    ) {
    }

    public function index()
    {
        $user = Auth::user();
        $transactions = $this->walletQueryService->getUserTransactions($user->id);

        return view('dashboard.wallet.index', compact('user', 'transactions'));
    }

    public function recharge(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:syriatel_cash,mtn_cash,stripe',
            'proof' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $this->rechargeRequestService->createRechargeRequest(
            $validated,
            Auth::user(),
            $request->hasFile('proof')
        );

        return back()->with('success', 'تم إرسال طلب الشحن بنجاح وهو قيد المراجعة.');
    }
}
