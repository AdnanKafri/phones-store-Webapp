@extends('layouts.app')

@section('title', 'سوق الجوالات - PhoneMarket')

@section('content')
@php
    $featuredProducts = $feedItems
        ->filter(fn ($item) => class_basename($item) === 'Product')
        ->take(4);

    $featuredRequests = $feedItems
        ->filter(fn ($item) => class_basename($item) === 'DeviceRequest')
        ->take(2);

    $availableProductsCount = $feedItems
        ->filter(fn ($item) => class_basename($item) === 'Product' && $item->status === 'available')
        ->count();
@endphp

<div class="home-page">
    <section class="home-hero">
        <div class="container">
            <div class="home-hero__shell">
                <div class="row align-items-center g-4">
                    <div class="col-lg-7">
                        <span class="home-hero__eyebrow">
                            <i class="bi bi-stars"></i>
                            سوق ذكي للهواتف الجديدة والمستعملة
                        </span>
                        <h1 class="home-hero__title">ابحث، قارن، واشترِ الهاتف المناسب بثقة وسرعة</h1>
                        <p class="home-hero__copy">
                            منصة تجمع بين السوق المفتوح والتجربة الذكية، لتصل إلى أجهزة موثوقة وعروض حقيقية
                            وطلبات شراء مباشرة من المستخدمين.
                        </p>

                        <div class="home-hero__actions">
                            <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#smartSearchModal">
                                <i class="bi bi-search me-2"></i>ابدأ بالبحث الذكي
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                                تصفح الأجهزة
                            </a>
                            @auth
                                <a href="{{ route('products.create') }}" class="btn btn-light rounded-pill px-4 home-hero__ghost">
                                    <i class="bi bi-plus-lg me-2"></i>أضف إعلانك
                                </a>
                            @endauth
                        </div>

                        <div class="home-hero__metrics">
                            <div class="home-metric-card">
                                <strong>{{ $availableProductsCount }}</strong>
                                <span>جهاز متاح الآن</span>
                            </div>
                            <div class="home-metric-card">
                                <strong>{{ $categories->count() }}</strong>
                                <span>تصنيف منظم</span>
                            </div>
                            <div class="home-metric-card">
                                <strong>{{ $featuredRequests->count() }}</strong>
                                <span>طلبات شراء حديثة</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="home-hero__panel">
                            <div class="home-hero__panel-header">
                                <span>لقطة سريعة من السوق</span>
                                <span class="badge text-bg-light">مباشر</span>
                            </div>

                            <div class="home-hero__mini-search">
                                <i class="bi bi-lightning-charge"></i>
                                <span>موبايل للألعاب بسعر 400$</span>
                            </div>

                            <div class="home-hero__highlights">
                                @foreach($featuredProducts->take(3) as $product)
                                    <a href="{{ route('product.show', $product) }}" class="home-hero__highlight-card">
                                        <div class="home-hero__highlight-media">
                                            @if($product->images->count() > 0)
                                                <img src="{{ $product->primary_image_url }}" alt="{{ $product->brand }} {{ $product->model }}">
                                            @else
                                                <i class="bi bi-phone"></i>
                                            @endif
                                        </div>
                                        <div class="home-hero__highlight-body">
                                            <strong>{{ $product->brand }} {{ $product->model }}</strong>
                                            <span>{{ number_format($product->price, 0) }} $</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="home-categories">
        <div class="container">
            <div class="section-heading">
                <div>
                    <span class="section-heading__eyebrow">ابدأ من التصنيف</span>
                    <h2 class="section-heading__title">اكتشف أشهر العلامات بسرعة</h2>
                </div>
                <a href="{{ route('categories.index') }}" class="btn btn-outline-primary rounded-pill px-4">عرض الكل</a>
            </div>

            <div class="home-categories__rail">
                @foreach($categories->take(8) as $category)
                    <a href="{{ route('categories.show', $category->slug) }}" class="home-category-card">
                        <span class="home-category-card__icon">
                            <i class="bi bi-phone"></i>
                        </span>
                        <strong>{{ $category->name }}</strong>
                        <small>{{ $category->products_count }} جهاز</small>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="home-featured section-padding">
        <div class="container">
            <div class="section-heading">
                <div>
                    <span class="section-heading__eyebrow">منتجات مميزة</span>
                    <h2 class="section-heading__title">عروض تلفت النظر من السوق الآن</h2>
                </div>
                <a href="{{ route('products.index') }}" class="btn btn-outline-primary rounded-pill px-4">تصفح كل العروض</a>
            </div>

            <div class="row g-4">
                @forelse($featuredProducts as $product)
                    <div class="col-md-6 col-xl-3">
                        <a href="{{ route('product.show', $product) }}" class="home-product-card">
                            <div class="home-product-card__media">
                                @if($product->images->count() > 0)
                                    <img src="{{ $product->primary_image_url }}" alt="{{ $product->brand }} {{ $product->model }}">
                                @else
                                    <div class="home-product-card__placeholder">
                                        <i class="bi bi-phone"></i>
                                    </div>
                                @endif
                                <span class="badge {{ $product->condition === 'new' ? 'text-bg-success' : 'text-bg-warning' }} home-product-card__badge">
                                    {{ $product->condition === 'new' ? 'جديد' : 'مستعمل' }}
                                </span>
                            </div>
                            <div class="home-product-card__body">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <h3 class="h6 fw-bold mb-1">{{ $product->brand }} {{ $product->model }}</h3>
                                        <p class="text-muted small mb-0">{{ $product->category?->name }}</p>
                                    </div>
                                    <span class="home-product-card__price">{{ number_format($product->price, 0) }} $</span>
                                </div>
                                <div class="home-product-card__meta">
                                    <span><i class="bi bi-person"></i>{{ $product->seller->name ?? 'البائع' }}</span>
                                    <span><i class="bi bi-clock"></i>{{ $product->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="home-empty-state">
                            <i class="bi bi-inbox"></i>
                            <h3 class="h5 fw-bold">لا توجد عروض مميزة حاليًا</h3>
                            <p class="text-muted mb-0">سنُظهر هنا أفضل العروض بمجرد توفرها في السوق.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="home-feed section-padding">
        <div class="container">
            <div class="section-heading">
                <div>
                    <span class="section-heading__eyebrow">الحراك المباشر</span>
                    <h2 class="section-heading__title">تابع الإعلانات والطلبات الجديدة لحظة بلحظة</h2>
                </div>
                <div class="home-feed__filters">
                    <button class="btn btn-sm btn-primary rounded-pill px-4 active" onclick="filterFeed(event, 'all')">
                        <i class="bi bi-grid-fill me-1"></i>الكل
                    </button>
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-4" onclick="filterFeed(event, 'products')">
                        <i class="bi bi-phone me-1"></i>للبيع
                    </button>
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-4" onclick="filterFeed(event, 'requests')">
                        <i class="bi bi-broadcast me-1"></i>مطلوب
                    </button>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-xl-8">
                    <div class="d-flex flex-column gap-4">
                        @forelse($feedItems as $item)
                            @if(class_basename($item) === 'Product')
                                <article class="market-feed-card feed-item {{ $item->source === 'inventory' && $item->isOutOfStock() ? 'market-feed-card--muted' : '' }}" data-type="product">
                                    <div class="market-feed-card__header">
                                        <div class="market-feed-card__author">
                                            <span class="market-feed-card__avatar">{{ strtoupper(substr($item->seller->name ?? 'U', 0, 1)) }}</span>
                                            <div>
                                                <strong>{{ $item->seller->name ?? 'مستخدم' }}</strong>
                                                <div class="text-muted small">{{ $item->created_at->diffForHumans() }}</div>
                                            </div>
                                        </div>

                                        <div class="market-feed-card__badges">
                                            <span class="badge text-bg-light">للبيع</span>
                                            @if($item->source === 'inventory' && $item->isOutOfStock())
                                                <span class="badge text-bg-danger">نفدت الكمية</span>
                                            @elseif($item->status === 'sold')
                                                <span class="badge text-bg-secondary">مباع</span>
                                            @elseif($item->condition === 'new')
                                                <span class="badge text-bg-success">جديد</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="market-feed-card__content">
                                        <div>
                                            <h3 class="h5 fw-bold mb-2">{{ $item->brand }} {{ $item->model }}</h3>
                                            <p class="text-muted mb-0">
                                                {{ $item->description ? \Illuminate\Support\Str::limit($item->description, 120) : 'عرض جديد متاح الآن ضمن السوق.' }}
                                            </p>
                                        </div>

                                        <div class="market-feed-card__price">{{ number_format($item->price, 0) }} $</div>
                                    </div>

                                    <div class="market-feed-card__media">
                                        @if($item->images->count() > 0)
                                            <img src="{{ $item->primary_image_url }}" alt="{{ $item->brand }} {{ $item->model }}">
                                        @else
                                            <div class="market-feed-card__placeholder">
                                                <i class="bi bi-phone"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="market-feed-card__footer">
                                        <div class="market-feed-card__meta">
                                            <span><i class="bi bi-tag"></i>{{ $item->category?->name }}</span>
                                            <span><i class="bi bi-box-seam"></i>{{ $item->source === 'inventory' ? 'من المتجر' : 'إعلان مستخدم' }}</span>
                                        </div>

                                        @if($item->source === 'inventory' && $item->isOutOfStock())
                                            <button class="btn btn-secondary rounded-pill px-4" disabled>غير متوفر</button>
                                        @else
                                            <a href="{{ route('product.show', $item) }}" class="btn btn-primary rounded-pill px-4">عرض التفاصيل</a>
                                        @endif
                                    </div>
                                </article>
                            @elseif(class_basename($item) === 'DeviceRequest')
                                <article class="market-feed-card market-feed-card--request feed-item" data-type="request">
                                    <div class="market-feed-card__header">
                                        <div class="market-feed-card__author">
                                            <span class="market-feed-card__avatar market-feed-card__avatar--request">
                                                <i class="bi bi-search"></i>
                                            </span>
                                            <div>
                                                <strong>{{ $item->user->name ?? 'مستخدم' }}</strong>
                                                <div class="text-muted small">{{ $item->created_at->diffForHumans() }}</div>
                                            </div>
                                        </div>

                                        <div class="market-feed-card__badges">
                                            <span class="badge text-bg-primary">مطلوب</span>
                                        </div>
                                    </div>

                                    <div class="market-feed-card__content market-feed-card__content--request">
                                        <div>
                                            <h3 class="h5 fw-bold mb-2">يبحث عن {{ $item->brand }} {{ $item->model }}</h3>
                                            <p class="text-muted mb-0">{{ $item->notes ?: 'طلب شراء جديد ينتظر من يملك هذا الجهاز.' }}</p>
                                        </div>
                                    </div>

                                    <div class="market-feed-card__footer">
                                        <div class="market-feed-card__meta">
                                            <span><i class="bi bi-broadcast"></i>طلب نشط في السوق</span>
                                        </div>

                                        @auth
                                            @if(auth()->id() !== $item->user_id)
                                                <form action="{{ route('device-requests.offer', $item) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success rounded-pill px-4">
                                                        لدي هذا الجهاز
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted small">هذا طلبك الحالي</span>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}" class="btn btn-light rounded-pill px-4">سجّل دخولك للرد</a>
                                        @endauth
                                    </div>
                                </article>
                            @endif
                        @empty
                            <div class="home-empty-state">
                                <i class="bi bi-inbox"></i>
                                <h3 class="h5 fw-bold">لا توجد عناصر في السوق حاليًا</h3>
                                <p class="text-muted mb-0">ستظهر هنا أحدث الإعلانات والطلبات بمجرد إضافتها.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="home-side-stack">
                        <div class="home-side-panel">
                            <span class="section-heading__eyebrow">لماذا PhoneMarket؟</span>
                            <h3 class="h4 fw-bold mb-3">تجربة سوق أوضح وأسرع</h3>
                            <div class="home-benefit-list">
                                <div class="home-benefit-item">
                                    <i class="bi bi-stars"></i>
                                    <div>
                                        <strong>بحث ذكي</strong>
                                        <span>افهم احتياجك الطبيعي وحوّله إلى نتائج فعلية.</span>
                                    </div>
                                </div>
                                <div class="home-benefit-item">
                                    <i class="bi bi-layout-split"></i>
                                    <div>
                                        <strong>مقارنة مباشرة</strong>
                                        <span>قارن المواصفات قبل الشراء دون مغادرة المنصة.</span>
                                    </div>
                                </div>
                                <div class="home-benefit-item">
                                    <i class="bi bi-shield-check"></i>
                                    <div>
                                        <strong>سوق موثوق</strong>
                                        <span>إعلانات وطلبات منظمة مع تجربة واضحة للمستخدمين.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($featuredRequests->isNotEmpty())
                            <div class="home-side-panel">
                                <div class="section-heading section-heading--compact">
                                    <div>
                                        <span class="section-heading__eyebrow">طلبات حديثة</span>
                                        <h3 class="h5 fw-bold mb-0">مستخدمون يبحثون الآن</h3>
                                    </div>
                                </div>

                                <div class="home-request-list">
                                    @foreach($featuredRequests as $request)
                                        <div class="home-request-item">
                                            <div>
                                                <strong>{{ $request->brand }} {{ $request->model }}</strong>
                                                <p class="text-muted small mb-0">{{ $request->notes ?: 'طلب جديد من أحد المستخدمين.' }}</p>
                                            </div>
                                            <span class="text-muted small">{{ $request->created_at->diffForHumans() }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function filterFeed(event, type) {
    const items = document.querySelectorAll('.feed-item');
    const buttons = document.querySelectorAll('[onclick^="filterFeed"]');
    const targetButton = event?.currentTarget;

    buttons.forEach((button) => {
        button.classList.remove('btn-primary', 'active');
        button.classList.add('btn-outline-primary');
    });

    if (targetButton) {
        targetButton.classList.remove('btn-outline-primary');
        targetButton.classList.add('btn-primary', 'active');
    }

    items.forEach((item) => {
        const shouldShow = type === 'all' || (type === 'products' && item.dataset.type === 'product') || (type === 'requests' && item.dataset.type === 'request');
        item.style.display = shouldShow ? '' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('form[action*="/offer"]').forEach((form) => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = this.querySelector('button');
            const originalContent = btn.innerHTML;

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
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    btn.classList.remove('btn-outline-success');
                    btn.classList.add('btn-success');
                    btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> تم الإرسال بنجاح';
                    showToast(data.message);
                    return;
                }

                btn.disabled = false;
                btn.innerHTML = originalContent;
                alert(data.message || 'حدث خطأ غير متوقع.');
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = originalContent;
                alert('حدث خطأ في الاتصال، يرجى المحاولة لاحقًا.');
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

        if (toastEl) {
            toastEl.remove();
        }
    }, 3000);
}
</script>
@endsection
