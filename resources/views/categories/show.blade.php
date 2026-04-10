@extends('layouts.app')

@section('title', $category->name . ' - Products')

@section('content')

<!-- Breadcrumb -->
<div class="container mt-4">
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Categories', 'url' => route('categories.index')],
        ['label' => $category->name]
    ]" />
</div>

<!-- Category Header -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold mb-3">{{ $category->name }}</h1>
            @if($category->description)
                <p class="lead text-muted">{{ $category->description }}</p>
            @endif
            <p class="text-muted">{{ $products->total() }} products found</p>
        </div>

        <!-- Products Grid -->
        <div class="row g-4">
            @forelse($products as $product)
                <div class="col-md-6 col-lg-3">
                    <div class="product-card h-100 position-relative">
                        @if($product->condition === 'new')
                            <span class="badge bg-success position-absolute top-0 start-0 m-3 z-2">New</span>
                        @else
                            <span class="badge bg-warning position-absolute top-0 start-0 m-3 z-2">Used</span>
                        @endif

                        @if($product->status !== 'available')
                            <span class="badge bg-danger position-absolute top-0 end-0 m-3 z-2">{{ ucfirst($product->status) }}</span>
                        @endif
                        <div class="product-img-container">
                            @if($product->images->count() > 0)
                                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                                     alt="{{ $product->brand }} {{ $product->model }}">
                            @else
                                <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center text-muted">
                                    <i class="bi bi-phone fs-1"></i>
                                </div>
                            @endif
                        </div>
                        <div class="product-body">
                            <h5 class="mb-1 text-truncate">{{ $product->brand }} {{ $product->model }}</h5>
                            <p class="text-muted small mb-3">{{ $product->category->name }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="product-price">${{ number_format($product->price, 2) }}</span>
                                <a href="{{ route('product.show', $product) }}" class="btn btn-sm btn-outline-primary rounded-pill"><i class="bi bi-eye"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-5">
                    <i class="bi bi-inbox fs-1 mb-3 d-block"></i>
                    <h4>No Products Found</h4>
                    <p>There are no products in this category yet.</p>
                    <a href="{{ route('home') }}" class="btn btn-primary rounded-pill mt-3">Browse All Products</a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="d-flex justify-content-center mt-5">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</section>

@endsection
