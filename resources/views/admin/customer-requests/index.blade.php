@extends('admin.layout')

@section('title', 'Customer Requests')
@section('page-title', 'Customer Requests')

@section('content')
<div class="page-header mb-4">
    <div>
        <h2 class="page-title">Customer Requests (Wanted Devices)</h2>
        <p class="page-subtitle">Manage user requests for devices they're looking for</p>
    </div>
</div>

<!-- Search & Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.customer-requests.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by device name..." value="{{ request('search') }}">
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
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="matched" {{ request('status') === 'matched' ? 'selected' : '' }}>Matched</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search me-1"></i>Search
                    </button>
                    <a href="{{ route('admin.customer-requests.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Requests Table -->
<div class="card table-card">
    <div class="card-header">
        <h5 class="mb-0">All Requests ({{ $requests->total() }})</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Device</th>
                        <th>User</th>
                        <th>Category</th>
                        <th>Price Range</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                    <tr>
                        <td>#{{ $request->id }}</td>
                        <td><strong>{{ $request->device_name }}</strong></td>
                        <td>{{ $request->user->name }}</td>
                        <td>
                            @if($request->category)
                                <span class="badge bg-secondary">{{ $request->category->name }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($request->min_price || $request->max_price)
                                ${{ number_format($request->min_price ?? 0, 0) }} - ${{ number_format($request->max_price ?? 0, 0) }}
                            @else
                                <span class="text-muted">Not specified</span>
                            @endif
                        </td>
                        <td>
                            @if($request->status === 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($request->status === 'matched')
                                <span class="badge bg-success">Matched</span>
                            @else
                                <span class="badge bg-secondary">Cancelled</span>
                            @endif
                        </td>
                        <td>{{ $request->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.customer-requests.show', $request) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('admin.customer-requests.destroy', $request) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this request?');">
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
                        <td colspan="8" class="text-center py-4 text-muted">No customer requests found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($requests->hasPages())
    <div class="card-footer">
        {{ $requests->links() }}
    </div>
    @endif
</div>
@endsection
