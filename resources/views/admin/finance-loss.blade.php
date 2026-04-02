@extends('layouts.admin')
@section('title', 'Doanh thu lỗ')

@section('admin-content')
<div class="page-header">
    <h1>📉 Doanh thu lỗ</h1>
    <p>Theo dõi ngày bị lỗ và phân bổ lãi/lỗ theo game trong 30 ngày gần nhất.</p>
</div>

<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1rem">
    <div class="stat-card">
        <div class="stat-label">Số ngày lỗ (30 ngày)</div>
        <div class="stat-value" style="color:#ef4444">{{ $lossDays }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Tổng lỗ (30 ngày)</div>
        <div class="stat-value" style="color:#ef4444">{{ number_format((float)abs($totalLoss), 0) }} PT</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Tổng lãi (30 ngày)</div>
        <div class="stat-value" style="color:#10b981">{{ number_format((float)$totalProfit, 0) }} PT</div>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem">
    <div class="card">
        <div class="card-header"><span>Lãi/Lỗ theo game (30 ngày)</span></div>
        <div class="card-body" style="padding:0">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr><th>Game</th><th>Lãi/Lỗ</th></tr>
                    </thead>
                    <tbody>
                        @forelse($lossByGame as $g)
                        <tr>
                            <td style="font-weight:700">{{ strtoupper($g->game_type) }}</td>
                            <td style="font-weight:700; color:{{ (float)$g->net >= 0 ? '#10b981' : '#ef4444' }}">{{ (float)$g->net >= 0 ? '+' : '' }}{{ number_format((float)$g->net, 0) }} PT</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" style="text-align:center; padding:1.5rem; color:var(--text-muted)">Chưa có dữ liệu</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span>Doanh thu theo ngày (30 ngày)</span></div>
        <div class="card-body" style="padding:0">
            <div class="table-wrapper" style="max-height:420px; overflow:auto">
                <table>
                    <thead>
                        <tr><th>Ngày</th><th>Doanh thu</th><th>Chi trả</th><th>Lãi/Lỗ</th></tr>
                    </thead>
                    <tbody>
                        @forelse($days as $d)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($d->report_date)->format('d/m/Y') }}</td>
                            <td>{{ number_format((float)$d->revenue, 0) }}</td>
                            <td>{{ number_format((float)$d->payout, 0) }}</td>
                            <td style="font-weight:700; color:{{ (float)$d->net >= 0 ? '#10b981' : '#ef4444' }}">{{ (float)$d->net >= 0 ? '+' : '' }}{{ number_format((float)$d->net, 0) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" style="text-align:center; padding:1.5rem; color:var(--text-muted)">Chưa có dữ liệu</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
