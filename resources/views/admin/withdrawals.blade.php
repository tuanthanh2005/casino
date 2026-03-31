@extends('layouts.admin')
@section('title', 'Quản lý Rút Tiền')

@section('admin-content')
<div class="page-header d-flex justify-between align-center">
    <div>
        <h1>💸 Quản lý Rút / Đổi Tiền</h1>
        <p>Xử lý đơn chuyển khoản và đổi thẻ cào · Thuế 2%</p>
    </div>
</div>

{{-- STATS --}}
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,0.15)"><i class="bi bi-hourglass-split" style="color:#f59e0b"></i></div>
        <div class="stat-value" style="color:#f59e0b">{{ $stats['pending'] }}</div>
        <div class="stat-label">Đang chờ xử lý</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(239,68,68,0.15)"><i class="bi bi-arrow-up-right-circle" style="color:#ef4444"></i></div>
        <div class="stat-value" style="color:#ef4444">{{ number_format((float)$stats['pending_pts'], 0) }} PT</div>
        <div class="stat-label">Tổng xu chờ chi trả</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(16,185,129,0.15)"><i class="bi bi-check-circle" style="color:#10b981"></i></div>
        <div class="stat-value" style="color:#10b981">{{ number_format((float)$stats['paid_today'], 0) }}</div>
        <div class="stat-label">Đã chi hôm nay</div>
    </div>
</div>

{{-- TABLE --}}
<div class="card">
    <div class="card-header"><span><i class="bi bi-list-check"></i> Danh sách đơn rút</span></div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Mã đơn</th><th>Người dùng</th><th>Phương thức</th>
                    <th>Điểm rút</th><th>Thuế</th><th>Thực chi</th>
                    <th>Thông tin chi trả</th><th>Trạng thái</th><th>Thời gian</th><th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($withdrawals as $w)
                <tr>
                    <td><code style="color:var(--primary)">{{ $w->order_code }}</code></td>
                    <td>
                        <div style="font-weight:600">{{ $w->user?->name }}</div>
                        <div style="font-size:0.72rem; color:var(--text-muted)">{{ $w->user?->email }}</div>
                    </td>
                    <td>{{ $w->method_label }}</td>
                    <td>{{ number_format($w->points_used, 0) }} PT</td>
                    <td style="color:#ef4444">-{{ number_format($w->tax_amount, 0) }}</td>
                    <td style="font-weight:800; color:#10b981">{{ number_format($w->net_amount, 0) }}</td>
                    <td style="font-size:0.8rem">
                        @if($w->method === 'bank_transfer')
                            <div><span style="color:var(--text-muted)">NH:</span> <strong>{{ $w->bank_name }}</strong></div>
                            <div><span style="color:var(--text-muted)">STK:</span> <code>{{ $w->bank_account }}</code></div>
                            <div><span style="color:var(--text-muted)">Tên:</span> {{ $w->bank_holder }}</div>
                        @else
                            <div><span style="color:var(--text-muted)">Loại thẻ:</span> <strong>{{ strtoupper($w->card_type ?? '—') }}</strong></div>
                            <div style="color:var(--text-muted)">Mệnh giá: {{ number_format($w->net_amount,0) }} đ</div>
                        @endif
                    </td>
                    <td>
                        @if($w->status==='pending')
                            <span class="badge badge-warning">⏳ Chờ xử lý</span>
                        @elseif($w->status==='approved')
                            <span class="badge badge-success">✅ Hoàn thành</span>
                        @else
                            <span class="badge badge-danger">❌ Từ chối</span>
                            @if($w->admin_note)
                            <div style="font-size:0.72rem; color:var(--text-muted)">{{ $w->admin_note }}</div>
                            @endif
                        @endif
                    </td>
                    <td style="color:var(--text-muted); font-size:0.75rem">{{ $w->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($w->status === 'pending')
                        <div style="display:flex; gap:0.4rem; flex-wrap:wrap">
                            <button onclick="approveWithdrawal({{ $w->id }},'{{ $w->method }}')" class="btn btn-success btn-sm">✅ Xong</button>
                            <button onclick="rejectWithdrawal({{ $w->id }})" class="btn btn-danger btn-sm">↩️ Hoàn</button>
                        </div>
                        @else
                            <span style="color:var(--text-muted); font-size:0.8rem">Đã xử lý</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center" style="padding:2rem; color:var(--text-muted)">Chưa có đơn rút nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:1rem 1.5rem">{{ $withdrawals->links() }}</div>
</div>

{{-- MODAL --}}
<div class="modal-overlay" id="wd-modal">
    <div class="modal-box">
        <div class="modal-title" id="wd-title">Xác nhận</div>
        <div style="margin-bottom:1rem; font-size:0.875rem; color:var(--text-muted)" id="wd-body"></div>
        <div style="margin-bottom:1rem">
            <label style="font-size:0.8rem; color:var(--text-muted); display:block; margin-bottom:0.4rem">Ghi chú</label>
            <input type="text" id="wd-note" class="form-control" placeholder="Nhập ghi chú...">
        </div>
        <div style="display:flex; gap:0.75rem; justify-content:flex-end">
            <button onclick="closeModal()" class="btn btn-outline">Hủy</button>
            <button id="wd-confirm" class="btn btn-primary">Xác nhận</button>
        </div>
    </div>
</div>
@endsection

@push('admin-scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let pendingAction = null;

function closeModal() {
    document.getElementById('wd-modal').classList.remove('active');
    pendingAction = null;
}
function openModal(title, body, confirmText, cls, action) {
    document.getElementById('wd-title').textContent = title;
    document.getElementById('wd-body').textContent  = body;
    document.getElementById('wd-note').value = '';
    const btn = document.getElementById('wd-confirm');
    btn.textContent = confirmText;
    btn.className = `btn ${cls}`;
    pendingAction = action;
    document.getElementById('wd-modal').classList.add('active');
}
document.getElementById('wd-confirm').onclick = async () => {
    if (!pendingAction) return;
    const note = document.getElementById('wd-note').value;
    const data = await pendingAction(note);
    showToast(data.message, data.success ? 'success' : 'error');
    if (data.success) setTimeout(() => location.reload(), 1200);
    closeModal();
};

async function post(url, note) {
    const r = await fetch(url, {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
        body: JSON.stringify({ admin_note: note })
    });
    return r.json();
}

function approveWithdrawal(id, method) {
    const msg = method === 'card'
        ? 'Xác nhận đã gửi thẻ cào cho người dùng?'
        : 'Xác nhận đã chuyển khoản thành công?';
    openModal('✅ Hoàn thành đơn rút', msg, 'Xác nhận hoàn thành', 'btn-success',
        note => post(`/admin/withdrawals/${id}/approve`, note));
}
function rejectWithdrawal(id) {
    openModal('↩️ Từ chối & Hoàn điểm', 'Từ chối đơn rút và hoàn lại điểm cho người dùng?',
        'Từ chối & Hoàn', 'btn-danger',
        note => post(`/admin/withdrawals/${id}/reject`, note));
}
</script>
@endpush
