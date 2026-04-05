@extends('layouts.app')

@section('title', $category->name . ' - Aquarium Community')

@section('content')
<div class="py-5" style="background: white; border-bottom: 1px solid var(--border);">
    <div class="container text-center py-5 px-4">
        <span class="badge mb-4">RESOURCE HUB</span>
        <h1 class="mx-auto fw-black" style="font-size: 4rem; letter-spacing: -0.05em; line-height: 1.05;">{{ $category->name }}</h1>
        <p class="mx-auto mt-4 text-secondary" style="max-width: 600px; line-height: 1.7; font-size: 1.125rem;">{{ $category->description ?? 'Expertly curated guides and step-by-step tutorials to help you master ' . $category->name . ' without the stress.' }}</p>
    </div>
</div>

<div class="container py-5 mt-4">
    <div class="row g-4 g-lg-5">
        @foreach($posts as $post)
        <div class="col-md-6 col-lg-4">
            <article class="card">
                <a href="/blog/{{ $post->slug }}" class="card-img-wrap">
                    @if($post->featured_image)
                        <img src="{{ asset('uploads/posts/' . $post->featured_image) }}" alt="{{ $post->title }}">
                    @else
                        <div style="width: 100%; height: 100%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 3rem;">🐡</div>
                    @endif
                </a>
                <div class="card-body">
                    <h3 class="card-title">
                        <a href="/blog/{{ $post->slug }}" class="text-dark text-decoration-none stretched-link">{{ $post->title }}</a>
                    </h3>
                    <p class="card-excerpt" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">{{ $post->excerpt }}</p>
                    <div class="mt-auto d-flex justify-content-between align-items-center">
                        <span style="font-size: 0.7rem; font-weight: 600; color: #94a3b8; text-transform: uppercase;">{{ $post->published_at?->format('F j, Y') ?? 'DRAFT' }}</span>
                    </div>
                </div>
            </article>
        </div>
        @endforeach
    </div>

    @if($posts->hasPages())
    <div class="mt-5 pt-5 d-flex justify-content-center">
        {{ $posts->links() }}
    </div>
    @endif
</div>

<style>
    .fw-black { font-weight: 900; }
</style>
@endsection
