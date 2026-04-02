@extends('layouts.app')

@section('title', 'Đăng ký')

@section('content')
<div class="auth-page">
    <div class="auth-box">
        <div class="auth-logo">
            <h1>🌊 AquaHub</h1>
            <p>Tạo tài khoản - Nhận 100 Point khởi đầu miễn phí!</p>
        </div>

        <div class="auth-card">
            <h2 style="font-size:1.3rem; font-weight:700; margin-bottom:1.5rem">Tạo tài khoản</h2>

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="auth-input-group">
                    <i class="bi bi-person"></i>
                    <input type="text" name="name" class="form-control" placeholder="Tên hiển thị"
                           value="{{ old('name') }}" required>
                </div>

                <div class="auth-input-group">
                    <i class="bi bi-envelope"></i>
                    <input type="email" name="email" class="form-control" placeholder="Email"
                           value="{{ old('email') }}" required>
                </div>

                <div class="auth-input-group">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="password" class="form-control" placeholder="Mật khẩu (tối thiểu 8 ký tự)" required>
                </div>

                <div class="auth-input-group">
                    <i class="bi bi-lock-fill"></i>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Xác nhận mật khẩu" required>
                </div>

                <button type="submit" class="btn-auth" style="margin-top:0.5rem">
                    <i class="bi bi-rocket-takeoff"></i> Đăng ký ngay
                </button>
            </form>

            <div class="auth-footer">
                Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.auth-page {
    min-height: calc(100vh - 80px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}
.auth-box { width: 100%; max-width: 420px; }
.auth-logo { text-align: center; margin-bottom: 2rem; }
.auth-logo h1 {
    font-size: 2rem;
    font-weight: 900;
    background: linear-gradient(135deg, #6366f1, #f59e0b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.auth-logo p { color: var(--text-muted); font-size: 0.9rem; margin-top: 0.5rem; }
.auth-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 20px; padding: 2rem; }
.auth-input-group { position: relative; margin-bottom: 1rem; }
.auth-input-group .bi { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); }
.auth-input-group .form-control { padding-left: 2.5rem; }
.btn-auth {
    width: 100%; padding: 0.875rem;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white; border: none; border-radius: 12px; font-size: 1rem;
    font-weight: 700; cursor: pointer; transition: all 0.2s; font-family: 'Inter', sans-serif;
}
.btn-auth:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(99,102,241,0.4); }
.auth-footer { text-align: center; margin-top: 1.5rem; font-size: 0.875rem; color: var(--text-muted); }
.auth-footer a { color: var(--primary); text-decoration: none; font-weight: 600; }
</style>
@endpush
