@extends('layouts.app')

@push('styles')
<style>
    .auth-page {
        min-height: calc(100vh - 80px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }

    .auth-box {
        width: 100%;
        max-width: 420px;
    }

    .auth-logo {
        text-align: center;
        margin-bottom: 2rem;
    }

    .auth-logo h1 {
        font-size: 2rem;
        font-weight: 900;
        background: linear-gradient(135deg, #6366f1, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .auth-logo p {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-top: 0.5rem;
    }

    .auth-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 2rem;
    }

    .auth-input-group {
        position: relative;
        margin-bottom: 1rem;
    }

    .auth-input-group .bi {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
    }

    .auth-input-group .form-control {
        padding-left: 2.5rem;
    }

    .btn-auth {
        width: 100%;
        padding: 0.875rem;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Inter', sans-serif;
    }

    .btn-auth:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(99,102,241,0.4);
    }

    .auth-footer {
        text-align: center;
        margin-top: 1.5rem;
        font-size: 0.875rem;
        color: var(--text-muted);
    }

    .auth-footer a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
    }

    .error-text {
        color: var(--danger);
        font-size: 0.8rem;
        margin-top: 0.25rem;
    }

    .divider {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin: 1.5rem 0;
        color: var(--text-muted);
        font-size: 0.8rem;
    }
    .divider::before, .divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border);
    }
</style>
@endpush

@section('title', 'Đăng nhập')

@section('content')
<div class="auth-page">
    <div class="auth-box">
        <div class="auth-logo">
            <h1>₿ CryptoBet</h1>
            <p>Dự đoán giá BTC - Nhận Point - Đổi quà</p>
        </div>

        <div class="auth-card">
            <h2 style="font-size:1.3rem; font-weight:700; margin-bottom:1.5rem">Đăng nhập</h2>

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="auth-input-group">
                    <i class="bi bi-envelope"></i>
                    <input type="email" name="email" class="form-control" placeholder="Email của bạn"
                           value="{{ old('email') }}" required autocomplete="email">
                </div>

                <div class="auth-input-group">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
                </div>

                <label style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1.25rem; font-size:0.875rem; cursor:pointer; color:var(--text-muted)">
                    <input type="checkbox" name="remember" style="accent-color:var(--primary)">
                    Ghi nhớ đăng nhập
                </label>

                <button type="submit" class="btn-auth">
                    <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                </button>
            </form>

            <div class="auth-footer">
                Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a>
            </div>
        </div>
    </div>
</div>
@endsection
