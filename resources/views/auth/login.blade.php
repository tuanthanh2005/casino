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
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
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

        .divider::before,
        .divider::after {
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
                <h1>🌊 AquaHub</h1>
                <p>Chơi game - Nhận Point - Đổi quà</p>
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

                    <input type="checkbox" name="remember" style="accent-color:var(--primary)">
                    Ghi nhớ đăng nhập
                    </label>

                    <div style="margin-bottom:1.5rem; text-align:right">
                        <a href="javascript:void(0)" onclick="openForgotModal()"
                            style="font-size:0.85rem; color:var(--text-muted); text-decoration:none">Quên mật khẩu?</a>
                    </div>

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

    <!-- FORGOT PASSWORD MODAL -->
    <div class="profile-modal" id="forgot-modal">
        <div class="profile-modal-card" style="max-width:400px">
            <div class="profile-modal-header">
                <div style="display:flex; align-items:center; gap:0.75rem">
                    <span style="font-size:1.5rem">🔑</span>
                    <div style="font-weight:800; font-size:1.1rem">Quên mật khẩu?</div>
                </div>
                <button class="btn-close-modal" onclick="closeForgotModal()"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="profile-modal-body" style="text-align:center">
                <p style="color:var(--text-muted); margin-bottom:1.5rem; line-height:1.6">
                    Vui lòng liên hệ Admin qua email để được hỗ trợ cấp lại mật khẩu mới.
                </p>
                <div
                    style="background:rgba(99,102,241,0.1); padding:1rem; border-radius:12px; border:1px dashed var(--primary); margin-bottom:1.5rem">
                    <div style="margin-bottom:0.75rem">
                        <div
                            style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px">
                            Email hỗ trợ:</div>
                        <div style="font-weight:700; color:var(--primary); font-size:1.1rem">tranthanhtuanfix@gmail.com
                        </div>
                    </div>
                    <div style="margin-bottom:0.75rem">
                        <div
                            style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px">
                            Telegram:</div>
                        <div style="font-weight:700; color:var(--primary); font-size:1.1rem">@specademy</div>
                    </div>
                    <div>
                        <div
                            style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px">
                            Zalo:</div>
                        <div style="font-weight:700; color:var(--primary); font-size:1.1rem">0708910952</div>
                    </div>
                </div>
                <p style="font-size:0.8rem; color:var(--text-muted)">
                    <i>Gợi ý: Cung cấp Tên hiển thị hoặc Email đăng ký chính xác để Admin dễ dàng tra cứu.</i>
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openForgotModal() {
                document.getElementById('forgot-modal').classList.add('open');
            }
            function closeForgotModal() {
                document.getElementById('forgot-modal').classList.remove('open');
            }
            document.getElementById('forgot-modal').addEventListener('click', function (e) {
                if (e.target === this) closeForgotModal();
            });
        </script>
    @endpush
@endsection