@extends('admin.layout')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
<div class="page-header mb-4">
    <div>
        <h2 class="page-title">Reports Management</h2>
        <p class="page-subtitle">Manage user and product reports</p>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reports.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Reports Table -->
<div class="card table-card">
    <div class="card-header">
        <h5 class="mb-0">All Reports ({{ $reports->total() }})</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Reporter</th>
                        <th>Reported Item</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr>
                        <td>#{{ $report->id }}</td>
                        <td>{{ $report->reporter->name }}</td>
                        <td>
                            <strong>{{ class_basename($report->reportable_type) }}</strong>
                            <br>
                            <small class="text-muted">
                                @if($report->reportable_type === 'App\\Models\\Product')
                                    {{ $report->reportable->name ?? 'Deleted' }}
                                @elseif($report->reportable_type === 'App\\Models\\User')
                                    {{ $report->reportable->name ?? 'Deleted' }}
                                @endif
                            </small>
                        </td>
                        <td>{{ Str::limit($report->reason, 40) }}</td>
                        <td>
                            @if($report->status === 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($report->status === 'reviewed')
                                <span class="badge bg-info">Reviewed</span>
                            @else
                                <span class="badge bg-success">Resolved</span>
                            @endif
                        </td>
                        <td>{{ $report->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.reports.show', $report) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('admin.reports.destroy', $report) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this report?');">
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
                        <td colspan="7" class="text-center py-4 text-muted">No reports found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($reports->hasPages())
    <div class="card-footer">
        {{ $reports->links() }}
    </div>
    @endif
</div>
@endsection
