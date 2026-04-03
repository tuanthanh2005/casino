{{-- App Download / PWA Install Banner --}}
@php
    $appEnabled = \App\Models\GameSetting::get('app_download_enabled', '1');
    if ($appEnabled != '1')
        return;

    $bannerTitle = \App\Models\GameSetting::get('app_banner_title', 'Cài App AquaHub');
    $bannerSubtitle = \App\Models\GameSetting::get('app_banner_subtitle', 'Cài trực tiếp lên điện thoại — không cần App Store');
    $androidUrl = \App\Models\GameSetting::get('app_android_url', '');
    $iosUrl = \App\Models\GameSetting::get('app_ios_url', '');
    $androidIcon = \App\Models\GameSetting::get('app_android_icon', '');
    $iosIcon = \App\Models\GameSetting::get('app_ios_icon', '');
    $androidLabel = \App\Models\GameSetting::get('app_android_label', 'Cài cho Android');
    $iosLabel = \App\Models\GameSetting::get('app_ios_label', 'Cài cho iOS');
@endphp

{{-- ═══════════════ BANNER ═══════════════ --}}
<div class="app-download-banner" id="app-download-banner">
    <div class="app-banner-inner">

        {{-- Icon + Text --}}
        <div class="app-banner-text">
            <div class="app-banner-icon-wrap">📱</div>
            <div>
                <div class="app-banner-subtitle">{{ $bannerSubtitle }}</div>
                <div class="app-banner-title">{{ $bannerTitle }}</div>
            </div>
        </div>

        {{-- Nút tải --}}
        <div class="app-banner-buttons">

            {{-- NÚT ANDROID: PWA install hoặc link APK --}}
            @if($androidUrl && $androidUrl !== '#')
                {{-- Có link APK/Play Store → mở link --}}
                <a href="{{ $androidUrl }}" class="app-dl-btn app-dl-btn--android" target="_blank" rel="noopener">
                    @if($androidIcon)
                        <img src="{{ asset($androidIcon) }}" class="app-dl-btn-icon" alt="">
                    @else
                        <span class="app-dl-btn-emoji">🤖</span>
                    @endif
                    <div class="app-dl-btn-text">
                        <span class="app-dl-btn-store">Google Play</span>
                        <span class="app-dl-btn-label">{{ $androidLabel }}</span>
                    </div>
                </a>
            @else
                {{-- Không có link → dùng PWA install --}}
                <button type="button" class="app-dl-btn app-dl-btn--android" id="btn-pwa-android"
                    onclick="triggerPwaInstall('android')">
                    @if($androidIcon)
                        <img src="{{ asset($androidIcon) }}" class="app-dl-btn-icon" alt="">
                    @else
                        <span class="app-dl-btn-emoji">🤖</span>
                    @endif
                    <div class="app-dl-btn-text">
                        <span class="app-dl-btn-store" id="android-store-label">Cài vào máy</span>
                        <span class="app-dl-btn-label">{{ $androidLabel }}</span>
                    </div>
                </button>
            @endif

            {{-- NÚT IOS: hướng dẫn "Add to Home Screen" --}}
            @if($iosUrl && $iosUrl !== '#')
                <a href="{{ $iosUrl }}" class="app-dl-btn app-dl-btn--ios" target="_blank" rel="noopener">
                    @if($iosIcon)
                        <img src="{{ asset($iosIcon) }}" class="app-dl-btn-icon" alt="">
                    @else
                        <span class="app-dl-btn-emoji">🍎</span>
                    @endif
                    <div class="app-dl-btn-text">
                        <span class="app-dl-btn-store">App Store</span>
                        <span class="app-dl-btn-label">{{ $iosLabel }}</span>
                    </div>
                </a>
            @else
                <button type="button" class="app-dl-btn app-dl-btn--ios" onclick="showIosGuide()">
                    @if($iosIcon)
                        <img src="{{ asset($iosIcon) }}" class="app-dl-btn-icon" alt="">
                    @else
                        <span class="app-dl-btn-emoji">🍎</span>
                    @endif
                    <div class="app-dl-btn-text">
                        <span class="app-dl-btn-store">iPhone / iPad</span>
                        <span class="app-dl-btn-label">{{ $iosLabel }}</span>
                    </div>
                </button>
            @endif

        </div>

        {{-- Thu gọn --}}
        <button class="app-banner-close" onclick="collapseAppBanner()" aria-label="Thu gọn">
            <i class="bi bi-chevron-up"></i>
        </button>
    </div>
</div>

