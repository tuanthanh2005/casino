{{-- App Download Banner Component --}}
{{-- Sử dụng GameSetting để lấy data, hiển thị nút tải Android & iOS --}}
@php
    $appEnabled = \App\Models\GameSetting::get('app_download_enabled', '1');
    if ($appEnabled != '1') return;

    $bannerTitle    = \App\Models\GameSetting::get('app_banner_title', 'Tải App AquaHub');
    $bannerSubtitle = \App\Models\GameSetting::get('app_banner_subtitle', 'Trải nghiệm tốt hơn với ứng dụng di động');
    $androidUrl     = \App\Models\GameSetting::get('app_android_url', '#');
    $iosUrl         = \App\Models\GameSetting::get('app_ios_url', '#');
    $androidIcon    = \App\Models\GameSetting::get('app_android_icon', '');
    $iosIcon        = \App\Models\GameSetting::get('app_ios_icon', '');
    $androidLabel   = \App\Models\GameSetting::get('app_android_label', 'Tải cho Android');
    $iosLabel       = \App\Models\GameSetting::get('app_ios_label', 'Tải cho iOS');
@endphp

{{-- ═══════════════════════════════════════ --}}
{{-- BANNER (full width, collapsible)       --}}
{{-- ═══════════════════════════════════════ --}}
<div class="app-download-banner" id="app-download-banner">
    <div class="app-banner-inner">
        <!-- Left: Icon + Text -->
        <div class="app-banner-text">
            <div class="app-banner-icon-wrap">
                <span class="app-banner-main-icon">📱</span>
            </div>
            <div>
                <div class="app-banner-subtitle">{{ $bannerSubtitle }}</div>
                <div class="app-banner-title">{{ $bannerTitle }}</div>
            </div>
        </div>

        <!-- Right: Buttons -->
        <div class="app-banner-buttons">
            @if($androidUrl && $androidUrl !== '#')
                <a href="{{ $androidUrl }}"
                   class="app-dl-btn app-dl-btn--android"
                   target="_blank" rel="noopener">
            @else
                <a href="javascript:void(0)"
                   class="app-dl-btn app-dl-btn--android"
                   onclick="appDlNotReady('Android')"
                   data-coming-soon="1">
            @endif
                @if($androidIcon)
                    <img src="{{ asset($androidIcon) }}" class="app-dl-btn-icon" alt="Android">
                @else
                    <span class="app-dl-btn-emoji">🤖</span>
                @endif
                <div class="app-dl-btn-text">
                    <span class="app-dl-btn-store">
                        @if(!$androidUrl || $androidUrl === '#')
                            Sắp ra mắt
                        @else
                            Google Play
                        @endif
                    </span>
                    <span class="app-dl-btn-label">{{ $androidLabel }}</span>
                </div>
            </a>

            @if($iosUrl && $iosUrl !== '#')
                <a href="{{ $iosUrl }}"
                   class="app-dl-btn app-dl-btn--ios"
                   target="_blank" rel="noopener">
            @else
                <a href="javascript:void(0)"
                   class="app-dl-btn app-dl-btn--ios"
                   onclick="appDlNotReady('iOS')"
                   data-coming-soon="1">
            @endif
                @if($iosIcon)
                    <img src="{{ asset($iosIcon) }}" class="app-dl-btn-icon" alt="iOS">
                @else
                    <span class="app-dl-btn-emoji">🍎</span>
                @endif
                <div class="app-dl-btn-text">
                    <span class="app-dl-btn-store">
                        @if(!$iosUrl || $iosUrl === '#')
                            Sắp ra mắt
                        @else
                            App Store
                        @endif
                    </span>
                    <span class="app-dl-btn-label">{{ $iosLabel }}</span>
                </div>
            </a>
        </div>

        <!-- Close button — thu nhỏ thành floating pill -->
        <button class="app-banner-close" onclick="collapseAppBanner()" aria-label="Thu gọn">
            <i class="bi bi-chevron-up"></i>
        </button>
    </div>
