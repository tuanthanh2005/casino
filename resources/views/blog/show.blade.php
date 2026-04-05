@extends('layouts.app')

@section('title', $post->meta_title ?: $post->title . ' - Aquahub Resource')
@section('meta_description', $post->meta_description ?: $post->excerpt)
@section('meta_image', $post->featured_image ? asset('uploads/posts/' . $post->featured_image) : asset('av.png'))

@section('content')
<div class="py-5" style="background: white;">
    <div class="container" style="max-width: 760px; padding: 4rem 1.5rem;">
        <div class="text-center mb-5">
            <span class="badge" style="background: #f1f5f9; color: #475569; padding: 0.3rem 0.6rem; font-size: 0.65rem;">{{ $category->name ?? 'AQUAKEEPING' }}</span>
            <h1 class="mx-auto" style="font-size: 3.5rem; letter-spacing: -0.05em; font-weight: 900; line-height: 1.05; margin: 1.5rem 0 2rem; color: var(--dark);">{{ $post->title }}</h1>
            
            <div class="d-flex align-items-center justify-content-center gap-3 py-4 border-top border-bottom" style="border-color: rgba(15, 23, 42, 0.05) !important;">
                <div style="width: 48px; height: 48px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; color: var(--dark); border: 2px solid white; box-shadow: var(--shadow-md);">A</div>
                <div class="text-start">
                    <p class="mb-0 fw-bold small" style="color: var(--dark); line-height: 1.2;">{{ $post->author->name }}</p>
                    <p class="mb-0 small" style="color: #64748b; font-size: 0.75rem;">Last update: {{ $post->updated_at->format('F j, Y') }} • 8 min read</p>
                </div>
            </div>
        </div>

        @if($post->featured_image)
            <figure class="mb-5 py-4">
                <img src="{{ asset('uploads/posts/' . $post->featured_image) }}" alt="{{ $post->title }}" style="width: 100%; border-radius: 12px; box-shadow: var(--shadow-xl);">
            </figure>
        @endif

        <div id="author-note" class="mb-5 p-4 rounded-4" style="background: #f8fafc; border: 1px solid var(--border);">
            <p class="mb-0 small text-secondary" style="line-height: 1.7; font-weight: 500;">
                <strong class="text-dark">Editorial Note:</strong> Our passionate contributors spend weeks researching and testing aquarium equipment. Every guide represents our deep commitment to your success.
            </p>
        </div>

        <div id="toc" class="mb-5 p-4 rounded-4" style="background: white; border: 1px solid var(--border);">
            <h4 class="h6 mb-3 text-dark fw-bold">Inside this guide:</h4>
            <ul id="toc-list" class="list-unstyled mb-0 d-flex flex-row flex-wrap gap-x-4 gap-y-2 small"></ul>
        </div>

        <div id="content-body" class="prose" style="font-size: 1.25rem; line-height: 1.85; color: #334155; font-family: var(--font-body); letter-spacing: -0.01em;">
            {!! $post->renderContent() !!}
        </div>

        @if($post->faqs->count() > 0)
        <div class="mt-5 pt-5 border-top" style="border-top: 1px solid var(--border) !important;">
            <h2 class="h3 fw-black mb-4">Frequently Asked Questions</h2>
            <div class="d-flex flex-column gap-3">
                @foreach($post->faqs as $faq)
                <div class="p-4 bg-light rounded-4 border" style="border-color: var(--border) !important;">
                    <h3 class="h6 mb-2 fw-bold text-dark">{{ $faq->question }}</h3>
                    <p class="mb-0 small text-secondary line-height-1-7">{{ $faq->answer }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="mt-5 p-5 text-center rounded-4" style="background: var(--dark); color: white;">
            <h3 class="h4 mb-3 text-white">Join 15,000+ happy fishkeepers</h3>
            <p class="small opacity-75 mb-4">Get the best tips to your inbox every week.</p>
            <a href="/newsletter" class="btn btn-primary" style="background: white; color: var(--dark); padding: 0.75rem 2rem;">Join Now</a>
        </div>
    </div>
</div>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "{{ $post->title }}",
  "image": ["{{ $post->featured_image ? asset('uploads/posts/' . $post->featured_image) : asset('av.png') }}"],
  "datePublished": "{{ $post->published_at?->toIso8601String() }}",
  "author": { "@type": "Person", "name": "{{ $post->author->name }}" }
}
</script>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tocList = document.getElementById('toc-list');
        const headings = document.querySelectorAll('.prose h2');
        if (headings.length === 0) {
            document.getElementById('toc').style.display = 'none';
            return;
        }
        headings.forEach((heading, index) => {
            const id = 'heading-' + index;
            heading.setAttribute('id', id);
            const li = document.createElement('li');
            li.style.flex = '0 0 calc(50% - 1rem)';
            li.innerHTML = `<a href="#${id}" class="text-secondary text-decoration-none hover-primary fw-bold" style="font-size:0.75rem;"># ${heading.textContent}</a>`;
            tocList.appendChild(li);
        });
    });
</script>
<style>
    .fw-black { font-weight: 900; letter-spacing: -0.04em; }
    .prose h2 { font-size: 2.25rem; font-weight: 900; margin: 4rem 0 1.5rem; color: var(--dark); letter-spacing: -0.04em; }
    .prose h3 { font-size: 1.5rem; font-weight: 700; margin: 2rem 0 1rem; color: var(--dark); }
    .prose p { margin-bottom: 2rem; }
    .prose ul { margin-bottom: 2.5rem; }
    .prose li { margin-bottom: 0.75rem; }
    .hover-primary:hover { color: var(--primary) !important; }
</style>
@endpush
@endsection
