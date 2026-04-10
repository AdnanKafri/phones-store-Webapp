<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = WalletTransaction::with(['user']);
        
        if ($request->has('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }
        
        if ($request->has('type') && $request->type !== '') {
            $query->where('type', $request->type);
        }
        
        $transactions = $query->latest()->paginate(20);
        
        return view('admin.transactions.index', compact('transactions'));
    }

    public function show(WalletTransaction $transaction)
    {
        return view('admin.transactions.show', compact('transaction'));
    }

}
