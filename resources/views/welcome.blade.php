@extends('layouts.app')

@section('content')
<div class="hero" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; padding: 6rem 0; text-align: center; position: relative; overflow: hidden;">
    <!-- Abstract background elements -->
    <div style="position: absolute; width: 400px; height: 400px; background: radial-gradient(circle, rgba(2, 132, 199, 0.4) 0%, transparent 70%); top: -100px; right: -50px; border-radius: 50%;"></div>
    <div style="position: absolute; width: 300px; height: 300px; background: radial-gradient(circle, rgba(34, 197, 94, 0.2) 0%, transparent 70%); bottom: -50px; left: -50px; border-radius: 50%;"></div>

    <div class="container" style="position: relative; z-index: 2;">
        <span style="background: rgba(56, 189, 248, 0.1); color: #38bdf8; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.875rem; font-weight: 600; text-transform: uppercase;">Aquarium for Beginners</span>
        <h1 style="font-size: 4rem; margin: 1.5rem 0; color: white;">Dive Into Your First Aquarium</h1>
        <p style="font-size: 1.25rem; color: #94a3b8; max-width: 600px; margin: 0 auto 2.5rem;">Expert guides and reviews to help you setup, maintain, and enjoy your new underwater world without the stress.</p>
        <div style="display: flex; gap: 1rem; justify-content: center;">
            <a href="/category/beginners" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1.125rem;">Explore Guides</a>
            <a href="/category/product-reviews" class="btn" style="padding: 1rem 2rem; font-size: 1.125rem; background: white; color: #0f172a;">Best Products</a>
        </div>
    </div>
</div>

<section class="section">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 3rem;">
            <div>
                <h2 style="margin-bottom: 0.5rem;">Latest Beginner Guides</h2>
                <p style="color: #64748b;">Freshly published advice for your aquarium journey.</p>
            </div>
            <a href="/blog" style="font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">View All Posts <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg></a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            @foreach($latest_posts as $post)
            <article class="card" style="display: flex; flex-direction: column; transition: transform 0.3s ease;">
                <div style="height: 200px; background: #e2e8f0; overflow: hidden; position: relative;">
                    @if($post->featured_image)
                        <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);">
                            <span style="font-size: 3rem;">🐟</span>
                        </div>
                    @endif
                    <div style="position: absolute; top: 1rem; left: 1rem;">
                        <span style="background: rgba(2, 132, 199, 0.9); color: white; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">{{ $post->category->name }}</span>
                    </div>
                </div>
                <div style="padding: 1.5rem; flex-grow: 1;">
                    <h3 style="font-size: 1.25rem; margin-bottom: 1rem;"><a href="/blog/{{ $post->slug }}" style="color: inherit;">{{ $post->title }}</a></h3>
                    <p style="color: #64748b; font-size: 0.875rem; margin-bottom: 1.5rem; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">{{ $post->excerpt }}</p>
                    <div style="margin-top: auto; display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #f1f5f9; padding-top: 1rem;">
                        <span style="font-size: 0.75rem; color: #94a3b8;">{{ $post->published_at?->format('M d, Y') ?? 'N/A' }}</span>
                        <a href="/blog/{{ $post->slug }}" style="font-size: 0.875rem; font-weight: 700;">Read More</a>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>

<section class="section" style="background: white;">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 3rem;">Popular Categories</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem;">
            @foreach($categories as $category)
            <a href="/category/{{ $category->slug }}" style="background: #f8fafc; padding: 2rem; border-radius: 12px; text-align: center; transition: all 0.3s ease; border: 1px solid transparent;" onmouseover="this.style.borderColor='var(--primary)'; this.style.backgroundColor='white';" onmouseout="this.style.borderColor='transparent'; this.style.backgroundColor='#f8fafc';">
                <div style="width: 60px; height: 60px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 1.5rem; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05);">📦</div>
                <h3 style="font-size: 1.125rem; margin-bottom: 0.5rem; color: #0f172a;">{{ $category->name }}</h3>
                <p style="color: #64748b; font-size: 0.875rem;">{{ $category->posts_count }} articles Available</p>
            </a>
            @endforeach
        </div>
    </div>
</section>

<section class="section" style="background: #0284c7; color: white;">
    <div class="container" style="display: flex; align-items: center; justify-content: space-between; gap: 4rem; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 300px;">
            <h2 style="color: white; font-size: 2.5rem; margin-bottom: 1.5rem;">Aquarium Best Practices & FAQ</h2>
            <p style="font-size: 1.125rem; color: #e0f2fe; margin-bottom: 2rem;">Common questions beginners ask when setting up their first aquarium.</p>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <details style="background: rgba(255, 255, 255, 0.1); padding: 1rem; border-radius: 8px; cursor: pointer;">
                    <summary style="font-weight: 600;">How long does it take to cycle a new tank?</summary>
                    <p style="margin-top: 1rem; color: #bae6fd;">Usually 4 to 6 weeks. It is vital to establish beneficial bacteria before adding most fish.</p>
                </details>
                <details style="background: rgba(255, 255, 255, 0.1); padding: 1rem; border-radius: 8px; cursor: pointer;">
                    <summary style="font-weight: 600;">How much should I feed my fish?</summary>
                    <p style="margin-top: 1rem; color: #bae6fd;">As much as they can eat in 2 minutes, twice a day. Overfeeding is the leading cause of poor water quality.</p>
                </details>
            </div>
        </div>
        <div style="flex: 1; min-width: 300px; background: white; padding: 2.5rem; border-radius: 12px; color: #0f172a; box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);">
            <h3 style="margin-bottom: 1rem;">Join Our Community</h3>
            <p style="color: #64748b; margin-bottom: 2rem;">Get the latest guides and product alerts delivered to your inbox.</p>
            <form action="#" onsubmit="return false;" style="display: flex; flex-direction: column; gap: 1rem;">
                <input type="email" placeholder="Your email address" style="padding: 1rem; border: 1px solid #e2e8f0; border-radius: 6px;">
                <button type="submit" class="btn btn-primary" style="padding: 1rem;">Subscribe Now</button>
            </form>
            <p style="font-size: 0.75rem; color: #94a3b8; text-align: center; margin-top: 1.5rem;">Join 5,000+ happy aquarium owners.</p>
        </div>
    </div>
</section>
@endsection
