@php
    $isExploreActive = request()->routeIs('home')
        || request()->routeIs('products.*')
        || request()->routeIs('categories.*')
        || request()->routeIs('compare.*')
        || request()->routeIs('search');

    $isServicesActive = request()->routeIs('device-requests.*');
    $searchQuery = request('q', '');
@endphp

<nav class="navbar navbar-expand-lg navbar-floating navbar-premium">
    <div class="container-fluid">
        <a class="navbar-brand logo-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
            <span class="logo-brand__icon"><i class="bi bi-phone"></i></span>
            <span>PhoneMarket</span>
        </a>

        <div class="d-flex align-items-center gap-2 d-lg-none">
            <button
                type="button"
                class="btn nav-search-trigger"
                data-bs-toggle="modal"
                data-bs-target="#smartSearchModal"
                aria-label="افتح البحث الذكي"
            >
                <i class="bi bi-search"></i>
            </button>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto mb-3 mb-lg-0 align-items-lg-center gap-lg-1">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="bi bi-house-door d-lg-none me-2"></i>الرئيسية
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ $isExploreActive ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-compass d-lg-none me-2"></i>استكشف
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-premium border-0 shadow-lg">
                        <li>
                            <a class="dropdown-item py-2 {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                                <i class="bi bi-grid me-2"></i>المنتجات
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                                <i class="bi bi-tags me-2"></i>التصنيفات
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 {{ request()->routeIs('compare.*') ? 'active' : '' }}" href="{{ route('compare.index') }}">
                                <i class="bi bi-layout-split me-2"></i>مقارنة الأجهزة
                            </a>
                        </li>
                        <li>
                            <button
                                type="button"
                                class="dropdown-item py-2 text-end {{ request()->routeIs('search') ? 'active' : '' }}"
                                data-bs-toggle="modal"
                                data-bs-target="#smartSearchModal"
                            >
                                <i class="bi bi-stars me-2"></i>البحث الذكي
                            </button>
                        </li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ $isServicesActive ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-gear d-lg-none me-2"></i>الخدمات
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-premium border-0 shadow-lg">
                        <li>
                            <a class="dropdown-item py-2 {{ request()->routeIs('device-requests.*') ? 'active' : '' }}" href="{{ route('device-requests.create') }}">
                                <i class="bi bi-phone-flip me-2"></i>اطلب جهازًا
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2 flex-wrap justify-content-lg-end nav-toolbar">
                <button
                    type="button"
                    class="btn nav-search-trigger d-none d-lg-inline-flex"
                    data-bs-toggle="modal"
                    data-bs-target="#smartSearchModal"
                    aria-label="افتح البحث الذكي"
                >
                    <i class="bi bi-search"></i>
                </button>

                @auth
                    <a href="{{ route('products.create') }}" class="btn btn-primary rounded-pill px-4 py-2 d-inline-flex align-items-center gap-2 nav-cta-button">
                        <i class="bi bi-plus-lg"></i>
                        <span>بيع جهاز</span>
                    </a>

                    <div class="dropdown">
                        <a href="#" class="position-relative text-dark fs-5 nav-icon-link" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell"></i>
                            @if(auth()->user()->unreadNotifications()->exists())
                                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 nav-notification-menu">
                            <li><h6 class="dropdown-header text-end">التنبيهات</h6></li>
                            @forelse(auth()->user()->unreadNotifications as $notification)
                                <li>
                                    <a class="dropdown-item d-flex align-items-start gap-2 py-2 text-end" href="#">
                                        <div class="flex-grow-1" style="white-space: normal;">
                                            <strong class="d-block small text-dark">{{ $notification->data['title'] ?? 'تنبيه' }}</strong>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $notification->data['message'] ?? '' }}</small>
                                        </div>
                                        @if(isset($notification->data['type']) && $notification->data['type'] == 'wallet')
                                            <i class="bi bi-wallet2 text-success"></i>
                                        @else
                                            <i class="bi bi-info-circle text-primary"></i>
                                        @endif
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                            @empty
                                <li><span class="dropdown-item text-muted text-center small py-3">لا توجد تنبيهات جديدة</span></li>
                            @endforelse
                            <li><a class="dropdown-item text-center small text-primary fw-bold py-2" href="{{ route('notifications.index') }}">عرض الكل</a></li>
                        </ul>
                    </div>

                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center nav-user-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center nav-user-avatar">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 mt-2 text-end dropdown-menu-premium">
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('dashboard') }}">
                                    <i class="bi bi-speedometer2 me-2"></i>لوحة التحكم
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person me-2"></i>الملف الشخصي
                                </a>
                            </li>
                            @if(auth()->user()->role === 'admin')
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-shield-check me-2"></i>لوحة الإدارة
                                    </a>
                                </li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger py-2">
                                        <i class="bi bi-box-arrow-right me-2"></i>تسجيل الخروج
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill px-4 py-2">تسجيل الدخول</a>
                    <a href="{{ route('register') }}" class="btn btn-primary rounded-pill px-4 py-2">إنشاء حساب</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<div class="modal fade smart-search-modal" id="smartSearchModal" tabindex="-1" aria-labelledby="smartSearchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down modal-lg">
        <div class="modal-content border-0">
            <div class="modal-body p-0">
                <div class="smart-search-panel">
                    <div class="smart-search-panel__header">
                        <div>
                            <span class="smart-search-panel__badge">
                                <i class="bi bi-stars"></i>
                                بحث ذكي
                            </span>
                            <h2 id="smartSearchModalLabel" class="h3 fw-bold mb-2">ابحث عن الهاتف المناسب بلغتك الطبيعية</h2>
                            <p class="text-muted mb-0">اكتب اسم جهاز أو صف احتياجك مثل: موبايل للألعاب بسعر 400$</p>
                        </div>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form class="smart-search-form" action="{{ route('search') }}" method="GET" role="search">
                        <div class="smart-search-field">
                            <span class="smart-search-field__icon" aria-hidden="true">
                                <i class="bi bi-search"></i>
                            </span>
                            <input
                                type="search"
                                name="q"
                                class="form-control smart-search-field__input"
                                value="{{ $searchQuery }}"
                                placeholder="ابحث عن جهاز أو اكتب: موبايل للألعاب بسعر 400$"
                                aria-label="البحث الذكي عن الهواتف"
                                dir="auto"
                                required
                            >
                            <button type="submit" class="btn btn-primary smart-search-field__button">
                                <span>ابدأ البحث</span>
                                <i class="bi bi-arrow-left-short fs-4"></i>
                            </button>
                        </div>
                    </form>

                    <div class="smart-search-suggestions">
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill smart-search-chip" data-query="بدي موبايل قوي للألعاب بسعر 400$">ألعاب تحت 400$</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill smart-search-chip" data-query="أريد iPhone مستعمل بكاميرا قوية">iPhone مستعمل بكاميرا قوية</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill smart-search-chip" data-query="هاتف ببطارية قوية للاستخدام اليومي">بطارية قوية</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.querySelector('.navbar-floating');
        const searchModal = document.getElementById('smartSearchModal');

        if (navbar) {
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
        }

        if (!searchModal) {
            return;
        }

        const input = searchModal.querySelector('input[name="q"]');

        searchModal.addEventListener('shown.bs.modal', function() {
            input?.focus();
            input?.select();
        });

        searchModal.querySelectorAll('.smart-search-chip').forEach(function(button) {
            button.addEventListener('click', function() {
                if (!input) {
                    return;
                }

                input.value = button.dataset.query || '';
                input.focus();
            });
        });
    });
</script>
