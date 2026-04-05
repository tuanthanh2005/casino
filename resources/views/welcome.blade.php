@extends('layouts.app')

@section('content')
<div class="hero">
    <div class="container text-center">
        <span class="badge-category mb-4 d-inline-block">Aquarium for Beginners</span>
        <h1 class="mx-auto" style="max-width: 900px;">Dive Into Your First <span style="background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Successful Aquarium</span></h1>
        <p class="mx-auto text-muted">Ready-to-use guides, expert fish care tutorials, and step-by-step tank setup advice to help you build a stunning underwater world without the stress.</p>
        <div class="d-flex gap-3 justify-content-center">
            <a href="/category/beginners" class="btn btn-primary shadow-sm" style="padding: 1rem 2rem; border-radius: 99px;">
                Explore Guides
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
            </a>
            <a href="/category/product-reviews" class="btn btn-secondary shadow-sm" style="padding: 1rem 2rem; border-radius: 99px; border: 1px solid var(--border);">Best Products</a>
        </div>
    </div>
</div>

<section class="py-5" style="background: #fdfdfd;">
    <div class="container container-sm py-5">
        <div class="d-flex justify-content-between align-items-end mb-4 mb-lg-5">
            <div>
                <h2 class="mb-2">Latest Beginner Guides</h2>
                <p class="text-secondary small">Freshly published advice for your aquarium journey.</p>
            </div>
            <a href="/blog" class="text-primary text-decoration-none fw-bold small d-flex align-items-center gap-2">
                View All Posts 
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
            </a>
        </div>

        <div class="row g-4 g-lg-5">
            @foreach($latest_posts as $post)
            <div class="col-md-6 col-lg-4">
                <article class="card border-0 shadow-sm transition-all shadow-hover h-100" style="border: 1px solid rgba(15, 23, 42, 0.05) !important;">
                    <a href="/blog/{{ $post->slug }}" class="d-block overflow-hidden position-relative" style="aspect-ratio: 16/10; border-radius: 12px 12px 0 0;">
                        @if($post->featured_image)
                            <img src="{{ asset('uploads/posts/' . $post->featured_image) }}" alt="{{ $post->title }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);" class="card-img-hover">
                        @else
                            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); display: flex; align-items: center; justify-content: center;">
                                <span style="font-size: 3rem;">🐠</span>
                            </div>
                        @endif
                        <div class="position-absolute" style="top: 1rem; left: 1rem;">
                            <span class="badge-category" style="background: white; border: 1px solid rgba(0,0,0,0.05); font-size: 0.65rem;">{{ $post->category->name }}</span>
                        </div>
                    </a>
                    <div class="card-body p-4 p-lg-4 d-flex flex-column">
                        <h3 class="h5 mb-3" style="line-height: 1.4; letter-spacing: -0.01em;">
                            <a href="/blog/{{ $post->slug }}" class="text-dark text-decoration-none stretched-link">{{ $post->title }}</a>
                        </h3>
                        <p class="text-secondary small mb-4" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.7;">{{ $post->excerpt }}</p>
                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center" style="border-top-color: rgba(15,23,42,0.03) !important;">
                            <span class="text-secondary" style="font-size: 0.7rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">{{ $post->published_at?->format('M d, Y') ?? 'Draft' }}</span>
                            <span class="text-primary fw-bold" style="font-size: 0.75rem;">Read Guide →</span>
                        </div>
                    </div>
                </article>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5" style="background: white;">
    <div class="container py-5">
        <h2 class="text-center mb-5">Popular Categories</h2>
        <div class="row g-4">
            @foreach($categories as $category)
            <div class="col-md-6 col-lg-3">
                <a href="/category/{{ $category->slug }}" class="d-block text-decoration-none p-4 p-lg-5 text-center border rounded-4 transition-all" style="border: 1px solid rgba(15, 23, 42, 0.05) !important;" onmouseover="this.style.borderColor='var(--primary)'; this.style.backgroundColor='#f0f9ff';" onmouseout="this.style.borderColor='rgba(15, 23, 42, 0.05)'; this.style.backgroundColor='transparent';">
                    <div style="width: 64px; height: 64px; background: #e0f2fe; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 1.75rem; box-shadow: 0 4px 6px -1px rgba(11, 165, 233, 0.1);">📦</div>
                    <h3 class="h6 mb-2 text-dark">{{ $category->name }}</h3>
                    <p class="text-secondary mb-0 small">{{ $category->posts_count }} Articles</p>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5" style="background: var(--dark); border-radius: 40px 40px 0 0; margin-top: 100px;">
    <div class="container py-5 text-center px-4">
        <div class="mx-auto" style="max-width: 600px;">
            <h2 class="text-white mb-3 h1">Join Our Community</h2>
            <p style="color: #94a3b8; margin-bottom: 2.5rem;" class="lead">Get the latest aquarium setup guides and product deals delivered to your inbox every week.</p>
            <form class="d-flex flex-column flex-sm-row gap-2">
                <input type="email" placeholder="Your email address" class="form-control" style="background: #1e293b; border: 1px solid #334155; color: white; padding: 0.875rem 1.5rem; border-radius: 99px;">
                <button type="submit" class="btn btn-primary" style="padding: 0.875rem 2.5rem; border-radius: 99px; font-weight: 700; white-space: nowrap;">Subscribe Now</button>
            </form>
            <p style="color: #64748b; font-size: 0.75rem; margin-top: 1.5rem;">We respect your privacy. No spam, ever.</p>
        </div>
    </div>
</section>

<style>
    .transition-all { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
    .shadow-hover:hover { box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1) !important; }
    .card:hover .card-img-hover { transform: scale(1.1); }
</style>
@endsection
