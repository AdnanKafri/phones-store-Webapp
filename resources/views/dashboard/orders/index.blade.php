@extends('layouts.dashboard')

@section('title', 'طلباتي')

@section('dashboard-content')
    <h4 class="fw-bold mb-4">طلباتي</h4>

        @if($orders->count() > 0)
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 border-0">Product</th>
                                    <th class="py-3 border-0">Type</th>
                                    <th class="py-3 border-0">Total</th>
                                    <th class="py-3 border-0">Status</th>
                                    <th class="py-3 border-0">Date</th>
                                    <th class="pe-4 py-3 border-0 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                @if($order->product->images->count() > 0)
                                                    <img src="{{ asset('storage/' . $order->product->images->first()->image_path) }}" class="rounded me-3" width="50" height="50" style="object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                        <i class="bi bi-phone text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-0 text-dark">{{ $order->product->brand }} {{ $order->product->model }}</h6>
                                                    @if($order->variant)
                                                        <small class="text-muted">{{ $order->variant->color_name }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($order->order_type === 'inventory')
                                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill">Store Purchase</span>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">User Request</span>
                                            @endif
                                        </td>
                                        <td class="fw-bold">${{ number_format($order->total_price, 2) }}</td>
                                        <td>
                                            @if($order->status === 'pending')
                                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill">Pending</span>
                                            @elseif($order->status === 'approved')
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill">Approved</span>
                                            @elseif($order->status === 'shipping')
                                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">Shipped</span>
                                            @elseif($order->status === 'completed')
                                                <span class="badge bg-success rounded-pill">Completed</span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">Rejected</span>
                                            @endif
                                        </td>
                                        <td class="text-muted small">{{ $order->created_at->format('M d, Y') }}</td>
                                        <td class="pe-4 text-end">
                                            @if($order->status === 'approved' && $order->order_type === 'user')
                                                <a href="{{ route('orders.confirmation', $order->id) }}" class="btn btn-sm btn-outline-info rounded-pill" title="View Invoice">
                                                    <i class="bi bi-receipt"></i> Invoice
                                                </a>
                                                <button class="btn btn-sm btn-outline-success rounded-pill" data-bs-toggle="modal" data-bs-target="#contactModal{{ $order->id }}">
                                                    <i class="bi bi-telephone me-1"></i> Contact Seller
                                                </button>
                                                
                                                <!-- Contact Modal -->
                                                <div class="modal fade text-start" id="contactModal{{ $order->id }}" tabindex="-1">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content rounded-4 border-0">
                                                            <div class="modal-header border-0">
                                                                <h5 class="modal-title">Seller Contact</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body text-center p-4">
                                                                <div class="display-6 mb-3 text-success"><i class="bi bi-whatsapp"></i></div>
                                                                <h5>{{ $order->product->seller->name }}</h5>
                                                                <p class="text-muted">Contact the seller to arrange payment and collection.</p>
                                                                <div class="bg-light p-3 rounded-3 mt-3">
                                                                    <strong class="fs-5">{{ $order->product->seller->email }}</strong>
                                                                    <!-- Assuming phone isn't in user model yet, generic message -->
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <a href="{{ route('orders.confirmation', $order->id) }}" class="btn btn-sm btn-outline-info rounded-pill" title="View Invoice">
                                                    <i class="bi bi-receipt"></i> Invoice
                                                </a>
                                                <a href="{{ route('product.show', $order->product) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                                    View Item
                                                </a>
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
                <i class="bi bi-bag fs-1 text-muted mb-3 d-block"></i>
                <h4 class="text-muted">No orders yet</h4>
                <a href="{{ route('products.index') }}" class="btn btn-primary rounded-pill mt-3">Browse Products</a>
            </div>
        @endif
@endsection
