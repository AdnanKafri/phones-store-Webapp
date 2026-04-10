@extends('layouts.app')

@section('title', 'تسجيل الدخول - فون ماركت')

@section('content')

<div class="auth-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="auth-card">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold mb-2">أهلاً بك مجدداً</h2>
                        <p class="text-muted">سجل الدخول للمتابعة</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">البريد الإلكتروني</label>
                            <div class="input-with-icon">
                                <i class="bi bi-envelope"></i>
                                <input id="email" type="email" class="form-control text-start @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" required autocomplete="email" autofocus 
                                       placeholder="example@email.com" dir="ltr">
                                @error('email')
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
                                       name="password" required autocomplete="current-password" 
                                       placeholder="أدخل كلمة المرور">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-3 form-check">
                            <input class="form-check-input float-end" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label me-2" for="remember">
                                تذكرني
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold mb-3">
                            تسجيل الدخول
                        </button>

                        <!-- Forgot Password -->
                        @if (Route::has('password.request'))
                            <div class="text-center mb-3">
                                <a class="text-muted small" href="{{ route('password.request') }}">
                                    نسيت كلمة المرور؟
                                </a>
                            </div>
                        @endif

                        <!-- Register Link -->
                        <div class="text-center">
                            <p class="text-muted mb-0">
                                ليس لديك حساب؟ 
                                <a href="{{ route('register') }}" class="text-primary fw-bold">إنشاء حساب جديد</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
