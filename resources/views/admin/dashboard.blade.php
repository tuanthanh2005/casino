@extends('layouts.admin')

@section('title', 'Dashboard')

@section('admin-content')
<div class="page-header">
    <h1>📊 Dashboard</h1>
    <p>Tổng quan hệ thống CryptoBet</p>
</div>

<!-- STATS GRID -->
<div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:1.25rem; margin-bottom:2rem">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(99,102,241,0.15)">
            <i class="bi bi-people" style="color:#818cf8"></i>
        </div>
        <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
        <div class="stat-label">Người dùng</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,0.15)">
            <i class="bi bi-coin" style="color:#f59e0b"></i>
        </div>
        <div class="stat-value">{{ number_format($stats['total_balance'], 0) }}</div>
        <div class="stat-label">Point đang lưu hành</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(16,185,129,0.15)">
            <i class="bi bi-lightning" style="color:#10b981"></i>
        </div>
        <div class="stat-value">{{ number_format($stats['total_bets_today']) }}</div>
        <div class="stat-label">Cược hôm nay</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(239,68,68,0.15)">
            <i class="bi bi-gift" style="color:#ef4444"></i>
        </div>
        <div class="stat-value">{{ $stats['pending_exchanges'] }}</div>
        <div class="stat-label">Đổi quà chờ duyệt</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(168,85,247,0.15)">
            <i class="bi bi-controller" style="color:#c084fc"></i>
        </div>
        <div class="stat-value">{{ $stats['total_sessions'] }}</div>
        <div class="stat-label">Tổng phiên cược</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(6,182,212,0.15)">
            <i class="bi bi-activity" style="color:#22d3ee"></i>
        </div>
        <div class="stat-value">{{ $stats['active_sessions'] > 0 ? 'Đang mở' : 'Không có' }}</div>
        <div class="stat-label">Phiên hiện tại</div>
    </div>
</div>

<!-- QUICK ACTIONS -->
<div class="card mb-4" style="margin-bottom:1.5rem">
    <div class="card-header">
        <span><i class="bi bi-zap"></i> Hành động nhanh</span>
    </div>
    <div class="card-body">
        <div style="display:flex; flex-wrap:wrap; gap:0.75rem">
            <button onclick="createSession()" class="btn btn-primary">
                <i class="bi bi-play-circle"></i> Tạo phiên mới
            </button>
            <a href="{{ route('admin.sessions') }}" class="btn btn-outline">
                <i class="bi bi-list-check"></i> Xem tất cả phiên
            </a>
            <a href="{{ route('admin.exchanges') }}?status=pending" class="btn btn-warning">
                <i class="bi bi-gift"></i> Xử lý đổi quà ({{ $stats['pending_exchanges'] }})
            </a>
            <a href="{{ route('admin.users') }}" class="btn btn-outline">
                <i class="bi bi-people"></i> Quản lý User
            </a>
        </div>
    </div>
</div>

<!-- RECENT BETS -->
<div class="card">
    <div class="card-header">
        <span><i class="bi bi-clock-history"></i> Lịch sử cược gần đây</span>
        <a href="{{ route('admin.sessions') }}" class="btn btn-sm btn-outline">Xem tất cả</a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Phiên</th>
                    <th>Cửa</th>
                    <th>Số điểm</th>
                    <th>Kết quả</th>
                    <th>Lợi nhuận</th>
                    <th>Thời gian</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentBets as $bet)
                <tr>
                    <td>{{ $bet->id }}</td>
                    <td>
                        <div style="font-weight:600">{{ $bet->user?->name }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted)">{{ $bet->user?->email }}</div>
                    </td>
                    <td>#{{ $bet->session_id }}</td>
                    <td>
                        @if($bet->bet_type === 'long')
                            <span style="color:#10b981; font-weight:700">▲ LONG</span>
                        @else
                            <span style="color:#ef4444; font-weight:700">▼ SHORT</span>
                        @endif
                    </td>
                    <td>{{ number_format($bet->bet_amount, 0) }} PT</td>
                    <td>
                        @if($bet->status === 'won')
                            <span class="badge badge-success">Thắng</span>
                        @elseif($bet->status === 'lost')
                            <span class="badge badge-danger">Thua</span>
                        @else
                            <span class="badge badge-warning">Chờ</span>
                        @endif
                    </td>
                    <td>
                        @if($bet->status === 'won')
                            <span style="color:#10b981">+{{ number_format($bet->profit, 2) }}</span>
                        @elseif($bet->status === 'lost')
                            <span style="color:#ef4444">-{{ number_format($bet->bet_amount, 0) }}</span>
                        @else
                            —
                        @endif
                    </td>
                    <td style="color:var(--text-muted)">{{ $bet->created_at->format('d/m H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center" style="color:var(--text-muted); padding:2rem">Chưa có cược nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
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
</script>
@endpush
