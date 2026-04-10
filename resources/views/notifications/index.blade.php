@extends('layouts.dashboard')

@section('title', 'التنبيهات')

@section('dashboard-content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">التنبيهات</h5>
                @if($notifications->count() > 0)
                    <span class="badge bg-primary rounded-pill">{{ $notifications->whereNull('read_at')->count() }} جديد</span>
                @endif
            </div>
            <div class="card-body p-0">
                @if($notifications->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($notifications as $notification)
                            <div class="list-group-item border-bottom-0 py-3 px-4 {{ $notification->read_at ? '' : 'bg-light' }}">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="rounded-circle p-2 {{ $notification->read_at ? 'bg-light text-muted' : 'bg-primary bg-opacity-10 text-primary' }}">
                                        @if($notification->data['type'] ?? '' == 'device_offer')
                                            <i class="bi bi-megaphone"></i>
                                        @elseif($notification->data['type'] ?? '' == 'order_status')
                                            <i class="bi bi-box-seam"></i>
                                        @else
                                            <i class="bi bi-bell"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h6 class="mb-1 fw-bold text-dark">{{ $notification->data['title'] ?? 'إشعار جديد' }}</h6>
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1 text-muted">{{ $notification->data['message'] ?? '' }}</p>
                                        
                                        @if(isset($notification->data['contact_info']) && ($notification->data['type'] ?? '') == 'device_offer')
                                            <div class="card bg-light border-0 mt-2 p-3">
                                                <h6 class="card-title fw-bold text-dark small mb-2">بيانات التواصل مع العارض:</h6>
                                                <div class="d-flex flex-wrap gap-3">
                                                    @if(!empty($notification->data['contact_info']['phone']))
                                                        <a href="tel:{{ $notification->data['contact_info']['phone'] }}" class="btn btn-sm btn-white border rounded-pill text-dark shadow-sm">
                                                            <i class="bi bi-telephone text-primary me-2"></i>{{ $notification->data['contact_info']['phone'] }}
                                                        </a>
                                                    @endif
                                                    
                                                    @if(!empty($notification->data['contact_info']['email']))
                                                        <a href="mailto:{{ $notification->data['contact_info']['email'] }}" class="btn btn-sm btn-white border rounded-pill text-dark shadow-sm">
                                                            <i class="bi bi-envelope text-danger me-2"></i>{{ $notification->data['contact_info']['email'] }}
                                                        </a>
                                                    @endif

                                                    @if(empty($notification->data['contact_info']['phone']) && empty($notification->data['contact_info']['email']))
                                                        <small class="text-muted fst-italic">لم يقم المستخدم بإضافة وسيلة تواصل بعد.</small>
                                                    @endif
                                                </div>
                                            </div>
                                        @elseif(isset($notification->data['offerer_name']))
                                             <small class="text-primary fw-bold">المستخدم: {{ $notification->data['offerer_name'] }}</small>
                                        @endif
                                    </div>
                                    @unless($notification->read_at)
                                        <span class="badge bg-primary rounded-circle p-1 mt-2"> </span>
                                    @endunless
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="p-3">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="text-muted mb-3">
                            <i class="bi bi-bell-slash display-4"></i>
                        </div>
                        <h5 class="text-muted">لا توجد تنبيهات حالياً</h5>
                        <p class="text-muted small">سنقوم بإشعارك عند وجود أي تحديثات جديدة.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
