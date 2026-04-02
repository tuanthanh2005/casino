@extends('layouts.app')

@section('title', 'Hỗ Trợ MXH - Kháng Cáo TikTok Chuyên Nghiệp')

@push('styles')
<style>
    :root {
        --nav-primary: #69C9D0;
        --nav-secondary: #EE1D52;
        --nav-dark: #010101;
    }
    .nav-hero {
        background: linear-gradient(135deg, #010101 0%, #1a0a0f 50%, #0a1a1f 100%);
        padding: 4rem 0 3rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .nav-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(105,201,208,0.15) 0%, transparent 70%);
    }
    .nav-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(105,201,208,0.1);
        border: 1px solid rgba(105,201,208,0.3);
        color: var(--nav-primary);
        padding: 0.4rem 1rem;
        border-radius: 100px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        letter-spacing: 0.5px;
    }
    .nav-hero h1 {
        font-size: 2.8rem;
        font-weight: 900;
        line-height: 1.2;
        margin-bottom: 1rem;
        background: linear-gradient(135deg, #fff 0%, var(--nav-primary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .nav-hero p {
        color: #9ca3af;
        font-size: 1.05rem;
        max-width: 550px;
        margin: 0 auto 2rem;
        line-height: 1.7;
    }
    .tiktok-logo {
        width: 48px;
        height: 48px;
        background: #010101;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin-bottom: 1rem;
        border: 1px solid rgba(105,201,208,0.3);
    }

    /* Services Grid */
    .services-section { padding: 3rem 0 5rem; }
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }
    .service-card {
        background: #0d1117;
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 20px;
        padding: 1.75rem;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        text-decoration: none;
        display: block;
        color: inherit;
    }
    .service-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(105,201,208,0.05) 0%, transparent 60%);
        opacity: 0;
        transition: opacity 0.3s;
    }
    .service-card:hover { transform: translateY(-5px); border-color: rgba(105,201,208,0.3); color: inherit; }
    .service-card:hover::before { opacity: 1; }
    .service-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        margin-bottom: 1.25rem;
    }
    .service-name {
        font-size: 1.05rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #f9fafb;
    }
    .service-desc {
        font-size: 0.85rem;
        color: #6b7280;
        line-height: 1.6;
        margin-bottom: 1.25rem;
    }
    .service-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 1rem;
        border-top: 1px solid rgba(255,255,255,0.06);
    }
    .service-price {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--nav-primary);
    }
    .service-deadline {
        font-size: 0.75rem;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    .btn-register {
        background: linear-gradient(135deg, var(--nav-primary), #4fb3bc);
        color: #000;
        border: none;
        padding: 0.4rem 1rem;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .btn-register:hover { transform: scale(1.05); color: #000; }

    /* Steps */
    .steps-section {
        background: #080c12;
        padding: 3rem 0;
        border-top: 1px solid rgba(255,255,255,0.05);
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    .steps-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2rem;
        margin-top: 2rem;
    }
    .step-item { text-align: center; }
    .step-num {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--nav-primary), #4fb3bc);
        color: #000;
        font-weight: 900;
        font-size: 1.1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }
    .step-title { font-size: 0.9rem; font-weight: 700; margin-bottom: 0.4rem; }
    .step-desc { font-size: 0.8rem; color: #6b7280; line-height: 1.5; }

    .section-title { font-size: 1.5rem; font-weight: 800; text-align: center; }
    .section-sub { color: #6b7280; text-align: center; font-size: 0.9rem; margin-top: 0.4rem; }

    @media (max-width: 768px) {
        .nav-hero h1 { font-size: 1.8rem; }
        .steps-grid { grid-template-columns: repeat(2, 1fr); }
        .services-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<!-- Hero -->
<div class="nav-hero">
    <div class="container" style="position:relative;z-index:1">
        <div class="tiktok-logo">🎵</div>
        <div class="nav-hero-badge">
            <i class="bi bi-shield-fill-check"></i> Dịch Vụ Hỗ Trợ MXH Chuyên Nghiệp
        </div>
        <h1>Kháng Cáo TikTok<br>Nhanh & Hiệu Quả</h1>
        <p>Chúng tôi giúp bạn soạn đơn kháng cáo chuẩn TikTok Trust & Safety — tối ưu tỷ lệ mở khóa tài khoản, gỡ video, và phục hồi quyền lợi.</p>
        <a href="{{ route('nav.my-orders') }}" class="btn-register" style="padding: 0.6rem 1.5rem; font-size: 0.9rem;">
            <i class="bi bi-clock-history"></i> Đơn của tôi
        </a>
    </div>
</div>

<!-- Steps -->
<div class="steps-section">
    <div class="container">
        <div class="section-title" style="color:#f9fafb">Quy Trình 4 Bước</div>
        <div class="section-sub">Đơn giản, minh bạch, nhanh chóng</div>
        <div class="steps-grid">
            <div class="step-item">
                <div class="step-num">1</div>
                <div class="step-title">Chọn Dịch Vụ</div>
                <div class="step-desc">Chọn loại vấn đề TikTok bạn đang gặp phải</div>
            </div>
            <div class="step-item">
                <div class="step-num">2</div>
                <div class="step-title">Điền Thông Tin</div>
                <div class="step-desc">Nhập thông tin tài khoản & upload ảnh xác minh</div>
            </div>
            <div class="step-item">
                <div class="step-num">3</div>
                <div class="step-title">Thanh Toán</div>
                <div class="step-desc">Thanh toán qua QR ngân hàng hoặc PT trong tài khoản</div>
            </div>
            <div class="step-item">
                <div class="step-num">4</div>
                <div class="step-title">Nhận Kết Quả</div>
                <div class="step-desc">Admin xử lý & gửi kháng cáo, cập nhật kết quả cho bạn</div>
            </div>
        </div>
    </div>
</div>

<!-- Services -->
<div class="services-section">
    <div class="container">
        <div class="section-title" style="color:#f9fafb">Chọn Dịch Vụ</div>
        <div class="section-sub">{{ $services->count() }} dịch vụ hỗ trợ TikTok đang có</div>

        @if($services->isEmpty())
            <div style="text-align:center;padding:3rem;color:#6b7280">
                <i class="bi bi-inbox" style="font-size:3rem;display:block;margin-bottom:1rem"></i>
                Chưa có dịch vụ nào. Vui lòng quay lại sau!
            </div>
        @else
        <div class="services-grid">
            @foreach($services as $service)
            <a href="{{ route('nav.show', $service->slug) }}" class="service-card">
                <div class="service-icon" style="background: {{ $service->color }}22; color: {{ $service->color }}">
                    <i class="bi {{ $service->icon }}"></i>
                </div>
                <div class="service-name">{{ $service->name }}</div>
                <div class="service-desc">{{ $service->description }}</div>
                <div class="service-footer">
                    <div>
                        <div class="service-price">{{ number_format((float)$service->price, 0, ',', '.') }} PT</div>
                        <div class="service-deadline">
                            <i class="bi bi-clock"></i> Kháng trong {{ $service->appeal_deadline_days }} ngày
                        </div>
                    </div>
                    <span class="btn-register">Đăng ký →</span>
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
