{{-- App Download / PWA Install Banner --}}
@php
    $appEnabled = \App\Models\GameSetting::get('app_download_enabled', '1');
    if ($appEnabled != '1') return;

    $bannerTitle    = \App\Models\GameSetting::get('app_banner_title', 'Cài App AquaHub');
    $bannerSubtitle = \App\Models\GameSetting::get('app_banner_subtitle', 'Cài trực tiếp lên điện thoại — không cần App Store');
    $androidUrl     = \App\Models\GameSetting::get('app_android_url', '');
    $iosUrl         = \App\Models\GameSetting::get('app_ios_url', '');
    $androidIcon    = \App\Models\GameSetting::get('app_android_icon', '');
    $iosIcon        = \App\Models\GameSetting::get('app_ios_icon', '');
    $androidLabel   = \App\Models\GameSetting::get('app_android_label', 'Cài cho Android');
    $iosLabel       = \App\Models\GameSetting::get('app_ios_label', 'Cài cho iOS');
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
                <a href="{{ $androidUrl }}" class="app-dl-btn app-dl-btn--android"
                   target="_blank" rel="noopener">
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
                <a href="{{ $iosUrl }}" class="app-dl-btn app-dl-btn--ios"
                   target="_blank" rel="noopener">
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
                <button type="button" class="app-dl-btn app-dl-btn--ios"
                        onclick="showIosGuide()">
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

{{-- ═══════════════ FLOATING PILL ═══════════════ --}}
<div class="app-floating-pill" id="app-floating-pill" onclick="expandAppBanner()">
    <div class="app-pill-icon">📱</div>
    <div class="app-pill-text">
        <span class="app-pill-label">Cài App</span>
        <span class="app-pill-stores">Miễn phí</span>
    </div>
    <div class="app-pill-arrow"><i class="bi bi-chevron-down" style="font-size:0.7rem"></i></div>
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
    background: linear-gradient(135deg, rgba(6,182,212,0.08), rgba(16,185,129,0.05), rgba(99,102,241,0.06));
    border: 1px solid rgba(6,182,212,0.2);
    border-radius: 20px;
    margin-bottom: 1.5rem;
    overflow: hidden;
    position: relative;
    animation: bannerFadeIn 0.5s ease;
}

@keyframes bannerFadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}

.app-banner-inner {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem 3.5rem 1.25rem 1.5rem; /* padding-right chừa chỗ nút close */
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
    width: 46px; height: 46px;
    background: linear-gradient(135deg, rgba(6,182,212,0.2), rgba(16,185,129,0.2));
    border: 1px solid rgba(6,182,212,0.3);
    border-radius: 13px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
    animation: iconPulse 3s ease infinite;
}

@keyframes iconPulse {
    0%, 100% { transform: scale(1); }
    50%       { transform: scale(1.08); }
}

