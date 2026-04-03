@extends('layouts.app')

@section('title', 'Blog AquaHub - Kiến thức game & SEO')
@section('meta_description', 'Blog AquaHub chia sẻ kiến thức chơi game, phân tích xu hướng và nội dung tối ưu SEO cho cộng đồng.')
@section('meta_keywords', 'blog aquahub, seo aquahub, kinh nghiem choi game, huong dan mini game')

@section('content')
<div style="max-width:1100px; margin:0 auto;">
    <div class="card" style="padding:1.5rem; margin-bottom:1.2rem; background:linear-gradient(145deg, rgba(6,182,212,0.12), rgba(16,185,129,0.08));">
        <h1 style="font-size:1.85rem; margin-bottom:0.45rem;">📰 AquaHub Blog</h1>
        <p class="text-muted">Chia sẻ kiến thức, mẹo chơi game và nội dung tối ưu SEO cho hệ sinh thái AquaHub.</p>
    </div>

    <div class="grid-3" style="grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:1rem;">
        @forelse($posts as $post)
            <article class="card" style="display:flex; flex-direction:column; overflow:hidden;">
                @if($post->cover_image)
                    <div class="blog-card-cover-frame">
                        <img src="{{ $post->cover_image_url }}" alt="{{ $post->title }}" class="blog-card-cover-image">
                    </div>
                @else
                    <div style="height:170px; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg, rgba(6,182,212,0.15), rgba(16,185,129,0.18)); font-size:2rem;">🌊</div>
                @endif

                <div style="padding:1rem; display:flex; flex-direction:column; gap:0.55rem; flex:1;">
                    <div class="badge badge-primary" style="width:fit-content;">
                        {{ optional($post->published_at)->format('d/m/Y') }}
                    </div>
                    <h2 style="font-size:1.05rem; line-height:1.45; min-height:46px;">{{ $post->title }}</h2>
                    <p class="text-muted" style="font-size:0.85rem; line-height:1.55; flex:1;">
                        {{ \Illuminate\Support\Str::limit($post->excerpt ?: strip_tags($post->content), 120) }}
                    </p>
                    <a href="{{ route('blog.show', $post) }}" class="btn btn-outline" style="justify-content:center;">
                        Xem chi tiết <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </article>
        @empty
            <div class="card" style="padding:2rem; grid-column:1/-1; text-align:center; color:var(--text-muted);">
                Chưa có bài viết nào được xuất bản.
            </div>
        @endforelse
    </div>

    @if($posts->hasPages())
        <div style="margin-top:1.25rem;">
            {{ $posts->links() }}
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .blog-card-cover-frame {
        width: 100%;
        height: 170px;
        padding: 0.45rem;
        background:
            radial-gradient(circle at 20% 20%, rgba(6, 182, 212, 0.16), transparent 42%),
            radial-gradient(circle at 80% 80%, rgba(16, 185, 129, 0.14), transparent 46%),
            #07101e;
    }

    .blog-card-cover-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
        border-radius: 10px;
    }
</style>
@endpush
