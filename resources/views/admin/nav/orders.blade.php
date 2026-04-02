@extends('layouts.admin')
@section('title', 'Đơn Hàng NAV')

@section('admin-content')
<div class="page-header">
    <div class="d-flex justify-between align-center">
        <div>
            <h1>📋 Đơn Hàng Hỗ Trợ MXH</h1>
            <p>Quản lý và xử lý đơn kháng cáo TikTok</p>
        </div>
        <div style="display:flex;gap:0.5rem">
            <a href="{{ route('admin.nav.settings') }}" class="btn btn-outline"><i class="bi bi-gear"></i> Cài đặt</a>
            <a href="{{ route('admin.nav.services') }}" class="btn btn-primary"><i class="bi bi-grid"></i> Dịch vụ</a>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
@endif

<!-- Filters -->
<div class="card mb-3" style="margin-bottom:1rem">
    <div class="card-body" style="padding:1rem">
        <form method="GET" style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:center">
            <input type="text" name="search" class="form-control" placeholder="Tìm mã đơn, username, tên..." value="{{ request('search') }}" style="flex:1;min-width:200px">
            <select name="status" class="form-control" style="width:180px">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="pending_payment" {{ request('status')=='pending_payment'?'selected':'' }}>⏳ Chờ thanh toán</option>
                <option value="paid" {{ request('status')=='paid'?'selected':'' }}>💰 Đã thanh toán</option>
                <option value="processing" {{ request('status')=='processing'?'selected':'' }}>🔄 Đang xử lý</option>
                <option value="completed" {{ request('status')=='completed'?'selected':'' }}>✅ Hoàn thành</option>
                <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>❌ Đã huỷ</option>
            </select>
            <select name="service_id" class="form-control" style="width:200px">
                <option value="">-- Tất cả dịch vụ --</option>
                @foreach($services as $svc)
                <option value="{{ $svc->id }}" {{ request('service_id')==$svc->id?'selected':'' }}>{{ $svc->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Lọc</button>
            <a href="{{ route('admin.nav.orders') }}" class="btn btn-outline">Reset</a>
        </form>
    </div>
</div>

<!-- Stats bar -->
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem">
    @php
        $statuses = ['paid' => ['💰','Chờ xử lý','badge-primary'], 'processing' => ['🔄','Đang xử lý','badge-primary'], 'completed' => ['✅','Hoàn thành','badge-success'], 'pending_payment' => ['⏳','Chờ TT','badge-warning']];
    @endphp
    @foreach($statuses as $st => [$icon, $label, $badge])
    @php $cnt = \App\Models\NavOrder::where('status', $st)->count(); @endphp
    <div class="stat-card" style="padding:1rem">
        <div style="font-size:1.5rem;font-weight:900;color:var(--text)">{{ $cnt }}</div>
        <div style="font-size:0.78rem;color:var(--text-muted)">{{ $icon }} {{ $label }}</div>
    </div>
    @endforeach
</div>

<div class="card">
    <div class="card-body" style="padding:0">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Dịch vụ</th>
                        <th>TikTok / KH</th>
                        <th>Số tiền</th>
                        <th>TT Thanh toán</th>
                        <th>Hạn KC</th>
                        <th>Ngày tạo</th>
                        <th>Xem</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td style="font-family:monospace;font-weight:700;color:#69C9D0">{{ $order->order_code }}</td>
                        <td style="font-size:0.8rem;color:var(--text-muted)">{{ $order->service->name ?? '-' }}</td>
                        <td>
                            <div style="font-weight:600;color:var(--text)">{{ $order->tiktok_username }}</div>
                            <div style="font-size:0.75rem;color:var(--text-muted)">{{ $order->customer_name }}</div>
                        </td>
                        <td style="font-weight:700;color:#69C9D0">{{ number_format((float)$order->amount, 0, ',', '.') }}</td>
                        <td><span class="badge {{ $order->status_badge }}">{{ $order->status_label }}</span></td>
                        <td>
                            @if($order->appeal_deadline)
                                @if($order->isExpired())
                                <span style="color:#ef4444;font-size:0.78rem"><i class="bi bi-x-circle"></i> Hết hạn</span>
                                @elseif($order->days_left <= 7)
                                <span style="color:#f59e0b;font-size:0.78rem"><i class="bi bi-clock"></i> {{ $order->days_left }}d</span>
                                @else
                                <span style="color:#6b7280;font-size:0.78rem">{{ $order->appeal_deadline->format('d/m/Y') }}</span>
                                @endif
                            @else
                            <span style="color:#6b7280">-</span>
                            @endif
                        </td>
                        <td style="color:var(--text-muted);font-size:0.8rem">{{ $order->created_at->format('d/m H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.nav.orders.detail', $order->id) }}" class="btn btn-outline btn-sm">
                                <i class="bi bi-eye"></i> Xem
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align:center;color:var(--text-muted);padding:2rem">Không có đơn nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div style="padding:1rem 1.5rem;border-top:1px solid var(--border)">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
