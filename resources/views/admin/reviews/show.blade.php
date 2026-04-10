@extends('admin.layout')

@section('title', 'View Review')
@section('page-title', 'Review Details')

@section('content')
<div class="page-header mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.reviews.index') }}">Reviews</a></li>
            <li class="breadcrumb-item active">View</li>
        </ol>
    </nav>
    <h2 class="page-title">Review Details</h2>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Review Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <p class="text-muted mb-1">Product</p>
                    <h5>{{ $review->product->name }}</h5>
                    <a href="{{ route('admin.products.show', $review->product) }}" class="btn btn-sm btn-outline-primary">
                        View Product
                    </a>
                </div>
                
                <div class="mb-4">
                    <p class="text-muted mb-1">Reviewer</p>
                    <h5>{{ $review->user->name }}</h5>
                    <p class="text-muted">{{ $review->user->email }}</p>
                    <a href="{{ route('admin.users.show', $review->user) }}" class="btn btn-sm btn-outline-primary">
                        View User Profile
                    </a>
                </div>
                
                <div class="mb-4">
                    <p class="text-muted mb-1">Rating</p>
                    <div class="d-flex align-items-center gap-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $review->rating)
                                <i class="bi bi-star-fill text-warning" style="font-size: 1.5rem;"></i>
                            @else
                                <i class="bi bi-star text-muted" style="font-size: 1.5rem;"></i>
                            @endif
                        @endfor
                        <span class="fw-bold ms-2">({{ $review->rating }}/5)</span>
                    </div>
                </div>
                
                <div class="mb-4">
                    <p class="text-muted mb-1">Comment</p>
                    <p class="lead">{{ $review->comment ?? 'No comment provided' }}</p>
                </div>
                
                <div class="mb-3">
                    <p class="text-muted mb-1">Submitted</p>
                    <p>{{ $review->created_at->format('F d, Y h:i A') }}</p>
                </div>
                
                <hr>
                
                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this review?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i>Delete Review
                    </button>
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Reviews
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
