@extends('admin.layout')

@section('title', 'View Category')
@section('page-title', 'Category Details')

@section('content')
<div class="page-header mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
            <li class="breadcrumb-item active">View</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">{{ $category->name }}</h2>
        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-2"></i>Edit Category
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-4">Category Information</h5>
                
                <div class="mb-3">
                    <p class="text-muted mb-1">Name</p>
                    <p class="fw-bold">{{ $category->name }}</p>
                </div>
                
                <div class="mb-3">
                    <p class="text-muted mb-1">Slug</p>
                    <p><code>{{ $category->slug }}</code></p>
                </div>
                
                <div class="mb-3">
                    <p class="text-muted mb-1">Icon</p>
                    <p>
                        @if($category->icon)
                            <i class="{{ $category->icon }}" style="font-size: 2rem;"></i>
                            <br>
                            <code>{{ $category->icon }}</code>
                        @else
                            <span class="text-muted">No icon set</span>
                        @endif
                    </p>
                </div>
                
                <hr>
                
                <div class="mb-3">
                    <p class="text-muted mb-1">Total Products</p>
                    <h3 class="mb-0">{{ $category->products->count() }}</h3>
                </div>
                
                <div class="mb-3">
                    <p class="text-muted mb-1">Created</p>
                    <p>{{ $category->created_at->format('F d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Products</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Seller</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($category->products as $product)
                            <tr>
                                <td><strong>{{ $product->name }}</strong></td>
                                <td>{{ $product->user->name }}</td>
                                <td>${{ number_format($product->price, 2) }}</td>
                                <td>
                                    @if($product->status === 'available')
                                        <span class="badge bg-success">Available</span>
                                    @elseif($product->status === 'sold')
                                        <span class="badge bg-secondary">Sold</span>
                                    @else
                                        <span class="badge bg-warning">Hidden</span>
                                    @endif
                                </td>
                                <td>{{ $product->created_at->format('M d, Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No products in this category yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
