@extends('layouts.admin')
@section('title', 'Tổng thống kê doanh thu')

@section('admin-content')
<div class="page-header">
    <h1>📊 Tổng thống kê doanh thu</h1>
    <p>Tổng hợp doanh thu, chi trả và lãi/lỗ từ game.</p>
</div>

<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1rem">
    <div class="stat-card">
        <div class="stat-label">Hôm nay</div>
        <div class="stat-value" style="font-size:1.25rem; color:#60a5fa">{{ number_format((float)$today['revenue'], 0) }} PT</div>
        <div class="stat-label">Chi trả: {{ number_format((float)$today['payout'], 0) }} PT · Lãi/Lỗ: <span style="color:{{ $today['net'] >= 0 ? '#10b981' : '#ef4444' }}">{{ $today['net'] >= 0 ? '+' : '' }}{{ number_format((float)$today['net'], 0) }}</span></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Tuần này</div>
        <div class="stat-value" style="font-size:1.25rem; color:#60a5fa">{{ number_format((float)$week['revenue'], 0) }} PT</div>
        <div class="stat-label">Chi trả: {{ number_format((float)$week['payout'], 0) }} PT · Lãi/Lỗ: <span style="color:{{ $week['net'] >= 0 ? '#10b981' : '#ef4444' }}">{{ $week['net'] >= 0 ? '+' : '' }}{{ number_format((float)$week['net'], 0) }}</span></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Tháng này</div>
        <div class="stat-value" style="font-size:1.25rem; color:#60a5fa">{{ number_format((float)$month['revenue'], 0) }} PT</div>
        <div class="stat-label">Chi trả: {{ number_format((float)$month['payout'], 0) }} PT · Lãi/Lỗ: <span style="color:{{ $month['net'] >= 0 ? '#10b981' : '#ef4444' }}">{{ $month['net'] >= 0 ? '+' : '' }}{{ number_format((float)$month['net'], 0) }}</span></div>
    </div>
</div>

<div class="card">
    <div class="card-header"><span>Doanh thu theo game (tháng này)</span></div>
    <div class="card-body" style="padding:0">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Game</th>
                        <th>Tổng ván</th>
                        <th>Doanh thu cược</th>
                        <th>Chi trả</th>
                        <th>Lãi/Lỗ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($byGame as $g)
                    <tr>
                        <td style="font-weight:700">{{ strtoupper($g->game_type) }}</td>
                        <td>{{ number_format((int)$g->total_games, 0) }}</td>
                        <td>{{ number_format((float)$g->revenue, 0) }} PT</td>
                        <td>{{ number_format((float)$g->payout, 0) }} PT</td>
                        <td style="font-weight:700; color:{{ (float)$g->net >= 0 ? '#10b981' : '#ef4444' }}">{{ (float)$g->net >= 0 ? '+' : '' }}{{ number_format((float)$g->net, 0) }} PT</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center; padding:2rem; color:var(--text-muted)">Chưa có dữ liệu</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
