@extends('layouts.dashboard')

@section('title', 'الملف الشخصي')

@section('dashboard-content')
<div class="container-fluid">
    <div class="row g-4">
        <!-- Basic Information -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">المعلومات الشخصية</h5>
                </div>
                <div class="card-body p-4">
                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">الاسم الكامل</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="mt-2">
                                    <p class="text-muted small mb-1">بريدك الإلكتروني غير مفعل.</p>
                                    <button form="send-verification" class="btn btn-link p-0 text-decoration-none small">
                                        إعادة إرسال رابط التفعيل
                                    </button>
                                </div>
                                @if (session('status') === 'verification-link-sent')
                                    <div class="alert alert-success mt-2 py-2 px-3 small">
                                        تم إرسال رابط تفعيل جديد إلى بريدك الإلكتروني.
                                    </div>
                                @endif
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary rounded-pill px-4 mt-3">
                            <i class="bi bi-save me-2"></i>حفظ التغييرات
                        </button>

                        @if (session('status') === 'profile-updated')
                            <span class="text-success ms-3 small fade-in-out fw-bold"><i class="bi bi-check-circle me-1"></i>تم الحفظ بنجاح</span>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">الأمان وكلمة المرور</h5>
                </div>
                <div class="card-body p-4">
                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <!-- Current Password -->
                        <div class="mb-3">
                            <label for="current_password" class="form-label">كلمة المرور الحالية</label>
                            <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" id="current_password" name="current_password" autocomplete="current-password">
                            @error('current_password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور الجديدة</label>
                            <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" id="password" name="password" autocomplete="new-password">
                            @error('password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                            <input type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                            @error('password_confirmation', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-outline-danger rounded-pill px-4 mt-3">
                            <i class="bi bi-shield-lock me-2"></i>تحديث كلمة المرور
                        </button>

                        @if (session('status') === 'password-updated')
                            <span class="text-success ms-3 small fade-in-out fw-bold"><i class="bi bi-check-circle me-1"></i>تم التحديث بنجاح</span>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Form for Verification -->
<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<script>
    // Simple fade out for success messages
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            var successMessages = document.querySelectorAll('.fade-in-out');
            successMessages.forEach(function(msg) {
                msg.style.transition = "opacity 0.5s";
                msg.style.opacity = 0;
            });
        }, 3000); // 3 seconds
    });
</script>
@endsection
