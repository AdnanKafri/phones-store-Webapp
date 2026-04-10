<?php

namespace App\Http\Controllers;

use App\Models\WalletTransaction;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserWalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $transactions = $user->walletTransactions()->latest()->paginate(10);
        return view('dashboard.wallet.index', compact('user', 'transactions'));
    }

    public function recharge(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:syriatel_cash,mtn_cash,stripe',
            'proof' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Create Payment Request for ALL methods (Manual Approval Required)
        \App\Models\PaymentRequest::create([
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'type' => 'deposit',
            'status' => 'pending',
            'payment_method' => $request->method,
            'proof_image' => $request->hasFile('proof') ? $request->file('proof')->store('payment_proofs', 'public') : null,
        ]);

        NotificationService::broadcastToAdmins('New Recharge Request', "User " . Auth::user()->name . " requested recharge of \${$request->amount}.", 'wallet');

        return back()->with('success', 'تم إرسال طلب الشحن بنجاح وهو قيد المراجعة.');
    }
}
