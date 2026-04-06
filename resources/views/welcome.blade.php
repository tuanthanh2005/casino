@extends('layouts.app')
@section('title', __('Home') . ' - Aquahub.pro')

@section('content')
<div class="hero">
    <div class="container text-center px-4">
        <span class="badge mb-4">{{ __('AQUARIUM RESOURCE') }}</span>
        <h1 class="mx-auto" style="max-width: 900px; line-height: 1.05;">{{ __('Dive Into Your First Successful Aquarium') }}</h1>
        <p class="mx-auto">{{ __('Step-by-step guides, fish care tutorials, and setup advice to help you build a stunning underwater world without the stress.') }}</p>
        <div class="d-flex gap-3 justify-content-center">
            <a href="/category/beginners" class="btn btn-primary px-4 py-2">{{ __('Explore All Guides') }}</a>
            <a href="/category/product-reviews" class="btn btn-secondary px-4 py-2">{{ __('Compare Best Products') }}</a>
        </div>
    </div>
</div>

<section class="py-5" style="background: white;">
    <div class="container py-5">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 4rem;">
            <div>
                <h2 class="h3 fw-black mb-1">{{ __('Latest Beginner Guides') }}</h2>
                <p style="color: #64748b; font-size: 0.875rem;">{{ __('Step-by-step advice for your aquarium journey.') }}</p>
            </div>
            <a href="/blog" style="color: var(--dark); font-weight: 700; font-size: 0.8125rem; text-decoration: none; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid var(--primary); padding-bottom: 4px;">{{ __('View All Posts') }}</a>
        </div>

        <div class="row g-4 g-lg-5">
            @foreach($latest_posts as $post)
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
                        <span class="badge mb-3" style="font-size: 0.6rem; padding: 0.2rem 0.5rem; background: #ecfeff; color: #0891b2; border: 1px solid #cffafe;">{{ $post->category->name }}</span>
                        <h3 class="card-title">
                            <a href="/blog/{{ $post->slug }}" class="text-dark text-decoration-none stretched-link">{{ $post->title }}</a>
                        </h3>
                        <p class="card-excerpt d-none d-sm-block" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">{{ $post->excerpt }}</p>
                        <div class="mt-auto d-flex justify-content-between align-items-center">
                            <span style="font-size: 0.7rem; font-weight: 600; color: #94a3b8; text-transform: uppercase;">{{ $post->published_at?->format('F j, Y') ?? 'DRAFT' }}</span>
                        </div>
                    </div>
                </article>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container py-5">
        <h2 class="text-center h4 fw-black mb-5">{{ __('Browse Our Collection By Topic') }}</h2>
        <div class="row g-4 d-flex justify-content-center">
            @foreach($categories as $category)
            <div class="col-md-6 col-lg-3">
                <a href="/category/{{ $category->slug }}" class="d-block p-4 p-lg-5 text-center bg-white border rounded-4 transition-all text-decoration-none shadow-sm hover-shadow" style="border: 1px solid var(--border) !important;">
                    <div style="width: 48px; height: 48px; background: #ecfeff; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 1.5rem;">📦</div>
                    <h3 class="h6 mb-2 text-dark fw-bold">{{ $category->name }}</h3>
                    <p class="text-secondary mb-0 extra-small" style="font-size: 0.75rem;">{{ $category->posts_count }} {{ __('Articles') }}</p>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5" style="background: white; border-top: 1px solid var(--border);">
    <div class="container text-center py-5">
        <div class="mx-auto px-3" style="max-width: 600px;">
            <h2 class="fw-black h1 mb-4" style="line-height: 1.1;">{{ __('Join 15K+ Aquarium Beginners') }}</h2>
            <p style="color: #64748b; line-height: 1.7; margin-bottom: 2.5rem;">{{ __('Get the absolute best aquarium guides and step-by-step tutorials delivered to your inbox every single week. No spam. Just expert advice.') }}</p>
            <form class="d-flex flex-column flex-sm-row gap-2">
                <input type="email" placeholder="{{ __('Your primary email address') }}" class="form-control" style="background: #f1f5f9; border: 1px solid var(--border); padding: 0.8rem 1.5rem; border-radius: 99px; text-align: center;">
                <button type="submit" class="btn btn-primary px-5 py-2">{{ __('Join Newsletter') }}</button>
            </form>
        </div>
    </div>
</section>

<style>
    .transition-all { transition: all 0.2s ease; }
    .extra-small { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; color: #94a3b8; }
    .fw-black { font-weight: 900; letter-spacing: -0.04em; }
    .hero { border-bottom: 1px solid var(--border); }
    .hover-shadow:hover { transform: translateY(-4px); box-shadow: var(--shadow-xl) !important; border-color: #cbd5e1 !important; }
</style>
@endsection
