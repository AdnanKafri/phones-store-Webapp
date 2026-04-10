@extends('layouts.app')

@section('title', 'تأكيد الطلب')

@section('content')
<section class="section-padding py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Success Card -->
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-success text-white text-center py-4 border-0">
                        <div class="mb-3">
                            <i class="bi bi-check-circle-fill display-1"></i>
                        </div>
                        <h2 class="fw-bold mb-0">تم استلام طلبك بنجاح!</h2>
                        <p class="mb-0 opacity-75">رقم الطلب: #{{ $order->id }}</p>
                    </div>
                    
                    <div class="card-body p-4 p-md-5">
                        
                        <!-- Order Status Alert -->
                        @if($order->status == 'pending')
                            <div class="alert alert-warning border-0 bg-warning bg-opacity-10 d-flex align-items-center mb-4" role="alert">
                                <i class="bi bi-hourglass-split fs-3 text-warning me-3"></i>
                                <div>
                                    <h5 class="alert-heading fw-bold mb-1">طلبك قيد المراجعة</h5>
                                    <p class="mb-0 text-muted small">سيقوم فريقنا (أو البائع) بمراجعة طلبك وإشعارك عند الموافقة.</p>
                                </div>
                            </div>
                        @elseif($order->status == 'approved')
                            <div class="alert alert-success border-0 bg-success bg-opacity-10 d-flex align-items-center mb-4" role="alert">
                                <i class="bi bi-check-lg fs-3 text-success me-3"></i>
                                <div>
                                    <h5 class="alert-heading fw-bold mb-1">تمت الموافقة على الطلب</h5>
                                    <p class="mb-0 text-muted small">تم تأكيد طلبك وجاري العمل على توصيله.</p>
                                </div>
                            </div>
                        @elseif($order->status == 'rejected')
                             <div class="alert alert-danger border-0 bg-danger bg-opacity-10 d-flex align-items-center mb-4" role="alert">
                                <i class="bi bi-x-circle fs-3 text-danger me-3"></i>
                                <div>
                                    <h5 class="alert-heading fw-bold mb-1">تم رفض الطلب</h5>
                                    <p class="mb-0 text-muted small">عذراً، لم يتم قبول هذا الطلب. يرجى التواصل معنا للمساعدة.</p>
                                </div>
                            </div>
                        @endif

                        <!-- Product Details -->
                        <div class="bg-light rounded-4 p-4 mb-4">
                            <h5 class="fw-bold mb-3">تفاصيل المنتج</h5>
                            <div class="d-flex align-items-center">
                                <img src="{{ $order->product->primary_image_url }}" alt="{{ $order->product->brand }}" 
                                     class="rounded-3 object-fit-cover shadow-sm me-3" style="width: 80px; height: 80px;">
                                <div>
                                    <h6 class="fw-bold mb-1">{{ $order->product->brand }} {{ $order->product->model }}</h6>
                                    @if($order->variant)
                                        <span class="badge bg-secondary mb-1">{{ $order->variant->color_name }}</span>
                                    @else
                                        <span class="badge bg-secondary mb-1">{{ $order->product->color }}</span>
                                    @endif
                                    <div class="text-primary fw-bold">${{ number_format($order->total_price, 2) }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Parties Details -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100 bg-light">
                                    <small class="text-muted text-uppercase fw-bold ls-1 d-block mb-2">البائع (Seller)</small>
                                    @if($order->product->source === 'inventory')
                                        <div class="fw-bold">PhoneMarket Official</div>
                                        <div class="text-muted small">Platform Store</div>
                                    @else
                                        <div class="fw-bold">{{ $order->product->seller->name }}</div>
                                        <div class="text-muted small">{{ $order->product->seller->email }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100 bg-light">
                                    <small class="text-muted text-uppercase fw-bold ls-1 d-block mb-2">المشتري (Buyer)</small>
                                    <div class="fw-bold">{{ $order->user->name }}</div>
                                    <div class="text-muted small">{{ $order->user->email }}</div>
                                    <div class="text-muted small">{{ $order->user->phone_number ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100">
                                    <small class="text-muted text-uppercase fw-bold ls-1 d-block mb-2">معلومات الدفع</small>
                                    <div class="fw-bold">
                                        @if($order->payment_method == 'wallet')
                                            <i class="bi bi-wallet2 text-primary me-2"></i> المحفظة
                                        @elseif($order->payment_method == 'cod')
                                            <i class="bi bi-cash-stack text-success me-2"></i> الدفع عند الاستلام
                                        @else
                                             <i class="bi bi-credit-card text-info me-2"></i> {{ ucfirst($order->payment_method) }}
                                        @endif
                                    </div>
                                    <div class="text-muted small mt-1">المبلغ الإجمالي: ${{ number_format($order->total_price, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                 <div class="p-3 border rounded-3 h-100">
                                    <small class="text-muted text-uppercase fw-bold ls-1 d-block mb-2">تاريخ الطلب</small>
                                    <div class="fw-bold">{{ $order->created_at->format('Y-m-d') }}</div>
                                    <div class="text-muted small mt-1">{{ $order->created_at->format('h:i A') }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-3 border rounded-3">
                                    <small class="text-muted text-uppercase fw-bold ls-1 d-block mb-2">عنوان الشحن / ملاحظات</small>
                                    <p class="mb-0 text-muted">{{ $order->shipping_address }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="d-grid gap-2">
                            <a href="{{ route('dashboard') }}" class="btn btn-primary rounded-pill py-3 fw-bold">
                                العودة للقائمة الرئيسية
                            </a>
                            <a href="{{ route('dashboard.orders') }}" class="btn btn-outline-secondary rounded-pill py-3">
                                عرض طلباتي
                            </a>
                             @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.orders') }}" class="btn btn-outline-dark rounded-pill py-3">
                                لوحة تحكم الإدارة
                            </a>
                            @endif
                        </div>

                    </div>
                    <div class="card-footer bg-light text-center py-3 border-0">
                        <small class="text-muted">شكراً لثقتك بنا! منصة جوالات - PhoneMarket</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
