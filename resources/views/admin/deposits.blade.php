@extends('layouts.admin')
@section('title', 'Quản lý Nạp Tiền')

@section('admin-content')
<div class="page-header d-flex justify-between align-center">
    <div>
        <h1>💳 Quản lý Nạp Tiền</h1>
        <p>Duyệt đơn nạp Bank QR và thẻ cào</p>
    </div>
</div>

{{-- STATS --}}
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,0.15)"><i class="bi bi-hourglass-split" style="color:#f59e0b"></i></div>
        <div class="stat-value" style="color:#f59e0b">{{ $stats['pending'] }}</div>
        <div class="stat-label">Đang chờ duyệt</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(16,185,129,0.15)"><i class="bi bi-coin" style="color:#10b981"></i></div>
        <div class="stat-value" style="color:#10b981">{{ number_format((float)$stats['today'], 0) }} PT</div>
        <div class="stat-label">Cấp hôm nay</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(99,102,241,0.15)"><i class="bi bi-wallet2" style="color:#818cf8"></i></div>
        <div class="stat-value" style="color:#818cf8">{{ number_format((float)$stats['total'], 0) }} PT</div>
        <div class="stat-label">Tổng đã nạp</div>
    </div>
</div>

{{-- TABLE --}}
<div class="card">
    <div class="card-header"><span><i class="bi bi-list-check"></i> Danh sách đơn nạp</span></div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Mã đơn</th><th>Người dùng</th><th>Phương thức</th>
                    <th>Số tiền</th><th>Chi tiết thẻ</th><th>Trạng thái</th><th>Thời gian</th><th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deposits as $d)
                <tr>
                    <td><code style="color:var(--primary)">{{ $d->order_code }}</code></td>
                    <td>
                        <div style="font-weight:600">{{ $d->user?->name }}</div>
                        <div style="font-size:0.72rem; color:var(--text-muted)">{{ $d->user?->email }}</div>
                    </td>
                    <td>{{ $d->method_label }}</td>
                    <td style="font-weight:700; color:var(--accent)">{{ number_format($d->amount, 0) }} đ</td>
                    <td style="font-size:0.8rem">
                        @if($d->method === 'card')
                            <div><span style="color:var(--text-muted)">Loại:</span> {{ strtoupper($d->card_type ?? '—') }}</div>
                            <div><span style="color:var(--text-muted)">MệnhGiá:</span> {{ number_format($d->card_amount,0) }}đ</div>
                            <div><span style="color:var(--text-muted)">Serial:</span> <code>{{ $d->card_serial }}</code></div>
                            <div><span style="color:var(--text-muted)">PIN:</span> <code>{{ $d->card_pin }}</code></div>
                        @else
                            <span style="color:var(--text-muted)">—</span>
                        @endif
                    </td>
                    <td>
                        @if($d->status==='pending')
                            <span class="badge badge-warning">⏳ Chờ duyệt</span>
                        @elseif($d->status==='approved')
                            <span class="badge badge-success">✅ Đã duyệt</span>
                            <div style="font-size:0.72rem; color:#10b981; margin-top:0.2rem">+{{ number_format($d->points_credited,0) }} PT</div>
                        @else
                            <span class="badge badge-danger">❌ Từ chối</span>
                        @endif
                        @if($d->admin_note)
                            <div style="font-size:0.72rem; color:var(--text-muted); margin-top:0.2rem">{{ $d->admin_note }}</div>
                        @endif
                    </td>
                    <td style="color:var(--text-muted); font-size:0.75rem">{{ $d->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($d->status === 'pending')
                        <div style="display:flex; gap:0.4rem; flex-wrap:wrap">
                            <button onclick="approveDeposit({{ $d->id }})" class="btn btn-success btn-sm">✅ Duyệt</button>
                            <button onclick="rejectDeposit({{ $d->id }})" class="btn btn-danger btn-sm">❌ Từ chối</button>
                        </div>
                        @else
                            <span style="color:var(--text-muted); font-size:0.8rem">Đã xử lý</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center" style="padding:2rem; color:var(--text-muted)">Chưa có đơn nạp nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:1rem 1.5rem">{{ $deposits->links() }}</div>
</div>

{{-- MODAL GHI CHÚ --}}
<div class="modal-overlay" id="deposit-modal">
    <div class="modal-box">
        <div class="modal-title" id="modal-title">Xác nhận hành động</div>
        <div style="margin-bottom:1rem; font-size:0.875rem; color:var(--text-muted)" id="modal-body"></div>
        <div style="margin-bottom:1rem">
            <label style="font-size:0.8rem; color:var(--text-muted); display:block; margin-bottom:0.4rem">Ghi chú (không bắt buộc)</label>
            <input type="text" id="modal-note" class="form-control" placeholder="Nhập ghi chú...">
        </div>
        <div style="display:flex; gap:0.75rem; justify-content:flex-end">
            <button onclick="closeModal()" class="btn btn-outline">Hủy</button>
            <button id="modal-confirm" class="btn btn-primary">Xác nhận</button>
        </div>
    </div>
</div>
@endsection

@push('admin-scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let pendingAction = null;

function closeModal() {
    document.getElementById('deposit-modal').classList.remove('active');
    pendingAction = null;
}
function openModal(title, body, confirmText, confirmClass, action) {
    document.getElementById('modal-title').textContent = title;
    document.getElementById('modal-body').textContent = body;
    document.getElementById('modal-note').value = '';
    const btn = document.getElementById('modal-confirm');
    btn.textContent = confirmText;
    btn.className = `btn ${confirmClass}`;
    pendingAction = action;
    document.getElementById('deposit-modal').classList.add('active');
}

document.getElementById('modal-confirm').onclick = async () => {
    if (!pendingAction) return;
    const note = document.getElementById('modal-note').value;
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

function approveDeposit(id) {
    openModal('✅ Duyệt đơn nạp', 'Xác nhận duyệt và cấp điểm cho người dùng?', 'Duyệt ngay', 'btn-success',
        note => post(`/admin/deposits/${id}/approve`, note));
}
function rejectDeposit(id) {
    openModal('❌ Từ chối đơn nạp','Xác nhận từ chối đơn này?','Từ chối','btn-danger',
        note => post(`/admin/deposits/${id}/reject`, note));
}
</script>
@endpush
