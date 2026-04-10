@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px;">
                <div class="card-body p-4 text-center">
                    <div class="mb-3">
                        <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center mx-auto fs-3 fw-bold" style="width: 80px; height: 80px;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1">{{ auth()->user()->name }}</h5>
                    <p class="text-muted small mb-3">{{ auth()->user()->email }}</p>
                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-gear me-1"></i> الإعدادات
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                <i class="bi bi-box-arrow-right me-1"></i> خروج
                            </button>
                        </form>
                    </div>
                    <hr>
                    <div class="text-start">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action border-0 rounded-3 px-3 py-2 mb-1 {{ request()->routeIs('dashboard') ? 'active bg-primary text-white' : '' }}">
                                <i class="bi bi-speedometer2 me-2"></i> نظرة عامة
                            </a>
                            <a href="{{ route('dashboard.my-listings') }}" class="list-group-item list-group-item-action border-0 rounded-3 px-3 py-2 mb-1 {{ request()->routeIs('dashboard.my-listings') ? 'active bg-primary text-white' : '' }}">
                                <i class="bi bi-list-ul me-2"></i> إعلاناتي
                            </a>
                            <a href="{{ route('dashboard.orders') }}" class="list-group-item list-group-item-action border-0 rounded-3 px-3 py-2 mb-1 {{ request()->routeIs('dashboard.orders') ? 'active bg-primary text-white' : '' }}">
                                <i class="bi bi-bag me-2"></i> طلباتي
                            </a>
                            <a href="{{ route('dashboard.sales') }}" class="list-group-item list-group-item-action border-0 rounded-3 px-3 py-2 mb-1 {{ request()->routeIs('dashboard.sales') ? 'active bg-primary text-white' : '' }}">
                                <i class="bi bi-currency-dollar me-2"></i> طلبات المبيعات
                            </a>
                            <a href="{{ route('wallet.index') }}" class="list-group-item list-group-item-action border-0 rounded-3 px-3 py-2 mb-1 {{ request()->routeIs('wallet.index') ? 'active bg-primary text-white' : '' }}">
                                <i class="bi bi-wallet2 me-2"></i> المحفظة
                            </a>
                            <a href="{{ route('notifications.index') }}" class="list-group-item list-group-item-action border-0 rounded-3 px-3 py-2 mb-1 {{ request()->routeIs('notifications.index') ? 'active bg-primary text-white' : '' }}">
                                <i class="bi bi-bell me-2"></i> التنبيهات
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            @yield('dashboard-content')
        </div>
    </div>
</div>
@endsection
