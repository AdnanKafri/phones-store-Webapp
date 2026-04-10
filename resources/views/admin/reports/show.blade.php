@extends('admin.layout')

@section('title', 'View Report')
@section('page-title', 'Report Details')

@section('content')
<div class="page-header mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
            <li class="breadcrumb-item active">View</li>
        </ol>
    </nav>
    <h2 class="page-title">Report Details</h2>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Report Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <p class="text-muted mb-1">Reporter</p>
                    <h5>{{ $report->reporter->name }}</h5>
                    <p class="text-muted">{{ $report->reporter->email }}</p>
                    <a href="{{ route('admin.users.show', $report->reporter) }}" class="btn btn-sm btn-outline-primary">
                        View Reporter Profile
                    </a>
                </div>
                
                <div class="mb-4">
                    <p class="text-muted mb-1">Reported Item Type</p>
                    <h5>{{ class_basename($report->reportable_type) }}</h5>
                </div>
                
                <div class="mb-4">
                    <p class="text-muted mb-1">Reported Item</p>
                    @if($report->reportable_type === 'App\\Models\\Product' && $report->reportable)
                        <h5>{{ $report->reportable->name }}</h5>
                        <a href="{{ route('admin.products.show', $report->reportable) }}" class="btn btn-sm btn-outline-primary">
                            View Product
                        </a>
                    @elseif($report->reportable_type === 'App\\Models\\User' && $report->reportable)
                        <h5>{{ $report->reportable->name }}</h5>
                        <a href="{{ route('admin.users.show', $report->reportable) }}" class="btn btn-sm btn-outline-primary">
                            View User
                        </a>
                    @else
                        <p class="text-muted">Item has been deleted</p>
                    @endif
                </div>
                
                <div class="mb-4">
                    <p class="text-muted mb-1">Reason</p>
                    <p class="lead">{{ $report->reason }}</p>
                </div>
                
                <div class="mb-4">
                    <p class="text-muted mb-1">Current Status</p>
                    <p>
                        @if($report->status === 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @elseif($report->status === 'reviewed')
                            <span class="badge bg-info">Reviewed</span>
                        @else
                            <span class="badge bg-success">Resolved</span>
                        @endif
                    </p>
                </div>
                
                <div class="mb-4">
                    <p class="text-muted mb-1">Reported On</p>
                    <p>{{ $report->created_at->format('F d, Y h:i A') }}</p>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Update Status</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.reports.update', $report) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Change Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="reviewed" {{ $report->status === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                            <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Update Status
                        </button>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back to Reports
                        </a>
                    </div>
                </form>
                
                <hr class="my-4">
                
                <form action="{{ route('admin.reports.destroy', $report) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this report?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i>Delete Report
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
