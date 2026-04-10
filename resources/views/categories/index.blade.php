@extends('layouts.app')

@section('title', 'كل التصنيفات')

@section('content')

<!-- Breadcrumb -->
<div class="container mt-4">
    <x-breadcrumb :items="[
        ['label' => 'الرئيسية', 'url' => route('home')],
        ['label' => 'التصنيفات']
    ]" />
</div>

<!-- Categories Page -->
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold mb-3">تصفح كل التصنيفات</h1>
            <p class="lead text-muted">استكشف مجموعتنا الكاملة من تصنيفات الأجهزة</p>
        </div>

        <div class="row g-4">
            @forelse($categories as $category)
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('categories.show', $category->slug) }}" class="text-decoration-none">
                        <div class="category-card h-100">
                            <div class="category-icon">
                                <i class="bi bi-phone"></i>
                            </div>
                            <h5 class="mb-2">{{ $category->name }}</h5>
                            <small class="text-muted">{{ $category->products_count }} منتج</small>
                            @if($category->description)
                                <p class="text-muted small mt-2 mb-0">{{ Str::limit($category->description, 60) }}</p>
                            @endif
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-5">
                    <i class="bi bi-inbox fs-1 mb-3 d-block"></i>
                    <h4>لا توجد تصنيفات متاحة</h4>
                    <p>ستظهر التصنيفات هنا بمجرد إضافتها.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

@endsection
