<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }
        
        $users = $query->latest()->paginate(15);
        
        return view('admin.wallets.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('walletTransactions');
        $transactions = $user->walletTransactions()->latest()->paginate(20);
        
        return view('admin.wallets.show', compact('user', 'transactions'));
    }

    public function recharge(Request $request, User $user)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:255',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($user, $validated) {
            $balanceBefore = $user->wallet_balance;
            $user->wallet_balance += $validated['amount'];
            $user->save();

            WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'credit',
                'amount' => $validated['amount'],
                'balance_before' => $balanceBefore,
                'balance_after' => $user->wallet_balance,
                'reason' => 'Admin Recharge',
                'description' => $validated['notes'] ?? 'Manual recharge by admin',
            ]);
        });

        return back()->with('success', 'Wallet recharged successfully.');
    }
}
