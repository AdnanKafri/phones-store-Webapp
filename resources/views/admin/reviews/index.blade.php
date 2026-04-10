@extends('admin.layout')

@section('title', 'Reviews')
@section('page-title', 'Reviews')

@section('content')
<div class="page-header mb-4">
    <div>
        <h2 class="page-title">Reviews Management</h2>
        <p class="page-subtitle">Manage all product reviews</p>
    </div>
</div>

<!-- Search & Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reviews.index') }}">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search by product name..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="rating" class="form-select">
                        <option value="">All Ratings</option>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} Stars</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search me-1"></i>Search
                    </button>
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Reviews Table -->
<div class="card table-card">
    <div class="card-header">
        <h5 class="mb-0">All Reviews ({{ $reviews->total() }})</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>User</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                    <tr>
                        <td>#{{ $review->id }}</td>
                        <td><strong>{{ $review->product->name }}</strong></td>
                        <td>{{ $review->user->name }}</td>
                        <td>
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                    <i class="bi bi-star-fill text-warning"></i>
                                @else
                                    <i class="bi bi-star text-muted"></i>
                                @endif
                            @endfor
                            <span class="ms-1">({{ $review->rating }})</span>
                        </td>
                        <td>{{ Str::limit($review->comment, 60) }}</td>
                        <td>{{ $review->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.reviews.show', $review) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this review?');">
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
                        <td colspan="7" class="text-center py-4 text-muted">No reviews found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($reviews->hasPages())
    <div class="card-footer">
        {{ $reviews->links() }}
    </div>
    @endif
</div>
@endsection
