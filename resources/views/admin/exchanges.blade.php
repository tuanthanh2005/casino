@extends('layouts.admin')

@section('title', 'Quản lý Đổi Quà')

@section('admin-content')
<div class="page-header">
    <h1>🎁 Quản lý Yêu Cầu Đổi Quà</h1>
    <p>Duyệt hoặc từ chối các yêu cầu đổi Point lấy phần thưởng</p>
</div>

<!-- Filter -->
<div class="card mb-3" style="margin-bottom:1.25rem">
    <div class="card-body" style="padding:1rem 1.5rem">
        <div style="display:flex; gap:0.5rem; flex-wrap:wrap">
            <a href="{{ route('admin.exchanges') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline' }}">
                Tất cả
            </a>
            <a href="{{ route('admin.exchanges', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') === 'pending' ? 'btn-warning' : 'btn-outline' }}">
                Chờ duyệt
            </a>
            <a href="{{ route('admin.exchanges', ['status' => 'approved']) }}" class="btn btn-sm {{ request('status') === 'approved' ? 'btn-success' : 'btn-outline' }}">
                Đã duyệt
            </a>
            <a href="{{ route('admin.exchanges', ['status' => 'rejected']) }}" class="btn btn-sm {{ request('status') === 'rejected' ? 'btn-danger' : 'btn-outline' }}">
                Từ chối
            </a>
        </div>
    </div>
</div>

<!-- Exchanges Table -->
<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Người dùng</th>
                    <th>Phần thưởng</th>
                    <th>Điểm đổi</th>
                    <th>Trạng thái</th>
                    <th>Ghi chú Admin</th>
                    <th>Ngày yêu cầu</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($exchanges as $ex)
                <tr>
                    <td>{{ $ex->id }}</td>
                    <td>
                        <div style="font-weight:600">{{ $ex->user?->name }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted)">{{ $ex->user?->email }}</div>
                    </td>
                    <td>{{ $ex->rewardItem?->name ?? 'N/A' }}</td>
                    <td><strong style="color:var(--accent)">{{ number_format($ex->points_spent, 0) }} PT</strong></td>
                    <td>{!! $ex->status_label !!}</td>
                    <td style="font-size:0.8rem; color:var(--text-muted)">{{ $ex->admin_note ?? '—' }}</td>
                    <td style="color:var(--text-muted)">{{ $ex->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($ex->status === 'pending')
                        <div style="display:flex; gap:0.5rem">
                            <button onclick="openExchangeModal({{ $ex->id }}, 'approve')"
                                    class="btn btn-sm btn-success">
                                <i class="bi bi-check2"></i> Duyệt
                            </button>
                            <button onclick="openExchangeModal({{ $ex->id }}, 'reject')"
                                    class="btn btn-sm btn-danger">
                                <i class="bi bi-x"></i> Từ chối
                            </button>
                        </div>
                        @else
                            <span style="color:var(--text-muted); font-size:0.8rem">Đã xử lý</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center" style="color:var(--text-muted); padding:2rem">
                        Không có yêu cầu nào
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($exchanges->hasPages())
    <div style="padding:1rem 1.5rem; border-top:1px solid var(--border)">
        {{ $exchanges->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Process Modal -->
<div class="modal-overlay" id="exchange-process-modal">
    <div class="modal-box">
        <div class="modal-title" id="process-modal-title">Xử lý yêu cầu</div>
        <p id="process-modal-desc" style="color:var(--text-muted); font-size:0.875rem; margin-bottom:1.5rem"></p>

        <div class="mb-3">
            <label style="font-size:0.8rem; color:var(--text-muted); display:block; margin-bottom:0.4rem">Ghi chú cho người dùng</label>
            <textarea id="process-admin-note" class="form-control" rows="3"
                      placeholder="VD: Tài khoản Netflix: user@email.com | Pass: 123456..."></textarea>
        </div>

        <div style="display:flex; gap:0.75rem">
            <button onclick="closeExchangeModal()" class="btn btn-outline" style="flex:1">Hủy</button>
            <button onclick="submitExchange()" class="btn btn-primary" style="flex:1" id="process-submit-btn">
                Xác nhận
            </button>
        </div>
    </div>
</div>
@endsection

@push('admin-scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let pendingExchangeId = null;
let pendingAction = null;

function openExchangeModal(id, action) {
    pendingExchangeId = id;
    pendingAction = action;

    if (action === 'approve') {
        document.getElementById('process-modal-title').textContent = '✅ Duyệt yêu cầu #' + id;
        document.getElementById('process-modal-desc').textContent = 'Nhập thông tin tài khoản/phần thưởng vào ghi chú để user nhận.';
        document.getElementById('process-submit-btn').className = 'btn btn-success';
    } else {
        document.getElementById('process-modal-title').textContent = '❌ Từ chối yêu cầu #' + id;
        document.getElementById('process-modal-desc').textContent = 'Điểm sẽ được hoàn lại cho người dùng tự động.';
        document.getElementById('process-submit-btn').className = 'btn btn-danger';
    }

    document.getElementById('process-submit-btn').textContent = action === 'approve' ? 'Duyệt & Gửi' : 'Từ chối & Hoàn điểm';
    document.getElementById('process-admin-note').value = '';
    document.getElementById('exchange-process-modal').classList.add('active');
}

function closeExchangeModal() {
    document.getElementById('exchange-process-modal').classList.remove('active');
}

async function submitExchange() {
    if (!pendingExchangeId || !pendingAction) return;

    const note = document.getElementById('process-admin-note').value.trim();
    const btn = document.getElementById('process-submit-btn');
    btn.disabled = true;

    const url = pendingAction === 'approve'
        ? `/admin/exchanges/${pendingExchangeId}/approve`
        : `/admin/exchanges/${pendingExchangeId}/reject`;

    try {
        const resp = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ admin_note: note })
        });

        const data = await resp.json();
        showToast(data.message, data.success ? 'success' : 'error');
        if (data.success) {
            closeExchangeModal();
            setTimeout(() => location.reload(), 1200);
        }
    } catch (e) {
        showToast('Lỗi kết nối', 'error');
    }

    btn.disabled = false;
}

document.getElementById('exchange-process-modal').addEventListener('click', function(e) {
    if (e.target === this) closeExchangeModal();
});
</script>
@endpush
