@extends('layouts.admin')

@section('title', 'Cài Đặt App Download')

@push('admin-styles')
<style>
    .app-preview-card {
        background: linear-gradient(135deg, #0d1117, #161b27);
        border: 1px solid rgba(99,102,241,0.2);
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
    }

    .download-btn-preview {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.8rem 1.5rem;
        border-radius: 14px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        border: none;
        text-decoration: none;
        transition: all 0.3s;
        min-width: 180px;
        justify-content: center;
    }

    .download-btn-android {
        background: linear-gradient(135deg, #3ddc84, #1da462);
        color: #fff;
        box-shadow: 0 8px 25px rgba(61,220,132,0.35);
    }

    .download-btn-android:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(61,220,132,0.5);
    }

    .download-btn-ios {
        background: linear-gradient(135deg, #555, #1c1c1e);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.15);
        box-shadow: 0 8px 25px rgba(0,0,0,0.4);
    }

    .download-btn-ios:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(0,0,0,0.5);
        background: linear-gradient(135deg, #666, #2a2a2a);
    }

    .download-btn-preview img {
        width: 28px;
        height: 28px;
        object-fit: contain;
        border-radius: 6px;
    }

    .download-btn-preview .btn-icon-fallback {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }

    .icon-upload-zone {
        border: 2px dashed rgba(99,102,241,0.4);
        border-radius: 16px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.25s;
        background: rgba(99,102,241,0.03);
        position: relative;
    }

    .icon-upload-zone:hover, .icon-upload-zone.drag-over {
        border-color: var(--primary);
        background: rgba(6,182,212,0.08);
    }

    .icon-upload-zone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }

    .icon-preview {
        width: 80px;
        height: 80px;
        border-radius: 18px;
        object-fit: contain;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        padding: 8px;
    }

    .icon-placeholder {
        width: 80px;
        height: 80px;
        border-radius: 18px;
        background: rgba(99,102,241,0.1);
        border: 1px dashed rgba(99,102,241,0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin: 0 auto;
    }

    .section-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .section-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 700;
        font-size: 1rem;
    }

    .section-header .section-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .section-body {
        padding: 1.5rem;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 52px;
        height: 28px;
    }

    .toggle-switch input { opacity: 0; width: 0; height: 0; }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(99,102,241,0.2);
        border: 1px solid rgba(99,102,241,0.3);
        border-radius: 28px;
        transition: all 0.3s;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        width: 20px;
        height: 20px;
        left: 4px;
        bottom: 3px;
        background: white;
        border-radius: 50%;
        transition: all 0.3s;
    }

    .toggle-switch input:checked + .toggle-slider {
        background: var(--primary);
        border-color: var(--primary);
    }

    .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(24px);
    }
</style>
@endpush

@section('admin-content')
<div class="page-header">
    <h1>📱 Cài Đặt App Download</h1>
    <p>Quản lý nút tải ứng dụng Android & iOS cho người dùng</p>
</div>

