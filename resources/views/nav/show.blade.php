@extends('layouts.app')

@section('title', $service->name . ' - Hỗ Trợ MXH')

@push('styles')
<style>
    .nav-form-page { padding: 2rem 0 4rem; }
    .nav-breadcrumb {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8rem;
        color: #6b7280;
        margin-bottom: 2rem;
    }
    .nav-breadcrumb a { color: #69C9D0; text-decoration: none; }
    .nav-breadcrumb a:hover { text-decoration: underline; }

    .form-layout {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 2rem;
        align-items: start;
    }
    .form-card {
        background: #0d1117;
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 20px;
        overflow: hidden;
    }
    .form-card-header {
        padding: 1.5rem;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .form-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .form-card-body { padding: 1.75rem; }

    .form-section-title {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #69C9D0;
        font-weight: 700;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .form-group { margin-bottom: 1.25rem; }
    .form-label { font-size: 0.85rem; font-weight: 600; color: #d1d5db; margin-bottom: 0.4rem; display: block; }
    .form-label span { color: #ef4444; }
    .form-input {
        width: 100%;
        background: #161b27;
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 10px;
        color: #f9fafb;
        padding: 0.65rem 0.875rem;
        font-size: 0.875rem;
        font-family: 'Inter', sans-serif;
        transition: border-color 0.2s;
    }
    .form-input:focus { outline: none; border-color: #69C9D0; }
    .form-input option { background: #161b27; }
    textarea.form-input { resize: vertical; min-height: 90px; }

    .upload-zone {
        border: 2px dashed rgba(255,255,255,0.12);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
    }
    .upload-zone:hover { border-color: #69C9D0; background: rgba(105,201,208,0.04); }
    .upload-zone input { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
    .upload-zone .icon { font-size: 1.5rem; color: #69C9D0; margin-bottom: 0.4rem; }
    .upload-zone .label { font-size: 0.8rem; color: #6b7280; }
    .upload-zone .label strong { color: #9ca3af; }
    .upload-preview { display: none; margin-top: 0.75rem; }
    .upload-preview img { max-height: 100px; border-radius: 8px; }

    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

    /* Sidebar */
    .info-card {
        background: #0d1117;
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 20px;
        padding: 1.5rem;
        position: sticky;
        top: 1rem;
    }
    .price-display {
        text-align: center;
        padding: 1.5rem;
        background: rgba(105,201,208,0.06);
        border-radius: 14px;
        margin-bottom: 1.25rem;
        border: 1px solid rgba(105,201,208,0.15);
    }
    .price-display .amount { font-size: 2rem; font-weight: 900; color: #69C9D0; }
    .price-display .unit { font-size: 0.85rem; color: #6b7280; }

    .info-list { list-style: none; padding: 0; margin: 0; }
    .info-list li {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.6rem 0;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        font-size: 0.82rem;
        color: #9ca3af;
    }
    .info-list li:last-child { border-bottom: none; }
    .info-list li i { color: #69C9D0; font-size: 0.9rem; margin-top: 1px; flex-shrink: 0; }

    .payment-toggle {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }
    .payment-option {
        border: 2px solid rgba(255,255,255,0.08);
        border-radius: 12px;
        padding: 0.875rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .payment-option.active { border-color: #69C9D0; background: rgba(105,201,208,0.08); }
    .payment-option input { display: none; }
    .payment-option .p-icon { font-size: 1.4rem; display: block; margin-bottom: 0.3rem; }
    .payment-option .p-name { font-size: 0.78rem; font-weight: 700; color: #d1d5db; }
    .payment-option .p-sub { font-size: 0.7rem; color: #6b7280; }

    .btn-submit {
        width: 100%;
        background: linear-gradient(135deg, #69C9D0, #4fb3bc);
        color: #000;
        border: none;
        padding: 0.875rem;
        border-radius: 12px;
        font-size: 0.95rem;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Inter', sans-serif;
    }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(105,201,208,0.3); }

    .pt-balance {
        text-align: center;
        font-size: 0.82rem;
        color: #6b7280;
        margin-bottom: 1rem;
    }
    .pt-balance strong { color: #69C9D0; }

    @media (max-width: 900px) {
        .form-layout { grid-template-columns: 1fr; }
        .info-card { position: static; }
        .grid-2 { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="nav-form-page">
<div class="container">
    <!-- Breadcrumb -->
    <div class="nav-breadcrumb">
        <a href="{{ route('nav.index') }}"><i class="bi bi-house"></i> Hỗ Trợ MXH</a>
        <i class="bi bi-chevron-right"></i>
        <span>{{ $service->name }}</span>
    </div>

    <form action="{{ route('nav.store', $service->slug) }}" method="POST" enctype="multipart/form-data" id="navForm">
    @csrf
    <div class="form-layout">

        <!-- Main Form -->
        <div>
            <!-- TikTok Info -->
            <div class="form-card mb-4" style="margin-bottom:1.5rem">
                <div class="form-card-header">
                    <div class="form-card-icon" style="background:{{ $service->color }}22;color:{{ $service->color }}">
                        <i class="bi {{ $service->icon }}"></i>
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:1rem">{{ $service->name }}</div>
                        <div style="font-size:0.8rem;color:#6b7280">{{ $service->description }}</div>
                    </div>
                </div>
                <div class="form-card-body">
                    <div class="form-section-title"><i class="bi bi-tiktok"></i> Thông Tin TikTok</div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Username TikTok <span>*</span></label>
                            <input type="text" name="tiktok_username" class="form-input" placeholder="@username" value="{{ old('tiktok_username') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Số followers</label>
                            <input type="number" name="follower_count" class="form-input" placeholder="VD: 1500" value="{{ old('follower_count') }}" min="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email đăng ký TikTok</label>
                            <input type="email" name="registered_email" class="form-input" placeholder="email@example.com" value="{{ old('registered_email') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Số điện thoại đăng ký</label>
                            <input type="text" name="registered_phone" class="form-input" placeholder="09xxxxxxxx" value="{{ old('registered_phone') }}">
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Loại vi phạm (nếu biết)</label>
                            <select name="violation_type" class="form-input">
                                <option value="">-- Không rõ --</option>
                                <option value="Community Guidelines">Vi phạm Quy tắc cộng đồng</option>
                                <option value="Spam/Fake Activity">Spam / Hoạt động giả</option>
                                <option value="Nudity/Sexual Content">Nội dung nhạy cảm</option>
                                <option value="Dangerous Content">Nội dung nguy hiểm</option>
                                <option value="Misinformation">Thông tin sai lệch</option>
                                <option value="Intellectual Property">Vi phạm bản quyền</option>
                                <option value="Minor Safety">An toàn trẻ em</option>
                                <option value="Hateful Behavior">Ngôn ngữ thù địch</option>
                                <option value="Other">Khác</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Ngày bị khóa</label>
                            <input type="date" name="violation_date" class="form-input" value="{{ old('violation_date') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Ghi chú thêm (tình huống cụ thể)</label>
                        <textarea name="account_notes" class="form-input" placeholder="Mô tả thêm về tình huống, video nào bị gỡ, có bị cảnh báo trước không...">{{ old('account_notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Upload -->
            <div class="form-card mb-4" style="margin-bottom:1.5rem">
                <div class="form-card-body">
                    <div class="form-section-title"><i class="bi bi-image"></i> Upload Ảnh Xác Minh</div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">CCCD / CMND mặt trước</label>
                            <div class="upload-zone" onclick="this.querySelector('input').click()">
                                <input type="file" name="id_card_front" accept="image/*" onchange="previewImg(this)">
                                <div class="icon"><i class="bi bi-credit-card"></i></div>
                                <div class="label"><strong>Nhấn để chọn ảnh</strong><br>JPG, PNG tối đa 5MB</div>
                                <div class="upload-preview" id="prev_id_front"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">CCCD / CMND mặt sau</label>
                            <div class="upload-zone" onclick="this.querySelector('input').click()">
                                <input type="file" name="id_card_back" accept="image/*" onchange="previewImg(this)">
                                <div class="icon"><i class="bi bi-credit-card-2-back"></i></div>
                                <div class="label"><strong>Nhấn để chọn ảnh</strong><br>JPG, PNG tối đa 5MB</div>
                                <div class="upload-preview" id="prev_id_back"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ảnh chụp màn hình thông báo bị khóa</label>
                        <div class="upload-zone" onclick="this.querySelector('input').click()">
                            <input type="file" name="screenshot_path" accept="image/*" onchange="previewImg(this)">
                            <div class="icon"><i class="bi bi-phone"></i></div>
                            <div class="label"><strong>Chụp màn hình TikTok thông báo bị khóa</strong><br>Giúp admin hiểu rõ tình trạng</div>
                            <div class="upload-preview" id="prev_screenshot"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liên hệ -->
            <div class="form-card">
                <div class="form-card-body">
                    <div class="form-section-title"><i class="bi bi-person-lines-fill"></i> Thông Tin Liên Hệ</div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Họ tên của bạn <span>*</span></label>
                            <input type="text" name="customer_name" class="form-input" placeholder="Nguyễn Văn A" value="{{ old('customer_name', auth()->user()->name) }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">SĐT / Zalo liên hệ <span>*</span></label>
                            <input type="text" name="customer_contact" class="form-input" placeholder="09xxxxxxxx" value="{{ old('customer_contact') }}" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <div class="info-card">
                <div class="price-display">
                    <div class="amount">{{ number_format((float)$service->price, 0, ',', '.') }}</div>
                    <div class="unit">PT / VNĐ</div>
                </div>

                <div style="font-size:0.82rem;font-weight:700;color:#9ca3af;margin-bottom:0.75rem;text-transform:uppercase;letter-spacing:1px">Phương thức thanh toán</div>

                <div class="payment-toggle">
                    <label class="payment-option active" id="opt-bank">
                        <input type="radio" name="payment_method" value="bank" checked onchange="togglePayment('bank')">
                        <span class="p-icon">🏦</span>
                        <span class="p-name">Ngân hàng</span>
                        <span class="p-sub">QR VietQR</span>
                    </label>
                    <label class="payment-option" id="opt-points">
                        <input type="radio" name="payment_method" value="points" onchange="togglePayment('points')">
                        <span class="p-icon">💎</span>
                        <span class="p-name">Điểm PT</span>
                        <span class="p-sub">Trừ ngay</span>
                    </label>
                </div>

                <div class="pt-balance" id="pt-balance-info" style="display:none">
                    Số dư PT hiện tại: <strong>{{ number_format((float)auth()->user()->balance_point, 0, ',', '.') }} PT</strong>
                    @if(auth()->user()->balance_point < $service->price)
                    <br><span style="color:#ef4444;font-size:0.75rem"><i class="bi bi-exclamation-circle"></i> Không đủ PT để thanh toán</span>
                    @endif
                </div>

                <ul class="info-list" style="margin-bottom:1.25rem">
                    <li><i class="bi bi-clock"></i> Thời hạn kháng cáo: <strong style="color:#f9fafb;margin-left:auto">{{ $service->appeal_deadline_days }} ngày</strong></li>
                    <li><i class="bi bi-shield-check"></i> Soạn đơn chuẩn TikTok Trust & Safety</li>
                    <li><i class="bi bi-translate"></i> Kháng cáo bằng tiếng Anh chuyên nghiệp</li>
                    <li><i class="bi bi-headset"></i> Hỗ trợ & cập nhật kết quả</li>
                </ul>

                @if($errors->any())
                <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);border-radius:10px;padding:0.875rem;margin-bottom:1rem;font-size:0.82rem;color:#ef4444">
                    @foreach($errors->all() as $e)
                    <div><i class="bi bi-exclamation-circle"></i> {{ $e }}</div>
                    @endforeach
                </div>
                @endif

                <button type="submit" class="btn-submit">
                    <i class="bi bi-send"></i> Đăng Ký Dịch Vụ
                </button>

                <div style="text-align:center;margin-top:0.75rem;font-size:0.75rem;color:#6b7280">
                    <i class="bi bi-lock-fill"></i> Thông tin của bạn được bảo mật tuyệt đối
                </div>
            </div>
        </div>
    </div>
    </form>
</div>
</div>
@endsection

@push('scripts')
<script>
function togglePayment(method) {
    document.getElementById('opt-bank').classList.toggle('active', method === 'bank');
    document.getElementById('opt-points').classList.toggle('active', method === 'points');
    document.getElementById('pt-balance-info').style.display = method === 'points' ? 'block' : 'none';
}

function previewImg(input) {
    const map = {
        id_card_front: 'prev_id_front',
        id_card_back: 'prev_id_back',
        screenshot_path: 'prev_screenshot',
    };
    const previewId = map[input.name];
    if (!previewId) return;
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.innerHTML = `<img src="${e.target.result}" style="max-height:100px;border-radius:8px">`;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
