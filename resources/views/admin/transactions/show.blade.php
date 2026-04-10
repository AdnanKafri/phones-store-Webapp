@extends('admin.layout')

@section('title', 'Transaction Details')

@section('content')
<div class="page-header mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.transactions.index') }}">Transactions</a></li>
            <li class="breadcrumb-item active">View #{{ $transaction->id }}</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">Transaction Details <small class="text-muted fs-6">#{{ $transaction->id }}</small></h2>
        <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to List
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Main Details Card -->
        <div class="card mb-4 border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-info-circle me-2"></i>Transaction Info
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1 text-uppercase fw-bold ls-1">Amount</small>
                        <div class="fs-4 fw-bold {{ $transaction->type === 'deposit' ? 'text-success' : 'text-danger' }}">
                            {{ $transaction->type === 'deposit' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1 text-uppercase fw-bold ls-1">Type</small>
                        @if($transaction->type === 'deposit')
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">Deposit (Credit)</span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2">Withdraw (Debit)</span>
                        @endif
                    </div>
                    
                    <div class="col-12">
                        <div class="p-3 bg-light rounded-3 border">
                            <small class="text-muted d-block mb-1 text-uppercase fw-bold ls-1">Reason / Description</small>
                            <h6 class="fw-bold mb-1">{{ ucfirst($transaction->reason) }}</h6>
                            <p class="mb-0 text-muted">{{ $transaction->description }}</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1 text-uppercase fw-bold ls-1">Date</small>
                        <div class="fw-bold">{{ $transaction->created_at->format('Y-m-d') }}</div>
                        <div class="text-muted small">{{ $transaction->created_at->format('h:i:s A') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Balance Impact Card -->
        <div class="card mb-4 border-0 shadow-sm rounded-4">
             <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-activity me-2"></i>Balance Impact
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center position-relative">
                    <div class="text-center w-25">
                        <small class="text-muted d-block mb-2">Before</small>
                        <div class="fs-5 fw-bold text-secondary">${{ number_format($transaction->balance_before, 2) }}</div>
                    </div>
                    
                    <div class="flex-grow-1 mx-3 position-relative" style="height: 2px; background: #e9ecef;">
                        <div class="position-absolute top-50 start-50 translate-middle bg-white px-2 text-muted">
                            <i class="bi bi-arrow-right"></i>
                        </div>
                    </div>

                    <div class="text-center w-25">
                        <small class="text-muted d-block mb-2">After</small>
                        <div class="fs-5 fw-bold text-dark">${{ number_format($transaction->balance_after, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4">
             <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-person me-2"></i>User Details
                </h5>
            </div>
            <div class="card-body text-center pt-4">
                <div class="mb-3">
                     <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white shadow-sm" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ strtoupper(substr($transaction->user->name, 0, 1)) }}
                    </div>
                </div>
                <h5 class="fw-bold mb-1">{{ $transaction->user->name }}</h5>
                <p class="text-muted mb-3">{{ $transaction->user->email }}</p>
                
                <div class="d-grid">
                    <a href="{{ route('admin.users.edit', $transaction->user_id) }}" class="btn btn-outline-primary rounded-pill">
                        View Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
