@extends('layouts.app')

@section('title', 'كل المنتجات')

@section('content')

<!-- Breadcrumb -->
<div class="container mt-4">
    <x-breadcrumb :items="[
        ['label' => 'الرئيسية', 'url' => route('home')],
        ['label' => 'المنتجات']
    ]" />
</div>

<!-- Products Page -->
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold mb-3">كل المنتجات</h1>
            <p class="lead text-muted">تصفح مجموعتنا الكاملة من الأجهزة المحمولة</p>
            <p class="text-muted">{{ $products->total() }} منتج متوفر</p>
        </div>

        <!-- Filter Bar -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="bg-white p-4 rounded-4 shadow-sm">
                    <form action="{{ route('products.index') }}" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small text-muted text-uppercase fw-bold">المصدر</label>
                            <select name="source" class="form-select rounded-pill" onchange="this.form.submit()">
                                <option value="">كل المصادر</option>
                                <option value="inventory" {{ request('source') == 'inventory' ? 'selected' : '' }}>متجر فون ماركت (جديد)</option>
                                <option value="user" {{ request('source') == 'user' ? 'selected' : '' }}>بائعين خواص (مستعمل)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted text-uppercase fw-bold">الحالة</label>
                            <select name="status" class="form-select rounded-pill" onchange="this.form.submit()">
                                <option value="all">كل الحالات</option>
                                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>متاح</option>
                                <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>مباع</option>
                                <option value="hidden" {{ request('status') == 'hidden' ? 'selected' : '' }}>مخفي</option>
                            </select>
                        </div>
                        <div class="col-md-4 text-md-end">
                             <a href="{{ route('categories.index') }}" class="btn btn-outline-primary rounded-pill">
                                <i class="bi bi-funnel me-2"></i>تصفية حسب التصنيف
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="row g-4">
            @forelse($products as $product)
                <div class="col-md-6 col-lg-3">
                    <div class="product-card h-100 position-relative">
                        @if($product->condition === 'new')
                            <span class="badge bg-success position-absolute top-0 start-0 m-3 z-2">جديد</span>
                        @else
                            <span class="badge bg-warning position-absolute top-0 start-0 m-3 z-2">مستعمل</span>
                        @endif
                        
                        @if($product->status !== 'available')
                            <span class="badge bg-danger position-absolute top-0 end-0 m-3 z-2">
                                @if($product->status == 'sold') مباع @elseif($product->status == 'hidden') مخفي @else {{ $product->status }} @endif
                            </span>
                        @endif
                        <div class="product-img-container">
                            @if($product->images->count() > 0)
                                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                                     alt="{{ $product->brand }} {{ $product->model }}">
                            @else
                                <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center text-muted">
                                    <i class="bi bi-phone fs-1"></i>
                                </div>
                            @endif
                        </div>
                        <div class="product-body">
                            <h5 class="mb-1 text-truncate">{{ $product->brand }} {{ $product->model }}</h5>
                            <p class="text-muted small mb-3">{{ $product->category->name }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="product-price" dir="ltr">${{ number_format($product->price, 2) }}</span>
                                <a href="{{ route('product.show', $product) }}" class="btn btn-sm btn-outline-primary rounded-pill"><i class="bi bi-eye"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-5">
                    <i class="bi bi-inbox fs-1 mb-3 d-block"></i>
                    <h4>لا توجد منتجات متاحة</h4>
                    <p>تحقق لاحقاً من وجود عروض جديدة.</p>
                    <a href="{{ route('home') }}" class="btn btn-primary rounded-pill mt-3">الذهاب للرئيسية</a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="d-flex justify-content-center mt-5">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</section>

@endsection
