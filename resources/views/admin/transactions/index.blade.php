@extends('admin.layout')
@section('title', 'Transactions')
@section('page-title', 'Transactions')
@section('content')
<div class="page-header mb-4">
    <div><h2 class="page-title">Purchase Transactions</h2><p class="page-subtitle">Manage all sales transactions</p></div>
</div>
<div class="card mb-4">
    <div class="card-body">
        <form method="GET">
            <div class="row g-3">
                <div class="col-md-6"><input type="text" name="search" class="form-control" placeholder="Search by product..." value="{{ request('search') }}"></div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3"><button type="submit" class="btn btn-primary me-2"><i class="bi bi-search me-1"></i>Search</button><a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-secondary">Clear</a></div>
            </div>
        </form>
    </div>
</div>
<div class="card table-card">
    <div class="card-header"><h5 class="mb-0">All Financial Transactions ({{ $transactions->total() }})</h5></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Reason/Description</th>
                        <th>Balance After</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                    <tr>
                        <td>#{{ $transaction->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                    {{ strtoupper(substr($transaction->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="mb-0 small fw-bold">{{ $transaction->user->name }}</h6>
                                    <small class="text-muted" style="font-size: 10px;">{{ $transaction->user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($transaction->type === 'deposit')
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill">Deposit (+)</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">Withdraw (-)</span>
                            @endif
                        </td>
                        <td class="fw-bold {{ $transaction->type === 'deposit' ? 'text-success' : 'text-danger' }}">
                            {{ $transaction->type === 'deposit' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                        </td>
                        <td>
                            <div class="fw-bold small">{{ ucfirst($transaction->reason) }}</div>
                            <small class="text-muted">{{ Str::limit($transaction->description, 50) }}</small>
                        </td>
                        <td>${{ number_format($transaction->balance_after, 2) }}</td>
                        <td>
                            <span title="{{ $transaction->created_at }}">{{ $transaction->created_at->format('M d, Y H:i') }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted">No transactions found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transactions->hasPages())<div class="card-footer bg-white border-0 py-3">{{ $transactions->links() }}</div>@endif
</div>
@endsection
