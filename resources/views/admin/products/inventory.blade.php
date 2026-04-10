@extends('admin.layout')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 fw-bold text-gray-800">إدارة المخزون (Inventory)</h2>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary rounded-pill">
            <i class="bi bi-plus-lg me-2"></i> إضافة منتج جديد
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">المنتج</th>
                            <th>السعر</th>
                            <th>الألوان / الكمية</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        @if($product->primary_image_url)
                                            <img src="{{ $product->primary_image_url }}" class="rounded-3 me-3" width="50" height="50" style="object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="bi bi-phone text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $product->brand }} {{ $product->model }}</h6>
                                            <small class="text-muted">{{ $product->category->name ?? 'غير محدد' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-bold">${{ number_format($product->price, 2) }}</td>
                                <td>
                                    @if($product->variants->count() > 0)
                                        <div class="d-flex flex-column gap-1">
                                            @foreach($product->variants as $variant)
                                                <span class="badge bg-secondary bg-opacity-10 text-dark border d-flex justify-content-between" style="max-width: 150px;">
                                                    <span>{{ $variant->color_name }}</span>
                                                    <span class="fw-bold {{ $variant->stock_quantity == 0 ? 'text-danger' : 'text-success' }}">
                                                        {{ $variant->stock_quantity }}
                                                    </span>
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted small">لا يوجد خيارات</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->status == 'available')
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">متوفر</span>
                                    @elseif($product->status == 'sold')
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">تم البيع</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">{{ $product->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                        <i class="bi bi-pencil"></i> تعديل
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-box-seam display-4 d-block mb-3 opacity-50"></i>
                                    لا يوجد منتجات في المخزون حالياً
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($products->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
