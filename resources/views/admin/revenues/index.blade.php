@extends('admin.layout')

@section('title', 'Revenue Tracking')
@section('page-title', 'Revenue Tracking')

@section('content')
<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="page-title">Revenue Tracking</h2>
            <p class="page-subtitle">Overview of platform revenue and transactions</p>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Revenue</h6>
                        <h2 class="mt-2 mb-0">${{ number_format($totalRevenue, 2) }}</h2>
                    </div>
                    <i class="bi bi-currency-dollar fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Completed Transactions</h6>
                        <h2 class="mt-2 mb-0">{{ $completedTransactions }}</h2>
                    </div>
                    <i class="bi bi-receipt fs-1"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.revenues.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Seller</label>
                    <select name="seller_id" class="form-select">
                        <option value="">All Sellers</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('seller_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Buyer</label>
                    <select name="buyer_id" class="form-select">
                        <option value="">All Buyers</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('buyer_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="w-100">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-filter me-1"></i>Filter
                        </button>
                        <a href="{{ route('admin.revenues.index') }}" class="btn btn-outline-secondary w-100">Clear</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Revenues Table -->
<div class="card table-card">
    <div class="card-header">
        <h5 class="mb-0">Revenue History ({{ $revenues->total() }})</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Transaction ID</th>
                        <th>Product</th>
                        <th>Seller</th>
                        <th>Buyer</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($revenues as $revenue)
                    <tr>
                        <td>{{ $revenue->date->format('M d, Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.transactions.show', $revenue->transaction_id) }}">
                                #{{ $revenue->transaction_id }}
                            </a>
                        </td>
                        <td>{{ $revenue->product->name }}</td>
                        <td>{{ $revenue->seller->name }}</td>
                        <td>{{ $revenue->buyer->name }}</td>
                        <td>
                            <strong class="text-success">${{ number_format($revenue->amount, 2) }}</strong>
                        </td>
                        <td>
                            @if($revenue->status === 'completed')
                                <span class="badge bg-success">Completed</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No records found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($revenues->hasPages())
    <div class="card-footer">
        {{ $revenues->links() }}
    </div>
    @endif
</div>
@endsection
