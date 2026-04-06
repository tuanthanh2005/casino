@extends('layouts.app')

@section('title', __('Search results for') . ': ' . ($query ?: __('Search guides...')))

@section('content')
<div style="background: #f8fafc; padding: 4rem 0 3rem; border-bottom: 1px solid #e2e8f0;">
    <div class="container" style="max-width: 800px; text-align: center;">
        <h1 style="font-size: 2.5rem; margin-bottom: 2rem;">{{ __('Search Results') }}</h1>
        <form action="/search" method="GET" style="display: flex; gap: 1rem; max-width: 600px; margin: 0 auto;">
            <input type="text" name="q" value="{{ $query }}" placeholder="{{ __('Search our aquarium guides...') }}" style="flex-grow: 1; padding: 1rem 1.5rem; border: 1px solid #cbd5e1; border-radius: 9999px; font-size: 1rem; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05);">
            <button type="submit" class="btn btn-primary" style="padding: 0 2rem; border-radius: 9999px;">{{ __('Search') }}</button>
        </form>
        @if($query)
            <p style="margin-top: 2rem; color: #64748b; font-size: 0.875rem;">{{ __('Showing results for') }}: <strong>"{{ $query }}"</strong></p>
        @endif
    </div>
</div>

<section class="section">
    <div class="container" style="max-width: 1000px;">
        @if($posts->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2.5rem;">
                @foreach($posts as $post)
                <article style="display: flex; flex-direction: column;">
                    <a href="/blog/{{ $post->slug }}" style="display: block; aspect-ratio: 16/9; background: #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 1.5rem; text-decoration: none;">
                        @if($post->featured_image)
                            <img src="{{ asset('uploads/posts/' . $post->featured_image) }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);">
                                <span style="font-size: 2.5rem;">🔍</span>
                            </div>
                        @endif
                    </a>
                    <h3 style="font-size: 1.125rem; line-height: 1.3; margin-bottom: 0.75rem;"><a href="/blog/{{ $post->slug }}" style="color: inherit; text-decoration: none;">{{ $post->title }}</a></h3>
                    <p style="color: #64748b; font-size: 0.8125rem; margin-bottom: 1.5rem;">{{ Str::limit(strip_tags($post->excerpt), 120) }}</p>
                    <div style="margin-top: auto; display: flex; align-items: center; justify-content: space-between; font-size: 0.75rem; color: #94a3b8;">
                        <span>{{ $post->category->name }}</span>
                        <span>{{ $post->published_at?->format('M d, Y') }}</span>
                    </div>
                </article>
                @endforeach
            </div>
            <div style="margin-top: 5rem;">
                {{ $posts->links() }}
            </div>
        @elseif($query)
            <div style="text-align: center; padding: 5rem 0;">
                <div style="font-size: 4rem; margin-bottom: 2rem;">🏖️</div>
                <h3 style="margin-bottom: 1rem;">{{ __('No results found') }} "{{ $query }}"</h3>
                <p style="color: #64748b; margin-bottom: 2rem;">{{ __('Try different keywords or browse our categories instead.') }}</p>
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    @foreach(\App\Models\Category::take(3)->get() as $cat)
                        <a href="/category/{{ $cat->slug }}" class="btn" style="background: #f1f5f9; color: #475569; font-size: 0.8125rem;">{{ $cat->name }}</a>
                    @endforeach
                </div>
            </div>
        @else
            <div style="text-align: center; padding: 5rem 0;">
                <h3 style="color: #64748b;">{{ __('Enter a keyword to search our guides.') }}</h3>
            </div>
        @endif
    </div>
</section>
@endsection
