@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 5rem 0 3rem;">
    <div class="container" style="max-width: 900px; text-align: center;">
        <h1 style="font-size: 3.5rem; margin-bottom: 2rem;">{{ $category->name }}</h1>
        <div style="font-size: 1.125rem; color: #475569; line-height: 1.8; margin-bottom: 3rem;">
            {{ $category->description ?? 'Explore our best guides.' }}
        </div>
        
        <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
            <span style="font-size: 0.875rem; color: #94a3b8; font-weight: 600; text-transform: uppercase;">Related:</span>
            @foreach(\App\Models\Category::take(3)->get() as $rel)
                <a href="/category/{{ $rel->slug }}" style="color: #64748b; font-size: 0.875rem;">{{ $rel->name }}</a>
            @endforeach
        </div>
    </div>
</div>

<section class="section">
    <div class="container" style="max-width: 1000px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2.5rem;">
            @foreach($posts as $post)
            <article style="display: flex; flex-direction: column;">
                <a href="/blog/{{ $post->slug }}" style="display: block; aspect-ratio: 16/9; background: #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 1.5rem; text-decoration: none;">
                    @if($post->featured_image)
                        <img src="{{ asset('storage/' . $post->featured_image) }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #bae6fd;">
                            <span style="font-size: 2rem;">🐟</span>
                        </div>
                    @endif
                </a>
                <h3 style="font-size: 1.25rem;"><a href="/blog/{{ $post->slug }}" style="color: inherit; text-decoration: none;">{{ $post->title }}</a></h3>
                <p style="color: #64748b; font-size: 0.875rem;">{{ $post->excerpt }}</p>
                <div style="margin-top: auto; font-size: 0.75rem; color: #94a3b8;">
                    <span>{{ $post->published_at?->format('M d, Y') ?? 'N/A' }}</span>
                </div>
            </article>
            @endforeach
        </div>

        <div style="margin-top: 5rem;">
            {{ $posts->links() }}
        </div>
    </div>
</section>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [{
    "@type": "ListItem",
    "position": 1,
    "name": "Home",
    "item": "{{ url('/') }}"
  },{
    "@type": "ListItem",
    "position": 2,
    "name": "{{ $category->name }}",
    "item": "{{ url()->current() }}"
  }]
}
</script>

@endsection
