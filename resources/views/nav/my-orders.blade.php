@extends('layouts.app')
@section('title', 'Đơn Hàng Của Tôi - Hỗ Trợ MXH')
@section('content')
<div style="padding:2rem 0 4rem">
<div class="container" style="max-width:900px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem">
        <div>
            <h1 style="font-size:1.5rem;font-weight:900">Đơn Hàng Của Tôi</h1>
            <p style="color:#6b7280;font-size:0.875rem;margin-top:0.25rem">Hỗ Trợ MXH - Lịch sử dịch vụ</p>
        </div>
        <a href="{{ route('nav.index') }}" style="background:linear-gradient(135deg,#69C9D0,#4fb3bc);color:#000;padding:0.6rem 1.25rem;border-radius:10px;text-decoration:none;font-size:0.85rem;font-weight:800">
            <i class="bi bi-plus"></i> Đặt thêm dịch vụ
        </a>
    </div>

    @if($orders->isEmpty())
    <div style="background:#0d1117;border:1px solid rgba(255,255,255,0.08);border-radius:20px;padding:3rem;text-align:center;color:#6b7280">
        <i class="bi bi-inbox" style="font-size:3rem;display:block;margin-bottom:1rem"></i>
        Bạn chưa có đơn hàng nào.
        <br><br>
        <a href="{{ route('nav.index') }}" style="color:#69C9D0;text-decoration:none">Xem các dịch vụ →</a>
    </div>
    @else
    <div style="background:#0d1117;border:1px solid rgba(255,255,255,0.08);border-radius:20px;overflow:hidden">
        <table style="width:100%;border-collapse:collapse;font-size:0.85rem">
            <thead>
                <tr>
                    <th style="padding:1rem;text-align:left;color:#6b7280;font-weight:500;border-bottom:1px solid rgba(255,255,255,0.06)">Mã đơn</th>
                    <th style="padding:1rem;text-align:left;color:#6b7280;font-weight:500;border-bottom:1px solid rgba(255,255,255,0.06)">Dịch vụ</th>
                    <th style="padding:1rem;text-align:left;color:#6b7280;font-weight:500;border-bottom:1px solid rgba(255,255,255,0.06)">TikTok</th>
                    <th style="padding:1rem;text-align:left;color:#6b7280;font-weight:500;border-bottom:1px solid rgba(255,255,255,0.06)">Trạng thái</th>
                    <th style="padding:1rem;text-align:left;color:#6b7280;font-weight:500;border-bottom:1px solid rgba(255,255,255,0.06)">Ngày</th>
                    <th style="padding:1rem;text-align:left;color:#6b7280;font-weight:500;border-bottom:1px solid rgba(255,255,255,0.06)"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr style="border-bottom:1px solid rgba(255,255,255,0.04)">
                    <td style="padding:0.875rem 1rem;font-family:monospace;font-weight:700;color:#69C9D0">{{ $order->order_code }}</td>
                    <td style="padding:0.875rem 1rem;color:#f9fafb">{{ $order->service->name ?? '-' }}</td>
                    <td style="padding:0.875rem 1rem;color:#9ca3af">{{ $order->tiktok_username }}</td>
                    <td style="padding:0.875rem 1rem">
                        <span class="badge {{ $order->status_badge }}">{{ $order->status_label }}</span>
                    </td>
                    <td style="padding:0.875rem 1rem;color:#6b7280">{{ $order->created_at->format('d/m/Y') }}</td>
                    <td style="padding:0.875rem 1rem;">
                        @if($order->status === 'pending_payment')
                        <a href="{{ route('nav.payment', $order->order_code) }}" style="color:#69C9D0;font-size:0.8rem;text-decoration:none">
                            <i class="bi bi-qr-code"></i> Thanh toán
                        </a>
                        @else
                        <a href="{{ route('nav.success', $order->order_code) }}" style="color:#6b7280;font-size:0.8rem;text-decoration:none">
                            <i class="bi bi-eye"></i> Xem
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($orders->hasPages())
        <div style="padding:1rem 1.5rem;border-top:1px solid rgba(255,255,255,0.06)">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
    @endif
</div>
</div>
@endsection
