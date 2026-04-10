@extends('layouts.dashboard')

@section('title', 'لوحة التحكم')

@section('dashboard-content')
<div class="row g-4 mb-4">
    <!-- Stat Cards -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-primary text-white h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="rounded-circle bg-white bg-opacity-25 p-3">
                        <i class="bi bi-wallet2 fs-4"></i>
                    </div>
                </div>
                <h6 class="mb-1 text-white-50">رصيد المحفظة</h6>
                <h3 class="fw-bold mb-0" dir="ltr">${{ number_format($walletBalance ?? 0, 2) }}</h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-3">
                        <i class="bi bi-bag-check fs-4"></i>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill">نشط</span>
                </div>
                <h6 class="text-muted mb-1">إعلاناتي النشطة</h6>
                <h3 class="fw-bold mb-0 text-dark">{{ $activeListings }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="rounded-circle bg-info bg-opacity-10 text-info p-3">
                        <i class="bi bi-currency-dollar fs-4"></i>
                    </div>
                </div>
                <h6 class="text-muted mb-1">طلبات المبيعات</h6>
                <h3 class="fw-bold mb-0 text-dark">{{ $salesCount }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Notifications -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">آخر التنبيهات</h5>
                <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-light rounded-pill">عرض الكل</a>
            </div>
            <div class="card-body p-0">
                @if(isset($notifications) && $notifications->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($notifications as $notification)
                            <div class="list-group-item border-bottom-0 py-3 px-4">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="rounded-circle bg-light p-2 text-primary">
                                        <i class="bi bi-bell"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-1 fw-bold text-dark">{{ $notification->data['title'] ?? 'تنبيه' }}</p>
                                        <p class="mb-1 text-muted small">{{ $notification->data['message'] ?? '' }}</p>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    @unless($notification->read_at)
                                        <span class="badge bg-primary rounded-circle p-1"> </span>
                                    @endunless
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="text-muted mb-2"><i class="bi bi-bell-slash fs-1"></i></div>
                        <p class="text-muted">لا توجد تنبيهات جديدة</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
