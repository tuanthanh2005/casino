@extends('layouts.app')

@section('title', 'Video Highlights Bóng Đá')

@push('styles')
<style>
    .soccer-hero {
        background: linear-gradient(135deg, #0a0a0f 0%, #1e1b4b 100%);
        padding: 3rem 0;
        text-align: center;
        border-bottom: 1px solid rgba(99,102,241,0.2);
        margin-bottom: 2rem;
    }
    .video-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 1.5rem;
    }
    .video-card {
        background: #111827;
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 16px;
        overflow: hidden;
        transition: transform 0.2s;
    }
    .video-card:hover {
        transform: translateY(-5px);
        border-color: #6366f1;
    }
    .video-thumb {
        width: 100%;
        height: 190px;
        object-fit: cover;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    .video-info {
        padding: 1.25rem;
    }
    .video-title {
        font-size: 1rem;
        font-weight: 700;
        color: #f9fafb;
        margin-bottom: 0.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 2.8rem;
    }
    .video-meta {
        font-size: 0.8rem;
        color: #9ca3af;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .competition-badge {
        background: rgba(99,102,241,0.1);
        color: #818cf8;
        padding: 0.2rem 0.6rem;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 600;
    }
    .embed-container {
        position: relative;
        padding-bottom: 56.25%;
        height: 0;
        overflow: hidden;
        max-width: 100%;
        background: #000;
    }
    .embed-container iframe,
    .embed-container object,
    .embed-container embed {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    /* Modal styles for video */
    .video-modal {
        display: none;
        position: fixed;
        z-index: 10000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.9);
        backdrop-filter: blur(5px);
    }
    .modal-content {
        position: relative;
        margin: auto;
        top: 50%;
        transform: translateY(-50%);
        width: 90%;
        max-width: 900px;
        background: #111827;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .close-modal {
        position: absolute;
        right: 1.5rem;
        top: 1rem;
        color: #fff;
        font-size: 2rem;
        font-weight: bold;
        cursor: pointer;
        z-index: 11;
    }
</style>
@endpush

@section('content')
<div class="soccer-hero">
    <div class="container">
        <h1 style="font-size: 2.2rem; font-weight: 900; margin-bottom: 0.5rem;">⚽ Video Hightlights Bóng Đá</h1>
        <p style="color: #9ca3af;">Cập nhật những pha làm bàn và khoảnh khắc ấn tượng nhất</p>
    </div>
</div>

<div class="container">
    @if(isset($error))
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i> Lỗi: {{ $error }}
        </div>
    @elseif(!isset($videos) || count($videos) == 0)
        <div class="text-center" style="padding: 4rem 0;">
            <i class="bi bi-camera-video-off" style="font-size: 3rem; color: #374151;"></i>
            <p style="color: #9ca3af; margin-top: 1rem;">Không tìm thấy video nào vào lúc này.</p>
        </div>
    @else
        <div class="video-grid">
            @foreach($videos as $item)
            <div class="video-card" onclick="openVideo('{{ base64_encode($item['embed']) }}', '{{ addslashes($item['title']) }}')">
                <div style="position: relative;">
                    <img src="{{ $item['thumbnail'] }}" alt="{{ $item['title'] }}" class="video-thumb">
                    <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0">
                        <i class="bi bi-play-circle-fill" style="font-size: 3.5rem; color: #fff;"></i>
                    </div>
                </div>
                <div class="video-info">
                    <div class="video-title">{{ $item['title'] }}</div>
                    <div class="video-meta">
                        <span class="competition-badge">{{ $item['competition']['name'] ?? 'Football' }}</span>
                        <span>{{ \Carbon\Carbon::parse($item['date'])->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Video Modal -->
<div id="videoModal" class="video-modal">
    <span class="close-modal" onclick="closeVideo()">&times;</span>
    <div class="modal-content">
        <div id="videoPlayerContainer" class="embed-container"></div>
        <div style="padding: 1.5rem; background: #0f172a;">
            <h3 id="modalTitle" style="color: #fff; margin: 0; font-size: 1.2rem;"></h3>
        </div>
    </div>
</div>

<script>
    function openVideo(embedBase64, title) {
        const embed = atob(embedBase64);
        document.getElementById('videoPlayerContainer').innerHTML = embed;
        document.getElementById('modalTitle').innerText = title;
        document.getElementById('videoModal').style.display = "block";
        document.body.style.overflow = "hidden"; // Disable scroll
    }

    function closeVideo() {
        document.getElementById('videoModal').style.display = "none";
        document.getElementById('videoPlayerContainer').innerHTML = "";
        document.body.style.overflow = "auto"; // Enable scroll
    }

    // Close on click outside
    window.onclick = function(event) {
        const modal = document.getElementById('videoModal');
        if (event.target == modal) {
            closeVideo();
        }
    }
</script>
@endsection
