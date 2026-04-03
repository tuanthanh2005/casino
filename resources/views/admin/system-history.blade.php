@extends('layouts.admin')

@section('title', 'Lịch sử toàn hệ thống')

@section('admin-content')
<div class="page-header">
    <h1>🧾 Lịch sử toàn hệ thống</h1>
    <p>Timeline tổng hợp toàn bộ giao dịch người chơi để admin đối soát lỗi, kiểm tra số tiền/point và thời gian phát sinh.</p>
</div>

<div class="card" style="margin-bottom:1rem;">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.system.history') }}" style="display:grid; grid-template-columns:repeat(6, minmax(0, 1fr)); gap:0.75rem;">
            <div>
                <label class="form-label-admin">Người chơi</label>
                <select name="user_id" class="form-control">
                    <option value="0">Tất cả</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ (int)$filters['user_id'] === (int)$u->id ? 'selected' : '' }}>
                            {{ $u->name }} (#{{ $u->id }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label-admin">Nguồn</label>
                <select name="source" class="form-control">
                    <option value="">Tất cả</option>
                    @foreach($sources as $value => $label)
                        <option value="{{ $value }}" {{ $filters['source'] === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label-admin">Trạng thái</label>
                <select name="status" class="form-control">
                    <option value="">Tất cả</option>
                    @foreach(['pending','approved','rejected','completed','cancelled','won','lost','done','paid','processing'] as $st)
                        <option value="{{ $st }}" {{ $filters['status'] === $st ? 'selected' : '' }}>{{ strtoupper($st) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label-admin">Từ ngày</label>
                <input type="date" name="from" class="form-control" value="{{ $filters['from'] }}">
            </div>

            <div>
                <label class="form-label-admin">Đến ngày</label>
                <input type="date" name="to" class="form-control" value="{{ $filters['to'] }}">
            </div>

            <div>
                <label class="form-label-admin">Tìm nhanh</label>
                <input type="text" name="q" class="form-control" value="{{ $filters['q'] }}" placeholder="Tên user / mã đơn / ghi chú">
            </div>

            <div style="grid-column:1 / -1; display:flex; gap:0.5rem; justify-content:flex-end;">
                <a href="{{ route('admin.system.history') }}" class="btn btn-outline">Reset</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Lọc dữ liệu</button>
            </div>
        </form>
    </div>
</div>

<div class="grid-3" style="grid-template-columns:repeat(3,1fr); margin-bottom:1rem;">
    <div class="stat-card">
        <div class="stat-label">Tổng bản ghi</div>
        <div class="stat-value" style="font-size:1.45rem; color:var(--primary)">{{ number_format($summary['total_rows']) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Tổng biến động Point</div>
        <div class="stat-value" style="font-size:1.45rem; color:{{ $summary['sum_pt'] >= 0 ? '#10b981' : '#ef4444' }}">
            {{ $summary['sum_pt'] >= 0 ? '+' : '' }}{{ number_format($summary['sum_pt'], 0) }} PT
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Tổng tiền VNĐ (log)</div>
        <div class="stat-value" style="font-size:1.45rem; color:#f59e0b">{{ number_format($summary['sum_vnd'], 0) }} đ</div>
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Thời gian</th>
                    <th>Người chơi</th>
                    <th>Nguồn</th>
                    <th>Hành động</th>
                    <th>Trạng thái</th>
                    <th>Biến động PT</th>
                    <th>Số tiền (VNĐ)</th>
                    <th>Mã tham chiếu</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td style="white-space:nowrap">{{ \Carbon\Carbon::parse($log->occurred_at)->format('d/m/Y H:i:s') }}</td>
                        <td>
                            <div style="font-weight:600">{{ $log->user_name }}</div>
                            <div style="font-size:0.72rem; color:var(--text-muted)">UID: {{ $log->user_id }}</div>
                        </td>
                        <td><span class="badge badge-primary">{{ $sources[$log->source] ?? $log->source }}</span></td>
                        <td>{{ $log->action }}</td>
                        <td>
                            @php
                                $st = strtolower((string) $log->status);
                                $stClass = str_contains($st, 'approve') || str_contains($st, 'won') || str_contains($st, 'complete') || $st === 'done'
                                    ? 'badge-success'
                                    : (str_contains($st, 'reject') || str_contains($st, 'lost') || str_contains($st, 'cancel')
                                        ? 'badge-danger'
                                        : 'badge-warning');
                            @endphp
                            <span class="badge {{ $stClass }}">{{ strtoupper((string) $log->status) }}</span>
                        </td>
                        <td style="font-weight:700; color:{{ (float)$log->amount_pt >= 0 ? '#10b981' : '#ef4444' }}">
                            {{ (float)$log->amount_pt > 0 ? '+' : '' }}{{ number_format((float)$log->amount_pt, 0) }}
                        </td>
                        <td>{{ $log->amount_vnd !== null ? number_format((float)$log->amount_vnd, 0) . ' đ' : '—' }}</td>
                        <td style="white-space:nowrap; font-size:0.78rem">{{ $log->reference }}</td>
                        <td style="max-width:280px; color:var(--text-muted)">{{ \Illuminate\Support\Str::limit($log->note ?: '—', 120) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center" style="padding:2rem; color:var(--text-muted)">Không có dữ liệu theo bộ lọc hiện tại.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div style="padding:1rem 1.5rem; border-top:1px solid var(--border)">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection

@push('admin-styles')
<style>
    .form-label-admin {
        display: block;
        font-size: 0.78rem;
        color: var(--text-muted);
        margin-bottom: 0.35rem;
        font-weight: 500;
    }

    @media (max-width: 1100px) {
        form[method="GET"] {
            grid-template-columns: 1fr 1fr !important;
        }
    }

    @media (max-width: 760px) {
        form[method="GET"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endpush
