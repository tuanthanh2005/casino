@extends('layouts.app')

@push('styles')
<style>
    .auth-page {
        min-height: calc(100vh - 84px);
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2.5rem 1rem;
        overflow: hidden;
    }

    .auth-bg-glow {
        position: absolute;
        border-radius: 999px;
        filter: blur(80px);
        z-index: 0;
        opacity: 0.6;
        pointer-events: none;
    }

    .auth-bg-glow.a {
        width: 440px;
        height: 440px;
        top: -120px;
        left: -120px;
        background: radial-gradient(circle, rgba(6, 182, 212, 0.45), transparent 70%);
    }

    .auth-bg-glow.b {
        width: 360px;
        height: 360px;
        right: -100px;
        bottom: -120px;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.36), transparent 70%);
    }

    .auth-layout {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 1080px;
        display: grid;
        grid-template-columns: 1.05fr 0.95fr;
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 28px;
        background: linear-gradient(140deg, rgba(10, 16, 32, 0.92), rgba(8, 12, 24, 0.95));
        backdrop-filter: blur(18px);
        overflow: hidden;
        box-shadow: 0 40px 70px rgba(1, 6, 18, 0.55);
    }

    .auth-showcase {
        padding: 2.2rem;
        border-right: 1px solid rgba(148, 163, 184, 0.14);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background:
            linear-gradient(165deg, rgba(6, 182, 212, 0.1), transparent 45%),
            linear-gradient(20deg, rgba(16, 185, 129, 0.08), transparent 48%);
    }

    .auth-brand {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        font-size: 2rem;
        font-weight: 900;
        letter-spacing: -0.02em;
        margin-bottom: 0.85rem;
        background: linear-gradient(120deg, #22d3ee, #34d399);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .auth-tagline {
        color: #dbe9ff;
        max-width: 460px;
        line-height: 1.55;
        font-size: 1.02rem;
    }

    .auth-pill-row {
        margin-top: 1.3rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem;
    }

    .auth-pill {
        padding: 0.42rem 0.7rem;
        border-radius: 999px;
        border: 1px solid rgba(125, 211, 252, 0.26);
        background: rgba(2, 132, 199, 0.14);
        color: #c3ecff;
        font-size: 0.78rem;
        font-weight: 600;
        letter-spacing: 0.02em;
    }

    .auth-bonus {
        margin-top: 1.3rem;
        border: 1px solid rgba(52, 211, 153, 0.35);
        border-radius: 14px;
        background: rgba(16, 185, 129, 0.12);
        padding: 0.95rem;
        color: #cdfce8;
    }

    .auth-bonus-title {
        font-size: 0.84rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        opacity: 0.9;
        margin-bottom: 0.3rem;
    }

    .auth-bonus-value {
        font-size: 1.8rem;
        font-weight: 900;
        line-height: 1;
    }

    .auth-bonus-sub {
        margin-top: 0.25rem;
        font-size: 0.9rem;
        color: #9ff5d2;
    }

    .auth-side-note {
        margin-top: 1rem;
        color: #9fb3cd;
        font-size: 0.82rem;
    }

    .auth-panel {
        padding: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .auth-card {
        width: 100%;
        max-width: 440px;
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 20px;
        background: rgba(14, 21, 36, 0.88);
        padding: 1.55rem;
    }

    .auth-card h2 {
        margin-bottom: 0.35rem;
        font-size: 1.55rem;
        font-weight: 800;
        color: #f8fcff;
        letter-spacing: -0.02em;
    }

    .auth-card-sub {
        margin-bottom: 1.15rem;
        color: #9fb0c7;
        font-size: 0.9rem;
    }

    .auth-input-group {
        position: relative;
        margin-bottom: 0.95rem;
    }

    .auth-input-group .bi {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #8ba1bc;
    }

    .auth-input-group .form-control {
        padding-left: 2.5rem;
        border-radius: 12px;
        border-color: rgba(148, 163, 184, 0.22);
        background: rgba(226, 232, 240, 0.92);
        color: #0f172a;
        font-weight: 600;
    }

    .auth-input-group .form-control::placeholder {
        color: #64748b;
        font-weight: 500;
    }

    .auth-input-group .form-control:focus {
        border-color: #22d3ee;
        box-shadow: 0 0 0 3px rgba(34, 211, 238, 0.2);
    }

    .password-tips {
        margin: 0.35rem 0 1.15rem;
        padding: 0.75rem 0.8rem;
        border-radius: 12px;
        border: 1px solid rgba(148, 163, 184, 0.2);
        background: rgba(15, 23, 42, 0.45);
        color: #b8c8db;
        font-size: 0.82rem;
        line-height: 1.5;
    }

    .btn-auth {
        width: 100%;
        border: none;
        border-radius: 12px;
        padding: 0.88rem 1rem;
        background: linear-gradient(125deg, #06b6d4, #0ea5e9 55%, #22d3ee);
        color: #fff;
        font-size: 1.03rem;
        font-weight: 800;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s, filter 0.2s;
    }

    .btn-auth:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 30px rgba(14, 165, 233, 0.28);
        filter: brightness(1.03);
    }

    .auth-footer {
        margin-top: 1.1rem;
        text-align: center;
        font-size: 0.9rem;
        color: #9fb0c7;
    }

    .auth-footer a {
        color: #34d399;
        text-decoration: none;
        font-weight: 700;
    }

    .auth-footer a:hover {
        color: #6ee7b7;
    }

    @media (max-width: 900px) {
        .auth-layout {
            grid-template-columns: 1fr;
            max-width: 520px;
        }

        .auth-showcase {
            border-right: 0;
            border-bottom: 1px solid rgba(148, 163, 184, 0.14);
            padding: 1.35rem;
        }

        .auth-brand {
            font-size: 1.7rem;
        }

        .auth-panel {
            padding: 1rem;
        }
    }

    @media (max-width: 520px) {
        .auth-page {
            padding: 1rem 0.7rem;
        }

        .auth-showcase {
            padding: 1rem;
        }

        .auth-card {
            padding: 1.15rem;
        }
    }
</style>
@endpush

@section('title', 'Đăng ký')

@section('content')
<div class="auth-page">
    <div class="auth-bg-glow a"></div>
    <div class="auth-bg-glow b"></div>

    <div class="auth-layout">
        <div class="auth-showcase">
            <div>
                <div class="auth-brand">
                    <span>🌊</span>
                    <span>AquaHub</span>
                </div>
                <p class="auth-tagline">
                    Tạo tài khoản để mở khóa toàn bộ trải nghiệm: BTC prediction, mini games, nông trại và trung tâm đổi quà.
                </p>

                <div class="auth-pill-row">
                    <span class="auth-pill">Nhanh gọn 30s</span>
                    <span class="auth-pill">Bắt đầu ngay</span>
                    <span class="auth-pill">Không phí ẩn</span>
                </div>

                <div class="auth-bonus">
                    <div class="auth-bonus-title">Welcome Bonus</div>
                    <div class="auth-bonus-value">+100 Point</div>
                    <div class="auth-bonus-sub">Nhận ngay sau khi tạo tài khoản thành công</div>
                </div>
            </div>

            <p class="auth-side-note">Mẹo: dùng email thật để dễ lấy lại tài khoản khi cần hỗ trợ.</p>
        </div>

        <div class="auth-panel">
            <div class="auth-card">
                <h2>Tạo tài khoản</h2>
                <p class="auth-card-sub">Điền thông tin bên dưới để tham gia ngay.</p>

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
                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            placeholder="Tên hiển thị"
                            value="{{ old('name') }}"
                            required
                        >
                    </div>

                    <div class="auth-input-group">
                        <i class="bi bi-envelope"></i>
                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            placeholder="Email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                        >
                    </div>

                    <div class="auth-input-group">
                        <i class="bi bi-lock"></i>
                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            placeholder="Mật khẩu (tối thiểu 8 ký tự)"
                            required
                        >
                    </div>

                    <div class="auth-input-group">
                        <i class="bi bi-lock-fill"></i>
                        <input
                            type="password"
                            name="password_confirmation"
                            class="form-control"
                            placeholder="Xác nhận mật khẩu"
                            required
                        >
                    </div>

                    <div class="password-tips">
                        Mật khẩu nên có ít nhất 8 ký tự, gồm chữ và số để tài khoản an toàn hơn.
                    </div>

                    <button type="submit" class="btn-auth">
                        <i class="bi bi-rocket-takeoff"></i>
                        Đăng ký ngay
                    </button>
                </form>

                <div class="auth-footer">
                    Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
