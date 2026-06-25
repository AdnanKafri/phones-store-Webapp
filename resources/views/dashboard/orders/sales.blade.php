@extends('layouts.dashboard')

@section('title', 'طلبات المبيعات')

@section('dashboard-content')
    <h4 class="fw-bold mb-2">طلبات المبيعات الواردة</h4>
    <p class="text-muted mb-4 small">إدارة طلبات الشراء الواردة لمنتجاتك المعروضة</p>

        @if($orders->count() > 0)
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 border-0">Product</th>
                                    <th class="py-3 border-0">Buyer</th>
                                    <th class="py-3 border-0">Amount</th>
                                    <th class="py-3 border-0">Your Approval</th>
                                    <th class="py-3 border-0">Admin Approval</th>
                                    <th class="pe-4 py-3 border-0 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                @if($order->product)
                                                    <img src="{{ $order->product->primary_image_url }}" class="rounded me-3" width="50" height="50" style="object-fit: cover;" alt="{{ $order->product->brand }} {{ $order->product->model }}">
                                                @else
                                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                        <i class="bi bi-phone text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-0 text-dark">{{ $order->product->brand }} {{ $order->product->model }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-secondary bg-opacity-10 rounded-circle d-flex justify-content-center align-items-center me-2" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                                    {{ strtoupper(substr($order->user->name, 0, 1)) }}
                                                </div>
                                                <span>{{ $order->user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="fw-bold text-success">${{ number_format($order->total_price, 2) }}</td>
                                        
                                        <td>
                                            @if($order->seller_approval === 1)
                                                <span class="badge bg-success rounded-pill"><i class="bi bi-check-lg"></i> Approved</span>
                                            @elseif($order->seller_approval === 0)
                                                <span class="badge bg-danger rounded-pill">Rejected</span>
                                            @else
                                                <span class="badge bg-warning text-dark rounded-pill">Pending</span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @if($order->admin_approval === 1)
                                                <span class="badge bg-success rounded-pill"><i class="bi bi-check-lg"></i> Approved</span>
                                            @elseif($order->admin_approval === 0)
                                                <span class="badge bg-danger rounded-pill">Rejected</span>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-25 text-dark rounded-pill">Pending Review</span>
                                            @endif
                                        </td>

                                        <td class="pe-4 text-end">
                                            @if(is_null($order->seller_approval))
                                                <div class="d-flex justify-content-end gap-2">
                                                    <form action="{{ route('dashboard.sales.update', $order) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="action" value="approve">
                                                        <button class="btn btn-sm btn-success rounded-pill px-3">Approve</button>
                                                    </form>
                                                    <form action="{{ route('dashboard.sales.update', $order) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="action" value="reject">
                                                        <button class="btn btn-sm btn-outline-danger rounded-pill px-3">Reject</button>
                                                    </form>
                                                </div>
                                            @elseif($order->status === 'approved')
                                                <button class="btn btn-sm btn-outline-dark rounded-pill" disabled>Contact Revealed</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="mt-4 d-flex justify-content-center">
                {{ $orders->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                <h4 class="text-muted">No requests yet</h4>
                <p>When users want to buy your items, requests will appear here.</p>
            </div>
        @endif
@endsection
