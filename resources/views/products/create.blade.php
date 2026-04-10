@extends('layouts.app')

@section('title', 'إضافة إعلان جديد')

@section('content')

<!-- Breadcrumb -->
<div class="container mt-4">
    <x-breadcrumb :items="[
        ['label' => 'الرئيسية', 'url' => route('home')],
        ['label' => 'لوحة التحكم', 'url' => route('dashboard')],
        ['label' => 'إضافة إعلان']
    ]" />
</div>

<!-- Create Product Form -->
<section class="section-padding">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="display-6 fw-bold mb-2">إضافة إعلان جديد</h1>
                <p class="text-muted">قم بتعبئة التفاصيل أدناه لعرض جهازك للبيع</p>
            </div>
        </div>

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row g-4">
                <!-- Left Column: Images -->
                <div class="col-lg-5">
                    <div class="upload-card sticky-top" style="top: 120px;">
                        <h4 class="mb-3">صور المنتج</h4>
                        <p class="text-muted small mb-4">يمكنك رفع حتى 5 صور لجهازك</p>
                        
                        <div class="image-upload-area" id="imageUploadArea">
                            <i class="bi bi-cloud-upload fs-1 text-primary mb-3"></i>
                            <h5>اضغط أو اسحب الصور هنا</h5>
                            <p class="text-muted small">PNG, JPG بحد أقصى 5 ميجابايت لكل صورة</p>
                            <input type="file" name="images[]" id="imageInput" multiple accept="image/*" class="d-none">
                        </div>
                        
                        <div id="imagePreview" class="image-preview-grid mt-3"></div>
                        
                        @error('images')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Right Column: Form Fields -->
                <div class="col-lg-7">
                    <div class="form-card">
                        <h4 class="mb-4">تفاصيل المنتج</h4>
                        
                        <div class="row g-3">
                            <!-- Brand -->
                            <div class="col-md-6">
                                <label for="brand" class="form-label">الماركة *</label>
                                <input type="text" class="form-control @error('brand') is-invalid @enderror" 
                                       id="brand" name="brand" value="{{ old('brand') }}" required 
                                       placeholder="مثال: Apple, Samsung">
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Model -->
                            <div class="col-md-6">
                                <label for="model" class="form-label">الموديل *</label>
                                <input type="text" class="form-control @error('model') is-invalid @enderror" 
                                       id="model" name="model" value="{{ old('model') }}" required 
                                       placeholder="مثال: iPhone 14 Pro">
                                @error('model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Category -->
                            <div class="col-md-6">
                                <label for="category_id" class="form-label">القسم *</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id" required>
                                    <option value="">اختر القسم</option>
                                    @foreach(\App\Models\Category::all() as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Price -->
                            <div class="col-md-6">
                                <label for="price" class="form-label">السعر ($) *</label>
                                <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                       id="price" name="price" value="{{ old('price') }}" required 
                                       step="0.01" min="0" placeholder="0.00">
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Condition -->
                            <div class="col-12">
                                <label for="condition" class="form-label">الحالة *</label>
                                <select class="form-select @error('condition') is-invalid @enderror" 
                                        id="condition" name="condition" required>
                                    <option value="">اختر الحالة</option>
                                    <option value="new" {{ old('condition') == 'new' ? 'selected' : '' }}>جديد</option>
                                    <option value="used" {{ old('condition') == 'used' ? 'selected' : '' }}>مستعمل</option>
                                </select>
                                @error('condition')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label">الوصف التفصيلي</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4" 
                                          placeholder="اكتب وصفاً مفصلاً للمنتج...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Defects -->
                            <div class="col-12">
                                <label for="defects" class="form-label">العيوب (إن وجدت)</label>
                                <textarea class="form-control @error('defects') is-invalid @enderror" 
                                          id="defects" name="defects" rows="2" 
                                          placeholder="اذكر أي خدوش، كسور، أو مشاكل تقنية...">{{ old('defects') }}</textarea>
                                @error('defects')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Color -->
                            <div class="col-md-6">
                                <label for="color" class="form-label">اللون *</label>
                                <input type="text" class="form-control @error('color') is-invalid @enderror" 
                                       id="color" name="color" value="{{ old('color') }}" required 
                                       placeholder="مثال: أسود، أزرق">
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Condition Notes -->
                            <div class="col-12">
                                <label for="condition_notes" class="form-label">ملاحظات على الحالة</label>
                                <textarea class="form-control @error('condition_notes') is-invalid @enderror" 
                                          id="condition_notes" name="condition_notes" rows="2" 
                                          placeholder="أي تفاصيل إضافية حول نظافة الجهاز...">{{ old('condition_notes') }}</textarea>
                            </div>

                            <!-- Accessories -->
                            <div class="col-12">
                                <label for="accessories" class="form-label">الملحقات المرفقة</label>
                                <textarea class="form-control @error('accessories') is-invalid @enderror" 
                                          id="accessories" name="accessories" rows="2" 
                                          placeholder="مثال: الشاحن، العلبة، السماعات...">{{ old('accessories') }}</textarea>
                                @error('accessories')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Disassembled -->
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="disassembled_is" 
                                           name="disassembled_is" value="1" {{ old('disassembled_is') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="disassembled_is">
                                        تم فك الجهاز أو صيانته مسبقاً
                                    </label>
                                </div>
                            </div>

                            <!-- Location -->
                            <div class="col-12">
                                <label for="location" class="form-label">الموقع (المدينة / المحافظة) *</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                       id="location" name="location" value="{{ old('location') }}" required 
                                       placeholder="مثال: دمشق، حلب، اللاذقية">
                                <div class="form-text">سيتم عرض هذا الموقع للمشترين المحتملين.</div>
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit Buttons -->
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 ms-2">
                                    <i class="bi bi-check-lg me-2"></i>نشر الإعلان
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg rounded-pill px-5">
                                    إلغاء
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('imageUploadArea');
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    let selectedFiles = [];

    uploadArea.addEventListener('click', () => imageInput.click());

    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('drag-over');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('drag-over');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');
        handleFiles(e.dataTransfer.files);
    });

    imageInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });

    function handleFiles(files) {
        selectedFiles = Array.from(files).slice(0, 5);
        displayPreviews();
    }

    function displayPreviews() {
        imagePreview.innerHTML = '';
        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const div = document.createElement('div');
                div.className = 'image-preview-item';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" class="remove-image" onclick="removeImage(${index})">
                        <i class="bi bi-x"></i>
                    </button>
                `;
                imagePreview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }

    window.removeImage = function(index) {
        selectedFiles.splice(index, 1);
        displayPreviews();
        
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        imageInput.files = dt.files;
    };
});
</script>
@endpush

@endsection