{{-- ═══════════════ MINI BAR ═══════════════ --}}
{{-- Hiện ra khi banner chính bị thu gọn --}}
<div class="app-mini-bar" id="app-mini-bar" onclick="expandAppBanner()">
    <div class="app-mini-content">
        <i class="bi bi-chevron-down ms-auto" style="font-size:0.8rem"></i>
    </div>
</div>

{{-- ═══════════════ IOS GUIDE MODAL ═══════════════ --}}
<div class="ios-guide-overlay" id="ios-guide-overlay" onclick="closeIosGuide()">
    <div class="ios-guide-card" onclick="event.stopPropagation()">
        <button class="ios-guide-close" onclick="closeIosGuide()">
            <i class="bi bi-x-lg"></i>
        </button>
        <div class="ios-guide-header">
            <span style="font-size:2.5rem">🍎</span>
            <div>
                <div class="ios-guide-title">Cài App trên iPhone / iPad</div>
                <div class="ios-guide-sub">Không cần App Store, hoàn toàn miễn phí</div>
            </div>
        </div>
        <div class="ios-guide-steps">
            <div class="ios-step">
                <div class="ios-step-num">1</div>
                <div class="ios-step-text">
                    Mở trang này bằng <strong>Safari</strong>
                    <span class="ios-step-note">Không dùng Chrome hay Firefox</span>
                </div>
            </div>
            <div class="ios-step">
                <div class="ios-step-num">2</div>
                <div class="ios-step-text">
                    Bấm nút <strong>Chia sẻ</strong> <span style="font-size:1.1rem">⬆️</span> ở thanh dưới Safari
                    <span class="ios-step-note">Biểu tượng hình vuông có mũi tên đi lên</span>
                </div>
            </div>
            <div class="ios-step">
                <div class="ios-step-num">3</div>
                <div class="ios-step-text">
                    Chọn <strong>"Thêm vào Màn hình chính"</strong>
                    <span class="ios-step-note">Add to Home Screen</span>
                </div>
            </div>
            <div class="ios-step">
                <div class="ios-step-num">4</div>
                <div class="ios-step-text">
                    Bấm <strong>Thêm</strong> để xác nhận
                    <span class="ios-step-note">App sẽ xuất hiện trên màn hình chính như app thật</span>
                </div>
            </div>
        </div>
        <button class="ios-guide-done" onclick="closeIosGuide()">
            <i class="bi bi-check-circle"></i> Đã hiểu, đóng lại
        </button>
    </div>
</div>

