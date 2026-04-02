@extends('layouts.app')
@section('title', 'Đơn Hàng Thành Công - ' . $order->order_code)
@section('content')
<div style="padding:4rem 0;text-align:center">
<div class="container">
    <div style="max-width:480px;margin:0 auto;background:#0d1117;border:1px solid rgba(255,255,255,0.08);border-radius:24px;padding:2.5rem">
        <div style="width:72px;height:72px;background:rgba(16,185,129,0.1);border:2px solid #10b981;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;font-size:2rem">
            {{ $order->status === 'completed' ? '🏆' : '⏳' }}
        </div>
        <h1 style="font-size:1.5rem;font-weight:900;margin-bottom:0.5rem;color:#f9fafb">
            {{ $order->status === 'completed' ? 'Đơn Đã Hoàn Thành!' : 'Đã Nhận Đơn!' }}
        </h1>
        <p style="color:#6b7280;font-size:0.875rem;margin-bottom:2rem;line-height:1.6">
            @if($order->status === 'paid')
                Thanh toán của bạn đã xác nhận. Admin đang xử lý đơn kháng cáo cho tài khoản <strong style="color:#69C9D0">{{ $order->tiktok_username }}</strong>
            @elseif($order->status === 'processing')
                Đơn đang được xử lý. Admin đang soạn và gửi kháng cáo cho TikTok.
            @elseif($order->status === 'completed')
                Kháng cáo đã được gửi đến TikTok Trust & Safety! Kết quả từ TikTok thường mất 3-7 ngày.
            @else
                Đơn của bạn đã được ghi nhận. Vui lòng hoàn tất thanh toán.
            @endif
        </p>

        <div style="background:#161b27;border-radius:14px;padding:1.25rem;margin-bottom:1.5rem;text-align:left">
            <div style="display:flex;justify-content:space-between;padding:0.5rem 0;border-bottom:1px solid rgba(255,255,255,0.05);font-size:0.85rem">
                <span style="color:#6b7280">Mã đơn</span>
                <span style="font-weight:700;color:#69C9D0;font-family:monospace">{{ $order->order_code }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:0.5rem 0;border-bottom:1px solid rgba(255,255,255,0.05);font-size:0.85rem">
                <span style="color:#6b7280">Dịch vụ</span>
                <span style="font-weight:600;color:#f9fafb">{{ $order->service->name }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:0.5rem 0;border-bottom:1px solid rgba(255,255,255,0.05);font-size:0.85rem">
                <span style="color:#6b7280">TikTok</span>
                <span style="font-weight:600;color:#f9fafb">{{ $order->tiktok_username }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:0.5rem 0;border-bottom:1px solid rgba(255,255,255,0.05);font-size:0.85rem">
                <span style="color:#6b7280">Trạng thái</span>
                <span class="badge {{ $order->status_badge }}">{{ $order->status_label }}</span>
            </div>
            @if($order->appeal_deadline)
            <div style="display:flex;justify-content:space-between;padding:0.5rem 0;font-size:0.85rem">
                <span style="color:#6b7280">Hạn kháng cáo</span>
                <span style="font-weight:600;color:#f59e0b">{{ $order->appeal_deadline->format('d/m/Y') }}</span>
            </div>
            @endif
        </div>

        <div style="display:flex;gap:0.75rem">
            <a href="{{ route('nav.my-orders') }}" style="flex:1;background:#161b27;border:1px solid rgba(255,255,255,0.08);color:#f9fafb;padding:0.75rem;border-radius:10px;text-decoration:none;font-size:0.85rem;font-weight:600;text-align:center;transition:all 0.2s" onmouseover="this.style.background='#1a2030'" onmouseout="this.style.background='#161b27'">
                <i class="bi bi-list-ul"></i> Đơn của tôi
            </a>
            <a href="{{ route('nav.index') }}" style="flex:1;background:linear-gradient(135deg,#69C9D0,#4fb3bc);color:#000;padding:0.75rem;border-radius:10px;text-decoration:none;font-size:0.85rem;font-weight:800;text-align:center;transition:all 0.2s">
                <i class="bi bi-plus-circle"></i> Dịch vụ khác
            </a>
        </div>
    </div>
</div>
</div>
@endsection
