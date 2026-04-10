@extends('admin.layout')

@section('title', 'View User')
@section('page-title', 'User Details')

@section('content')
<div class="page-header mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
            <li class="breadcrumb-item active">View</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">User Details</h2>
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-2"></i>Edit User
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="user-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h4>{{ $user->name }}</h4>
                <p class="text-muted">{{ $user->email }}</p>
                @if($user->role === 'admin')
                    <span class="badge bg-danger">Admin</span>
                @else
                    <span class="badge bg-primary">User</span>
                @endif
                
                <hr class="my-4">
                
                <div class="text-start">
                    <p class="mb-2"><strong>Member Since:</strong></p>
                    <p class="text-muted">{{ $user->created_at->format('F d, Y') }}</p>
                    
                    <p class="mb-2 mt-3"><strong>Last Updated:</strong></p>
                    <p class="text-muted">{{ $user->updated_at->format('F d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Products ({{ $user->products->count() }})</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($user->products as $product)
                            <tr>
                                <td><strong>{{ $product->name }}</strong></td>
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
                                <td colspan="4" class="text-center py-4 text-muted">No products yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Reviews ({{ $user->reviews->count() }})</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Rating</th>
                                <th>Comment</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($user->reviews as $review)
                            <tr>
                                <td><strong>{{ $review->product->name }}</strong></td>
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
</div>
@endsection
