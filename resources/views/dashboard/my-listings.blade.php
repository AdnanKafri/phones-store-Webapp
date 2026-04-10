@extends('layouts.dashboard')

@section('title', 'إعلاناتي')

@section('dashboard-content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">إعلاناتي</h4>
            <p class="text-muted mb-0 small">إدارة جميع إعلانات المنتجات الخاصة بك</p>
        </div>
        <a href="{{ route('products.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-lg me-2"></i>إضافة إعلان جديد
        </a>
    </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            @forelse($products as $product)
                <div class="col-md-6 col-lg-3">
                    <div class="product-card h-100 position-relative">
                        <span class="badge bg-{{ $product->status === 'available' ? 'success' : 'secondary' }} position-absolute top-0 start-0 m-3 z-2">
                             @if($product->status == 'available') متاح @elseif($product->status == 'sold') مباع @else مخفي @endif
                        </span>
                        <div class="product-img-container">
                            @if($product->images->count() > 0)
                                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->brand }} {{ $product->model }}">
                            @else
                                <img src="https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?q=80&w=2670&auto=format&fit=crop" alt="{{ $product->brand }} {{ $product->model }}">
                            @endif
                        </div>
                        <div class="product-body">
                            <h5 class="mb-1 text-truncate">{{ $product->brand }} {{ $product->model }}</h5>
                            <p class="text-muted small mb-3">{{ $product->category->name }}</p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="product-price" dir="ltr">${{ number_format($product->price, 2) }}</span>
                                <span class="badge bg-{{ $product->condition === 'new' ? 'success' : 'warning' }}">{{ $product->condition === 'new' ? 'جديد' : 'مستعمل' }}</span>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                    <i class="bi bi-pencil me-1"></i> تعديل
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الإعلان؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill w-100">
                                        <i class="bi bi-trash me-1"></i> حذف
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                    <h4>لا توجد إعلانات حتى الآن</h4>
                    <p class="text-muted mb-4">لم تقم بإضافة أي إعلانات بيع بعد.</p>
                    <a href="{{ route('products.create') }}" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-plus-lg me-2"></i> أضف إعلانك الأول
                    </a>
                </div>
            @endforelse
        </div>

        @if($products->hasPages())
            <div class="d-flex justify-content-center mt-5">
                {{ $products->links() }}
            </div>
        @endif
@endsection
