@extends('layouts.admin')
@section('title', 'Chi Tiết Đơn - ' . $order->order_code)

@section('admin-content')
<div class="page-header">
    <div class="d-flex align-center gap-3">
        <a href="{{ route('admin.nav.orders') }}" class="btn btn-outline btn-sm"><i class="bi bi-arrow-left"></i></a>
        <div>
            <h1>Chi Tiết Đơn <span style="color:#69C9D0;font-family:monospace">{{ $order->order_code }}</span></h1>
            <p>{{ $order->service->name ?? '-' }} · Tạo lúc {{ $order->created_at->format('H:i d/m/Y') }}</p>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
@endif

<div style="display:grid;grid-template-columns:1fr 360px;gap:1.5rem;align-items:start">

<!-- LEFT: Thông tin -->
<div>
    <!-- Status bar -->
    <div class="card mb-3" style="margin-bottom:1.5rem">
        <div class="card-body" style="display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap">
            <div>
                <div style="font-size:0.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px">Trạng thái</div>
                <span class="badge {{ $order->status_badge }}" style="font-size:0.85rem;padding:0.4rem 0.9rem;margin-top:0.3rem">{{ $order->status_label }}</span>
            </div>
            <div>
                <div style="font-size:0.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px">Số tiền</div>
                <div style="font-size:1.2rem;font-weight:900;color:#69C9D0;margin-top:0.2rem">{{ number_format((float)$order->amount, 0, ',', '.') }} PT</div>
            </div>
            <div>
                <div style="font-size:0.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px">Thanh toán</div>
                <div style="font-size:0.85rem;font-weight:600;margin-top:0.2rem">{{ $order->payment_method === 'points' ? '💎 PT' : '🏦 Ngân hàng' }}</div>
            </div>
            @if($order->appeal_deadline)
            <div>
                <div style="font-size:0.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px">Hạn kháng cáo</div>
                <div style="font-size:0.85rem;font-weight:700;margin-top:0.2rem;color:{{ $order->isExpired() ? '#ef4444' : ($order->days_left <= 7 ? '#f59e0b' : '#10b981') }}">
                    {{ $order->appeal_deadline->format('d/m/Y') }}
                    ({{ $order->isExpired() ? 'ĐÃ HẾT HẠN' : 'còn ' . $order->days_left . ' ngày' }})
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- TikTok Info -->
    <div class="card mb-3" style="margin-bottom:1.5rem">
        <div class="card-header"><i class="bi bi-tiktok"></i> Thông Tin TikTok</div>
        <div class="card-body">
            <div class="grid-2">
                <div>
                    <div style="font-size:0.75rem;color:var(--text-muted)">Username</div>
                    <div style="font-weight:700;font-size:1.05rem;color:#69C9D0;margin-top:0.2rem">{{ $order->tiktok_username }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem;color:var(--text-muted)">Followers</div>
                    <div style="font-weight:600;margin-top:0.2rem">{{ $order->follower_count ? number_format($order->follower_count) : 'N/A' }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem;color:var(--text-muted)">Email đăng ký</div>
                    <div style="font-weight:500;margin-top:0.2rem">{{ $order->registered_email ?: 'N/A' }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem;color:var(--text-muted)">SĐT đăng ký</div>
                    <div style="font-weight:500;margin-top:0.2rem">{{ $order->registered_phone ?: 'N/A' }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem;color:var(--text-muted)">Loại vi phạm</div>
                    <div style="font-weight:500;margin-top:0.2rem">{{ $order->violation_type ?: 'Không rõ' }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem;color:var(--text-muted)">Ngày bị khóa</div>
                    <div style="font-weight:500;margin-top:0.2rem">{{ $order->violation_date ? $order->violation_date->format('d/m/Y') : 'N/A' }}</div>
                </div>
            </div>
            @if($order->account_notes)
            <div class="mt-3" style="margin-top:1rem">
                <div style="font-size:0.75rem;color:var(--text-muted);margin-bottom:0.3rem">Ghi chú thêm</div>
                <div style="background:var(--bg-card2);border-radius:8px;padding:0.75rem;font-size:0.875rem;color:var(--text)">{{ $order->account_notes }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Khách hàng -->
    <div class="card mb-3" style="margin-bottom:1.5rem">
        <div class="card-header"><i class="bi bi-person"></i> Thông Tin Khách Hàng</div>
        <div class="card-body">
            <div class="grid-2">
                <div>
                    <div style="font-size:0.75rem;color:var(--text-muted)">Họ tên</div>
                    <div style="font-weight:600;margin-top:0.2rem">{{ $order->customer_name }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem;color:var(--text-muted)">SĐT/Zalo</div>
                    <div style="font-weight:600;margin-top:0.2rem">{{ $order->customer_contact }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem;color:var(--text-muted)">Tài khoản web</div>
                    <div style="font-weight:500;margin-top:0.2rem">{{ $order->user->name ?? 'N/A' }} ({{ $order->user->email ?? '' }})</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ảnh đính kèm -->
    <div class="card mb-3" style="margin-bottom:1.5rem">
        <div class="card-header"><i class="bi bi-images"></i> Ảnh Xác Minh</div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem">
                @foreach([['id_card_front','CCCD Mặt trước','bi-credit-card'], ['id_card_back','CCCD Mặt sau','bi-credit-card-2-back'], ['screenshot_path','Màn hình bị khóa','bi-phone']] as [$field, $label, $icon])
                <div style="text-align:center">
                    <div style="font-size:0.75rem;color:var(--text-muted);margin-bottom:0.5rem"><i class="bi {{ $icon }}"></i> {{ $label }}</div>
                    @php($fieldUrl = $order->{$field . '_url'})
                    @if($fieldUrl)
                    <a href="{{ $fieldUrl }}" target="_blank">
                        <img src="{{ $fieldUrl }}" style="width:100%;max-height:150px;object-fit:cover;border-radius:10px;border:1px solid var(--border)">
                    </a>
                    @else
                    <div style="background:var(--bg-card2);border:1px dashed var(--border);border-radius:10px;height:100px;display:flex;align-items:center;justify-content:center;color:var(--text-muted);font-size:0.8rem">Không có</div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ⭐ Tạo báo cáo kháng cáo -->
    <div class="card">
        <div class="card-header" style="background:rgba(105,201,208,0.06)">
            <span><i class="bi bi-file-earmark-text" style="color:#69C9D0"></i> Tạo Báo Cáo Kháng Cáo TikTok</span>
            <button class="btn btn-primary btn-sm" onclick="generateAppeal()">
                <i class="bi bi-magic"></i> Tạo Ngay
            </button>
        </div>
        <div class="card-body">
            <div id="appealLoading" style="display:none;text-align:center;padding:1rem;color:var(--text-muted)">
                <i class="bi bi-arrow-repeat" style="animation:spin 1s linear infinite"></i> Đang tạo báo cáo...
            </div>
            <div id="appealOutput" style="display:none">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem">
                    <div style="font-size:0.8rem;color:#10b981"><i class="bi bi-check-circle"></i> Báo cáo đã sẵn sàng — Copy và paste vào form kháng cáo TikTok</div>
                    <button class="btn btn-success btn-sm" onclick="copyAppeal()"><i class="bi bi-clipboard"></i> Copy toàn bộ</button>
                </div>
                <textarea id="appealText" style="width:100%;background:#0a0f1a;border:1px solid rgba(105,201,208,0.2);border-radius:10px;color:#d1fae5;padding:1rem;font-size:0.82rem;font-family:monospace;line-height:1.6;min-height:400px;resize:vertical" readonly></textarea>
                <div style="margin-top:0.75rem;padding:0.75rem;background:rgba(99,102,241,0.06);border-radius:8px;font-size:0.78rem;color:var(--text-muted)">
                    <i class="bi bi-info-circle" style="color:var(--primary)"></i>
                    <strong>Hướng dẫn gửi:</strong>
                    Gửi đến <strong style="color:#69C9D0">feedback@tiktok.com</strong> hoặc tại
                    <a href="https://www.tiktok.com/legal/report/feedback" target="_blank" style="color:#69C9D0">tiktok.com/legal/report/feedback</a>.
                    Đính kèm ảnh CCCD 2 mặt của chủ tài khoản.
                </div>
            </div>
            <div id="appealEmpty" style="color:var(--text-muted);font-size:0.875rem;text-align:center;padding:1rem">
                <i class="bi bi-robot" style="font-size:2rem;display:block;margin-bottom:0.5rem;color:#69C9D0"></i>
                Nhấn nút <strong>"Tạo Ngay"</strong> để hệ thống tự động soạn bài kháng cáo chuẩn TikTok Trust & Safety
            </div>
        </div>
    </div>
</div>

<!-- RIGHT: Actions -->
<div>
    <!-- Cập nhật trạng thái -->
    <div class="card mb-3" style="margin-bottom:1rem">
        <div class="card-header">⚙️ Cập Nhật Trạng Thái</div>
        <div class="card-body">
            @if($order->status === 'paid')
            <form action="{{ route('admin.nav.orders.approve', $order->id) }}" method="POST" class="mb-3" style="margin-bottom:0.75rem">
                @csrf
                <div style="font-size:0.8rem;color:var(--text-muted);margin-bottom:0.5rem">Ghi chú admin</div>
                <textarea name="admin_notes" class="form-control" rows="2" placeholder="Ghi chú..." style="margin-bottom:0.5rem">{{ $order->admin_notes }}</textarea>
                <button type="submit" class="btn btn-success w-100"><i class="bi bi-check-circle"></i> Xác Nhận Thanh Toán & Xử Lý</button>
            </form>
            @endif

            @if($order->status === 'processing')
            <form action="{{ route('admin.nav.orders.complete', $order->id) }}" method="POST" class="mb-3" style="margin-bottom:0.75rem">
                @csrf
                <div style="font-size:0.8rem;color:var(--text-muted);margin-bottom:0.5rem">Ghi chú kết quả</div>
                <textarea name="admin_notes" class="form-control" rows="2" placeholder="Kháng cáo đã gửi lúc...">{{ $order->admin_notes }}</textarea>
                <button type="submit" class="btn btn-primary w-100 mt-3" style="margin-top:0.5rem"><i class="bi bi-trophy"></i> Đánh Dấu Hoàn Thành</button>
            </form>
            @endif

            <form action="{{ route('admin.nav.orders.status', $order->id) }}" method="POST">
                @csrf
                <div style="font-size:0.8rem;color:var(--text-muted);margin-bottom:0.5rem">Thay đổi trạng thái thủ công</div>
                <select name="status" class="form-control" style="margin-bottom:0.5rem">
                    @foreach(['pending_payment'=>'⏳ Chờ thanh toán','paid'=>'💰 Đã thanh toán','processing'=>'🔄 Đang xử lý','completed'=>'✅ Hoàn thành','cancelled'=>'❌ Huỷ'] as $v => $l)
                    <option value="{{ $v }}" {{ $order->status === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
                <textarea name="admin_notes" class="form-control" rows="2" placeholder="Ghi chú..." style="margin-bottom:0.5rem">{{ $order->admin_notes }}</textarea>
                <button type="submit" class="btn btn-outline w-100">Lưu Trạng Thái</button>
            </form>
        </div>
    </div>

    <!-- Timeline -->
    <div class="card">
        <div class="card-header">📅 Timeline</div>
        <div class="card-body" style="font-size:0.82rem">
            <div style="display:flex;flex-direction:column;gap:0.75rem">
                <div style="display:flex;gap:0.75rem;align-items:flex-start">
                    <div style="width:8px;height:8px;border-radius:50%;background:var(--primary);margin-top:4px;flex-shrink:0"></div>
                    <div>
                        <div style="font-weight:600">Tạo đơn</div>
                        <div style="color:var(--text-muted)">{{ $order->created_at->format('H:i d/m/Y') }}</div>
                    </div>
                </div>
                @if($order->payment_confirmed_at)
                <div style="display:flex;gap:0.75rem;align-items:flex-start">
                    <div style="width:8px;height:8px;border-radius:50%;background:var(--accent);margin-top:4px;flex-shrink:0"></div>
                    <div>
                        <div style="font-weight:600">Khách xác nhận TT</div>
                        <div style="color:var(--text-muted)">{{ $order->payment_confirmed_at->format('H:i d/m/Y') }}</div>
                    </div>
                </div>
                @endif
                @if($order->payment_verified_at)
                <div style="display:flex;gap:0.75rem;align-items:flex-start">
                    <div style="width:8px;height:8px;border-radius:50%;background:var(--success);margin-top:4px;flex-shrink:0"></div>
                    <div>
                        <div style="font-weight:600">Admin xác minh TT</div>
                        <div style="color:var(--text-muted)">{{ $order->payment_verified_at->format('H:i d/m/Y') }}</div>
                    </div>
                </div>
                @endif
                @if($order->appeal_sent_at)
                <div style="display:flex;gap:0.75rem;align-items:flex-start">
                    <div style="width:8px;height:8px;border-radius:50%;background:#69C9D0;margin-top:4px;flex-shrink:0"></div>
                    <div>
                        <div style="font-weight:600">Đã gửi kháng cáo TikTok</div>
                        <div style="color:var(--text-muted)">{{ $order->appeal_sent_at->format('H:i d/m/Y') }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

</div>
@endsection

@push('admin-styles')
<style>
@keyframes spin { 0%{transform:rotate(0deg)} 100%{transform:rotate(360deg)} }
</style>
@endpush

@push('admin-scripts')
<script>
async function generateAppeal() {
    document.getElementById('appealEmpty').style.display = 'none';
    document.getElementById('appealOutput').style.display = 'none';
    document.getElementById('appealLoading').style.display = 'block';
    try {
        const res = await fetch('{{ route("admin.nav.orders.appeal", $order->id) }}');
        const data = await res.json();
        document.getElementById('appealText').value = data.letter;
        document.getElementById('appealLoading').style.display = 'none';
        document.getElementById('appealOutput').style.display = 'block';
    } catch(e) {
        document.getElementById('appealLoading').style.display = 'none';
        document.getElementById('appealEmpty').style.display = 'block';
        showToast('Lỗi tạo báo cáo!', 'error');
    }
}

function copyAppeal() {
    const text = document.getElementById('appealText').value;
    navigator.clipboard.writeText(text).then(() => {
        showToast('Đã copy toàn bộ báo cáo kháng cáo!', 'success');
    });
}
</script>
@endpush
