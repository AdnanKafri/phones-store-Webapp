<nav class="navbar navbar-expand-lg navbar-floating">
    <div class="container-fluid">
        <a class="navbar-brand logo-brand" href="{{ url('/') }}">
            <i class="bi bi-phone"></i> PhoneMarket
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-center" style="font-family: 'Cairo', sans-serif;">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">الرئيسية</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">المنتجات</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">التصنيفات</a>
                </li>
                <li class="nav-item d-lg-block d-none mx-2">
                    <form class="nav-ai-search" action="{{ route('search') }}" method="GET">
                        <div class="nav-ai-search__icon">
                            <i class="bi bi-stars"></i>
                        </div>
                        <input
                            class="form-control nav-search-input py-2"
                            type="search"
                            name="q"
                            placeholder="ابحث بالذكاء الاصطناعي: موبايل ألعاب تحت 450$"
                            value="{{ request('q') }}"
                            aria-label="AI phone search"
                        >
                        <button type="submit" class="btn nav-ai-search__button">
                            <span class="d-none d-xl-inline">بحث ذكي</span>
                            <i class="bi bi-arrow-left-short fs-5"></i>
                        </button>
                    </form>
                </li>
                <li class="nav-item">
                     <a class="nav-link {{ request()->routeIs('device-requests.create') ? 'active' : '' }}" href="{{ route('device-requests.create') }}">اطلب جهاز</a>
                </li>
            </ul>
            
            <div class="d-flex align-items-center gap-2">
                @auth
                    <!-- Sell Button -->
                    <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                        <i class="bi bi-plus-lg"></i><span class="d-none d-md-inline">بيع</span>
                    </a>



                    <!-- Notifications Dropdown -->
                    <div class="dropdown">
                        <a href="#" class="position-relative text-dark fs-5 me-2" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell"></i>
                            @if(auth()->user()->unreadNotifications()->exists())
                                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2" style="width: 300px; max-height: 400px; overflow-y: auto;">
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
                    
                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center me-2" style="width: 35px; height: 35px;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 mt-2 text-end">
                            <li><a class="dropdown-item py-2" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>لوحة التحكم</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>الملف الشخصي</a></li>
                            @if(auth()->user()->role === 'admin')
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item py-2" href="{{ route('admin.dashboard') }}"><i class="bi bi-shield-check me-2"></i>لوحة الإدارة</a></li>
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
                    <!-- Guest Menu -->
                    <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill px-4 me-2">تسجيل الدخول</a>
                    <a href="{{ route('register') }}" class="btn btn-primary rounded-pill px-4">إنشاء حساب</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<div class="container d-lg-none mt-3">
    <form class="nav-ai-search nav-ai-search--mobile" action="{{ route('search') }}" method="GET">
        <div class="nav-ai-search__icon">
            <i class="bi bi-stars"></i>
        </div>
        <input
            class="form-control nav-search-input py-2"
            type="search"
            name="q"
            placeholder="ابحث بالذكاء الاصطناعي عن الهاتف المناسب"
            value="{{ request('q') }}"
            aria-label="AI phone search"
        >
        <button type="submit" class="btn nav-ai-search__button">
            <i class="bi bi-search"></i>
        </button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.querySelector('.navbar-floating');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    });
</script>
