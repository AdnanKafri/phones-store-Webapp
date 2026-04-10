@extends('admin.layout')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Order Management</h1>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-table me-1"></i>
                        All Orders
                    </div>
                    <div>
                        <a href="{{ route('admin.orders') }}" class="btn btn-sm {{ !request('type') ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
                        <a href="{{ route('admin.orders', ['type' => 'inventory']) }}" class="btn btn-sm {{ request('type') == 'inventory' ? 'btn-primary' : 'btn-outline-primary' }}">Inventory</a>
                        <a href="{{ route('admin.orders', ['type' => 'user']) }}" class="btn btn-sm {{ request('type') == 'user' ? 'btn-primary' : 'btn-outline-primary' }}">User Requests</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Product</th>
                                <th>Buyer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Seller Appr.</th>
                                <th>Admin Appr.</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>
                                        @if($order->order_type === 'inventory')
                                            <span class="badge bg-info text-dark">Store</span>
                                        @else
                                            <span class="badge bg-secondary">User</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $order->product->brand }} {{ $order->product->model }}
                                        @if($order->variant)
                                            <br><small>({{ $order->variant->color_name }})</small>
                                        @endif
                                    </td>
                                    <td>{{ $order->user->name }}<br><small>{{ $order->user->email }}</small></td>
                                    <td>${{ number_format($order->total_price, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status == 'approved' || $order->status == 'completed' ? 'success' : ($order->status == 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($order->order_type === 'user')
                                            @if($order->seller_approval === 1) <i class="fas fa-check text-success"></i>
                                            @elseif($order->seller_approval === 0) <i class="fas fa-times text-danger"></i>
                                            @else <i class="fas fa-clock text-warning"></i>
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->admin_approval === 1) <i class="fas fa-check text-success"></i>
                                        @elseif($order->admin_approval === 0) <i class="fas fa-times text-danger"></i>
                                        @else <i class="fas fa-clock text-warning"></i>
                                        @endif
                                    </td>
                                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            @if(is_null($order->admin_approval))
                                                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <button name="action" value="approve" class="btn btn-success btn-sm" title="Approve"><i class="fas fa-check"></i></button>
                                                </form>
                                                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <button name="action" value="reject" class="btn btn-danger btn-sm" title="Reject"><i class="fas fa-times"></i></button>
                                                </form>
                                            @endif
                                            
                                            @if($order->order_type === 'inventory' && $order->status === 'approved')
                                                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <button name="action" value="ship" class="btn btn-primary btn-sm" title="Mark Shipped"><i class="fas fa-shipping-fast"></i></button>
                                                </form>
                                            @endif
                                            
                                            <a href="{{ route('orders.confirmation', $order->id) }}" class="btn btn-info btn-sm" title="View Invoice" target="_blank">
                                                <i class="fas fa-file-invoice"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