<style>
    /* ─── BANNER BASE ──────────────────────────────────── */
    .app-download-banner {
        background: linear-gradient(135deg, rgba(6, 182, 212, 0.08), rgba(16, 185, 129, 0.05), rgba(99, 102, 241, 0.06));
        border: 1px solid rgba(6, 182, 212, 0.2);
        border-radius: 20px;
        margin-bottom: 1.5rem;
        overflow: hidden;
        position: relative;
        animation: bannerFadeIn 0.5s ease;
        transition: all 0.4s ease;
    }

    @keyframes bannerFadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .app-banner-inner {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem 3.5rem 1.25rem 1.5rem;
        flex-wrap: nowrap;
    }

    .app-banner-text {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        flex: 1;
        min-width: 0;
    }

    .app-banner-icon-wrap {
        width: 46px;
        height: 46px;
        background: linear-gradient(135deg, rgba(6, 182, 212, 0.2), rgba(16, 185, 129, 0.2));
        border: 1px solid rgba(6, 182, 212, 0.3);
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
        animation: iconPulse 3s ease infinite;
    }

    @keyframes iconPulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.08);
        }
    }

    .app-banner-subtitle {
        font-size: 0.76rem;
        color: #9ca3af;
        margin-bottom: 0.15rem;
    }

    .app-banner-title {
        font-size: 1rem;
        font-weight: 800;
        background: linear-gradient(135deg, #22d3ee, #34d399);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* ─── DOWNLOAD BUTTONS ─────────────────────────────── */
    .app-banner-buttons {
        display: flex;
        gap: 0.65rem;
        flex-shrink: 0;
    }

    .app-dl-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.6rem 1rem;
        border-radius: 12px;
        text-decoration: none;
        font-family: 'Inter', sans-serif;
        border: none;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        min-width: 148px;
        position: relative;
        overflow: hidden;
    }

    .app-dl-btn:hover {
        transform: translateY(-3px);
    }

    .app-dl-btn--android {
        background: linear-gradient(135deg, #1a9450, #3ddc84);
        color: white;
    }

    .app-dl-btn--ios {
        background: linear-gradient(135deg, #1c1c1e, #3a3a3c);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .app-dl-btn-icon {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        object-fit: contain;
        flex-shrink: 0;
    }

    .app-dl-btn-emoji {
        font-size: 1.4rem;
        line-height: 1;
        flex-shrink: 0;
    }

    .app-dl-btn-text {
        display: flex;
        flex-direction: column;
        line-height: 1.25;
        text-align: left;
    }

    .app-dl-btn-store {
        font-size: 0.62rem;
        opacity: 0.75;
    }

    .app-dl-btn-label {
        font-size: 0.8rem;
        font-weight: 700;
    }

    /* ─── CLOSE BUTTON ─────────────────────────────────── */
    .app-banner-close {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.05);
        color: #9ca3af;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        position: absolute;
        top: 0.7rem;
        right: 0.8rem;
    }

    /* ─── MINI BAR ────────────────────────────────────── */
    .app-mini-bar {
        display: none;
        background: linear-gradient(135deg, rgba(6, 182, 212, 0.15), rgba(16, 185, 129, 0.1));
        border-bottom: 1px solid rgba(6, 182, 212, 0.3);
        padding: 0.6rem 1.25rem;
        cursor: pointer;
        margin: -1.5rem -1rem 1.5rem -1rem;
        /* Bám lên trên main-content */
        transition: all 0.3s ease;
    }

    @media (max-width: 768px) {
        .app-mini-bar {
            margin: -1.25rem -0.75rem 1rem -0.75rem;
            /* Điều chỉnh lại cho mobile view */
        }
    }

    .app-mini-bar.visible {
        display: block;
        animation: miniBarSlide 0.4s ease;
    }

    @keyframes miniBarSlide {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .app-mini-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: #f9fafb;
    }

    .mini-icon {
        font-size: 1.1rem;
    }

    .mini-text {
        font-size: 0.78rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.9);
    }

    /* ─── iOS GUIDE ────────────────────────────────────── */
    .ios-guide-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(8px);
        z-index: 10000;
        align-items: flex-end;
        justify-content: center;
        padding: 1rem;
    }

    .ios-guide-overlay.open {
        display: flex;
    }

    .ios-guide-card {
        background: #111827;
        border: 1px solid rgba(6, 182, 212, 0.3);
        border-radius: 24px 24px 20px 20px;
        padding: 1.75rem 1.5rem;
        width: 100%;
        max-width: 480px;
        animation: guideSlide 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes guideSlide {
        from {
            transform: translateY(100%);
        }

        to {
            transform: translateY(0);
        }
    }

    /* ─── MOBILE RESPONSIVE ──────────────────────────── */
    @media (max-width: 768px) {
        .app-banner-inner {
            flex-direction: column;
            align-items: stretch;
            padding: 1rem 1rem;
            padding-right: 2.75rem;
            gap: 0.8rem;
        }

        .app-banner-buttons {
            display: grid !important;
            grid-template-columns: 1fr 1fr;
            width: 100%;
            gap: 0.5rem;
        }

        .app-dl-btn {
            min-width: 0;
            padding: 0.55rem 0.6rem;
        }
    }
</style>

<script>
    (function () {
        window.collapseAppBanner = function () {
            const banner = document.getElementById('app-download-banner');
            const mini = document.getElementById('app-mini-bar');
            if (!banner) return;

            banner.style.opacity = '0';
            banner.style.transform = 'translateY(-10px)';

            setTimeout(() => {
                banner.style.display = 'none';
                if (mini) mini.classList.add('visible');
            }, 300);

            try { sessionStorage.setItem('app_banner_state', 'collapsed'); } catch (e) { }
        };

        window.expandAppBanner = function () {
            const banner = document.getElementById('app-download-banner');
            const mini = document.getElementById('app-mini-bar');
            if (!banner) return;

            if (mini) mini.classList.remove('visible');
            banner.style.display = 'block';
            setTimeout(() => {
                banner.style.opacity = '1';
                banner.style.transform = 'translateY(0)';
            }, 10);
            try { sessionStorage.setItem('app_banner_state', 'open'); } catch (e) { }
        };

        window.triggerPwaInstall = function () {
            if (window.installPWA) window.installPWA();
        };

        window.showIosGuide = function () {
            document.getElementById('ios-guide-overlay')?.classList.add('open');
        };

        window.closeIosGuide = function () {
            document.getElementById('ios-guide-overlay')?.classList.remove('open');
        };

        document.addEventListener('DOMContentLoaded', function () {
            if (sessionStorage.getItem('app_banner_state') === 'collapsed') {
                const banner = document.getElementById('app-download-banner');
                const mini = document.getElementById('app-mini-bar');
                if (banner) banner.style.display = 'none';
                if (mini) mini.classList.add('visible');
            }
        });
    })();
</script>