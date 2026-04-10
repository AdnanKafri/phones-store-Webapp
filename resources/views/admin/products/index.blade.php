@extends('admin.layout')

@section('title', 'Products')
@section('page-title', 'Products')

@section('content')
<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="page-title">Products Management</h2>
            <p class="page-subtitle">Manage all marketplace products</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Add New Product
        </a>
    </div>
</div>

<!-- Search & Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.products.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Approval</option>
                        <option value="sold" {{ request('status') === 'sold' ? 'selected' : '' }}>Sold</option>
                        <option value="hidden" {{ request('status') === 'hidden' ? 'selected' : '' }}>Hidden</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search me-1"></i>Search
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="card table-card">
    <div class="card-header">
        <h5 class="mb-0">All Products ({{ $products->total() }})</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Seller</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Condition</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>#{{ $product->id }}</td>
                        <td><strong>{{ $product->name }}</strong></td>
                        <td>{{ $product->user->name }}</td>
                        <td><span class="badge bg-secondary">{{ $product->category->name }}</span></td>
                        <td><strong>${{ number_format($product->price, 2) }}</strong></td>
                        <td>
                            @if($product->condition === 'new')
                                <span class="badge bg-success">New</span>
                            @else
                                <span class="badge bg-info">Used</span>
                            @endif
                        </td>
                        <td>
                            @if($product->status === 'available')
                                <span class="badge bg-success">Available</span>
                            @elseif($product->status === 'pending')
                                <span class="badge bg-warning text-dark">Pending Approval</span>
                            @elseif($product->status === 'sold')
                                <span class="badge bg-secondary">Sold</span>
                            @else
                                <span class="badge bg-dark">Hidden</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No products found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($products->hasPages())
    <div class="card-footer">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection
