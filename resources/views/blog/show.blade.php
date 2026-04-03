@extends('layouts.app')

@section('title', ($post->meta_title ?: $post->title) . ' | AquaHub Blog')
@section('meta_description', $post->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($post->excerpt ?: $post->content), 155))
@section('meta_keywords', 'blog aquahub, seo aquahub, ' . $post->slug)
@section('canonical', route('blog.show', $post))

@section('content')
<div style="max-width:980px; margin:0 auto;">
    <article class="card" style="overflow:hidden;">
        @if($post->cover_image)
            <img src="{{ $post->cover_image_url }}" alt="{{ $post->title }}" style="width:100%; max-height:360px; object-fit:cover;">
        @endif

        <div class="card-body" style="padding:1.5rem 1.35rem;">
            <div class="mb-2" style="display:flex; align-items:center; gap:0.6rem; flex-wrap:wrap;">
                <span class="badge badge-primary">Blog SEO</span>
                <span class="text-muted" style="font-size:0.82rem;">{{ optional($post->published_at)->format('d/m/Y H:i') }}</span>
            </div>

            <h1 style="font-size:1.85rem; line-height:1.35; margin-bottom:0.7rem;">{{ $post->title }}</h1>

            @if($post->excerpt)
                <p style="font-size:1rem; color:#c9d8ef; line-height:1.65; margin-bottom:1rem;">{{ $post->excerpt }}</p>
            @endif

            <div style="height:1px; background:var(--border); margin-bottom:1rem;"></div>

            <div style="font-size:0.98rem; line-height:1.85; color:#e8eef8;">
                {!! nl2br(e($post->content)) !!}
            </div>
        </div>
    </article>

    @if($relatedPosts->isNotEmpty())
        <div style="margin-top:1.2rem;">
            <h3 style="margin-bottom:0.7rem;">Bài viết liên quan</h3>
            <div class="grid-2" style="grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:0.8rem;">
                @foreach($relatedPosts as $related)
                    <a href="{{ route('blog.show', $related) }}" class="card" style="text-decoration:none; color:inherit; padding:0.9rem; border-radius:14px;">
                        <div style="font-weight:700; line-height:1.5;">{{ $related->title }}</div>
                        <div class="text-muted" style="font-size:0.8rem; margin-top:0.35rem;">{{ optional($related->published_at)->format('d/m/Y') }}</div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
