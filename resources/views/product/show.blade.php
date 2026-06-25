@extends('layouts.app')

@section('title', $product->brand . ' ' . $product->model)

@section('content')

<!-- Breadcrumb -->
<div class="container mt-4">
    <x-breadcrumb :items="[
        ['label' => 'الرئيسية', 'url' => route('home')],
        ['label' => $product->category->name, 'url' => route('categories.show', $product->category->slug)],
        ['label' => $product->brand . ' ' . $product->model]
    ]" />
</div>

<!-- Product Hero -->
<section class="section-padding bg-white pt-5 mt-5">
    <div class="container">
        <div class="row g-5">
            <!-- Left: Gallery -->
            <div class="col-lg-7">
                <div class="product-gallery-main">
                    @if($product->images->count() > 0)
                        <img id="mainImage" src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->brand }}">
                    @else
                        <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center text-muted" style="min-height: 400px;">
                            <i class="bi bi-phone fs-1"></i>
                        </div>
                    @endif
                </div>
                <div class="thumbnail-container">
                    @foreach($product->images as $image)
                        <div class="thumbnail {{ $loop->first ? 'active' : '' }}" onclick="changeImage(this)">
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Thumbnail">
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Right: Info -->
            <div class="col-lg-5">
                <div class="ps-lg-3">
                    <div class="mb-3">
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 text-uppercase fs-7">{{ $product->category->name }}</span>
                        <span class="badge {{ $product->condition == 'new' ? 'bg-success' : 'bg-warning' }} bg-opacity-10 {{ $product->condition == 'new' ? 'text-success' : 'text-warning' }} rounded-pill px-3 py-2 text-uppercase fs-7 ms-2">
                            {{ $product->condition == 'new' ? 'جديد' : 'مستعمل' }}
                        </span>
                        
                        @if($product->source === 'inventory')
                            <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2 text-uppercase fs-7 ms-2">متجر رسمي</span>
                        @else
                            <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2 text-uppercase fs-7 ms-2">بائع خاص</span>
                        @endif

                        @if($product->source === 'inventory' && $product->isOutOfStock())
                            <span class="badge bg-danger rounded-pill px-3 py-2 text-uppercase fs-7 ms-2">نفدت الكمية</span>
                        @elseif($product->status !== 'available')
                            <span class="badge bg-danger rounded-pill px-3 py-2 text-uppercase fs-7 ms-2">
                                @if($product->status == 'sold') مباع @elseif($product->status == 'hidden') مخفي @else {{ $product->status }} @endif
                            </span>
                        @endif
                    </div>

                    <h1 class="display-5 fw-bold mb-2">{{ $product->brand }} {{ $product->model }}</h1>
                    <div class="mb-4">
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-half text-warning"></i>
                        <span class="text-muted ms-2">(4.5)</span>
                    </div>

                    <div class="mb-4">
                        <span class="product-price-large" dir="ltr">${{ number_format($product->price, 2) }}</span>
                    </div>
                    @if($compareDevice)
                        <div class="mb-4">
                            <a href="{{ route('compare.index', ['left_device_id' => $compareDevice->id]) }}" class="btn btn-outline-primary rounded-pill px-4">
                                <i class="bi bi-layout-split me-2"></i>قارن هذا الموديل
                            </a>
                        </div>
                    @endif

                    <!-- Seller Card -->
                    <div class="seller-card mb-4">
                        <div class="seller-avatar">
                            {{ strtoupper(substr($product->seller->name, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                        @if($product->source === 'inventory')
                            <h6 class="mb-0">متجر فون ماركت الرسمي</h6>
                            <small class="text-muted">
                                <i class="bi bi-check-circle-fill text-primary" style="font-size: 10px;"></i> منصة موثوقة
                            </small>
                        @else
                            <h6 class="mb-0">{{ $product->seller->name }}</h6>
                            <small class="text-muted">
                                <i class="bi bi-circle-fill text-success" style="font-size: 8px;"></i> متصل
                            </small>
                        @endif
                        </div>
                    </div>
                    
                    <!-- Conditional Purchase Section -->
                    @if($product->source === 'inventory')
                        <!-- Inventory Logic -->
                        <form action="{{ route('orders.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            
                        @if($product->isOutOfStock())
                            <!-- Out of Stock Message -->
                            <div class="alert alert-danger rounded-4 mb-3">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <strong>غير متوفر حالياً</strong>
                                <p class="mb-0 small mt-2">هذا المنتج نفدت كميته حالياً. يرجى التحقق لاحقاً.</p>
                            </div>
                            <button type="button" class="btn btn-secondary w-100 py-3 rounded-pill fw-bold mb-3" disabled>
                                <i class="bi bi-x-circle me-2"></i>غير متوفر للطلب
                            </button>
                        @else
                            <!-- Color Selection (if variants exist) -->
                            @if($product->variants->count() > 0)
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted small text-uppercase ls-1">اختر اللون</label>
                                    <div class="d-flex gap-2 flex-wrap">
                                        @foreach($product->variants as $variant)
                                            <input type="radio" class="btn-check" name="color" value="{{ $variant->id }}" id="color_{{ $variant->id }}" autocomplete="off" {{ $loop->first && $variant->stock_quantity > 0 ? 'checked' : '' }} {{ $variant->stock_quantity <= 0 ? 'disabled' : '' }}>
                                            <label class="btn {{ $variant->stock_quantity <= 0 ? 'btn-outline-secondary' : 'btn-outline-dark' }} rounded-pill px-3" for="color_{{ $variant->id }}">
                                                {{ $variant->color_name }}
                                                @if($variant->stock_quantity <= 0) <small>(نفذ)</small> @endif
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <button type="button" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-lg mb-3" data-bs-toggle="modal" data-bs-target="#inventoryOrderModal">
                                <i class="bi bi-cart-plus me-2"></i> شراء الآن
                            </button>

                            <!-- Inventory Order Modal -->
                            <div class="modal fade" id="inventoryOrderModal" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-4 border-0">
                                        <div class="modal-header border-0">
                                            <h5 class="modal-title">إتمام الطلب</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label">عنوان الشحن</label>
                                                <textarea class="form-control" name="shipping_address" rows="3" required placeholder="اسم الشارع، المدينة، المحافظة، رقم الجوال"></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">طريقة الدفع</label>
                                                <select class="form-select" name="payment_method" required>
                                                    <option value="cod">الدفع عند الاستلام</option>
                                                    <option value="wallet">محفظتي (${{ number_format(auth()->user()->wallet_balance ?? 0, 2) }})</option>
                                                    <option value="stripe">بطاقة ائتمان (Stripe)</option>
                                                </select>
                                            </div>
                                            <div class="alert alert-info small">
                                                <i class="bi bi-info-circle me-1"></i> خيار الدفع عند الاستلام متاح.
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100 rounded-pill py-2">تأكيد الطلب</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        </form>
                    @else
                        <!-- User Logic -->
                        @if($product->location)
                            <div class="mb-4 d-flex align-items-center text-muted">
                                <i class="bi bi-geo-alt-fill me-2 text-danger"></i>
                                <span>الموقع: <strong>{{ $product->location }}</strong></span>
                            </div>
                        @endif

                        @if(auth()->check() && auth()->id() !== $product->user_id)
                            <button type="button" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-lg mb-3" data-bs-toggle="modal" data-bs-target="#userRequestModal">
                                طلب شراء
                            </button>
                            
                            <!-- User Request Modal -->
                            <div class="modal fade" id="userRequestModal" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-4 border-0">
                                        <div class="modal-header border-0">
                                            <h5 class="modal-title">إرسال طلب شراء</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <form action="{{ route('orders.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <div class="mb-3">
                                                    <label class="form-label">تفاصيل التواصل / عرض السعر</label>
                                                    <textarea class="form-control" name="shipping_address" rows="3" required placeholder="مرحباً، أنا مهتم بهذا المنتج. أسكن في... جوالي..."></textarea>
                                                    <div class="form-text">سيتم مشاركة هذه البيانات مع البائع فقط في حال قبول الطلب.</div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">طريقة الدفع</label>
                                                    <select class="form-select" name="payment_method" required>
                                                        <option value="cod">الدفع عند الاستلام (اتفاق مع البائع)</option>
                                                        <option value="wallet">محفظتي (${{ number_format(auth()->user()->wallet_balance, 2) }})</option>
                                                    </select>
                                                    <div class="form-text">خصم الرصيد يتم فقط بعد موافقة الإدارة والبائع.</div>
                                                </div>
                                                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2">إرسال الطلب</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif(auth()->id() === $product->user_id)
                            <div class="alert alert-secondary text-center rounded-pill mb-3">
                                هذا المنتج خاص بك
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-lg mb-3">
                                سجل دخول للشراء
                            </a>
                        @endif
                    @endif
                    
                    <div class="mt-4 border-top pt-4">
                        <div class="d-flex align-items-center mb-2">
                             <i class="bi bi-shield-check text-success fs-4 me-3"></i>
                             <div>
                                 <h6 class="mb-0">بائع موثوق</h6>
                                 <small class="text-muted">تم التحقق من الهوية بواسطة فون ماركت</small>
                             </div>
                        </div>
                         <div class="d-flex align-items-center">
                             <i class="bi bi-truck text-primary fs-4 me-3"></i>
                             <div>
                                 <h6 class="mb-0">شحن آمن</h6>
                                 <small class="text-muted">خدمة التوصيل المتبعة (اختياري)</small>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Details & Specs -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-8">
                <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm mb-4">
                    <h3 class="mb-4">الوصف</h3>
                    <p class="text-muted leading-loose">
                        {{ $product->description ?? 'لا يوجد وصف.' }}
                    </p>
                </div>
                
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="spec-box">
                            <i class="bi bi-tools text-primary fs-3 mb-3"></i>
                            <div class="spec-label">العيوب</div>
                            <div class="spec-value">{{ $product->defects ?? 'لا يوجد' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="spec-box">
                            <i class="bi bi-gift text-primary fs-3 mb-3"></i>
                            <div class="spec-label">الملحقات</div>
                             <div class="spec-value">{{ $product->accessories ?? 'لا يوجد' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                         <div class="spec-box">
                            <i class="bi bi-phone-vibrate text-primary fs-3 mb-3"></i>
                            <div class="spec-label">تم فكه/صيانته؟</div>
                             <div class="spec-value">{{ $product->disassembled_is ? 'نعم' : 'لا' }}</div>
                        </div>
                    </div>
                     <div class="col-md-6">
                         <div class="spec-box">
                            <i class="bi bi-battery-charging text-primary fs-3 mb-3"></i>
                            <div class="spec-label">ملاحظات الحالة</div>
                             <div class="spec-value">{{ $product->condition_notes ?? 'لا يوجد' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Reviews -->

            </div>
            
            <div class="col-md-4">
                <div class="sticky-top" style="top: 100px;">
                    <div class="bg-white p-4 rounded-4 shadow-sm border border-light">
                        <h4 class="mb-4">معلومات البائع</h4>
                         <div class="text-center mb-3">
                            <div class="bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold; color: var(--primary-color);">
                                {{ strtoupper(substr($product->seller->name, 0, 1)) }}
                            </div>
                            <h5>{{ $product->seller->name }}</h5>
                            <p class="text-muted small">عضو منذ {{ $product->seller->created_at->format('M Y') }}</p>
                        </div>
                        <div class="d-grid gap-2">
                            <!-- Contact Seller trigger modal -->
                            <button type="button" class="btn btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#sellerContactModal">
                                تواصل مع البائع
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Seller Contact Modal -->
<div class="modal fade" id="sellerContactModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title">معلومات التواصل</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-4">
                    <div class="text-muted small text-uppercase mb-1">البريد الإلكتروني</div>
                    <div class="fs-5 fw-bold">{{ $product->seller->email }}</div>
                </div>
                @if($product->seller->phone)
                <div>
                    <div class="text-muted small text-uppercase mb-1">رقم الهاتف</div>
                    <div class="fs-5 fw-bold" dir="ltr">{{ $product->seller->phone }}</div>
                </div>
                @else
                <div class="alert alert-light small">رقم الهاتف غير متوفر.</div>
                @endif
                <div class="mt-4 pt-3 border-top">
                    <small class="text-muted">استخدم المنصة لطلب هذا المنتج. الدفع مؤمن بواسطة إدارة الموقع.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Related Products -->
@if($relatedProducts->count() > 0)
<section class="section-padding">
    <div class="container">
        <h3 class="mb-4">قد يعجبك أيضاً</h3>
        <div class="row g-4">
            @foreach($relatedProducts as $related)
                <div class="col-md-6 col-lg-3">
                     <div class="product-card h-100">
                         <div class="product-img-container">
                             @if($related->images->count() > 0)
                                <img src="{{ asset('storage/' . $related->images->first()->image_path) }}" 
                                     alt="{{ $related->brand }}">
                             @else
                                <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center text-muted">
                                    <i class="bi bi-phone fs-1"></i>
                                </div>
                             @endif
                        </div>
                        <div class="product-body">
                            <h5 class="mb-1 text-truncate">{{ $related->brand }} {{ $related->model }}</h5>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="product-price" dir="ltr">${{ number_format($related->price, 2) }}</span>
                                <a href="{{ route('product.show', $related) }}" class="btn btn-sm btn-outline-primary rounded-pill">عرض</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@push('scripts')
<script>
    function changeImage(thumbnail) {
        // Remove active class from all thumbnails
        document.querySelectorAll('.thumbnail').forEach(el => el.classList.remove('active'));
        // Add active class to clicked thumbnail
        thumbnail.classList.add('active');
        // Change main image source
        const newSrc = thumbnail.querySelector('img').src;
        const mainImage = document.getElementById('mainImage');
        
        // Simple fade effect
        mainImage.style.opacity = 0;
        setTimeout(() => {
            mainImage.src = newSrc;
            mainImage.style.opacity = 1;
        }, 200);
    }
</script>
@endpush

@endsection