.app-banner-subtitle { font-size: 0.76rem; color: #9ca3af; margin-bottom: 0.15rem; white-space: nowrap; }
.app-banner-title {
    font-size: 1rem; font-weight: 800;
    background: linear-gradient(135deg, #22d3ee, #34d399);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    white-space: nowrap;
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

.app-dl-btn:hover { transform: translateY(-3px); }
.app-dl-btn:active { transform: scale(0.97); }

.app-dl-btn--android {
    background: linear-gradient(135deg, #1a9450, #3ddc84);
    color: white;
    box-shadow: 0 4px 18px rgba(61,220,132,0.35);
}
.app-dl-btn--android:hover { box-shadow: 0 8px 28px rgba(61,220,132,0.5); color: white; }

.app-dl-btn--ios {
    background: linear-gradient(135deg, #1c1c1e, #3a3a3c);
    color: white;
    border: 1px solid rgba(255,255,255,0.1);
    box-shadow: 0 4px 18px rgba(0,0,0,0.3);
}
.app-dl-btn--ios:hover { box-shadow: 0 8px 28px rgba(0,0,0,0.5); color: white; }

.app-dl-btn-icon { width: 28px; height: 28px; border-radius: 6px; object-fit: contain; flex-shrink: 0; }
.app-dl-btn-emoji { font-size: 1.4rem; line-height: 1; flex-shrink: 0; }
.app-dl-btn-text { display: flex; flex-direction: column; line-height: 1.25; text-align: left; }
.app-dl-btn-store { font-size: 0.62rem; opacity: 0.75; }
.app-dl-btn-label { font-size: 0.8rem; font-weight: 700; }

/* ─── CLOSE BUTTON ─────────────────────────────────── */
.app-banner-close {
    width: 28px; height: 28px;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.05);
    color: #9ca3af;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.7rem;
    transition: all 0.2s;
    position: absolute; top: 0.7rem; right: 0.8rem;
}
.app-banner-close:hover { background: rgba(6,182,212,0.15); border-color: rgba(6,182,212,0.4); color: #22d3ee; }

/* ─── FLOATING PILL ────────────────────────────────── */
.app-floating-pill {
    display: none;
    position: fixed;
    bottom: 90px; right: 16px;
    z-index: 2100;
    align-items: center;
    gap: 0.45rem;
    background: linear-gradient(135deg, rgba(6,182,212,0.95), rgba(16,185,129,0.9));
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 50px;
    padding: 0.5rem 0.85rem 0.5rem 0.6rem;
    cursor: pointer;
    box-shadow: 0 6px 28px rgba(6,182,212,0.45), 0 2px 8px rgba(0,0,0,0.3);
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    user-select: none;
    white-space: nowrap;
}
.app-floating-pill.visible { display: flex; animation: pillPop 0.4s cubic-bezier(0.34,1.56,0.64,1); }
.app-floating-pill:hover   { transform: translateY(-4px) scale(1.04); box-shadow: 0 12px 36px rgba(6,182,212,0.6); }

@keyframes pillPop {
    from { transform: scale(0.5) translateY(20px); opacity: 0; }
    to   { transform: scale(1)   translateY(0);    opacity: 1; }
}

.app-pill-icon  { font-size: 1.2rem; }
.app-pill-text  { display: flex; flex-direction: column; line-height: 1.15; }
.app-pill-label { font-size: 0.7rem; font-weight: 800; color: white; }
.app-pill-stores{ font-size: 0.58rem; color: rgba(255,255,255,0.75); }
.app-pill-arrow { width: 18px; height: 18px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; margin-left: 2px; }

@media (min-width: 769px) {
    .app-floating-pill { bottom: 28px; right: 28px; }
}

/* ─── iOS GUIDE MODAL ──────────────────────────────── */
.ios-guide-overlay {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.75);
    backdrop-filter: blur(8px);
    z-index: 9999;
    align-items: flex-end;
    justify-content: center;
    padding: 1rem;
}
.ios-guide-overlay.open { display: flex; }

.ios-guide-card {
    background: linear-gradient(160deg, #1f2937, #111827);
    border: 1px solid rgba(6,182,212,0.25);
    border-radius: 24px 24px 20px 20px;
    padding: 1.75rem 1.5rem 1.5rem;
    width: 100%;
    max-width: 480px;
    position: relative;
    animation: guideSlideUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes guideSlideUp {
    from { transform: translateY(100px); opacity: 0; }
    to   { transform: translateY(0);     opacity: 1; }
}

.ios-guide-close {
    position: absolute; top: 1rem; right: 1rem;
    width: 30px; height: 30px;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.05);
    color: #9ca3af;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.75rem;
    transition: all 0.2s;
}
.ios-guide-close:hover { background: rgba(239,68,68,0.2); color: #ef4444; }

.ios-guide-header {
    display: flex; align-items: center; gap: 1rem;
    margin-bottom: 1.5rem;
    padding-right: 2rem;
}
.ios-guide-title { font-size: 1.1rem; font-weight: 800; color: #f9fafb; margin-bottom: 0.2rem; }
.ios-guide-sub   { font-size: 0.78rem; color: #9ca3af; }

.ios-guide-steps { display: flex; flex-direction: column; gap: 0.85rem; margin-bottom: 1.5rem; }

.ios-step {
    display: flex;
    align-items: flex-start;
    gap: 0.85rem;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 14px;
    padding: 0.85rem 1rem;
}

.ios-step-num {
    width: 28px; height: 28px; min-width: 28px;
    background: linear-gradient(135deg, #06b6d4, #10b981);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.8rem; font-weight: 800; color: white;
}

.ios-step-text {
    font-size: 0.87rem;
    color: #e5e7eb;
    line-height: 1.4;
}
.ios-step-note {
    display: block;
    font-size: 0.72rem;
    color: #9ca3af;
    margin-top: 0.2rem;
}

.ios-guide-done {
    width: 100%;
    padding: 0.85rem;
    background: linear-gradient(135deg, #06b6d4, #10b981);
    color: white;
    border: none;
    border-radius: 14px;
    font-size: 0.95rem;
    font-weight: 700;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 0.5rem;
    transition: all 0.2s;
}
.ios-guide-done:hover { opacity: 0.9; transform: translateY(-1px); }

/* ─── MOBILE ───────────────────────────────────────── */
@media (max-width: 768px) {
    .app-download-banner { border-radius: 14px; margin-bottom: 1rem; }

    .app-banner-inner {
        flex-direction: column;
        align-items: stretch;
        gap: 0.7rem;
        padding: 0.85rem 0.9rem;
        padding-right: 2.75rem;
    }

    .app-banner-text  { flex-direction: row; align-items: center; gap: 0.6rem; }
    .app-banner-icon-wrap { width: 36px; height: 36px; font-size: 1.1rem; }
    .app-banner-title   { font-size: 0.85rem; }
    .app-banner-subtitle{ font-size: 0.7rem;  }

    .app-banner-buttons {
        display: grid !important;
        grid-template-columns: 1fr 1fr;
        gap: 0.45rem;
        width: 100%;
    }

    .app-dl-btn {
        min-width: 0;
        padding: 0.5rem 0.6rem;
        border-radius: 10px;
        justify-content: center;
        gap: 0.4rem;
    }

    .app-dl-btn-emoji { font-size: 1.15rem; }
    .app-dl-btn-icon  { width: 22px; height: 22px; }
    .app-dl-btn-store { font-size: 0.58rem; }
    .app-dl-btn-label { font-size: 0.7rem; }

    .app-banner-close { top: 0.55rem; right: 0.6rem; width: 24px; height: 24px; font-size: 0.62rem; }
}
</style>

<script>
(function() {
    // ── Thu gọn ──
    window.collapseAppBanner = function() {
        const banner = document.getElementById('app-download-banner');
        const pill   = document.getElementById('app-floating-pill');
        if (!banner) return;

        banner.style.transition  = 'max-height 0.4s ease, opacity 0.3s ease, margin 0.4s ease';
        banner.style.maxHeight   = banner.scrollHeight + 'px';
        banner.style.overflow    = 'hidden';
        requestAnimationFrame(() => {
            banner.style.maxHeight    = '0';
            banner.style.opacity      = '0';
            banner.style.marginBottom = '0';
        });
        setTimeout(() => {
            banner.style.display = 'none';
            if (pill) pill.classList.add('visible');
        }, 420);
        try { sessionStorage.setItem('app_banner_state', 'collapsed'); } catch(e) {}
    };

    // ── Mở lại ──
    window.expandAppBanner = function() {
        const banner = document.getElementById('app-download-banner');
        const pill   = document.getElementById('app-floating-pill');
        if (!banner) return;

        if (pill) pill.classList.remove('visible');
        banner.style.display      = 'block';
        banner.style.maxHeight    = '0';
        banner.style.opacity      = '0';
        banner.style.overflow     = 'hidden';
        banner.style.marginBottom = '0';
        banner.style.transition   = 'max-height 0.4s ease, opacity 0.3s ease, margin 0.4s ease';
        requestAnimationFrame(() => {
            banner.style.maxHeight    = '400px';
            banner.style.opacity      = '1';
            banner.style.marginBottom = '1.5rem';
        });
        setTimeout(() => { banner.style.maxHeight = 'none'; banner.style.overflow = ''; }, 420);
        try { sessionStorage.setItem('app_banner_state', 'open'); } catch(e) {}
    };

    // ── Trigger PWA install (Android/Chrome) ──
    window.triggerPwaInstall = function(platform) {
        if (window.isPWAInstallable && window.isPWAInstallable()) {
            window.installPWA();
        } else {
            // Nếu chưa ready hoặc iOS → hiện hướng dẫn
            if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
                showIosGuide();
            } else {
                // Android nhưng chưa có prompt → nhắc dùng Chrome
                showGenericGuide();
            }
        }
    };

    // ── iOS: hiện modal hướng dẫn ──
    window.showIosGuide = function() {
        const overlay = document.getElementById('ios-guide-overlay');
        if (overlay) overlay.classList.add('open');
    };

    window.closeIosGuide = function() {
        const overlay = document.getElementById('ios-guide-overlay');
        if (overlay) overlay.classList.remove('open');
    };

    // ── Generic guide (desktop hoặc trình duyệt không hỗ trợ) ──
    window.showGenericGuide = function() {
        const existing = document.getElementById('app-generic-popup');
        if (existing) existing.remove();

        const popup = document.createElement('div');
        popup.id = 'app-generic-popup';
        popup.style.cssText = `
            position: fixed; bottom: 110px; left: 50%;
            transform: translateX(-50%) translateY(20px);
            background: linear-gradient(135deg, #1f2937, #111827);
            border: 1px solid rgba(6,182,212,0.4);
            border-radius: 16px; padding: 1rem 1.25rem;
            box-shadow: 0 12px 40px rgba(0,0,0,0.5);
            z-index: 9999; display: flex; align-items: center; gap: 0.75rem;
            font-family: 'Inter', sans-serif; min-width: 240px; max-width: 300px;
            opacity: 0; transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        `;
        popup.innerHTML = `
            <span style="font-size:2rem">💡</span>
            <div>
                <div style="font-weight:700;font-size:0.88rem;color:#f9fafb;margin-bottom:0.2rem">
                    Mở bằng Chrome để cài app
                </div>
                <div style="font-size:0.75rem;color:#9ca3af;line-height:1.4">
                    Trên Android: dùng Chrome → menu ⋮ → "Thêm vào màn hình chính"
                </div>
            </div>
        `;
        document.body.appendChild(popup);
        requestAnimationFrame(() => { popup.style.opacity = '1'; popup.style.transform = 'translateX(-50%) translateY(0)'; });
        setTimeout(() => {
            popup.style.opacity = '0';
            setTimeout(() => popup.remove(), 300);
        }, 4000);
    };

    // ── Khi PWA installable: cập nhật label nút Android ──
    document.addEventListener('pwa-installable', function() {
        const label = document.getElementById('android-store-label');
        if (label) label.textContent = '1 click cài ngay';
    });

    // ── Khôi phục trạng thái ──
    document.addEventListener('DOMContentLoaded', function() {
        try {
            if (sessionStorage.getItem('app_banner_state') === 'collapsed') {
                const banner = document.getElementById('app-download-banner');
                const pill   = document.getElementById('app-floating-pill');
                if (banner) banner.style.display = 'none';
                if (pill)   pill.classList.add('visible');
            }
        } catch(e) {}
    });
})();
</script>
