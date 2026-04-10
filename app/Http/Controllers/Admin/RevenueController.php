<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Revenue;
use App\Models\User;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $query = Revenue::with(['product', 'seller', 'buyer', 'transaction']);

        // Date Range Filter
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Seller Filter
        if ($request->has('seller_id') && $request->seller_id) {
            $query->where('seller_id', $request->seller_id);
        }

        // Buyer Filter
        if ($request->has('buyer_id') && $request->buyer_id) {
            $query->where('buyer_id', $request->buyer_id);
        }

        // Calculate Totals before pagination
        $totalRevenue = $query->sum('amount');
        $completedTransactions = $query->where('status', 'completed')->count();
        $pendingTransactions = $query->where('status', 'pending')->count(); // Assuming pending revenues are possible, though spec says completed usually creates revenue

        $revenues = $query->latest('date')->paginate(15);
        $users = User::all(); // For filter dropdowns

        return view('admin.revenues.index', compact('revenues', 'users', 'totalRevenue', 'completedTransactions'));
    }
}
