@extends('layouts.app')

@section('title', 'نتائج البحث: ' . $query)

@section('content')
<div class="container py-5">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
                <li class="breadcrumb-item active" aria-current="page">نتائج البحث</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-1">نتائج البحث عن: <span class="text-primary">"{{ $query }}"</span></h2>
        <p class="text-muted">نظهر لك المنتجات وطلبات الأجهزة المطابقة لبحثك.</p>
    </div>

    @if($products->isEmpty() && $requests->isEmpty())
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="bi bi-search text-muted" style="font-size: 4rem;"></i>
            </div>
            <h4>عذراً، لم نعثر على نتائج</h4>
            <p class="text-muted">حاول استخدام كلمات البحث المختلفة أو تصفح الأقسام.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary rounded-pill px-4 mt-2">تصفح جميع المنتجات</a>
        </div>
    @else
        <!-- Products Results -->
        @if($products->isNotEmpty())
            <h4 class="fw-bold mb-3 mt-4"><i class="bi bi-phone me-2"></i>المنتجات المعروضة</h4>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mb-5">
                @foreach($products as $product)
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm rounded-4 product-card">
                            <div class="position-relative">
                                @if($product->images->count() > 0)
                                    <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" class="card-img-top rounded-top-4" alt="{{ $product->brand }}" style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center rounded-top-4" style="height: 200px;">
                                        <i class="bi bi-phone fs-1 text-muted"></i>
                                    </div>
                                @endif
                                <span class="badge bg-{{ $product->condition == 'new' ? 'success' : 'warning' }} position-absolute top-0 end-0 m-3">{{ $product->condition == 'new' ? 'جديد' : 'مستعمل' }}</span>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title fw-bold text-truncate">{{ $product->brand }} {{ $product->model }}</h5>
                                <p class="card-text text-primary fw-bold mb-1">${{ number_format($product->price, 2) }}</p>
                                <div class="d-flex justify-content-between align-items-center small text-muted">
                                    <span><i class="bi bi-geo-alt me-1"></i>{{ $product->seller->city ?? 'سوريا' }}</span>
                                    <span>{{ $product->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-0 pt-0 pb-3">
                                <a href="{{ route('product.show', $product) }}" class="btn btn-outline-primary w-100 rounded-pill">التفاصيل</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Device Requests Results -->
        @if($requests->isNotEmpty())
            <h4 class="fw-bold mb-3 mt-4"><i class="bi bi-megaphone me-2"></i>طلبات الأجهزة</h4>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach($requests as $request)
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm rounded-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary p-3 me-3">
                                        <i class="bi bi-person fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0">{{ $request->user->name }}</h6>
                                        <small class="text-muted">يطلب جهاز</small>
                                    </div>
                                </div>
                                <h5 class="fw-bold text-primary mb-2">{{ $request->brand }} {{ $request->model }}</h5>
                                <p class="text-muted small mb-3">{{ Str::limit($request->notes, 80) }}</p>
                                
                                @auth
                                    @if(auth()->id() !== $request->user_id)
                                        <form action="{{ route('device-requests.offer', $request) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success w-100 rounded-pill">
                                                <i class="bi bi-check2-circle me-1"></i> لدي هذا الجهاز
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-sm btn-light w-100 rounded-pill">سجل دخولك للرد</a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('form[action*="/offer"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button');
            const originalContent = btn.innerHTML;
            
            // Loading State
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> جاري الإرسال...';
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    btn.classList.remove('btn-outline-success');
                    btn.classList.add('btn-success');
                    btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> تم الإرسال بنجاح';
                    
                    // Show Toast Notification
                    showToast(data.message);
                } else {
                    btn.innerHTML = originalContent;
                    alert(data.message || 'حدث خطأ ما');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.disabled = false;
                btn.innerHTML = originalContent;
                alert('حدث خطأ في الاتصال، يرجى المحاولة لاحقاً.');
            });
        });
    });
});

function showToast(message) {
    const toastHtml = `
        <div class="position-fixed bottom-0 start-0 p-3" style="z-index: 1100">
            <div id="liveToast" class="toast align-items-center text-white bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle me-2"></i> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', toastHtml);
    setTimeout(() => {
        const toastEl = document.getElementById('liveToast');
        if (toastEl) toastEl.remove();
    }, 3000);
}
</script>
@endsection
