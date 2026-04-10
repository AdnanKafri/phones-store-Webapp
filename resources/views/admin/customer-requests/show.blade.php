@extends('admin.layout')

@section('title', 'View Customer Request')
@section('page-title', 'Request Details')

@section('content')
<div class="page-header mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.customer-requests.index') }}">Customer Requests</a></li>
            <li class="breadcrumb-item active">View</li>
        </ol>
    </nav>
    <h2 class="page-title">Customer Request Details</h2>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Request Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <p class="text-muted mb-1">Device Name</p>
                    <h4>{{ $customerRequest->device_name }}</h4>
                </div>
                
                <div class="mb-4">
                    <p class="text-muted mb-1">Specifications</p>
                    <p>{{ $customerRequest->specifications }}</p>
                </div>
                
                @if($customerRequest->description)
                <div class="mb-4">
                    <p class="text-muted mb-1">Additional Description</p>
                    <p>{{ $customerRequest->description }}</p>
                </div>
                @endif
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Category</p>
                        <p>
                            @if($customerRequest->category)
                                <span class="badge bg-secondary">{{ $customerRequest->category->name }}</span>
                            @else
                                <span class="text-muted">Not specified</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Price Range</p>
                        <p>
                            @if($customerRequest->min_price || $customerRequest->max_price)
                                <strong>${{ number_format($customerRequest->min_price ?? 0, 2) }} - ${{ number_format($customerRequest->max_price ?? 0, 2) }}</strong>
                            @else
                                <span class="text-muted">Not specified</span>
                            @endif
                        </p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <p class="text-muted mb-1">Current Status</p>
                    <p>
                        @if($customerRequest->status === 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @elseif($customerRequest->status === 'matched')
                            <span class="badge bg-success">Matched</span>
                        @else
                            <span class="badge bg-secondary">Cancelled</span>
                        @endif
                    </p>
                </div>
                
                <div class="mb-3">
                    <p class="text-muted mb-1">Submitted</p>
                    <p>{{ $customerRequest->created_at->format('F d, Y h:i A') }}</p>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Update Status</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.customer-requests.update', $customerRequest) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Change Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" {{ $customerRequest->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="matched" {{ $customerRequest->status === 'matched' ? 'selected' : '' }}>Matched</option>
                            <option value="cancelled" {{ $customerRequest->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Update Status
                        </button>
                        <a href="{{ route('admin.customer-requests.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">User Information</h5>
            </div>
            <div class="card-body text-center">
                <div class="user-avatar mx-auto mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                    {{ strtoupper(substr($customerRequest->user->name, 0, 1)) }}
                </div>
                <h5>{{ $customerRequest->user->name }}</h5>
                <p class="text-muted">{{ $customerRequest->user->email }}</p>
                <a href="{{ route('admin.users.show', $customerRequest->user) }}" class="btn btn-sm btn-outline-primary">
                    View Profile
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
