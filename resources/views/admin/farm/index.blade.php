@extends('layouts.admin')
@section('title','Admin – Nông Trại')

@section('admin-content')
<div style="margin-bottom:1.5rem; display:flex; justify-content:space-between; align-items:center">
    <h1 style="font-size:1.6rem; font-weight:900; margin:0">🌾 Quản Lý Nông Trại</h1>
    <div style="display:flex; gap:0.75rem">
        <a href="{{ route('admin.farm.seeds') }}" class="btn btn-primary"><i class="bi bi-flower1"></i> Quản lý hạt giống</a>
        <a href="{{ route('admin.farm.transactions') }}" class="btn btn-outline"><i class="bi bi-receipt"></i> Lịch sử</a>
    </div>
</div>

{{-- STATS --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:1rem; margin-bottom:1.5rem">
    @foreach([
        ['🌱','Hạt đang bán',$stats['active_seeds'],'/'.$stats['total_seeds'].' loại','var(--primary)'],
        ['🌿','Đang trồng',$stats['growing_crops'],'cây','#3b82f6'],
        ['✅','Đã chín',$stats['ripe_crops'],'chờ harvest','#10b981'],
        ['💀','Cây chết',$stats['dead_crops'],'chờ xóa','#ef4444'],
        ['💰','Tổng bán',number_format($stats['total_sold_pt'],0),'PT','#10b981'],
        ['🛒','Tổng mua',number_format($stats['total_bought_pt'],0),'PT','#f59e0b'],
    ] as [$icon,$label,$val,$sub,$color])
    <div class="card" style="text-align:center; padding:1.25rem">
        <div style="font-size:1.8rem">{{ $icon }}</div>
        <div style="font-size:0.75rem; color:var(--text-muted); margin:0.25rem 0">{{ $label }}</div>
        <div style="font-size:1.35rem; font-weight:900; color:{{ $color }}">{{ $val }}</div>
        <div style="font-size:0.7rem; color:var(--text-muted)">{{ $sub }}</div>
    </div>
    @endforeach
</div>

{{-- TODAY --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.5rem">
    <div class="card" style="text-align:center; padding:1.25rem">
        <div style="font-size:0.8rem; color:var(--text-muted)">Hôm nay — Thu vào (mua hạt)</div>
        <div style="font-size:1.4rem; font-weight:900; color:#ef4444; margin-top:0.5rem">
            {{ number_format($stats['today_buy_pt'],0) }} PT
        </div>
    </div>
    <div class="card" style="text-align:center; padding:1.25rem">
        <div style="font-size:0.8rem; color:var(--text-muted)">Hôm nay — Chi ra (bán trái)</div>
        <div style="font-size:1.4rem; font-weight:900; color:#10b981; margin-top:0.5rem">
            {{ number_format($stats['today_sell_pt'],0) }} PT
        </div>
    </div>
</div>

{{-- RECENT TX --}}
<div class="card">
    <div class="card-header">📋 Giao dịch gần nhất</div>
    <div class="table-wrapper">
        <table>
            <thead><tr><th>User</th><th>Loại</th><th>Hạt</th><th>SL</th><th>PT</th><th>Thời gian</th></tr></thead>
            <tbody>
                @forelse($recentTx as $tx)
                <tr>
                    <td>{{ $tx->user->name ?? '—' }}</td>
                    <td>{{ $tx->type_label }}</td>
                    <td>{{ $tx->seedType->emoji }} {{ $tx->seedType->name }}</td>
                    <td>{{ $tx->quantity }}</td>
                    <td style="font-weight:700; color:{{ $tx->type==='sell_fruit'?'#10b981':($tx->type==='buy_seed'?'#ef4444':'var(--text-muted)') }}">
                        {{ $tx->total_pt ? number_format($tx->total_pt,0).' PT' : '—' }}
                    </td>
                    <td style="color:var(--text-muted); font-size:0.78rem">{{ $tx->created_at->format('d/m H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center; padding:2rem; color:var(--text-muted)">Chưa có giao dịch</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
