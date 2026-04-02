@extends('layouts.app')

@section('title', 'Thanh Toán - ' . $order->order_code)

@push('styles')
<style>
    .payment-page { padding: 3rem 0 5rem; }
    .payment-card {
        max-width: 480px;
        margin: 0 auto;
        background: #0d1117;
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 24px;
        overflow: hidden;
    }
    .payment-header {
        background: linear-gradient(135deg, #010101, #0a1a1f);
        padding: 2rem;
        text-align: center;
        border-bottom: 1px solid rgba(105,201,208,0.15);
    }
    .payment-header .order-code {
        font-family: monospace;
        font-size: 1.1rem;
        font-weight: 700;
        color: #69C9D0;
        letter-spacing: 2px;
    }
    .payment-header .service-name {
        font-size: 0.85rem;
        color: #6b7280;
        margin-top: 0.3rem;
    }

    .qr-section {
        padding: 2rem;
        text-align: center;
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }
    .qr-wrapper {
        display: inline-block;
        background: white;
        padding: 12px;
        border-radius: 16px;
        margin-bottom: 1rem;
    }
    .qr-wrapper img {
        width: 220px;
        height: 220px;
        display: block;
    }
    .amount-display {
        font-size: 2rem;
        font-weight: 900;
        color: #69C9D0;
    }
    .amount-unit { font-size: 0.85rem; color: #6b7280; }

    .bank-info {
        padding: 1.25rem 2rem;
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }
    .bank-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.6rem 0;
        border-bottom: 1px solid rgba(255,255,255,0.04);
        font-size: 0.85rem;
    }
    .bank-row:last-child { border-bottom: none; }
    .bank-row .label { color: #6b7280; }
    .bank-row .value {
        font-weight: 700;
        color: #f9fafb;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .copy-btn {
        background: rgba(105,201,208,0.1);
        border: 1px solid rgba(105,201,208,0.2);
        color: #69C9D0;
        padding: 0.15rem 0.5rem;
        border-radius: 6px;
        font-size: 0.7rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .copy-btn:hover { background: rgba(105,201,208,0.2); }

    .content-highlight {
        background: rgba(105,201,208,0.08);
        border: 1px solid rgba(105,201,208,0.2);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        text-align: center;
        margin: 1rem 2rem;
    }
    .content-highlight .label { font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem; }
    .content-highlight .code {
        font-size: 1.2rem;
        font-weight: 900;
        color: #69C9D0;
        letter-spacing: 2px;
        font-family: monospace;
    }

    .confirm-section { padding: 1.5rem 2rem 2rem; }
    .confirm-check {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: rgba(16,185,129,0.06);
        border: 1px solid rgba(16,185,129,0.2);
        border-radius: 12px;
        padding: 1rem;
        cursor: pointer;
        margin-bottom: 1.25rem;
        transition: all 0.2s;
    }
    .confirm-check:hover { background: rgba(16,185,129,0.1); }
    .confirm-check input[type=checkbox] {
        width: 18px; height: 18px;
        accent-color: #10b981;
        cursor: pointer;
        flex-shrink: 0;
    }
    .confirm-check .text { font-size: 0.85rem; color: #d1d5db; line-height: 1.4; }

    .btn-confirm {
        width: 100%;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border: none;
        padding: 0.875rem;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Inter', sans-serif;
        opacity: 0.5;
        pointer-events: none;
    }
    .btn-confirm.enabled {
        opacity: 1;
        pointer-events: auto;
    }
    .btn-confirm.enabled:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(16,185,129,0.3); }

    .deadline-warning {
        margin: 0 2rem 1rem;
        padding: 0.75rem 1rem;
        background: rgba(245,158,11,0.08);
        border: 1px solid rgba(245,158,11,0.25);
        border-radius: 10px;
        font-size: 0.8rem;
        color: #f59e0b;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="payment-page">
<div class="container">

    @if(session('success'))
    <div style="text-align:center;margin-bottom:1.5rem;color:#10b981;font-size:0.9rem">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
    @endif

    <div style="text-align:center;margin-bottom:1.5rem">
        <div style="font-size:0.85rem;color:#6b7280">🔒 Giao dịch bảo mật qua VietQR</div>
    </div>

    <div class="payment-card">
        <!-- Header -->
        <div class="payment-header">
            <div style="font-size:0.75rem;text-transform:uppercase;letter-spacing:2px;color:#6b7280;margin-bottom:0.5rem">Mã đơn hàng</div>
            <div class="order-code">{{ $order->order_code }}</div>
            <div class="service-name">{{ $order->service->name }}</div>
        </div>

        <!-- QR -->
        <div class="qr-section">
            <div style="font-size:0.8rem;color:#6b7280;margin-bottom:1rem">Quét mã QR để thanh toán</div>
            <div class="qr-wrapper">
                <img src="{{ $qrUrl }}" alt="QR Thanh Toán" onerror="this.src='https://placehold.co/220x220/ffffff/000000?text=QR+Error'">
            </div>
            <div class="amount-display">{{ number_format((float)$order->amount, 0, ',', '.') }}</div>
            <div class="amount-unit">VNĐ · Quét bằng app ngân hàng bất kỳ</div>
        </div>

        <!-- Nội dung chuyển khoản highlight -->
        <div class="content-highlight">
            <div class="label"><i class="bi bi-exclamation-triangle-fill" style="color:#f59e0b"></i> Nội dung chuyển khoản BẮT BUỘC nhập đúng</div>
            <div class="code" id="transferContent">{{ $order->order_code }}</div>
            <button onclick="copyText('{{ $order->order_code }}')" class="copy-btn" style="margin-top:0.5rem">
                <i class="bi bi-copy"></i> Sao chép
            </button>
        </div>

        <!-- Thông tin ngân hàng -->
        <div class="bank-info">
            <div class="bank-row">
                <span class="label">Ngân hàng</span>
                <span class="value">{{ $settings['bank_name'] ?? 'MB Bank' }}</span>
            </div>
            <div class="bank-row">
                <span class="label">Số tài khoản</span>
                <span class="value">
                    <span id="bankAcc">{{ $settings['bank_account'] ?? '' }}</span>
                    <button class="copy-btn" onclick="copyText('{{ $settings['bank_account'] ?? '' }}')">
                        <i class="bi bi-copy"></i>
                    </button>
                </span>
            </div>
            <div class="bank-row">
                <span class="label">Chủ tài khoản</span>
                <span class="value">{{ $settings['bank_owner'] ?? '' }}</span>
            </div>
            <div class="bank-row">
                <span class="label">Số tiền</span>
                <span class="value" style="color:#69C9D0">{{ number_format((float)$order->amount, 0, ',', '.') }} VNĐ</span>
            </div>
        </div>

        <!-- Deadline warning -->
        @if($order->appeal_deadline)
        <div class="deadline-warning">
            <i class="bi bi-clock-fill"></i>
            Hạn kháng cáo: <strong>{{ $order->appeal_deadline->format('d/m/Y') }}</strong>
            ({{ $order->days_left >= 0 ? 'còn ' . $order->days_left . ' ngày' : 'đã hết hạn' }})
        </div>
        @endif

        <!-- Confirm -->
        <div class="confirm-section">
            <form action="{{ route('nav.confirm', $order->order_code) }}" method="POST">
            @csrf
            <label class="confirm-check">
                <input type="checkbox" id="confirmCheck" onchange="toggleConfirm(this)">
                <div class="text">
                    Tôi đã chuyển khoản <strong style="color:#69C9D0">{{ number_format((float)$order->amount, 0, ',', '.') }} VNĐ</strong>
                    với nội dung <strong style="color:#69C9D0">{{ $order->order_code }}</strong> đến tài khoản ngân hàng trên.
                </div>
            </label>
            <button type="submit" class="btn-confirm" id="confirmBtn">
                <i class="bi bi-check-circle-fill"></i> Xác Nhận Đã Thanh Toán
            </button>
            </form>
            <div style="text-align:center;margin-top:1rem;font-size:0.78rem;color:#6b7280">
                Admin sẽ xác minh và xử lý đơn trong thời gian sớm nhất. Cám ơn bạn!
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
function toggleConfirm(cb) {
    const btn = document.getElementById('confirmBtn');
    btn.classList.toggle('enabled', cb.checked);
}
function copyText(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Đã sao chép: ' + text, 'success');
    }).catch(() => {
        const ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        ta.remove();
        showToast('Đã sao chép!', 'success');
    });
}
function showToast(msg, type) {
    const t = document.createElement('div');
    t.style.cssText = 'position:fixed;bottom:2rem;right:2rem;background:#0d1117;border:1px solid rgba(105,201,208,0.3);color:#69C9D0;padding:0.875rem 1.25rem;border-radius:12px;font-size:0.875rem;z-index:9999;animation:fadeIn 0.3s';
    t.textContent = '✓ ' + msg;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 2500);
}
</script>
@endpush
