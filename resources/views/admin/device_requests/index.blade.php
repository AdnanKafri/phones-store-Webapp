@extends('admin.layout')

@section('title', 'Device Requests')
@section('page-title', 'Device Requests')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Device</th>
                        <th>Notes</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light d-flex justify-content-center align-items-center me-2" style="width: 32px; height: 32px;">
                                        {{ strtoupper(substr($req->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $req->user->name }}</div>
                                        <div class="text-muted small">{{ $req->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $req->brand }}</div>
                                <div class="small">{{ $req->model }}</div>
                            </td>
                            <td>
                                <span class="d-inline-block text-truncate" style="max-width: 200px;" title="{{ $req->notes }}">
                                    {{ $req->notes ?? '-' }}
                                </span>
                            </td>
                            <td>
                                @if($req->status == 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($req->status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $req->created_at->format('M d, Y') }}</td>
                            <td>
                                <form action="{{ route('admin.device-requests.update', $req) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                        <option value="pending" {{ $req->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ $req->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ $req->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </form>
                                <form action="{{ route('admin.device-requests.destroy', $req) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Delete?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4">No requests found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $requests->links() }}
        </div>
    </div>
</div>
@endsection