<form action="{{ route('admin.app-settings.save') }}" method="POST" enctype="multipart/form-data" id="app-settings-form">
    @csrf

    <div style="display: grid; grid-template-columns: 1fr 380px; gap: 1.5rem; align-items: start;">
        <!-- LEFT COLUMN -->
        <div>
            <!-- Toggle bật/tắt -->
            <div class="section-card">
                <div class="section-header">
                    <div class="section-icon" style="background: rgba(6,182,212,0.15)">⚙️</div>
                    Bật / Tắt tính năng
                </div>
                <div class="section-body">
                    <div class="d-flex align-center justify-between">
                        <div>
                            <div style="font-weight: 600; margin-bottom: 0.25rem;">Hiển thị nút tải App</div>
                            <div style="font-size: 0.82rem; color: var(--text-muted)">Khi bật, người dùng sẽ thấy banner tải app ở trang chủ và layout</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="app_download_enabled" value="1" id="toggle-enabled" {{ ($settings['app_download_enabled'] ?? '1') == '1' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Banner text -->
            <div class="section-card">
                <div class="section-header">
                    <div class="section-icon" style="background: rgba(245,158,11,0.15)">✏️</div>
                    Tiêu đề Banner
                </div>
                <div class="section-body">
                    <div class="form-group">
                        <label class="form-label">Tiêu đề chính</label>
                        <input type="text" class="form-control" name="app_banner_title" value="{{ $settings['app_banner_title'] ?? 'Tải App AquaHub' }}" placeholder="Tải App AquaHub">
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label">Mô tả phụ</label>
                        <input type="text" class="form-control" name="app_banner_subtitle" value="{{ $settings['app_banner_subtitle'] ?? 'Trải nghiệm tốt hơn với ứng dụng di động' }}" placeholder="Trải nghiệm tốt hơn với ứng dụng di động">
                    </div>
                </div>
            </div>

            <!-- Android Settings -->
            <div class="section-card">
                <div class="section-header">
                    <div class="section-icon" style="background: rgba(61,220,132,0.15)">🤖</div>
                    Cài đặt Android
                </div>
                <div class="section-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; align-items: start;">
                        <!-- Icon upload -->
                        <div>
                            <label class="form-label">Icon Android</label>
                            @if(!empty($settings['app_android_icon']))
                                <div style="margin-bottom: 1rem; text-align: center;">
                                    <img src="{{ asset($settings['app_android_icon']) }}" class="icon-preview" id="android-icon-current" alt="Android Icon">
                                    <div style="margin-top: 0.5rem;">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeIcon('android')">
                                            <i class="bi bi-trash"></i> Xóa icon
                                        </button>
                                    </div>
                                </div>
                            @endif
                            <div class="icon-upload-zone" id="android-upload-zone">
                                <input type="file" name="app_android_icon" accept="image/*" onchange="previewIcon(this, 'android-preview')">
                                <div id="android-preview-container">
                                    <div class="icon-placeholder" id="android-placeholder" {{ empty($settings['app_android_icon']) ? '' : 'style=display:none' }}>🤖</div>
                                    <img id="android-preview" src="" style="width:80px;height:80px;border-radius:18px;object-fit:contain;display:none;margin:0 auto;">
                                </div>
                                <div style="margin-top: 0.75rem; font-size: 0.8rem; color: var(--text-muted)">
                                    <i class="bi bi-cloud-upload"></i> Click hoặc kéo thả ảnh vào đây<br>
                                    <span style="font-size: 0.72rem; color: rgba(255,255,255,0.3)">PNG, JPG, WEBP · Tối đa 2MB</span>
                                </div>
                            </div>
                        </div>

                        <!-- Text & URL -->
                        <div>
                            <div class="form-group">
                                <label class="form-label">Nhãn nút</label>
                                <input type="text" class="form-control" name="app_android_label" value="{{ $settings['app_android_label'] ?? 'Tải cho Android' }}" placeholder="Tải cho Android" id="android-label-input">
                            </div>
                            <div class="form-group mb-0">
                                <label class="form-label">Link tải APK / Play Store</label>
                                <input type="text" class="form-control" name="app_android_url" value="{{ $settings['app_android_url'] ?? '#' }}" placeholder="https://play.google.com/..." id="android-url-input">
                                <div style="margin-top: 0.4rem; font-size: 0.75rem; color: var(--text-muted)">Để trống hoặc <code>#</code> nếu chưa có link</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- iOS Settings -->
            <div class="section-card">
                <div class="section-header">
                    <div class="section-icon" style="background: rgba(99,102,241,0.15)">🍎</div>
                    Cài đặt iOS (iPhone / iPad)
                </div>
                <div class="section-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; align-items: start;">
                        <!-- Icon upload -->
                        <div>
                            <label class="form-label">Icon iOS</label>
                            @if(!empty($settings['app_ios_icon']))
                                <div style="margin-bottom: 1rem; text-align: center;">
                                    <img src="{{ asset($settings['app_ios_icon']) }}" class="icon-preview" id="ios-icon-current" alt="iOS Icon">
                                    <div style="margin-top: 0.5rem;">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeIcon('ios')">
                                            <i class="bi bi-trash"></i> Xóa icon
                                        </button>
                                    </div>
                                </div>
                            @endif
                            <div class="icon-upload-zone" id="ios-upload-zone">
                                <input type="file" name="app_ios_icon" accept="image/*" onchange="previewIcon(this, 'ios-preview')">
                                <div id="ios-preview-container">
                                    <div class="icon-placeholder" id="ios-placeholder" {{ empty($settings['app_ios_icon']) ? '' : 'style=display:none' }}>🍎</div>
                                    <img id="ios-preview" src="" style="width:80px;height:80px;border-radius:18px;object-fit:contain;display:none;margin:0 auto;">
                                </div>
                                <div style="margin-top: 0.75rem; font-size: 0.8rem; color: var(--text-muted)">
                                    <i class="bi bi-cloud-upload"></i> Click hoặc kéo thả ảnh vào đây<br>
                                    <span style="font-size: 0.72rem; color: rgba(255,255,255,0.3)">PNG, JPG, WEBP · Tối đa 2MB</span>
                                </div>
                            </div>
                        </div>

                        <!-- Text & URL -->
                        <div>
                            <div class="form-group">
                                <label class="form-label">Nhãn nút</label>
                                <input type="text" class="form-control" name="app_ios_label" value="{{ $settings['app_ios_label'] ?? 'Tải cho iOS' }}" placeholder="Tải cho iOS" id="ios-label-input">
                            </div>
                            <div class="form-group mb-0">
                                <label class="form-label">Link App Store</label>
                                <input type="text" class="form-control" name="app_ios_url" value="{{ $settings['app_ios_url'] ?? '#' }}" placeholder="https://apps.apple.com/..." id="ios-url-input">
                                <div style="margin-top: 0.4rem; font-size: 0.75rem; color: var(--text-muted)">Để trống hoặc <code>#</code> nếu chưa có link</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1rem;">
                <i class="bi bi-save"></i> Lưu cài đặt
            </button>
        </div>

        <!-- RIGHT COLUMN: Preview -->
        <div style="position: sticky; top: 2rem;">
            <div class="section-card">
                <div class="section-header">
                    <div class="section-icon" style="background: rgba(99,102,241,0.15)">👁️</div>
                    Xem trước
                </div>
                <div class="section-body">
                    <!-- Desktop Preview -->
                    <div style="margin-bottom: 1.5rem;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.75rem;">🖥 Desktop Banner</div>
                        <div style="background: linear-gradient(135deg, rgba(6,182,212,0.08), rgba(16,185,129,0.05)); border: 1px solid rgba(6,182,212,0.2); border-radius: 16px; padding: 1.5rem; text-align: center;">
                            <div style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 0.25rem;" id="preview-subtitle">{{ $settings['app_banner_subtitle'] ?? 'Trải nghiệm tốt hơn với ứng dụng di động' }}</div>
                            <div style="font-weight: 800; font-size: 1.2rem; margin-bottom: 1.25rem; background: linear-gradient(135deg, #06b6d4, #10b981); -webkit-background-clip: text; -webkit-text-fill-color: transparent;" id="preview-title">{{ $settings['app_banner_title'] ?? 'Tải App AquaHub' }}</div>
                            <div style="display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap;">
                                <a href="#" class="download-btn-preview download-btn-android" id="preview-android-btn">
                                    @if(!empty($settings['app_android_icon']))
                                        <img src="{{ asset($settings['app_android_icon']) }}" id="preview-android-icon" alt="">
                                    @else
                                        <span class="btn-icon-fallback" id="preview-android-icon-fallback">🤖</span>
                                    @endif
                                    <span id="preview-android-label">{{ $settings['app_android_label'] ?? 'Tải cho Android' }}</span>
                                </a>
                                <a href="#" class="download-btn-preview download-btn-ios" id="preview-ios-btn">
                                    @if(!empty($settings['app_ios_icon']))
                                        <img src="{{ asset($settings['app_ios_icon']) }}" id="preview-ios-icon" alt="">
                                    @else
                                        <span class="btn-icon-fallback" id="preview-ios-icon-fallback">🍎</span>
                                    @endif
                                    <span id="preview-ios-label">{{ $settings['app_ios_label'] ?? 'Tải cho iOS' }}</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Preview -->
                    <div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.75rem;">📱 Mobile Banner</div>
                        <div style="background: #0a0a0f; border-radius: 24px; border: 3px solid rgba(255,255,255,0.1); overflow: hidden; width: 180px; margin: 0 auto; padding: 1rem;">
                            <div style="background: linear-gradient(135deg, rgba(6,182,212,0.1), rgba(16,185,129,0.05)); border: 1px solid rgba(6,182,212,0.2); border-radius: 12px; padding: 0.875rem; text-align: center;">
                                <div style="font-size: 0.55rem; color: var(--text-muted); margin-bottom: 0.15rem;" id="preview-subtitle-m">{{ $settings['app_banner_subtitle'] ?? 'Trải nghiệm tốt hơn với ứng dụng di động' }}</div>
                                <div style="font-weight: 800; font-size: 0.7rem; margin-bottom: 0.75rem; color: #22d3ee;" id="preview-title-m">{{ $settings['app_banner_title'] ?? 'Tải App AquaHub' }}</div>
                                <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                                    <div style="background: linear-gradient(135deg, #3ddc84, #1da462); color: white; border-radius: 8px; padding: 0.4rem 0.5rem; font-size: 0.6rem; font-weight: 700; display: flex; align-items: center; gap: 0.25rem; justify-content: center;">
                                        <span id="preview-android-label-m">🤖 {{ $settings['app_android_label'] ?? 'Android' }}</span>
                                    </div>
                                    <div style="background: linear-gradient(135deg, #555, #1c1c1e); color: white; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 0.4rem 0.5rem; font-size: 0.6rem; font-weight: 700; display: flex; align-items: center; gap: 0.25rem; justify-content: center;">
                                        <span id="preview-ios-label-m">🍎 {{ $settings['app_ios_label'] ?? 'iOS' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tips -->
            <div style="background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.25); border-radius: 14px; padding: 1rem; margin-top: 1rem; font-size: 0.8rem; color: var(--text-muted);">
                <div style="font-weight: 700; color: #f59e0b; margin-bottom: 0.5rem;"><i class="bi bi-lightbulb"></i> Lưu ý</div>
                <ul style="list-style: disc; padding-left: 1.25rem; line-height: 1.8;">
                    <li>Icon nên có nền trong suốt (PNG)</li>
                    <li>Kích thước tối ưu: 128x128px</li>
                    <li>Link để <code>#</code> nếu chưa có link tải</li>
                    <li>Banner hiển thị trên cả Desktop và Mobile</li>
                </ul>
            </div>
        </div>
    </div>
</form>

@push('admin-scripts')
<script>
// Preview icon khi chọn file
function previewIcon(input, previewId) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        const platform = previewId.replace('-preview', '');
        const preview = document.getElementById(previewId);
        const placeholder = document.getElementById(platform + '-placeholder');
        if (preview) { preview.src = e.target.result; preview.style.display = 'block'; }
        if (placeholder) placeholder.style.display = 'none';

        // Update live preview buttons
        updatePreviewButton(platform, e.target.result);
    };
    reader.readAsDataURL(file);
}

function updatePreviewButton(platform, iconSrc) {
    const existingImg = document.getElementById('preview-' + platform + '-icon');
    const fallbackSpan = document.getElementById('preview-' + platform + '-icon-fallback');
    if (existingImg) {
        existingImg.src = iconSrc;
        existingImg.style.display = 'block';
    } else {
        // Create new img
        const btn = document.getElementById('preview-' + platform + '-btn');
        if (btn && fallbackSpan) {
            const img = document.createElement('img');
            img.id = 'preview-' + platform + '-icon';
            img.style.cssText = 'width:28px;height:28px;object-fit:contain;border-radius:6px;';
            img.src = iconSrc;
            btn.insertBefore(img, fallbackSpan);
            fallbackSpan.style.display = 'none';
        }
    }
}

// Real-time label preview
document.getElementById('android-label-input')?.addEventListener('input', function() {
    const v = this.value || 'Tải cho Android';
    document.getElementById('preview-android-label').textContent = v;
    document.getElementById('preview-android-label-m').textContent = '🤖 ' + v;
});
document.getElementById('ios-label-input')?.addEventListener('input', function() {
    const v = this.value || 'Tải cho iOS';
    document.getElementById('preview-ios-label').textContent = v;
    document.getElementById('preview-ios-label-m').textContent = '🍎 ' + v;
});

// Banner title/subtitle live update
document.querySelector('[name="app_banner_title"]')?.addEventListener('input', function() {
    const v = this.value || 'Tải App AquaHub';
    document.getElementById('preview-title').textContent = v;
    document.getElementById('preview-title-m').textContent = v;
});
document.querySelector('[name="app_banner_subtitle"]')?.addEventListener('input', function() {
    const v = this.value || 'Trải nghiệm tốt hơn với ứng dụng di động';
    document.getElementById('preview-subtitle').textContent = v;
    document.getElementById('preview-subtitle-m').textContent = v;
});

// Remove icon via AJAX
function removeIcon(platform) {
    if (!confirm('Xóa icon ' + (platform === 'android' ? 'Android' : 'iOS') + '?')) return;

    fetch('{{ route("admin.app-settings.remove-icon", ["platform" => "PLATFORM"]) }}'.replace('PLATFORM', platform), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            // Reload trang để refresh UI
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message || 'Có lỗi xảy ra', 'error');
        }
    });
}

// Drag & drop highlight
document.querySelectorAll('.icon-upload-zone').forEach(zone => {
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
    zone.addEventListener('drop', e => { e.preventDefault(); zone.classList.remove('drag-over'); });
});
</script>
@endpush
@endsection
