@extends('admin.layout')

@section('title', 'لوحة التحكم')
@section('page-title', 'نظرة عامة')

@section('content')
<div class="row g-4 mb-4">
    <!-- Platform Revenue -->
    <div class="col-md-4">
        <div class="card stat-card bg-primary text-white h-100">
            <div class="card-body">
                <div class="stat-icon bg-white text-primary mb-3">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <h3 class="stat-value" dir="ltr">${{ number_format($platformRevenue, 2) }}</h3>
                <p class="stat-label text-white-50 mb-0">إيرادات المنصة (مبيعات المتجر)</p>
            </div>
        </div>
    </div>

    <!-- Marketplace Volume -->
    <div class="col-md-4">
        <div class="card stat-card bg-success text-white h-100">
            <div class="card-body">
                <div class="stat-icon bg-white text-success mb-3">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <h3 class="stat-value" dir="ltr">${{ number_format($marketplaceVolume, 2) }}</h3>
                <p class="stat-label text-white-50 mb-0">حجم السوق (مبيعات الأعضاء)</p>
            </div>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="col-md-4">
        <div class="card stat-card bg-info text-white h-100">
            <div class="card-body">
                <div class="stat-icon bg-white text-info mb-3">
                    <i class="bi bi-cart-check"></i>
                </div>
                <h3 class="stat-value">{{ $totalOrders }}</h3>
                <p class="stat-label text-white-50 mb-0">إجمالي الطلبات المكتملة</p>
            </div>
        </div>
    </div>

    <!-- Users & Products -->
    <div class="col-md-6">
        <div class="card stat-card bg-white h-100 border bg-light">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-primary text-white ms-3">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold">{{ $totalUsers }}</h4>
                    <p class="text-muted mb-0">المستخدمين المسجلين</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card stat-card bg-white h-100 border bg-light">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-warning text-white ms-3">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold">{{ $totalProducts }}</h4>
                    <p class="text-muted mb-0">المنتجات النشطة</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">أحدث الطلبات</h5>
        <a href="{{ route('admin.orders') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>رقم الطلب</th>
                        <th>المستخدم</th>
                        <th>المبلغ</th>
                        <th>النوع</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->user->name }}</td>
                            <td class="fw-bold" dir="ltr">${{ number_format($order->total_price, 2) }}</td>
                            <td>
                                @if($order->order_type == 'inventory')
                                    <span class="badge bg-primary bg-opacity-10 text-primary">مخزون</span>
                                @else
                                    <span class="badge bg-purple bg-opacity-10 text-purple">سوق</span>
                                @endif
                            </td>
                            <td>
                                @if($order->status == 'approved')
                                    <span class="badge bg-success">مقبول</span>
                                @elseif($order->status == 'pending')
                                    <span class="badge bg-warning text-dark">قيد الانتظار</span>
                                @else
                                    <span class="badge bg-secondary">{{ $order->status }}</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $order->created_at->locale('ar')->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4">لا توجد طلبات حديثة.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
