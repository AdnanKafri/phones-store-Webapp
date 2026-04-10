@extends('admin.layout')

@section('title', 'Create Product')
@section('page-title', 'Create Product')

@section('content')
<div class="page-header mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
            <li class="breadcrumb-item active">Create</li>
        </ol>
    </nav>
    <h2 class="page-title">Create New Product</h2>
</div>

<div class="row">
    <div class="col-lg-12">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                    @csrf
                    
                    <!-- Source Selection -->
                    <div class="row mb-4 bg-light p-3 rounded mx-0">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Product Source</label>
                            <select class="form-select @error('source') is-invalid @enderror" id="source" name="source" required onchange="toggleSource()">
                                <option value="inventory" {{ old('source') == 'inventory' ? 'selected' : '' }}>Inventory (Store Product)</option>
                                <option value="user" {{ old('source') == 'user' ? 'selected' : '' }}>User Listing (Private Seller)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                             <label class="form-label fw-bold">Seller Owner</label>
                            <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Select Admin for Inventory items.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                <option value="available" {{ old('status') === 'available' ? 'selected' : '' }}>Available</option>
                                <option value="sold" {{ old('status') === 'sold' ? 'selected' : '' }}>Sold</option>
                                <option value="hidden" {{ old('status') === 'hidden' ? 'selected' : '' }}>Hidden</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="brand" class="form-label">Brand *</label>
                            <input type="text" class="form-control" name="brand" value="{{ old('brand') }}" required placeholder="e.g. Apple">
                        </div>
                        <div class="col-md-4">
                            <label for="model" class="form-label">Model *</label>
                            <input type="text" class="form-control" name="model" value="{{ old('model') }}" required placeholder="e.g. iPhone 15">
                        </div>
                        <div class="col-md-4">
                            <label for="category_id" class="form-label">Category *</label>
                            <select class="form-select" name="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="price" class="form-label">Price ($) *</label>
                            <input type="number" step="0.01" class="form-control" name="price" value="{{ old('price') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="condition" class="form-label">Condition *</label>
                            <select class="form-select" name="condition" required>
                                <option value="new" {{ old('condition') === 'new' ? 'selected' : '' }}>New</option>
                                <option value="used" {{ old('condition') === 'used' ? 'selected' : '' }}>Used</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Condition Notes</label>
                            <input type="text" class="form-control" name="condition_notes" value="{{ old('condition_notes') }}" placeholder="e.g. Minor scratches">
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control" name="description" rows="4" required>{{ old('description') }}</textarea>
                    </div>

                    <!-- Inventory Variants Section -->
                    <div id="inventorySection" class="mt-4 border rounded p-3 bg-light">
                        <h5 class="mb-3 text-primary"><i class="bi bi-palette me-2"></i>Product Variants (Colors & Stock)</h5>
                        <table class="table table-bordered bg-white" id="variantsTable">
                            <thead>
                                <tr>
                                    <th>Color Name</th>
                                    <th>Stock Quantity</th>
                                    <th width="50">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- JS will populate this -->
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addVariantRow()">
                            <i class="bi bi-plus-lg"></i> Add Variant
                        </button>
                    </div>

                    <!-- User Single Item Section -->
                    <div id="userSection" class="mt-4 border rounded p-3 bg-light d-none">
                        <h5 class="mb-3 text-secondary">Single Item Specifics</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Color</label>
                                <input type="text" class="form-control" name="color" value="{{ old('color') }}" placeholder="e.g. Black">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="images" class="form-label">Product Images</label>
                        <input type="file" class="form-control" name="images[]" multiple accept="image/*">
                        <div class="form-text">Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB.</div>
                    </div>
                    
                    <div class="d-flex gap-2 mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-4">Create Product</button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleSource() {
        const source = document.getElementById('source').value;
        const inventorySec = document.getElementById('inventorySection');
        const userSec = document.getElementById('userSection');

        if (source === 'inventory') {
            inventorySec.classList.remove('d-none');
            userSec.classList.add('d-none');
        } else {
            inventorySec.classList.add('d-none');
            userSec.classList.remove('d-none');
        }
    }

    let variantIndex = 0;
    
    function addVariantRow() {
        const tbody = document.querySelector('#variantsTable tbody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <input type="text" class="form-control" name="variants[${variantIndex}][color_name]" placeholder="e.g. Obsidian Black" required>
            </td>
            <td>
                <input type="number" class="form-control" name="variants[${variantIndex}][stock_quantity]" value="1" min="0" required>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i></button>
            </td>
        `;
        tbody.appendChild(tr);
        variantIndex++;
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        toggleSource();
        addVariantRow(); // Add one row by default for inventory
    });
</script>
@endpush
@endsection
