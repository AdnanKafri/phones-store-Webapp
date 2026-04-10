@extends('layouts.dashboard')

@section('title', 'محفظتي')

@section('dashboard-content')
    <div class="row">
        <!-- Balance Card -->
        <div class="col-md-4 mb-4">
            <div class="card bg-primary text-white border-0 shadow rounded-4 h-100">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title opacity-75">الرصيد الحالي</h5>
                        <h2 class="display-4 fw-bold mb-0" dir="ltr">${{ number_format($user->wallet_balance, 2) }}</h2>
                    </div>
                    <div class="mt-4">
                        <button class="btn btn-light rounded-pill px-4 fw-bold text-primary w-100" data-bs-toggle="modal" data-bs-target="#rechargeModal">
                            <i class="bi bi-plus-lg me-2"></i>شحن الرصيد
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions -->
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">أحدث المعاملات</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">النوع</th>
                                    <th>المبلغ</th>
                                    <th>التاريخ</th>
                                    <th>الوصف</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td class="ps-4">
                                            @if($transaction->type == 'deposit')
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">إيداع</span>
                                            @elseif($transaction->type == 'withdraw')
                                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">سحب</span>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">{{ $transaction->type }}</span>
                                            @endif
                                        </td>
                                        <td class="fw-bold {{ $transaction->type == 'deposit' ? 'text-success' : 'text-danger' }}" dir="ltr">
                                            {{ $transaction->type == 'deposit' ? '+' : '-' }} ${{ number_format($transaction->amount, 2) }}
                                        </td>
                                        <td class="text-muted small">
                                            {{ $transaction->created_at->format('Y-m-d h:i A') }}
                                        </td>
                                        <td class="text-muted small">
                                            {{ $transaction->description ?? $transaction->reason }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">لا توجد معاملات حتى الآن.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recharge Modal -->
<div class="modal fade" id="rechargeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">شحن المحفظة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('wallet.recharge') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">المبلغ ($)</label>
                        <input type="number" name="amount" class="form-control form-control-lg" min="1" step="0.01" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">طريقة الدفع</label>
                        <div class="d-grid gap-2">
                             <input type="radio" class="btn-check" name="method" id="method_stripe" value="stripe" checked>
                             <label class="btn btn-outline-primary text-start p-3 rounded-3" for="method_stripe">
                                 <i class="bi bi-credit-card me-2"></i> بطاقة ائتمان (Stripe)
                                 <div class="small text-muted ms-4">إيداع فوري</div>
                             </label>

                             <input type="radio" class="btn-check" name="method" id="method_vodafone" value="vodafone_cash">
                             <label class="btn btn-outline-dark text-start p-3 rounded-3" for="method_vodafone">
                                 <i class="bi bi-phone me-2"></i> شام كاش / تحويل يدوي
                                 <div class="small text-muted ms-4">يتطلب موافقة الإدارة (إرفاق إيصال)</div>
                             </label>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">إيصال الدفع (صورة)</label>
                        <input type="file" name="proof" class="form-control" accept="image/*">
                        <div class="form-text">مطلوب عند اختيار شام كاش أو التحويل اليدوي.</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold">متابعة للدفع</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
