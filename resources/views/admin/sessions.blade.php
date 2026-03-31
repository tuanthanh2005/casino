@extends('layouts.admin')

@section('title', 'Quản lý Phiên Cược')

@section('admin-content')
<div class="page-header d-flex align-center justify-between">
    <div>
        <h1>🎮 Quản lý Phiên Cược</h1>
        <p>Tạo và chốt kết quả các phiên cược BTC</p>
    </div>
    <button onclick="createSession()" class="btn btn-primary">
        <i class="bi bi-play-circle"></i> Tạo phiên mới
    </button>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Giá mở</th>
                    <th>Giá đóng</th>
                    <th>Hướng</th>
                    <th>Trạng thái</th>
                    <th>Số cược</th>
                    <th>Thời gian mở</th>
                    <th>Thời gian đóng</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessions as $session)
                <tr>
                    <td>{{ $session->id }}</td>
                    <td>${{ number_format($session->start_price, 2) }}</td>
                    <td>
                        @if($session->end_price)
                            ${{ number_format($session->end_price, 2) }}
                        @else
                            <span style="color:var(--text-muted)">—</span>
                        @endif
                    </td>
                    <td>
                        @if($session->status === 'completed' && $session->end_price)
                            @if($session->end_price > $session->start_price)
                                <span style="color:#10b981; font-weight:700">▲ LONG</span>
                            @elseif($session->end_price < $session->start_price)
                                <span style="color:#ef4444; font-weight:700">▼ SHORT</span>
                            @else
                                <span style="color:var(--text-muted)">= Hòa</span>
                            @endif
                        @else
                            <span style="color:var(--text-muted)">—</span>
                        @endif
                    </td>
                    <td>
                        @if($session->status === 'pending')
                            <span class="badge badge-warning">Đang mở</span>
                        @else
                            <span class="badge badge-success">Đã chốt</span>
                        @endif
                    </td>
                    <td>{{ $session->bets_count }}</td>
                    <td style="color:var(--text-muted); font-size:0.8rem">{{ $session->start_time?->format('d/m H:i:s') }}</td>
                    <td style="color:var(--text-muted); font-size:0.8rem">{{ $session->end_time?->format('d/m H:i:s') }}</td>
                    <td>
                        @if($session->status === 'pending')
                            <button onclick="resolveSession({{ $session->id }})"
                                    class="btn btn-sm btn-danger"
                                    id="resolve-btn-{{ $session->id }}">
                                <i class="bi bi-flag"></i> Chốt ngay
                            </button>
                        @else
                            <span style="color:var(--text-muted); font-size:0.8rem">Hoàn tất</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center" style="color:var(--text-muted); padding:2rem">
                        Chưa có phiên nào. Tạo phiên mới để bắt đầu!
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($sessions->hasPages())
    <div style="padding:1rem 1.5rem; border-top:1px solid var(--border)">
        {{ $sessions->links() }}
    </div>
    @endif
</div>
@endsection

@push('admin-scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

async function createSession() {
    try {
        const resp = await fetch('/admin/sessions/create', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        const data = await resp.json();
        showToast(data.message, data.success ? 'success' : 'error');
        if (data.success) setTimeout(() => location.reload(), 1500);
    } catch (e) {
        showToast('Lỗi kết nối', 'error');
    }
}

async function resolveSession(sessionId) {
    const btn = document.getElementById(`resolve-btn-${sessionId}`);
    if (!btn) return;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass"></i> Đang chốt...';

    try {
        const resp = await fetch(`/admin/sessions/${sessionId}/resolve`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        const data = await resp.json();
        showToast(data.message, data.success ? 'success' : 'error');
        if (data.success) setTimeout(() => location.reload(), 1500);
    } catch (e) {
        showToast('Lỗi kết nối', 'error');
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-flag"></i> Chốt ngay';
}
</script>
@endpush
