@extends('layouts.app')

@section('title', 'Edit Listing')

@section('content')

<!-- Breadcrumb -->
<div class="container mt-4">
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'My Listings', 'url' => route('dashboard.my-listings')],
        ['label' => 'Edit Listing']
    ]" />
</div>

<!-- Edit Product Form -->
<section class="section-padding">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="display-6 fw-bold mb-2">Edit Listing</h1>
                <p class="text-muted">Update your product details</p>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row g-4">
                <!-- Left Column: Images -->
                <div class="col-lg-5">
                    <div class="upload-card sticky-top" style="top: 120px;">
                        <h4 class="mb-3">Product Images</h4>
                        
                        <!-- Existing Images -->
                        @if($product->images->count() > 0)
                            <div class="mb-4">
                                <h6 class="text-muted small mb-3">Current Images ({{ $product->images->count() }}/5)</h6>
                                <div class="existing-images-grid">
                                    @foreach($product->images as $image)
                                        <div class="existing-image-item">
                                            <img src="{{ $image->url }}" alt="Product image">
                                            <div class="image-overlay">
                                                <label class="delete-checkbox">
                                                    <input type="checkbox" name="delete_images[]" value="{{ $image->id }}">
                                                    <span class="delete-label"><i class="bi bi-trash"></i> Delete</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <!-- Upload New Images -->
                        <div>
                            <h6 class="text-muted small mb-3">Add New Images</h6>
                            <p class="text-muted small mb-3">Upload up to {{ 5 - $product->images->count() }} more images</p>
                            
                            <div class="image-upload-area" id="imageUploadArea">
                                <i class="bi bi-cloud-upload fs-1 text-primary mb-3"></i>
                                <h5>Click or drag images here</h5>
                                <p class="text-muted small">PNG, JPG up to 5MB each</p>
                                <input type="file" name="images[]" id="imageInput" multiple accept="image/*" class="d-none">
                            </div>
                            
                            <div id="imagePreview" class="image-preview-grid mt-3"></div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Form Fields -->
                <div class="col-lg-7">
                    <div class="form-card">
                        <h4 class="mb-4">Product Details</h4>
                        
                        <div class="row g-3">
                            <!-- Brand -->
                            <div class="col-md-6">
                                <label for="brand" class="form-label">Brand *</label>
                                <input type="text" class="form-control @error('brand') is-invalid @enderror" 
                                       id="brand" name="brand" value="{{ old('brand', $product->brand) }}" required 
                                       placeholder="e.g., Apple, Samsung">
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Model -->
                            <div class="col-md-6">
                                <label for="model" class="form-label">Model *</label>
                                <input type="text" class="form-control @error('model') is-invalid @enderror" 
                                       id="model" name="model" value="{{ old('model', $product->model) }}" required 
                                       placeholder="e.g., iPhone 14 Pro">
                                @error('model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Color -->
                            <div class="col-md-6">
                                <label for="color" class="form-label">Color *</label>
                                <input type="text" class="form-control @error('color') is-invalid @enderror" 
                                       id="color" name="color" value="{{ old('color', $product->color) }}" required 
                                       placeholder="e.g., Black, Blue">
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Category -->
                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Category *</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach(\App\Models\Category::all() as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                                <label for="price" class="form-label">Price (USD) *</label>
                                <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                       id="price" name="price" value="{{ old('price', $product->price) }}" required 
                                       step="0.01" min="0" placeholder="0.00">
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Condition -->
                            <div class="col-md-6">
                                <label for="condition" class="form-label">Condition *</label>
                                <select class="form-select @error('condition') is-invalid @enderror" 
                                        id="condition" name="condition" required>
                                    <option value="">Select Condition</option>
                                    <option value="new" {{ old('condition', $product->condition) == 'new' ? 'selected' : '' }}>New</option>
                                    <option value="used" {{ old('condition', $product->condition) == 'used' ? 'selected' : '' }}>Used</option>
                                </select>
                                @error('condition')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status">
                                    <option value="available" {{ old('status', $product->status) == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="sold" {{ old('status', $product->status) == 'sold' ? 'selected' : '' }}>Sold</option>
                                    <option value="hidden" {{ old('status', $product->status) == 'hidden' ? 'selected' : '' }}>Hidden</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4" 
                                          placeholder="Describe your phone...">{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Defects -->
                            <div class="col-12">
                                <label for="defects" class="form-label">Defects (if any)</label>
                                <textarea class="form-control @error('defects') is-invalid @enderror" 
                                          id="defects" name="defects" rows="2" 
                                          placeholder="List any defects or issues...">{{ old('defects', $product->defects) }}</textarea>
                                @error('defects')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Accessories -->
                            <div class="col-12">
                                <label for="accessories" class="form-label">Included Accessories</label>
                                <textarea class="form-control @error('accessories') is-invalid @enderror" 
                                          id="accessories" name="accessories" rows="2" 
                                          placeholder="e.g., Charger, Box, Earphones...">{{ old('accessories', $product->accessories) }}</textarea>
                                @error('accessories')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Disassembled -->
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="disassembled_is" 
                                           name="disassembled_is" value="1" {{ old('disassembled_is', $product->disassembled_is) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="disassembled_is">
                                        This phone has been disassembled/opened before
                                    </label>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 me-2">
                                    <i class="bi bi-check-lg me-2"></i>Save Changes
                                </button>
                                <a href="{{ route('dashboard.my-listings') }}" class="btn btn-outline-secondary btn-lg rounded-pill px-5">
                                    Cancel
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
    
    // Get existing count safely
    const existingImagesCount = {{ $product->images->count() }};
    const maxTotalImages = 5;
    const maxNewImages = maxTotalImages - existingImagesCount;
    
    let selectedFiles = [];

    // Safety check for UI
    if (maxNewImages <= 0) {
        disableUpload("Maximum image limit (5) reached with existing images.");
    }

    uploadArea.addEventListener('click', () => {
        if (selectedFiles.length >= maxNewImages) {
            alert(`You can only upload ${maxNewImages} more image(s).`);
            return;
        }
        imageInput.click();
    });

    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        if (selectedFiles.length < maxNewImages) {
            uploadArea.classList.add('drag-over');
        }
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('drag-over');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');
        
        if (selectedFiles.length >= maxNewImages) {
            alert(`Limit reached. You can only add ${maxNewImages} new images.`);
            return;
        }
        handleFiles(e.dataTransfer.files);
    });

    imageInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });

    function handleFiles(files) {
        const newFiles = Array.from(files);
        const slotsRemaining = maxNewImages - selectedFiles.length;
        
        if (slotsRemaining <= 0) {
            alert(`Maximum image limit reached. Please remove some new or existing images first.`);
            return;
        }

        let filesToAdd = newFiles;
        if (newFiles.length > slotsRemaining) {
            filesToAdd = newFiles.slice(0, slotsRemaining);
            alert(`Added only ${slotsRemaining} image(s) to fit the limit of ${maxTotalImages} total.`);
        }

        // Add proper image type validation if needed here, filtered by accept="image/*" usually enough for UX
        
        selectedFiles = [...selectedFiles, ...filesToAdd];
        updateFileInput();
        displayPreviews();
    }

    function updateFileInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        imageInput.files = dt.files;
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
                    <button type="button" class="remove-image" onclick="removeNewImage(${index})">
                        <i class="bi bi-x"></i>
                    </button>
                `;
                imagePreview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
        
        // Update UI state based on count
        if (selectedFiles.length >= maxNewImages) {
            uploadArea.classList.add('opacity-50');
            uploadArea.style.cursor = 'not-allowed';
        } else {
            uploadArea.classList.remove('opacity-50');
            uploadArea.style.cursor = 'pointer';
        }
    }

    window.removeNewImage = function(index) {
        selectedFiles.splice(index, 1);
        updateFileInput(); // Sync input
        displayPreviews(); // Re-render
    };

    function disableUpload(msg) {
        uploadArea.classList.add('opacity-50');
        uploadArea.style.pointerEvents = 'none';
        // Optional: update text to show limit reached
    }
});
</script>
@endpush

@endsection
