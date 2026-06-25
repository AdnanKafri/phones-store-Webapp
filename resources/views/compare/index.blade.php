@extends('layouts.app')

@section('title', 'مقارنة الهواتف')

@section('content')
@php
    $specLabels = [
        'battery' => 'البطارية',
        'camera' => 'الكاميرا',
        'storage' => 'التخزين',
        'ram' => 'الذاكرة العشوائية',
        'processor' => 'المعالج',
        'performance' => 'الأداء',
        'display' => 'الشاشة',
        'operating_system' => 'نظام التشغيل',
    ];
@endphp

<div class="container py-5 compare-page" dir="rtl">
    <div class="compare-hero mb-4">
        <div class="compare-hero__content">
            <span class="compare-hero__badge">مقارنة الهواتف</span>
            <h1 class="display-6 fw-bold mb-2">قارن هاتفين جنبًا إلى جنب</h1>
            <p class="text-muted mb-3">
                اختر جهازين من الدليل الرئيسي وشاهد الفروقات بوضوح في البطارية والكاميرا والتخزين والذاكرة والمعالج والشاشة.
            </p>
            <div class="compare-hero__chips">
                <span><i class="bi bi-battery-charging"></i> البطارية</span>
                <span><i class="bi bi-camera"></i> الكاميرا</span>
                <span><i class="bi bi-cpu"></i> المعالج</span>
                <span><i class="bi bi-display"></i> الشاشة</span>
            </div>
        </div>
        <div class="compare-hero__actions">
            <a href="{{ route('products.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                العودة إلى المتجر
            </a>
        </div>
    </div>

    <div class="compare-shell">
        <form method="GET" action="{{ route('compare.index') }}" class="compare-selector">
            <div class="row g-3 align-items-end">
                <div class="col-lg-5">
                    <label for="left_device_id" class="form-label fw-semibold">الهاتف الأول</label>
                    <select id="left_device_id" name="left_device_id" class="form-select form-select-lg rounded-4" required>
                        <option value="">اختر هاتفًا</option>
                        @foreach($devices as $device)
                            <option value="{{ $device->id }}" {{ $leftDeviceId === $device->id ? 'selected' : '' }}>
                                {{ $device->brand }} {{ $device->model_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-5">
                    <label for="right_device_id" class="form-label fw-semibold">الهاتف الثاني</label>
                    <select id="right_device_id" name="right_device_id" class="form-select form-select-lg rounded-4" required>
                        <option value="">اختر هاتفًا</option>
                        @foreach($devices as $device)
                            <option value="{{ $device->id }}" {{ $rightDeviceId === $device->id ? 'selected' : '' }}>
                                {{ $device->brand }} {{ $device->model_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 d-grid">
                    <button type="submit" class="btn btn-primary btn-lg rounded-4 compare-submit-button">
                        قارن الآن
                    </button>
                </div>
            </div>

            @if($leftDeviceId || $rightDeviceId)
                <div class="mt-3 d-flex gap-2 flex-wrap">
                    <a href="{{ route('compare.index') }}" class="btn btn-outline-secondary rounded-pill">إعادة التعيين</a>
                    <button type="button" class="btn btn-outline-primary rounded-pill" id="swap-devices-button">تبديل الهاتفين</button>
                </div>
            @endif
        </form>

        @if($comparison)
            <div class="compare-results mt-4">
                <div class="row g-4 mb-4">
                    @foreach($comparison['devices'] as $device)
                        <div class="col-md-6">
                            <div class="compare-device-card h-100">
                                <div class="compare-device-card__media">
                                    @if($device['image_url'])
                                        <img src="{{ $device['image_url'] }}" alt="{{ $device['name'] }}">
                                    @else
                                        <div class="compare-device-card__placeholder">
                                            <i class="bi bi-phone"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="compare-device-card__body">
                                    <div class="d-flex justify-content-between align-items-start gap-3">
                                        <div>
                                            <h3 class="h4 fw-bold mb-1">{{ $device['name'] }}</h3>
                                            <p class="text-muted mb-2">
                                                {{ $device['release_year'] ? 'إصدار ' . $device['release_year'] : 'سنة الإصدار غير متوفرة' }}
                                            </p>
                                        </div>
                                        <span class="badge text-bg-light">
                                            {{ $device['marketplace_products_count'] }} {{ $device['marketplace_products_count'] == 1 ? 'إعلان' : 'إعلان' }}
                                        </span>
                                    </div>
                                    <div class="compare-device-card__chips">
                                        <span>
                                            <strong>المعالج:</strong>
                                            {{ $device['specifications']['processor'] }}
                                        </span>
                                        <span>
                                            <strong>الذاكرة:</strong>
                                            {{ $device['specifications']['ram'] }}
                                        </span>
                                        <span>
                                            <strong>التخزين:</strong>
                                            {{ $device['specifications']['storage'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="table-responsive">
                    <table class="table compare-table align-middle">
                        <thead>
                            <tr>
                                <th scope="col">المواصفة</th>
                                <th scope="col">{{ $comparison['devices'][0]['name'] }}</th>
                                <th scope="col">{{ $comparison['devices'][1]['name'] }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comparison['rows'] as $row)
                                <tr class="{{ $row['different'] ? 'compare-table__row--different' : '' }}">
                                    <th scope="row">{{ $specLabels[$row['key']] ?? $row['label'] }}</th>
                                    <td>{{ $row['values'][0] ?: 'غير متوفر' }}</td>
                                    <td>{{ $row['values'][1] ?: 'غير متوفر' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="compare-empty mt-4">
                <div class="compare-empty__icon">
                    <i class="bi bi-layout-split"></i>
                </div>
                <h3 class="h4 fw-bold">اختر جهازين لبدء المقارنة</h3>
                <p class="text-muted mb-0">
                    تعتمد المقارنة على بيانات موحدة من الدليل الرئيسي، لذلك تبقى المواصفات دقيقة حتى لو كانت بعض إعلانات السوق غير مكتملة.
                </p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const swapButton = document.getElementById('swap-devices-button');

    if (!swapButton) {
        return;
    }

    swapButton.addEventListener('click', function () {
        const leftSelect = document.getElementById('left_device_id');
        const rightSelect = document.getElementById('right_device_id');
        const temp = leftSelect.value;

        leftSelect.value = rightSelect.value;
        rightSelect.value = temp;
    });
});
</script>
@endpush
