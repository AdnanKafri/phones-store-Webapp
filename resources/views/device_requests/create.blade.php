@extends('layouts.app')

@section('title', 'طلب جهاز خاص')

@section('content')
<div class="container mt-4">
    <x-breadcrumb :items="[
        ['label' => 'الرئيسية', 'url' => route('home')],
        ['label' => 'طلب جهاز']
    ]" />

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-primary text-white p-4 text-center">
                    <i class="bi bi-phone-vibrate fs-1 mb-2"></i>
                    <h3 class="fw-bold mb-0">تبحث عن هاتف محدد؟</h3>
                    <p class="mb-0 opacity-75">أخبرنا بما تحتاجه، وسنحاول توفيره لك.</p>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('device-requests.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">الماركة (Brand) *</label>
                            <input type="text" name="brand" class="form-control form-control-lg @error('brand') is-invalid @enderror" placeholder="مثال: Apple, Samsung" value="{{ old('brand') }}" required>
                            @error('brand') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">الموديل (Model) *</label>
                            <input type="text" name="model" class="form-control form-control-lg @error('model') is-invalid @enderror" placeholder="مثال: iPhone 15 Pro Max" value="{{ old('model') }}" required>
                            @error('model') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">ملاحظات إضافية</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="ألوان محددة، سعة تخزين، الحالة (جديد/مستعمل)...">{{ old('notes') }}</textarea>
                            <div class="form-text">كلما زادت التفاصيل، زادت فرصة العثور على طلبك بدقة.</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm">
                                إرسال الطلب
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-light p-4 text-center text-muted small">
                    <i class="bi bi-shield-check me-1"></i> سيتم مشاركة طلبك مع شبكة البائعين الموثوقين لدينا.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
