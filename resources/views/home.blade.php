@extends('layouts.app')

@section('title', 'سوق الجوالات - PhoneMarket')

@section('content')

<!-- Hero Section -->
<section class="py-4 bg-white border-bottom">
    <div class="container text-center">
        <h1 class="h2 fw-bold mb-2" style="font-family: 'Cairo', sans-serif;">سوق الجوالات</h1>
        <p class="text-muted mb-3">اشتري وبيع الجوالات بكل سهولة</p>
        <div class="d-flex justify-content-center gap-2">
            <a href="{{ route('products.index') }}" class="btn btn-primary rounded-pill px-4">تصفح الأجهزة</a>
            @auth
                <a href="{{ route('products.create') }}" class="btn btn-outline-primary rounded-pill px-4">بيع جهازك</a>
            @endauth
        </div>
    </div>
</section>

<!-- Categories Filter Bar -->
<section class="py-2 bg-light border-bottom">
    <div class="container">
        <div class="d-flex align-items-center gap-2">
            <small class="text-muted text-nowrap">التصنيفات:</small>
            <div class="d-flex gap-2 overflow-auto flex-grow-1" style="scrollbar-width: thin;">
                <a href="{{ route('home') }}" class="badge bg-primary text-decoration-none py-2 px-3">الكل</a>
                @foreach($categories->take(8) as $category)
                    <a href="{{ route('categories.show', $category->slug) }}" class="badge bg-white text-dark border text-decoration-none py-2 px-3 text-nowrap">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- Feed Timeline -->
