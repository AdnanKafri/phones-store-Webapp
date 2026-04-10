@extends('admin.layout')
@section('title', 'User Wallet')
@section('page-title', 'User Wallet')
@section('content')
<div class="page-header mb-4"><nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.wallets.index') }}">Wallets</a></li><li class="breadcrumb-item active">{{ $user->name }}</li></ol></nav><h2 class="page-title">{{ $user->name }}'s Wallet</h2></div>
<div class="row g-4">
    <div class="col-lg-4">
        <!-- User Profile Card -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="user-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h4>{{ $user->name }}</h4>
                <p class="text-muted">{{ $user->email }}</p>
                <hr>
                <div class="mb-3">
                    <p class="text-muted mb-1">Current Balance</p>
                    <h2 class="text-success mb-0">${{ number_format($user->wallet_balance, 2) }}</h2>
                </div>
                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-primary">View Full Profile</a>
            </div>
        </div>

        <!-- Admin Recharge Card -->
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Admin Recharge</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.wallets.recharge', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to add funds to this wallet?');">
                    @csrf
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount ($)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="amount" name="amount" required placeholder="0.00">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Admin Note (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Reason for recharge..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-plus-circle me-1"></i>Add Funds
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Transaction History</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Balance Before</th>
                                <th>Balance After</th>
                                <th>Reason</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                            <tr>
                                <td>#{{ $transaction->id }}</td>
                                <td>
                                    @if($transaction->type === 'credit')
                                        <span class="badge bg-success">Credit</span>
                                    @else
                                        <span class="badge bg-danger">Debit</span>
                                    @endif
                                </td>
                                <td>
                                    <strong @if($transaction->type === 'credit')class="text-success"@else class="text-danger"@endif>
                                        {{ $transaction->type === 'credit' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                    </strong>
                                </td>
                                <td>${{ number_format($transaction->balance_before, 2) }}</td>
                                <td>${{ number_format($transaction->balance_after, 2) }}</td>
                                <td>
                                    {{ $transaction->reason }}<br>
                                    <small class="text-muted">{{ $transaction->description }}</small>
                                </td>
                                <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No transactions yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($transactions->hasPages())
                <div class="card-footer">{{ $transactions->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
