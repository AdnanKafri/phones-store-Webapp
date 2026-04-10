@extends('layouts.app')

@section('title', 'إنشاء حساب جديد - فون ماركت')

@section('content')

<div class="auth-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="auth-card">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold mb-2">إنشاء حساب جديد</h2>
                        <p class="text-muted">انضم إلى فون ماركت وابدأ البيع والشراء اليوم</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">الاسم الكامل</label>
                            <div class="input-with-icon">
                                <i class="bi bi-person"></i>
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name') }}" required autocomplete="name" autofocus 
                                       placeholder="الاسم الثلاثي">
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">البريد الإلكتروني</label>
                            <div class="input-with-icon">
                                <i class="bi bi-envelope"></i>
                                <input id="email" type="email" class="form-control text-start @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" required autocomplete="email" 
                                       placeholder="example@email.com" dir="ltr">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Phone (Optional) -->
                        <div class="mb-3">
                            <label for="phone" class="form-label fw-bold">رقم الهاتف <span class="text-muted small">(اختياري)</span></label>
                            <div class="input-with-icon">
                                <i class="bi bi-telephone"></i>
                                <input id="phone" type="text" class="form-control text-start @error('phone') is-invalid @enderror" 
                                       name="phone" value="{{ old('phone') }}" autocomplete="tel" 
                                       placeholder="+963 999 123 456" dir="ltr">
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">كلمة المرور</label>
                            <div class="input-with-icon">
                                <i class="bi bi-lock"></i>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                       name="password" required autocomplete="new-password" 
                                       placeholder="8 خانات على الأقل">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label fw-bold">تأكيد كلمة المرور</label>
                            <div class="input-with-icon">
                                <i class="bi bi-lock-fill"></i>
                                <input id="password_confirmation" type="password" class="form-control" 
                                       name="password_confirmation" required autocomplete="new-password" 
                                       placeholder="أعد كتابة كلمة المرور">
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold mb-3">
                            تسجيل الحساب
                        </button>

                        <!-- Login Link -->
                        <div class="text-center">
                            <p class="text-muted mb-0">
                                لديك حساب بالفعل؟ 
                                <a href="{{ route('login') }}" class="text-primary fw-bold">تسجيل الدخول</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