</div>

{{-- ═══════════════════════════════════════ --}}
{{-- FLOATING PILL — hiện sau khi thu gọn  --}}
{{-- ═══════════════════════════════════════ --}}
<div class="app-floating-pill" id="app-floating-pill" onclick="expandAppBanner()" title="Tải App {{ $bannerTitle }}">
    <div class="app-pill-icon">📱</div>
    <div class="app-pill-text">
        <span class="app-pill-label">Tải App</span>
        <span class="app-pill-stores">Android &amp; iOS</span>
    </div>
    <div class="app-pill-arrow">
        <i class="bi bi-chevron-down" style="font-size: 0.7rem;"></i>
    </div>
</div>

<style>
/* ─── BANNER ──────────────────────────────────────── */
.app-download-banner {
    background: linear-gradient(135deg, rgba(6,182,212,0.08) 0%, rgba(16,185,129,0.06) 50%, rgba(99,102,241,0.06) 100%);
    border: 1px solid rgba(6,182,212,0.2);
    border-radius: 20px;
    margin-bottom: 1.5rem;
    overflow: hidden;
    position: relative;
    animation: bannerFadeIn 0.5s ease;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes bannerFadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Shimmer overlay */
.app-download-banner::before {
    content: '';
    position: absolute;
    top: 0; left: -100%;
    width: 60%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.03), transparent);
    animation: bannerShimmer 4s ease infinite;
}

@keyframes bannerShimmer {
    0%   { left: -100%; }
    50%  { left: 100%; }
    100% { left: 100%; }
}

.app-banner-inner {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem 1.5rem 1.25rem 1.5rem;
    flex-wrap: nowrap;
}

.app-banner-text {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex: 1;
    min-width: 200px;
}

.app-banner-icon-wrap {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, rgba(6,182,212,0.2), rgba(16,185,129,0.2));
    border: 1px solid rgba(6,182,212,0.3);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
    animation: iconPulse 3s ease infinite;
}

@keyframes iconPulse {
    0%, 100% { transform: scale(1); }
    50%       { transform: scale(1.08); }
}

.app-banner-subtitle {
    font-size: 0.78rem;
    color: #9ca3af;
    margin-bottom: 0.2rem;
}

.app-banner-title {
    font-size: 1.05rem;
    font-weight: 800;
    background: linear-gradient(135deg, #22d3ee, #34d399);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* ─── DOWNLOAD BUTTONS ───────────────────────────── */
.app-banner-buttons {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.app-dl-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.6rem 1.1rem;
    border-radius: 12px;
    text-decoration: none;
    font-family: 'Inter', sans-serif;
    transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
    position: relative;
    overflow: hidden;
    min-width: 155px;
}

.app-dl-btn::before {
    content: '';
    position: absolute;
    inset: 0;
    opacity: 0;
    transition: opacity 0.25s;
    background: rgba(255,255,255,0.1);
}

.app-dl-btn:hover::before { opacity: 1; }
.app-dl-btn:hover { transform: translateY(-3px); }
.app-dl-btn:active { transform: translateY(-1px); }

.app-dl-btn--android {
    background: linear-gradient(135deg, #1a9450, #3ddc84);
    color: white;
    box-shadow: 0 4px 20px rgba(61,220,132,0.3);
}
.app-dl-btn--android:hover { box-shadow: 0 8px 30px rgba(61,220,132,0.45); color: white; }

.app-dl-btn--ios {
    background: linear-gradient(135deg, #1c1c1e, #3a3a3c);
    color: white;
    border: 1px solid rgba(255,255,255,0.12);
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}
.app-dl-btn--ios:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.45); color: white; }

.app-dl-btn-icon {
    width: 30px; height: 30px;
    border-radius: 7px;
    object-fit: contain;
    flex-shrink: 0;
    background: rgba(255,255,255,0.1);
}

.app-dl-btn-emoji { font-size: 1.5rem; line-height: 1; flex-shrink: 0; }

.app-dl-btn-text { display: flex; flex-direction: column; line-height: 1.2; }
.app-dl-btn-store { font-size: 0.65rem; opacity: 0.75; letter-spacing: 0.3px; }
.app-dl-btn-label { font-size: 0.82rem; font-weight: 700; }

/* ─── CLOSE (COLLAPSE) BUTTON ───────────────────── */
.app-banner-close {
    width: 30px; height: 30px;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.05);
    color: #9ca3af;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    transition: all 0.2s;
    flex-shrink: 0;
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
}
.app-banner-close:hover {
    background: rgba(6,182,212,0.15);
    border-color: rgba(6,182,212,0.4);
    color: #22d3ee;
}

/* ─── FLOATING PILL ──────────────────────────────── */
.app-floating-pill {
    display: none; /* hidden by default, shown after collapse */
    position: fixed;
    bottom: 90px;   /* above mobile bottom nav */
    right: 18px;
    z-index: 1500;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, rgba(6,182,212,0.95), rgba(16,185,129,0.9));
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 50px;
    padding: 0.55rem 1rem 0.55rem 0.65rem;
    cursor: pointer;
    box-shadow: 0 6px 30px rgba(6,182,212,0.45), 0 2px 8px rgba(0,0,0,0.3);
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    user-select: none;
    animation: pillPop 0.45s cubic-bezier(0.34, 1.56, 0.64, 1);
    min-width: 0;
    max-width: 200px;
    overflow: hidden;
    white-space: nowrap;
}

