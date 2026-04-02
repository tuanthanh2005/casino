@extends('layouts.app')

@section('title', 'Danh mục game')

@section('content')
<div class="page-enter" style="max-width:1200px; margin:0 auto;">
    <div style="margin-bottom:1.25rem">
        <h1 style="font-size:1.8rem; font-weight:900">🧩 Danh mục game</h1>
        <p style="color:var(--text-muted); margin-top:0.25rem">Bạn có thể vào game từ trang này hoặc từ dropdown Games trên desktop.</p>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:1rem;">
        <a href="{{ route('prediction') }}" class="card" style="text-decoration:none; color:inherit; padding:1rem; border-radius:16px; display:block;">
            <div style="font-size:2rem; margin-bottom:0.5rem">📈</div>
            <div style="font-weight:800; margin-bottom:0.2rem">BTC Long / Short</div>
            <div style="font-size:0.82rem; color:var(--text-muted)">Dự đoán xu hướng Bitcoin.</div>
        </a>

        <a href="{{ route('spin') }}" class="card" style="text-decoration:none; color:inherit; padding:1rem; border-radius:16px; display:block;">
            <div style="font-size:2rem; margin-bottom:0.5rem">🎡</div>
            <div style="font-weight:800; margin-bottom:0.2rem">Vòng Quay May Mắn</div>
            <div style="font-size:0.82rem; color:var(--text-muted)">Quay và săn hệ số thưởng cao.</div>
        </a>

        <a href="{{ route('dice') }}" class="card" style="text-decoration:none; color:inherit; padding:1rem; border-radius:16px; display:block;">
            <div style="font-size:2rem; margin-bottom:0.5rem">🎲</div>
            <div style="font-weight:800; margin-bottom:0.2rem">Tài Xỉu</div>
            <div style="font-size:0.82rem; color:var(--text-muted)">Đoán Tài/Xỉu với 3 xúc xắc.</div>
        </a>

        <a href="{{ route('rps') }}" class="card" style="text-decoration:none; color:inherit; padding:1rem; border-radius:16px; display:block;">
            <div style="font-size:2rem; margin-bottom:0.5rem">✊</div>
            <div style="font-weight:800; margin-bottom:0.2rem">Kéo Búa Bao</div>
            <div style="font-size:0.82rem; color:var(--text-muted)">Chơi nhanh 1 click, có mode BO3.</div>
        </a>

        <a href="{{ route('farm') }}" class="card" style="text-decoration:none; color:inherit; padding:1rem; border-radius:16px; display:block;">
            <div style="font-size:2rem; margin-bottom:0.5rem">🌾</div>
            <div style="font-weight:800; margin-bottom:0.2rem">Nông Trại</div>
            <div style="font-size:0.82rem; color:var(--text-muted)">Trồng, thu hoạch và bán nông sản.</div>
        </a>
    </div>
</div>
@endsection
