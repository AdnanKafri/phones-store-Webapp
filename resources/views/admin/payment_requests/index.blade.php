@extends('admin.layout')

@section('title', 'Wallet Recharges')
@section('page-title', 'Wallet Recharges')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Proof</th>
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
                            <td class="fw-bold text-success">${{ number_format($req->amount, 2) }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $req->payment_method ?? 'Manual' }}</span>
                            </td>
                            <td>
                                @if($req->proof_image)
                                    <a href="{{ asset('storage/' . $req->proof_image) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-image me-1"></i> View
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
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
                            <td class="small text-muted">{{ $req->created_at->format('M d, Y h:i A') }}</td>
                            <td>
                                @if($req->status == 'pending')
                                <form action="{{ route('admin.payment-requests.update', $req) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button name="status" value="approved" class="btn btn-sm btn-success" onclick="return confirm('Approve recharge?')">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button name="status" value="rejected" class="btn btn-sm btn-danger" onclick="return confirm('Reject request?')">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                                @else
                                    <span class="text-muted small">Processed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4">No requests found.</td></tr>
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