.app-floating-pill.visible {
    display: flex;
}

@keyframes pillPop {
    from { transform: scale(0.5) translateY(20px); opacity: 0; }
    to   { transform: scale(1) translateY(0);      opacity: 1; }
}

.app-floating-pill:hover {
    transform: translateY(-4px) scale(1.04);
    box-shadow: 0 12px 40px rgba(6,182,212,0.6), 0 4px 12px rgba(0,0,0,0.3);
}

.app-floating-pill:active {
    transform: scale(0.97);
}

.app-pill-icon {
    font-size: 1.25rem;
    line-height: 1;
    flex-shrink: 0;
    animation: iconPulse 2s ease infinite;
}

.app-pill-text {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
    flex: 1;
}

.app-pill-label {
    font-size: 0.72rem;
    font-weight: 800;
    color: white;
    letter-spacing: 0.3px;
}

.app-pill-stores {
    font-size: 0.6rem;
    color: rgba(255,255,255,0.75);
}

.app-pill-arrow {
    width: 20px; height: 20px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
    margin-left: 0.1rem;
}

/* Desktop: pill ở góc dưới bên phải, không cần offset nav */
@media (min-width: 769px) {
    .app-floating-pill {
        bottom: 28px;
        right: 28px;
    }
}

/* ─── MOBILE RESPONSIVE ──────────────────────────── */
@media (max-width: 768px) {
    .app-download-banner {
        border-radius: 14px;
        margin-bottom: 1rem;
    }

    /* Ẩn shimmer trên mobile cho nhẹ */
    .app-download-banner::before { display: none; }

    /* Layout dọc trên mobile */
    .app-banner-inner {
        flex-direction: column;
        align-items: stretch;
        gap: 0.75rem;
        padding: 0.9rem 0.9rem 0.9rem 0.9rem;
        padding-right: 2.5rem; /* chừa chỗ cho nút close */
    }

    /* Hàng trên: icon + text ngang nhau */
    .app-banner-text {
        flex-direction: row;
        align-items: center;
        gap: 0.65rem;
        min-width: 0;
        flex: none;
    }

    .app-banner-icon-wrap {
        width: 38px;
        height: 38px;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .app-banner-title { font-size: 0.88rem; }
    .app-banner-subtitle { font-size: 0.72rem; }

    /* Hàng dưới: 2 nút tải cạnh nhau, chiếm full width */
    .app-banner-buttons {
        display: grid !important;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
        width: 100%;
    }

    .app-dl-btn {
        min-width: 0;
        padding: 0.5rem 0.65rem;
        border-radius: 10px;
        gap: 0.45rem;
        justify-content: center;
    }

    .app-dl-btn-emoji  { font-size: 1.2rem; }
    .app-dl-btn-icon   { width: 24px; height: 24px; }
    .app-dl-btn-store  { font-size: 0.6rem; }
    .app-dl-btn-label  { font-size: 0.72rem; }

    /* Close button góc trên phải, không che nội dung */
    .app-banner-close {
        top: 0.6rem;
        right: 0.6rem;
        width: 26px;
        height: 26px;
        font-size: 0.65rem;
    }
}
</style>

<script>
(function() {
    // ── Thu gọn banner → show floating pill ──
    window.collapseAppBanner = function() {
        const banner = document.getElementById('app-download-banner');
        const pill   = document.getElementById('app-floating-pill');
        if (!banner) return;

        banner.style.transition = 'max-height 0.4s ease, opacity 0.3s ease, margin 0.4s ease';
        banner.style.maxHeight  = banner.scrollHeight + 'px';
        banner.style.overflow   = 'hidden';
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

    // ── Bấm pill → show lại banner, ẩn pill ──
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
            banner.style.maxHeight    = '300px';
            banner.style.opacity      = '1';
            banner.style.marginBottom = '1.5rem';
        });

        setTimeout(() => {
            banner.style.maxHeight = 'none';
            banner.style.overflow  = '';
        }, 420);

        try { sessionStorage.setItem('app_banner_state', 'open'); } catch(e) {}
    };

    // ── Hiện thông báo "Sắp ra mắt" khi link chưa có ──
    window.appDlNotReady = function(platform) {
        // Tạo popup mini
        const existing = document.getElementById('app-coming-soon-popup');
        if (existing) existing.remove();

        const popup = document.createElement('div');
        popup.id = 'app-coming-soon-popup';
        popup.style.cssText = `
            position: fixed;
            bottom: 110px;
            left: 50%;
            transform: translateX(-50%) translateY(20px);
            background: linear-gradient(135deg, #1f2937, #111827);
            border: 1px solid rgba(6,182,212,0.4);
            border-radius: 16px;
            padding: 1rem 1.5rem;
            box-shadow: 0 12px 40px rgba(0,0,0,0.5), 0 0 0 1px rgba(6,182,212,0.1);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-family: 'Inter', sans-serif;
            min-width: 240px;
            max-width: 320px;
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            text-align: left;
        `;

        const icon = platform === 'Android' ? '🤖' : '🍎';
        popup.innerHTML = `
            <span style="font-size:2rem;line-height:1">${icon}</span>
            <div>
                <div style="font-weight:700;font-size:0.9rem;color:#f9fafb;margin-bottom:0.2rem">
                    App ${platform} sắp ra mắt!
                </div>
                <div style="font-size:0.78rem;color:#9ca3af;line-height:1.4">
                    Chúng tôi đang hoàn thiện ứng dụng.<br>Vui lòng quay lại sau nhé 🙏
                </div>
            </div>
        `;

        document.body.appendChild(popup);

        // Animate in
        requestAnimationFrame(() => {
            popup.style.opacity   = '1';
            popup.style.transform = 'translateX(-50%) translateY(0)';
        });

        // Auto dismiss sau 3s
        setTimeout(() => {
            popup.style.opacity   = '0';
            popup.style.transform = 'translateX(-50%) translateY(10px)';
            setTimeout(() => popup.remove(), 300);
        }, 3000);
    };

    // ── Khôi phục trạng thái khi tải trang ──
    document.addEventListener('DOMContentLoaded', function() {
        try {
            const state = sessionStorage.getItem('app_banner_state');
            if (state === 'collapsed') {
                const banner = document.getElementById('app-download-banner');
                const pill   = document.getElementById('app-floating-pill');
                if (banner) banner.style.display = 'none';
                if (pill)   pill.classList.add('visible');
            }
        } catch(e) {}
    });
})();
</script>
