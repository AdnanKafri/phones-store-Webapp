<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة التحكم') - فون ماركت</title>
    
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" integrity="sha384-dpuaG1suU0eT09tx5plTaGMLBsfDLzUCCUXOY2j/LSvXYuG6Bqs43ALlhIqAJVRb" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --sidebar-width: 260px;
            --header-height: 60px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            right: 0; /* RTL: Start from right */
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: -2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            font-size: 1.5rem;
            font-weight: 700;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-item {
            margin: 0.25rem 0.75rem;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: white;
        }
        
        .nav-link.active {
            background-color: rgba(255,255,255,0.2);
            color: white;
            font-weight: 600;
        }
        
        .nav-link i {
            margin-left: 0.75rem; /* RTL: Icon margin */
            font-size: 1.1rem;
        }
        
        /* Main Content */
        .main-content {
            margin-right: var(--sidebar-width); /* RTL: Margin on right */
            min-height: 100vh;
        }
        
        /* Header */
        .top-header {
            background: white;
            height: var(--header-height);
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        
        .header-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        /* Content Area */
        .content-wrapper {
            padding: 2rem;
        }
        
        /* Cards */
        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }
        
        .stat-card .card-body {
            padding: 1.5rem;
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin: 0.5rem 0 0.25rem;
        }
        
        .stat-label {
            color: #64748b;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        /* Tables */
        .table-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        
        .table-card .card-header {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            color: #1e293b;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            color: #475569;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem;
            text-align: right; /* RTL */
        }
        
        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }
        
        /* Buttons */
        .btn {
            border-radius: 8px;
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        
        /* Badges */
        .badge {
            padding: 0.4rem 0.75rem;
            border-radius: 6px;
            font-weight: 500;
        }
        
        /* Forms */
        .form-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 0.625rem 0.875rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        /* Breadcrumb */
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 1.5rem;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            content: "›"; /* In RTL this should visually point left, but standard chevron is fine */
            color: #94a3b8;
            float: right; /* RTL fix */
            padding-left: 0.5rem;
        }
        
        /* Page Header */
        .page-header {
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }
        
        .page-subtitle {
            color: #64748b;
            font-size: 1rem;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-phone"></i> إدارة فون ماركت
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>الرئيسية</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.orders') }}" class="nav-link {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
                        <i class="bi bi-cart"></i>
                        <span>الطلبات</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>المستخدمين</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <i class="bi bi-grid"></i>
                        <span>التصنيفات</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.inventory.index') }}" class="nav-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam-fill"></i>
                        <span>المخزون</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <i class="bi bi-list-ul"></i>
                        <span>كل الإعلانات</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.device-requests.index') }}" class="nav-link {{ request()->routeIs('admin.device-requests.*') ? 'active' : '' }}">
                        <i class="bi bi-phone-vibrate"></i>
                        <span>طلبات الأجهزة</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.transactions.index') }}" class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                        <i class="bi bi-receipt"></i>
                        <span>المعاملات المالية</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.payment-requests.index') }}" class="nav-link {{ request()->routeIs('admin.payment-requests.*') ? 'active' : '' }}">
                        <i class="bi bi-wallet2"></i>
                        <span>طلبات الشحن</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.wallets.index') }}" class="nav-link {{ request()->routeIs('admin.wallets.*') ? 'active' : '' }}">
                        <i class="bi bi-credit-card"></i>
                        <span>المحافظ</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Header -->
        <header class="top-header">
            <h1 class="header-title">@yield('page-title', 'لوحة التحكم')</h1>
            
            <div class="user-menu">
                <div class="dropdown">
                    <button class="btn btn-link text-decoration-none dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="text-dark">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="bi bi-house me-2"></i>لوحة المستخدم</a></li>
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>الملف الشخصي</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>تسجيل الخروج
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>
        
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @yield('content')
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
