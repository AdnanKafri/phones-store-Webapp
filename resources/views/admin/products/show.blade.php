@extends('admin.layout')

@section('title', 'View Product')
@section('page-title', 'Product Details')

@section('content')
<div class="page-header mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
            <li class="breadcrumb-item active">View</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">{{ $product->name }}</h2>
        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-2"></i>Edit Product
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Product Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Product Name</p>
                        <p class="fw-bold">{{ $product->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Price</p>
                        <p class="fw-bold text-success">${{ number_format($product->price, 2) }}</p>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <p class="text-muted mb-1">Category</p>
                        <p><span class="badge bg-secondary">{{ $product->category->name }}</span></p>
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted mb-1">Condition</p>
                        <p>
                            @if($product->condition === 'new')
                                <span class="badge bg-success">New</span>
                            @else
                                <span class="badge bg-info">Used</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted mb-1">Status</p>
                        <p>
                            @if($product->status === 'available')
                                <span class="badge bg-success">Available</span>
                            @elseif($product->status === 'sold')
                                <span class="badge bg-secondary">Sold</span>
                            @else
                                <span class="badge bg-warning">Hidden</span>
                            @endif
                        </p>
                    </div>
                </div>
                
                <div class="mb-3">
                    <p class="text-muted mb-1">Description</p>
                    <p>{{ $product->description }}</p>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Created</p>
                        <p>{{ $product->created_at->format('F d, Y h:i A') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Last Updated</p>
                        <p>{{ $product->updated_at->format('F d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        @if($product->source === 'inventory')
        <!-- Colors & Stock (Inventory Products Only) -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Colors & Stock</h5>
            </div>
            <div class="card-body">
                @if($product->variants->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">Color</th>
                                    <th>Color Name</th>
                                    <th>Stock Quantity</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->variants as $variant)
                                <tr>
                                    <td>
                                        <div style="width: 30px; height: 30px; background-color: {{ $variant->color_code }}; border: 1px solid #ddd; border-radius: 4px;"></div>
                                    </td>
                                    <td>{{ $variant->color_name }}</td>
                                    <td>
                                        <span class="badge {{ $variant->stock_quantity > 10 ? 'bg-success' : ($variant->stock_quantity > 0 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ $variant->stock_quantity }} units
                                        </span>
                                    </td>
                                    <td>
                                        @if($variant->stock_quantity > 0)
                                            <span class="badge bg-success">In Stock</span>
                                        @else
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>No color variants added yet. Add variants when editing this product.
                    </div>
                @endif
            </div>
        </div>
        @endif
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Reviews ({{ $product->reviews->count() }})</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Rating</th>
                                <th>Comment</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($product->reviews as $review)
                            <tr>
                                <td>{{ $review->user->name }}</td>
                                <td>
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <i class="bi bi-star-fill text-warning"></i>
                                        @else
                                            <i class="bi bi-star text-muted"></i>
                                        @endif
                                    @endfor
                                </td>
                                <td>{{ Str::limit($review->comment, 50) }}</td>
                                <td>{{ $review->created_at->format('M d, Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No reviews yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Product Images</h5>
            </div>
            <div class="card-body">
                @if($product->images->count() > 0)
                    <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner rounded">
                            @foreach($product->images as $key => $image)
                                <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                    <img src="{{ $image->url }}" class="d-block w-100" alt="Product Image" style="height: 300px; object-fit: cover;">
                                    @if($image->is_primary)
                                        <div class="carousel-caption d-none d-md-block">
                                            <span class="badge bg-primary">Primary Image</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @if($product->images->count() > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        @endif
                    </div>
                    
                    <div class="row g-2 mt-2">
                        @foreach($product->images as $key => $image)
                            <div class="col-3">
                                <img src="{{ $image->url }}" class="img-thumbnail {{ $image->is_primary ? 'border-primary' : '' }}" alt="Thumbnail" style="height: 60px; width: 100%; object-fit: cover; cursor: pointer;" onclick="document.querySelector('#productCarousel').querySelector('.carousel-item.active').classList.remove('active'); document.querySelectorAll('#productCarousel .carousel-item')[{{ $key }}].classList.add('active');">
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 text-muted bg-light rounded">
                        <i class="bi bi-image fs-1 d-block mb-2"></i>
                        No images available
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Seller Information</h5>
            </div>
            <div class="card-body text-center">
                <div class="user-avatar mx-auto mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                    {{ strtoupper(substr($product->user->name, 0, 1)) }}
                </div>
                <h5>{{ $product->user->name }}</h5>
                <p class="text-muted">{{ $product->user->email }}</p>
                <a href="{{ route('admin.users.show', $product->user) }}" class="btn btn-sm btn-outline-primary">
                    View Profile
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