<section class="py-4 bg-light">
    <div class="container">
        <!-- Filter Pills -->
        <div class="mb-3 d-flex justify-content-center gap-2">
            <button class="btn btn-sm btn-primary rounded-pill px-4 active" onclick="filterFeed('all')">
                <i class="bi bi-grid-fill me-1"></i>الكل
            </button>
            <button class="btn btn-sm btn-outline-primary rounded-pill px-4" onclick="filterFeed('products')">
                <i class="bi bi-phone me-1"></i>للبيع
            </button>
            <button class="btn btn-sm btn-outline-primary rounded-pill px-4" onclick="filterFeed('requests')">
                <i class="bi bi-search me-1"></i>مطلوب
            </button>
        </div>

        <!-- Feed Items (Social Media Style) -->
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <div class="d-flex flex-column gap-3">
                    @forelse($feedItems as $item)
                        @if(class_basename($item) === 'Product')
                            <!-- Product Post (Social Media Style) -->
                            <div class="card border-0 shadow-sm rounded-3 feed-item {{ $item->source === 'inventory' && $item->isOutOfStock() ? 'opacity-75' : '' }}" data-type="product" style="{{ $item->source === 'inventory' && $item->isOutOfStock() ? 'pointer-events: none;' : '' }}">
                                <!-- Post Header -->
                                <div class="card-header bg-white border-0 p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($item->seller->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="me-3 flex-grow-1">
                                            <h6 class="mb-0 fw-bold">{{ $item->seller->name ?? 'مستخدم' }}</h6>
                                            <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <span class="badge bg-primary-subtle text-primary">للبيع</span>
                                            @if($item->source === 'inventory' && $item->isOutOfStock())
                                                <span class="badge bg-danger">نفدت الكمية</span>
                                            @elseif($item->status === 'sold')
                                                <span class="badge bg-secondary">مباع</span>
                                            @elseif($item->condition === 'new')
                                                <span class="badge bg-success">جديد</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Post Content (Description) -->
                                <div class="card-body p-3 pt-0">
                                    <p class="mb-2">
                                        عرض جهاز <strong>{{ $item->brand }} {{ $item->model }}</strong> للبيع
                                        @if($item->category)
                                            • <span class="text-muted">{{ $item->category->name }}</span>
                                        @endif
                                    </p>
                                </div>

                                <!-- Product Image (Full Width) -->
                                @if($item->images->count() > 0)
                                    <div class="bg-light" style="max-height: 500px; overflow: hidden;">
                                        <img src="{{ asset('storage/' . $item->images->first()->image_path) }}" 
                                             class="w-100" 
                                             style="object-fit: cover;" 
                                             alt="{{ $item->brand }} {{ $item->model }}">
                                    </div>
                                @else
                                    <div class="d-flex align-items-center justify-content-center bg-light" style="height: 300px;">
                                        <i class="bi bi-phone text-muted" style="font-size: 4rem;"></i>
                                    </div>
                                @endif

                                <!-- Post Footer (Price & Action) -->
                                <div class="card-footer bg-white border-0 p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h4 class="mb-0 fw-bold text-primary">{{ number_format($item->price, 0) }} $</h4>
                                        @if($item->source === 'inventory' && $item->isOutOfStock())
                                            <button class="btn btn-secondary rounded-pill px-4" disabled>
                                                <i class="bi bi-x-circle me-1"></i>غير متوفر
                                            </button>
                                        @else
                                            <a href="{{ route('product.show', $item) }}" class="btn btn-primary rounded-pill px-4">
                                                <i class="bi bi-eye me-1"></i>عرض التفاصيل
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        @elseif(class_basename($item) === 'DeviceRequest')
                            <!-- Request Post (Social Media Style) -->
                            <div class="card border-0 shadow-sm rounded-3 feed-item" data-type="request" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);">
                                <!-- Post Header -->
                                <div class="card-header bg-transparent border-0 p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-white text-primary d-flex justify-content-center align-items-center border border-primary" style="width: 40px; height: 40px;">
                                            <i class="bi bi-search"></i>
                                        </div>
                                        <div class="me-3 flex-grow-1">
                                            <h6 class="mb-0 fw-bold">{{ $item->user->name ?? 'مستخدم' }}</h6>
                                            <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                                        </div>
                                        <span class="badge bg-primary">مطلوب</span>
                                    </div>
                                </div>

                                <!-- Request Content -->
                                <div class="card-body p-3 pt-0">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="bi bi-broadcast text-primary"></i>
                                        <h5 class="mb-0 fw-bold">يبحث عن: {{ $item->brand }} {{ $item->model }}</h5>
                                    </div>
                                    @if($item->notes)
                                        <p class="text-dark mb-0">{{ $item->notes }}</p>
                                    @endif
                                </div>

                                <!-- Request Visual (Icon Placeholder) -->
                                <div class="d-flex align-items-center justify-content-center bg-white bg-opacity-50 border-top border-bottom" style="height: 200px;">
                                    <div class="text-center">
                                        <i class="bi bi-phone-fill text-primary display-1 mb-2"></i>
                                        <p class="text-muted fw-bold">{{ $item->brand }} {{ $item->model }}</p>
                                    </div>
                                </div>

                                <!-- Request Footer -->
                                <div class="card-footer bg-transparent border-0 p-3">
                                    @auth
                                        @if(auth()->id() !== $item->user_id)
                                            <form action="{{ route('device-requests.offer', $item) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success w-100 rounded-pill">
                                                    <i class="bi bi-check2-circle me-1"></i> لدي هذا الجهاز
                                                </button>
                                            </form>
                                        @else
                                            <small class="text-muted d-block text-center"><i class="bi bi-person me-1"></i>هذا طلبك الخاص</small>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-sm btn-light w-100 rounded-pill">سجل دخولك للرد</a>
                                    @endauth
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-4 text-muted d-block mb-3"></i>
                            <p class="text-muted">لا توجد عناصر حالياً</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function filterFeed(type) {
    const items = document.querySelectorAll('.feed-item');
    const buttons = document.querySelectorAll('[onclick^="filterFeed"]');
    
    // Update button states
    buttons.forEach(btn => {
        btn.classList.remove('btn-primary', 'active');
        btn.classList.add('btn-outline-primary');
    });
    event.target.closest('button').classList.remove('btn-outline-primary');
    event.target.closest('button').classList.add('btn-primary', 'active');
    
    // Filter items
    items.forEach(item => {
        if (type === 'all') {
            item.style.display = 'block';
        } else if (type === 'products' && item.dataset.type === 'product') {
            item.style.display = 'block';
        } else if (type === 'requests' && item.dataset.type === 'request') {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

// Handle Offer Device Click (AJAX)
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
                    
                    // Show Toast Notification if verify
                    showToast(data.message);
                } else {
                    btn.innerHTML = originalContent; // Revert on logic error
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
    // Create Toast Element
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
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        const toastEl = document.getElementById('liveToast');
        if (toastEl) toastEl.remove();
    }, 3000);
}
</script>

@endsection
