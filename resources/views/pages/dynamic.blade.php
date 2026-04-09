@extends('layouts.app')

@section('title', $page->seo_title ?: $page->title . ' - Aquahub')
@section('meta_description', $page->seo_description ?: config('app.name'))

@section('content')
<div class="py-5" style="background: white; min-height: 70vh;">
    <div class="container" style="max-width: 800px; padding: 4rem 1.5rem;">
        <h1 class="mx-auto text-center" style="font-size: 3rem; font-weight: 900; margin-bottom: 3rem; color: var(--dark); letter-spacing: -0.04em;">
            {{ $page->title }}
        </h1>
        
        <div class="prose page-content" style="font-size: 1.125rem; line-height: 1.8; color: #334155;">
            {!! $page->content !!}
        </div>
    </div>
</div>

@push('styles')
<style>
    .page-content h2 { font-size: 2rem; font-weight: 800; margin-top: 3rem; margin-bottom: 1.5rem; color: #0f172a; }
    .page-content h3 { font-size: 1.5rem; font-weight: 700; margin-top: 2rem; margin-bottom: 1rem; color: #0f172a; }
    .page-content p { margin-bottom: 1.5rem; }
    .page-content ul, .page-content ol { margin-bottom: 1.5rem; padding-left: 1.5rem; }
    .page-content li { margin-bottom: 0.5rem; }
    .page-content a { color: #2563eb; text-decoration: underline; text-underline-offset: 4px; }
    .page-content a:hover { color: #1d4ed8; }
</style>
@endpush
@endsection
