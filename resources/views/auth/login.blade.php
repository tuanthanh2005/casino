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
            grid-template-columns: 1.1fr 0.9fr;
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
            margin-top: 1.5rem;
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

        .auth-highlights {
            margin-top: 1.6rem;
            display: grid;
            gap: 0.8rem;
        }

        .auth-highlight {
            border: 1px solid rgba(148, 163, 184, 0.14);
            border-radius: 14px;
            background: rgba(15, 23, 42, 0.5);
            padding: 0.78rem 0.9rem;
            color: #dceaff;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .auth-highlight i {
            color: #34d399;
            font-size: 1rem;
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
            max-width: 430px;
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
            margin-bottom: 1.2rem;
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

        .auth-row {
            margin-bottom: 1.3rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.8rem;
            color: #cbd9ec;
            font-size: 0.92rem;
        }

        .remember-wrap {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            cursor: pointer;
            user-select: none;
        }

        .remember-wrap input {
            accent-color: #22d3ee;
            width: 14px;
            height: 14px;
        }

        .forgot-link {
            color: #9fb0c7;
            text-decoration: none;
            font-size: 0.88rem;
        }

        .forgot-link:hover {
            color: #d9ecff;
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
            margin-top: 1.15rem;
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

            .auth-highlights {
                grid-template-columns: 1fr;
                margin-top: 1rem;
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

            .auth-highlights {
                display: none;
            }

            .auth-card {
                padding: 1.15rem;
            }
        }
    </style>
@endpush

@section('title', 'Đăng nhập')

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
                        Không chỉ đăng nhập, đây là cổng vào hệ sinh thái game giải trí: dự đoán, mini game, tích điểm và đổi quà trong một trải nghiệm mượt.
                    </p>

                    <div class="auth-pill-row">
                        <span class="auth-pill">Prediction</span>
                        <span class="auth-pill">Mini Games</span>
                        <span class="auth-pill">Farm & Rewards</span>
                    </div>

                    <div class="auth-highlights">
                        <div class="auth-highlight">
                            <i class="bi bi-lightning-charge-fill"></i>
                            <span>Đăng nhập 1 chạm và bắt đầu chơi ngay</span>
                        </div>
                        <div class="auth-highlight">
                            <i class="bi bi-shield-check"></i>
                            <span>Bảo mật tài khoản với luồng xác thực chuẩn Laravel</span>
                        </div>
                        <div class="auth-highlight">
                            <i class="bi bi-gift-fill"></i>
                            <span>Nhận thưởng, theo dõi điểm và giao dịch tại một nơi</span>
                        </div>
                    </div>
                </div>

                <p class="auth-side-note">Trải nghiệm tốt nhất trên cả desktop và mobile.</p>
            </div>

            <div class="auth-panel">
                <div class="auth-card">
                    <h2>Đăng nhập</h2>
                    <p class="auth-card-sub">Chào mừng quay lại, nhập thông tin để tiếp tục.</p>

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
                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                placeholder="Email của bạn"
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
                                placeholder="Mật khẩu"
                                required
                            >
                        </div>

                        <div class="auth-row">
                            <label class="remember-wrap">
                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                <span>Ghi nhớ đăng nhập</span>
                            </label>
                            <a href="javascript:void(0)" onclick="openForgotModal()" class="forgot-link">Quên mật khẩu?</a>
                        </div>

                        <button type="submit" class="btn-auth">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Đăng nhập
                        </button>
                    </form>

                    <div class="auth-footer">
                        Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a>
                    </div>
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