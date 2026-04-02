@extends('layouts.admin')
@section('title','Admin – Lịch Sử Nông Trại')

@section('admin-content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem">
    <h1 style="font-size:1.5rem; font-weight:900; margin:0">📋 Lịch Sử Giao Dịch Nông Trại</h1>
</div>

{{-- TOTALS --}}
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1.5rem">
    <div class="card" style="text-align:center; padding:1.25rem">
        <div style="font-size:0.8rem; color:var(--text-muted)">🛒 Tổng mua hạt</div>
        <div style="font-size:1.4rem; font-weight:900; color:#ef4444; margin-top:0.5rem">{{ number_format($totals['buy'],0) }} PT</div>
    </div>
    <div class="card" style="text-align:center; padding:1.25rem">
        <div style="font-size:0.8rem; color:var(--text-muted)">🌾 Tổng trái thu hoạch</div>
        <div style="font-size:1.4rem; font-weight:900; color:#6366f1; margin-top:0.5rem">{{ number_format($totals['harvest'],0) }} trái</div>
    </div>
    <div class="card" style="text-align:center; padding:1.25rem">
        <div style="font-size:0.8rem; color:var(--text-muted)">💰 Tổng bán trái</div>
        <div style="font-size:1.4rem; font-weight:900; color:#10b981; margin-top:0.5rem">{{ number_format($totals['sell'],0) }} PT</div>
    </div>
</div>

{{-- FILTER --}}
<form method="GET" style="display:flex; gap:0.75rem; margin-bottom:1rem; flex-wrap:wrap">
    <select name="type" class="form-control" style="width:auto">
        <option value="">Tất cả loại</option>
        <option value="buy_seed" {{ request('type')==='buy_seed'?'selected':'' }}>🛒 Mua hạt</option>
        <option value="harvest"  {{ request('type')==='harvest'?'selected':'' }}>🌾 Thu hoạch</option>
        <option value="sell_fruit" {{ request('type')==='sell_fruit'?'selected':'' }}>💰 Bán trái</option>
    </select>
    <select name="seed_id" class="form-control" style="width:auto">
        <option value="">Tất cả hạt</option>
        @foreach($seeds as $s)
        <option value="{{ $s->id }}" {{ request('seed_id')==$s->id?'selected':'' }}>{{ $s->emoji }} {{ $s->name }}</option>
        @endforeach
    </select>
    <input name="user_id" type="number" class="form-control" placeholder="User ID..." value="{{ request('user_id') }}" style="width:120px">
    <button type="submit" class="btn btn-primary">Lọc</button>
    <a href="{{ route('admin.farm.transactions') }}" class="btn btn-outline">Reset</a>
</form>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>User</th><th>Loại</th><th>Hạt</th><th>SL</th><th>Đơn giá</th><th>Tổng PT</th><th>Hệ số</th><th>Ghi chú</th><th>Thời gian</th></tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                <tr>
                    <td>
                        <div style="font-weight:600">{{ $tx->user->name ?? '#'.$tx->user_id }}</div>
                        <div style="font-size:0.72rem; color:var(--text-muted)">ID: {{ $tx->user_id }}</div>
                    </td>
                    <td>{{ $tx->type_label }}</td>
                    <td>{{ $tx->seedType->emoji }} {{ $tx->seedType->name }}</td>
                    <td>{{ $tx->quantity }}</td>
                    <td>{{ $tx->unit_price_pt ? number_format($tx->unit_price_pt,0).' PT' : '—' }}</td>
                    <td style="font-weight:700; color:{{ $tx->type==='sell_fruit'?'#10b981':($tx->type==='buy_seed'?'#ef4444':'var(--text-muted)') }}">
                        {{ $tx->total_pt ? number_format($tx->total_pt,0).' PT' : '—' }}
                    </td>
                    <td>
                        @if($tx->price_modifier)
                        @php $pct = round(($tx->price_modifier-1)*100,1); @endphp
                        <span style="color:{{ $pct>=0?'#10b981':'#ef4444' }}; font-size:0.8rem">
                            {{ $pct>=0?'+':'' }}{{ $pct }}%
                        </span>
                        @else —
                        @endif
                    </td>
                    <td style="font-size:0.78rem; color:var(--text-muted)">{{ $tx->note }}</td>
                    <td style="font-size:0.78rem; color:var(--text-muted); white-space:nowrap">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center; padding:2rem; color:var(--text-muted)">Không có giao dịch</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:1rem">{{ $transactions->links() }}</div>
</div>
@endsection
