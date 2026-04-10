@extends('admin.layout')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('content')
<div class="page-header mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
    <h2 class="page-title">Edit Product: {{ $product->name }}</h2>
</div>

<div class="row">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Product Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3">
                            <label for="price" class="form-label">Price ($)</label>
                            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $product->price) }}" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3">
                            <label for="condition" class="form-label">Condition</label>
                            <select class="form-select @error('condition') is-invalid @enderror" id="condition" name="condition" required>
                                <option value="new" {{ old('condition', $product->condition) === 'new' ? 'selected' : '' }}>New</option>
                                <option value="used" {{ old('condition', $product->condition) === 'used' ? 'selected' : '' }}>Used</option>
                            </select>
                            @error('condition')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <label for="user_id" class="form-label">Seller</label>
                            <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', $product->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="available" {{ old('status', $product->status) === 'available' ? 'selected' : '' }}>Available</option>
                                <option value="pending" {{ old('status', $product->status) === 'pending' ? 'selected' : '' }}>Pending Approval</option>
                                <option value="sold" {{ old('status', $product->status) === 'sold' ? 'selected' : '' }}>Sold</option>
                                <option value="hidden" {{ old('status', $product->status) === 'hidden' ? 'selected' : '' }}>Hidden</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    @if($product->source === 'inventory')
                    <!-- Colors & Stock Management (Inventory Products Only) -->
                    <div class="mt-4">
                        <label class="form-label fw-bold">Colors & Stock</label>
                        <div id="variants-container">
                            @foreach($product->variants as $index => $variant)
                            <div class="variant-row card mb-2 p-3">
                                <div class="row g-2 align-items-center">
                                    <div class="col-md-4">
                                        <label class="form-label small">Color Name</label>
                                        <input type="text" class="form-control form-control-sm" name="variants[{{ $index }}][color_name]" value="{{ $variant->color_name }}" required>
                                        <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">Color Code</label>
                                        <input type="color" class="form-control form-control-color form-control-sm" name="variants[{{ $index }}][color_code]" value="{{ $variant->color_code }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">Stock Quantity</label>
                                        <input type="number" class="form-control form-control-sm" name="variants[{{ $index }}][stock_quantity]" value="{{ $variant->stock_quantity }}" min="0" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small">&nbsp;</label>
                                        <button type="button" class="btn btn-sm btn-danger w-100 remove-variant" data-variant-id="{{ $variant->id }}">
                                            <i class="bi bi-trash"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-variant">
                            <i class="bi bi-plus-circle me-1"></i>Add Color
                        </button>
                    </div>
                    @endif

                    <div class="mt-4">
                        <label class="form-label">Product Images</label>
                        
                        @if($product->images->count() > 0)
                            <div class="row g-3 mb-3">
                                @foreach($product->images as $image)
                                    <div class="col-6 col-md-3">
                                        <div class="card h-100 {{ $image->is_primary ? 'border-primary' : '' }}">
                                            <div class="position-relative">
                                                <img src="{{ asset('storage/' . $image->image_path) }}" class="card-img-top" alt="Product Image" style="height: 150px; object-fit: cover;">
                                                @if($image->is_primary)
                                                    <span class="position-absolute top-0 start-0 badge bg-primary m-2">Primary</span>
                                                @endif
                                            </div>
                                            <div class="card-body p-2 text-center">
                                                <div class="btn-group w-100">
                                                    @if(!$image->is_primary)
                                                        <button type="submit" form="set-primary-{{ $image->id }}" class="btn btn-sm btn-outline-primary" title="Set as Primary">
                                                            <i class="bi bi-star"></i>
                                                        </button>
                                                    @endif
                                                    <button type="submit" form="delete-image-{{ $image->id }}" class="btn btn-sm btn-outline-danger" title="Delete Image" onclick="return confirm('Delete this image?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-light border mb-3">No images uploaded yet.</div>
                        @endif

                        <label for="images" class="form-label">Add New Images</label>
                        <input type="file" class="form-control @error('images') is-invalid @enderror" id="images" name="images[]" multiple accept="image/*">
                        <div class="form-text">You can select multiple images. Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB per image.</div>
                        @error('images')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('images.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Update Product
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@foreach($product->images as $image)
    <form id="delete-image-{{ $image->id }}" action="{{ route('admin.product-images.destroy', $image) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
    <form id="set-primary-{{ $image->id }}" action="{{ route('admin.product-images.set-primary', $image) }}" method="POST" class="d-none">
        @csrf
    </form>
@endforeach

@if($product->source === 'inventory')
<script>
let variantIndex = {{ $product->variants->count() }};

document.getElementById('add-variant').addEventListener('click', function() {
    const container = document.getElementById('variants-container');
    const newVariant = `
        <div class="variant-row card mb-2 p-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <label class="form-label small">Color Name</label>
                    <input type="text" class="form-control form-control-sm" name="variants[${variantIndex}][color_name]" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Color Code</label>
                    <input type="color" class="form-control form-control-color form-control-sm" name="variants[${variantIndex}][color_code]" value="#000000" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Stock Quantity</label>
                    <input type="number" class="form-control form-control-sm" name="variants[${variantIndex}][stock_quantity]" value="0" min="0" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">&nbsp;</label>
                    <button type="button" class="btn btn-sm btn-danger w-100 remove-variant-new">
                        <i class="bi bi-trash"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', newVariant);
    variantIndex++;
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-variant-new')) {
        e.target.closest('.variant-row').remove();
    }
    
    if (e.target.closest('.remove-variant')) {
        if (confirm('Are you sure you want to remove this color variant?')) {
            const variantId = e.target.closest('.remove-variant').dataset.variantId;
            const row = e.target.closest('.variant-row');
            row.innerHTML = `<input type="hidden" name="variants_to_delete[]" value="${variantId}">`;
            row.style.display = 'none';
        }
    }
});
</script>
@endif

@endsection
