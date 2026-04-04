@extends('layouts.admin')
@section('title', 'Cài đặt Thanh Toán NAV')

@section('admin-content')
<div class="page-header">
    <div class="d-flex justify-between align-center">
        <div>
            <h1>⚙️ Cài Đặt Thanh Toán</h1>
            <p>Cấu hình VietQR và phương thức thanh toán Hỗ Trợ MXH</p>
        </div>
        <a href="{{ route('admin.nav.orders') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Về danh sách đơn</a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
@endif

<div style="display:grid;grid-template-columns:1fr 340px;gap:1.5rem;align-items:start">

<form action="{{ route('admin.nav.settings.save') }}" method="POST">
@csrf

    <!-- VietQR Config -->
    <div class="card mb-3" style="margin-bottom:1.5rem">
        <div class="card-header"><i class="bi bi-bank" style="color:#69C9D0"></i> Cấu Hình VietQR</div>
        <div class="card-body">
            <div class="grid-2">
                <div class="mb-3">
                    <label style="display:block;font-size:0.8rem;color:var(--text-muted);margin-bottom:0.4rem">Tên ngân hàng</label>
                    <input type="text" name="bank_name" class="form-control" value="{{ $settings['bank_name'] ?? 'MB Bank' }}" placeholder="MB Bank">
                </div>
                <div class="mb-3">
                    <label style="display:block;font-size:0.8rem;color:var(--text-muted);margin-bottom:0.4rem">BIN ngân hàng (VietQR)</label>
                    <input type="text" name="bank_bin" class="form-control" value="{{ $settings['bank_bin'] ?? '970422' }}" placeholder="970422">
                    <div style="font-size:0.72rem;color:var(--text-muted);margin-top:0.3rem">MB Bank=970422 · VCB=970436 · TCB=970407 · ACB=970416</div>
                </div>
                <div class="mb-3">
                    <label style="display:block;font-size:0.8rem;color:var(--text-muted);margin-bottom:0.4rem">Số tài khoản</label>
                    <input type="text" name="bank_account" class="form-control" value="{{ $settings['bank_account'] ?? '' }}" placeholder="0783704196">
                </div>
                <div class="mb-3">
                    <label style="display:block;font-size:0.8rem;color:var(--text-muted);margin-bottom:0.4rem">Chủ tài khoản</label>
                    <input type="text" name="bank_owner" class="form-control" value="{{ $settings['bank_owner'] ?? '' }}" placeholder="NGUYEN VAN A">
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="card mb-3" style="margin-bottom:1.5rem">
        <div class="card-header"><i class="bi bi-credit-card"></i> Phương Thức Thanh Toán</div>
        <div class="card-body">
            <div style="display:flex;flex-direction:column;gap:1rem">
                <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer;background:var(--bg-card2);border-radius:12px;padding:1rem;border:1px solid var(--border)">
                    <input type="checkbox" name="bank_enabled" value="1" {{ ($settings['bank_enabled'] ?? '1') == '1' ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--primary)">
                    <div>
                        <div style="font-weight:700">🏦 Thanh toán qua Ngân hàng (VietQR)</div>
                        <div style="font-size:0.8rem;color:var(--text-muted)">Khách quét QR chuyển khoản, admin xác minh thủ công</div>
                    </div>
                </label>
                <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer;background:var(--bg-card2);border-radius:12px;padding:1rem;border:1px solid var(--border)">
                    <input type="checkbox" name="pt_enabled" value="1" {{ ($settings['pt_enabled'] ?? '1') == '1' ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--primary)">
                    <div>
                        <div style="font-weight:700">💎 Thanh toán bằng Điểm PT</div>
                        <div style="font-size:0.8rem;color:var(--text-muted)">Khách dùng số dư PT trong tài khoản, trừ tự động ngay lập tức</div>
                    </div>
                </label>
            </div>
        </div>
    </div>

    <!-- Sepay API Key -->
    <div class="card mb-3" style="margin-bottom:1.5rem">
        <div class="card-header"><i class="bi bi-robot" style="color:#69C9D0"></i> Cấu Hình Sepay (Nạp Tự Động)</div>
        <div class="card-body">
            <div class="mb-3">
                <label style="display:block;font-size:0.8rem;color:var(--text-muted);margin-bottom:0.4rem">Sepay API Key (Khóa bảo mật)</label>
                <input type="text" name="sepay_api_key" class="form-control" value="{{ $settings['sepay_api_key'] ?? '' }}" placeholder="Nhập API Key... (ví dụ: TokenCuaToi123)">
                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:0.4rem">
                    1. Bạn tự nghĩ ra một chuỗi bất kỳ và dán vào đây.<br>
                    2. Copy chuỗi đó dán vào phần "API Key" trên Sepay.<br>
                    3. Trong ô "Gọi đến URL" ở Sepay, hãy điền: <strong style="color:var(--primary)">{{ url('/webhook/sepay') }}</strong>
                </div>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%;padding:0.875rem;font-size:0.95rem">
        <i class="bi bi-save"></i> Lưu Cài Đặt
    </button>
</form>

<!-- Preview QR -->
<div class="card" style="position:sticky;top:1rem">
    <div class="card-header">👀 Xem Trước QR</div>
    <div class="card-body" style="text-align:center">
        <div style="background:white;padding:12px;border-radius:12px;display:inline-block;margin-bottom:1rem">
            <img id="qrPreview" src="{{ \App\Models\NavSetting::vietQrUrl('NAVDEMO', 100000) }}" style="width:180px;height:180px" alt="QR Preview">
        </div>
        <div style="font-size:0.8rem;color:var(--text-muted)">Đây là QR mẫu với mã đơn NAVDEMO và số tiền 100,000 VNĐ</div>
        <div style="margin-top:1rem;background:var(--bg-card2);border-radius:10px;padding:1rem;text-align:left;font-size:0.82rem">
            <div style="display:flex;justify-content:space-between;padding:0.3rem 0;border-bottom:1px solid var(--border)">
                <span style="color:var(--text-muted)">Ngân hàng</span>
                <span style="font-weight:700">{{ $settings['bank_name'] ?? 'MB Bank' }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:0.3rem 0;border-bottom:1px solid var(--border)">
                <span style="color:var(--text-muted)">STK</span>
                <span style="font-weight:700">{{ $settings['bank_account'] ?? '-' }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:0.3rem 0">
                <span style="color:var(--text-muted)">Chủ TK</span>
                <span style="font-weight:700">{{ $settings['bank_owner'] ?? '-' }}</span>
            </div>
        </div>
    </div>
</div>

</div>
@endsection
