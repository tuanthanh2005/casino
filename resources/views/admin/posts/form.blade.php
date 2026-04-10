@extends('layouts.admin')

@php
    $isEdit = isset($post);
    $action = $isEdit ? route('admin.posts.update', $post) : route('admin.posts.store');
    $pageTitle = $isEdit ? __('Edit Post') : __('Create New Post');
@endphp

@section('page_title', $pageTitle)

@section('header_actions')
    <a href="{{ route('admin.posts.index') }}" class="pf-btn pf-btn-ghost">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><polyline points="12 19 5 12 12 5"/></svg>
        {{ __('Back to Posts') }}
    </a>
@endsection

@section('admin_content')

{{-- ============================================================
     TOAST NOTIFICATIONS
     ============================================================ --}}
@if ($errors->any())
<div class="pf-toast pf-toast-error" id="pf-toast-error">
    <div class="pf-toast-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    </div>
    <div class="pf-toast-body">
        <p class="pf-toast-title">{{ __('Vui lòng sửa các lỗi sau') }}</p>
        <ul class="pf-toast-list">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    <button class="pf-toast-close" onclick="this.closest('.pf-toast').remove()">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
</div>
@endif

{{-- ============================================================
     PAGE HEADER BADGE
     ============================================================ --}}
<div class="pf-page-header">
    <div class="pf-page-badge {{ $isEdit ? 'pf-badge-edit' : 'pf-badge-create' }}">
        @if($isEdit)
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            {{ __('Editing:') }} <strong>{{ Str::limit($post->title, 50) }}</strong>
        @else
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            {{ __('Creating a new post') }}
        @endif
    </div>
</div>

{{-- ============================================================
     MAIN FORM
     ============================================================ --}}
<form id="pf-form" action="{{ $action }}" method="POST" enctype="multipart/form-data" novalidate>
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="pf-layout">

        {{-- ========================
             LEFT: MAIN CONTENT
             ======================== --}}
        <div class="pf-main">

            {{-- Card: Title & Slug --}}
            <div class="pf-card">
                <div class="pf-card-header">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    <span>{{ __('Post Information') }}</span>
                </div>
                <div class="pf-card-body">
                    <div class="pf-field {{ $errors->has('title') ? 'pf-field-error' : '' }}">
                        <label class="pf-label" for="title">{{ __('Post Title') }} <span class="pf-required">*</span></label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            class="pf-input pf-input-xl"
                            value="{{ old('title', $post->title ?? '') }}"
                            placeholder="{{ __('Enter a captivating title...') }}"
                            required
                            autofocus
                        >
                        @error('title')
                            <span class="pf-error-msg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="pf-field {{ $errors->has('slug') ? 'pf-field-error' : '' }}">
                        <label class="pf-label" for="slug">{{ __('Slug (URL)') }}</label>
                        <div class="pf-input-group">
                            <span class="pf-input-prefix">/blog/</span>
                            <input
                                type="text"
                                id="slug"
                                name="slug"
                                class="pf-input pf-input-prefixed"
                                value="{{ old('slug', $post->slug ?? '') }}"
                                placeholder="{{ __('auto-generated-from-title') }}"
                            >
                            <button type="button" class="pf-input-action" id="btn-regen-slug" title="{{ __('Re-generate from title') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                            </button>
                        </div>
                        <p class="pf-help">{{ __('Leave empty to auto-generate from title.') }}</p>
                        @error('slug')
                            <span class="pf-error-msg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="pf-field mb-0 {{ $errors->has('excerpt') ? 'pf-field-error' : '' }}">
                        <label class="pf-label" for="excerpt">{{ __('Excerpt / Summary') }}</label>
                        <textarea id="excerpt" name="excerpt" class="pf-input pf-textarea" rows="3" placeholder="{{ __('Short description displayed in post listings...') }}">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
                        <p class="pf-help">{{ __('If empty, will be auto-generated from content.') }}</p>
                        @error('excerpt')
                            <span class="pf-error-msg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Card: Content Editor --}}
            <div class="pf-card {{ $errors->has('content') ? 'pf-card-error' : '' }}">
                <div class="pf-card-header">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    <span>{{ __('Content') }} <span class="pf-required">*</span></span>
                </div>
                <div class="pf-card-body p-0">
                    <textarea name="content" id="editor" class="pf-editor-raw">{{ old('content', $post->content ?? '') }}</textarea>
                    @error('content')
                        <span class="pf-error-msg" style="margin: 0 1.5rem 1rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            {{-- Card: SEO --}}
            <div class="pf-card">
                <div class="pf-card-header">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <span>{{ __('SEO Settings') }}</span>
                    <span class="pf-badge-optional">{{ __('Optional') }}</span>
                </div>
                <div class="pf-card-body">
                    <div class="pf-field {{ $errors->has('meta_title') ? 'pf-field-error' : '' }}">
                        <label class="pf-label" for="meta_title">{{ __('Meta Title') }}</label>
                        <input type="text" id="meta_title" name="meta_title" class="pf-input" value="{{ old('meta_title', $post->meta_title ?? '') }}" placeholder="{{ __('Leave empty to use post title') }}" maxlength="60">
                        <div class="pf-seo-counter"><span id="meta-title-count">0</span>/60</div>
                        @error('meta_title')
                            <span class="pf-error-msg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                    <div class="pf-field mb-0 {{ $errors->has('meta_description') ? 'pf-field-error' : '' }}">
                        <label class="pf-label" for="meta_description">{{ __('Meta Description') }}</label>
                        <textarea id="meta_description" name="meta_description" class="pf-input pf-textarea" rows="3" placeholder="{{ __('Leave empty to use excerpt') }}" maxlength="160">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
                        <div class="pf-seo-counter"><span id="meta-desc-count">0</span>/160</div>
                        @error('meta_description')
                            <span class="pf-error-msg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================
             RIGHT: SIDEBAR
             ======================== --}}
        <div class="pf-sidebar">

            {{-- Card: Publish --}}
            <div class="pf-card pf-card-publish">
                <div class="pf-card-header">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span>{{ __('Publish') }}</span>
                </div>
                <div class="pf-card-body">
                    <div class="pf-field">
                        <label class="pf-label" for="status">{{ __('Status') }}</label>
                        <div class="pf-status-group" id="status-group">
                            @foreach(['draft' => ['label' => __('Draft'), 'color' => 'gray', 'icon' => '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>'],
                                        'published' => ['label' => __('Published'), 'color' => 'green', 'icon' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>'],
                                        'scheduled' => ['label' => __('Scheduled'), 'color' => 'amber', 'icon' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>']] as $val => $opt)
                                <label class="pf-status-option pf-status-{{ $opt['color'] }} {{ old('status', $post->status ?? 'draft') == $val ? 'active' : '' }}">
                                    <input type="radio" name="status" value="{{ $val }}" {{ old('status', $post->status ?? 'draft') == $val ? 'checked' : '' }}>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $opt['icon'] !!}</svg>
                                    {{ $opt['label'] }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="pf-field">
                        <label class="pf-label" for="lang">{{ __('Language') }} <span class="pf-required">*</span></label>
                        <select id="lang" name="lang" class="pf-input pf-select" required>
                            <option value="en" {{ old('lang', $post->lang ?? 'en') == 'en' ? 'selected' : '' }}>🇬🇧 {{ __('English') }}</option>
                            <option value="vi" {{ old('lang', $post->lang ?? '') == 'vi' ? 'selected' : '' }}>🇻🇳 {{ __('Tiếng Việt') }}</option>
                        </select>
                    </div>

                    <div class="pf-field">
                        <label class="pf-label" for="category_id">{{ __('Category') }} <span class="pf-required">*</span></label>
                        <select id="category_id" name="category_id" class="pf-input pf-select {{ $errors->has('category_id') ? 'pf-input-err' : '' }}" required>
                            <option value="">{{ __('— Select category —') }}</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $post->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="pf-error-msg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="pf-field mb-0">
                        <label class="pf-toggle">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $post->is_featured ?? false) ? 'checked' : '' }}>
                            <span class="pf-toggle-track"></span>
                            <span class="pf-toggle-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                {{ __('Featured Post') }}
                            </span>
                        </label>
                    </div>
                </div>

                <div class="pf-card-footer">
                    @if($isEdit)
                        <a href="/blog/{{ $post->slug }}" target="_blank" class="pf-btn pf-btn-ghost pf-btn-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                            {{ __('View Live Page') }}
                        </a>
                    @endif
                    <button type="submit" class="pf-btn pf-btn-primary pf-btn-full" id="pf-submit-btn">
                        @if($isEdit)
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            {{ __('Update Post') }}
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            {{ __('Publish Post') }}
                        @endif
                    </button>
                </div>
            </div>

            {{-- Card: Featured Image --}}
            <div class="pf-card">
                <div class="pf-card-header">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    <span>{{ __('Featured Image') }}</span>
                </div>
                <div class="pf-card-body">
                    <div class="pf-dropzone" id="pf-dropzone">
                        <div class="pf-dropzone-thumb" id="pf-thumb-wrapper" style="{{ ($isEdit && $post->featured_image) ? '' : 'display:none' }}">
                            <img id="pf-thumb" src="{{ ($isEdit && $post->featured_image) ? asset('uploads/posts/' . $post->featured_image) : '' }}" alt="Preview">
                            <button type="button" class="pf-thumb-remove" id="pf-thumb-remove" title="{{ __('Remove image') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                        <div class="pf-dropzone-placeholder" id="pf-dropzone-placeholder" style="{{ ($isEdit && $post->featured_image) ? 'display:none' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="pf-dropzone-icon"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            <p class="pf-dropzone-text">{{ __('Drag & drop or') }} <span>{{ __('click to upload') }}</span></p>
                            <p class="pf-dropzone-hint">PNG, JPG, WEBP — Max 2MB</p>
                        </div>
                        <input type="file" name="featured_image" id="pf-file-input" class="pf-file-hidden" accept="image/*">
                    </div>
                    @error('featured_image')
                        <span class="pf-error-msg mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            {{-- Card: Tags --}}
            <div class="pf-card">
                <div class="pf-card-header">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                    <span>{{ __('Tags') }}</span>
                </div>
                <div class="pf-card-body mb-0">
                    <div class="pf-field mb-0">
                        <label class="pf-label" for="tags_input">{{ __('Tags (comma-separated IDs)') }}</label>
                        <input type="text" id="tags_input" name="tags_input" class="pf-input" value="{{ old('tags_input', $isEdit ? $post->tags->pluck('id')->join(', ') : '') }}" placeholder="{{ __('e.g. 1, 3, 7') }}">
                        @if(isset($tags) && $tags->count())
                            <div class="pf-tag-cloud mt-2">
                                @foreach($tags as $tag)
                                    <button type="button" class="pf-tag-chip" data-id="{{ $tag->id }}">{{ $tag->name }}</button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>{{-- end sidebar --}}
    </div>{{-- end layout --}}
</form>

@push('styles')
<style>
/* ================================================================
   RESET & VARIABLES
   ================================================================ */
:root {
    --pf-primary: #6366f1;
    --pf-primary-dark: #4f46e5;
    --pf-primary-light: #e0e7ff;
    --pf-success: #22c55e;
    --pf-danger: #ef4444;
    --pf-warning: #f59e0b;
    --pf-gray-50: #f8fafc;
    --pf-gray-100: #f1f5f9;
    --pf-gray-200: #e2e8f0;
    --pf-gray-300: #cbd5e1;
    --pf-gray-400: #94a3b8;
    --pf-gray-500: #64748b;
    --pf-gray-600: #475569;
    --pf-gray-700: #334155;
    --pf-gray-800: #1e293b;
    --pf-gray-900: #0f172a;
    --pf-radius: 10px;
    --pf-radius-sm: 6px;
    --pf-shadow: 0 1px 3px rgba(0,0,0,.07), 0 1px 2px rgba(0,0,0,.05);
    --pf-shadow-md: 0 4px 6px -1px rgba(0,0,0,.1), 0 2px 4px -1px rgba(0,0,0,.06);
    --pf-transition: all .2s ease;
}

/* ================================================================
   LAYOUT
   ================================================================ */
.pf-layout {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 1.75rem;
    align-items: start;
}
.pf-main { display: flex; flex-direction: column; gap: 1.5rem; }
.pf-sidebar {
    display: flex; flex-direction: column; gap: 1.25rem;
    position: sticky; top: 1.5rem;
}
@media (max-width: 1100px) {
    .pf-layout { grid-template-columns: 1fr; }
    .pf-sidebar { position: static; }
}

/* ================================================================
   PAGE HEADER
   ================================================================ */
.pf-page-header { margin-bottom: 1.25rem; }
.pf-page-badge {
    display: inline-flex; align-items: center; gap: 0.5rem;
    padding: 0.4rem 0.875rem; border-radius: 999px;
    font-size: 0.8125rem; font-weight: 500;
}
.pf-badge-create { background: #ede9fe; color: #6d28d9; }
.pf-badge-edit   { background: #fef3c7; color: #b45309; }

/* ================================================================
   CARDS
   ================================================================ */
.pf-card {
    background: #fff;
    border: 1px solid var(--pf-gray-200);
    border-radius: var(--pf-radius);
    box-shadow: var(--pf-shadow);
    overflow: hidden;
    transition: var(--pf-transition);
}
.pf-card-error { border-color: #fecaca; }
.pf-card-publish { border-top: 3px solid var(--pf-primary); }
.pf-card-header {
    display: flex; align-items: center; gap: 0.6rem;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--pf-gray-100);
    background: var(--pf-gray-50);
    font-size: 0.875rem; font-weight: 700; color: var(--pf-gray-800);
}
.pf-card-body { padding: 1.5rem; }
.pf-card-body.p-0 { padding: 0; }
.pf-card-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--pf-gray-100);
    display: flex; flex-direction: column; gap: 0.6rem;
}
.pf-badge-optional {
    margin-left: auto; font-size: 0.7rem; font-weight: 600;
    background: var(--pf-gray-200); color: var(--pf-gray-500);
    padding: 0.2rem 0.6rem; border-radius: 999px;
    letter-spacing: 0.05em; text-transform: uppercase;
}

/* ================================================================
   FORM FIELDS
   ================================================================ */
.pf-field { margin-bottom: 1.25rem; }
.pf-field.mb-0 { margin-bottom: 0; }
.pf-field-error .pf-input { border-color: var(--pf-danger); background-color: #fff5f5; }
.pf-field-error .pf-input:focus { box-shadow: 0 0 0 3px rgba(239,68,68,.15); }

.pf-label {
    display: block; margin-bottom: 0.4rem;
    font-size: 0.8125rem; font-weight: 600; color: var(--pf-gray-700);
}
.pf-required { color: var(--pf-danger); }

.pf-input {
    display: block; width: 100%; box-sizing: border-box;
    padding: 0.625rem 0.875rem;
    font-size: 0.875rem; font-family: inherit; line-height: 1.5;
    color: var(--pf-gray-800);
    background: #fff;
    border: 1.5px solid var(--pf-gray-300);
    border-radius: var(--pf-radius-sm);
    transition: var(--pf-transition);
}
.pf-input:focus {
    outline: none; border-color: var(--pf-primary);
    box-shadow: 0 0 0 3px rgba(99,102,241,.15);
}
.pf-input-xl { font-size: 1.0625rem; font-weight: 600; padding: 0.75rem 1rem; }
.pf-input-err { border-color: var(--pf-danger); }
.pf-textarea { resize: vertical; min-height: 90px; }
.pf-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 0.75rem center; padding-right: 2.5rem; cursor: pointer; }

.pf-input-group { display: flex; align-items: stretch; }
.pf-input-prefix {
    display: flex; align-items: center;
    padding: 0 0.75rem; background: var(--pf-gray-100);
    border: 1.5px solid var(--pf-gray-300); border-right: none;
    border-radius: var(--pf-radius-sm) 0 0 var(--pf-radius-sm);
    font-size: 0.8125rem; color: var(--pf-gray-500); white-space: nowrap;
}
.pf-input-prefixed { border-radius: 0; flex: 1; }
.pf-input-action {
    display: flex; align-items: center; padding: 0 0.75rem;
    background: var(--pf-gray-100); border: 1.5px solid var(--pf-gray-300);
    border-left: none; border-radius: 0 var(--pf-radius-sm) var(--pf-radius-sm) 0;
    cursor: pointer; color: var(--pf-gray-500); transition: var(--pf-transition);
}
.pf-input-action:hover { background: var(--pf-gray-200); color: var(--pf-primary); }

.pf-help { font-size: 0.75rem; color: var(--pf-gray-400); margin-top: 0.375rem; margin-bottom: 0; }
.pf-error-msg {
    display: inline-flex; align-items: center; gap: 0.35rem;
    margin-top: 0.35rem; font-size: 0.8rem; color: var(--pf-danger); font-weight: 500;
}
.pf-seo-counter { text-align: right; font-size: 0.72rem; color: var(--pf-gray-400); margin-top: 0.25rem; }

/* ================================================================
   STATUS SELECTOR
   ================================================================ */
.pf-status-group { display: flex; flex-direction: column; gap: 0.5rem; }
.pf-status-option {
    display: flex; align-items: center; gap: 0.6rem;
    padding: 0.6rem 0.875rem; border-radius: var(--pf-radius-sm);
    border: 1.5px solid var(--pf-gray-200);
    cursor: pointer; font-size: 0.8125rem; font-weight: 500; color: var(--pf-gray-600);
    transition: var(--pf-transition); user-select: none;
}
.pf-status-option input[type="radio"] { display: none; }
.pf-status-option:hover { border-color: var(--pf-gray-300); background: var(--pf-gray-50); }
.pf-status-gray.active  { border-color: #94a3b8; background: #f1f5f9; color: var(--pf-gray-700); }
.pf-status-green.active { border-color: #86efac; background: #f0fdf4; color: #166534; }
.pf-status-amber.active { border-color: #fcd34d; background: #fffbeb; color: #92400e; }

/* ================================================================
   TOGGLE
   ================================================================ */
.pf-toggle { display: flex; align-items: center; gap: 0.75rem; cursor: pointer; user-select: none; }
.pf-toggle input { display: none; }
.pf-toggle-track {
    position: relative; width: 40px; height: 22px; flex-shrink: 0;
    background: var(--pf-gray-300); border-radius: 999px; transition: background .2s;
}
.pf-toggle-track::after {
    content:''; position: absolute; top: 3px; left: 3px;
    width: 16px; height: 16px; border-radius: 50%; background: #fff;
    box-shadow: 0 1px 2px rgba(0,0,0,.2); transition: left .2s;
}
.pf-toggle input:checked + .pf-toggle-track { background: var(--pf-primary); }
.pf-toggle input:checked + .pf-toggle-track::after { left: 21px; }
.pf-toggle-label { display: flex; align-items: center; gap: 0.4rem; font-size: 0.8125rem; font-weight: 600; color: var(--pf-gray-700); }

/* ================================================================
   BUTTONS
   ================================================================ */
.pf-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem;
    padding: 0.6rem 1.125rem; font-size: 0.875rem; font-weight: 600;
    border-radius: var(--pf-radius-sm); cursor: pointer; text-decoration: none;
    border: 1.5px solid transparent; transition: var(--pf-transition); white-space: nowrap;
}
.pf-btn-primary {
    background: linear-gradient(135deg, var(--pf-primary) 0%, var(--pf-primary-dark) 100%);
    color: #fff; border-color: var(--pf-primary-dark);
    box-shadow: 0 2px 4px rgba(99,102,241,.3);
}
.pf-btn-primary:hover {
    background: linear-gradient(135deg, var(--pf-primary-dark) 0%, #3730a3 100%);
    box-shadow: 0 4px 8px rgba(99,102,241,.4); transform: translateY(-1px);
    color: #fff;
}
.pf-btn-primary:active { transform: translateY(0); }
.pf-btn-ghost {
    background: #fff; color: var(--pf-gray-600);
    border-color: var(--pf-gray-300);
}
.pf-btn-ghost:hover { background: var(--pf-gray-50); color: var(--pf-gray-800); border-color: var(--pf-gray-400); }
.pf-btn-full { width: 100%; }
.pf-btn-sm { padding: 0.45rem 0.875rem; font-size: 0.8125rem; }

/* ================================================================
   DRAG & DROP IMAGE
   ================================================================ */
.pf-dropzone {
    position: relative; border: 2px dashed var(--pf-gray-300);
    border-radius: var(--pf-radius); padding: 0;
    cursor: pointer; transition: var(--pf-transition); overflow: hidden;
    background: var(--pf-gray-50); min-height: 160px;
    display: flex; align-items: center; justify-content: center;
}
.pf-dropzone:hover, .pf-dropzone.dragover {
    border-color: var(--pf-primary); background: #f5f3ff;
}
.pf-file-hidden {
    position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
}
.pf-dropzone-placeholder { text-align: center; padding: 2rem 1rem; pointer-events: none; }
.pf-dropzone-icon { color: var(--pf-gray-400); margin-bottom: 0.75rem; }
.pf-dropzone-text { font-size: 0.875rem; color: var(--pf-gray-500); margin: 0 0 0.25rem; }
.pf-dropzone-text span { color: var(--pf-primary); font-weight: 600; }
.pf-dropzone-hint { font-size: 0.75rem; color: var(--pf-gray-400); margin: 0; }

.pf-dropzone-thumb { position: relative; width: 100%; }
.pf-dropzone-thumb img { display: block; width: 100%; height: 180px; object-fit: cover; }
.pf-thumb-remove {
    position: absolute; top: 0.5rem; right: 0.5rem;
    width: 28px; height: 28px;
    background: rgba(0,0,0,.65); color: #fff; border: none;
    border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center;
    transition: var(--pf-transition);
}
.pf-thumb-remove:hover { background: var(--pf-danger); transform: scale(1.1); }

/* ================================================================
   TAGS
   ================================================================ */
.pf-tag-cloud { display: flex; flex-wrap: wrap; gap: 0.4rem; margin-top: 0.5rem; }
.pf-tag-chip {
    padding: 0.2rem 0.6rem; border-radius: 999px;
    background: var(--pf-gray-100); border: 1.5px solid var(--pf-gray-200);
    font-size: 0.75rem; color: var(--pf-gray-600); cursor: pointer;
    transition: var(--pf-transition);
}
.pf-tag-chip:hover { background: var(--pf-primary-light); border-color: var(--pf-primary); color: var(--pf-primary-dark); }
.pf-tag-chip.selected { background: var(--pf-primary-light); border-color: var(--pf-primary); color: var(--pf-primary-dark); font-weight: 600; }
.mt-2 { margin-top: 0.5rem; }

/* ================================================================
   TOAST NOTIFICATIONS
   ================================================================ */
.pf-toast {
    display: flex; align-items: flex-start; gap: 0.875rem;
    padding: 1rem 1.25rem; border-radius: var(--pf-radius);
    margin-bottom: 1.5rem; position: relative;
    animation: pf-slide-in .35s cubic-bezier(.22,1,.36,1);
}
@keyframes pf-slide-in {
    from { opacity: 0; transform: translateY(-12px); }
    to   { opacity: 1; transform: translateY(0); }
}
.pf-toast-error { background: #fff5f5; border: 1.5px solid #fecaca; }
.pf-toast-icon { flex-shrink: 0; margin-top: 1px; }
.pf-toast-error .pf-toast-icon { color: var(--pf-danger); }
.pf-toast-body { flex: 1; }
.pf-toast-title { font-size: 0.875rem; font-weight: 700; color: #991b1b; margin: 0 0 0.35rem; }
.pf-toast-list { margin: 0; padding-left: 1.1rem; }
.pf-toast-list li { font-size: 0.8125rem; color: #b91c1c; margin-bottom: 0.25rem; }
.pf-toast-close {
    flex-shrink: 0; background: none; border: none; cursor: pointer;
    color: #b91c1c; display: flex; align-items: center; opacity: .6; transition: var(--pf-transition);
}
.pf-toast-close:hover { opacity: 1; }

/* ================================================================
   CKEDITOR OVERRIDES
   ================================================================ */
.pf-editor-raw { display: none; }
.ck-editor__editable_inline {
    min-height: 480px !important; height: auto !important;
    font-family: 'Inter', system-ui, sans-serif !important;
    font-size: 1rem !important; line-height: 1.75 !important;
    color: var(--pf-gray-700) !important;
}
.ck.ck-editor__main > .ck-editor__editable {
    background: #fff !important; border: none !important; box-shadow: none !important;
}
.ck.ck-toolbar {
    background: var(--pf-gray-50) !important;
    border-bottom: 1px solid var(--pf-gray-200) !important;
    border-radius: 0 !important; padding: 0.5rem !important; border: none !important;
}
.ck.ck-content {
    border: none !important; padding: 2rem 2.5rem !important;
}
.ck-content h1,.ck-content h2,.ck-content h3,.ck-content h4 { color: var(--pf-gray-900); font-weight: 700; margin-top:1.5em; margin-bottom:.75em; }
.ck-content h1 { font-size: 1.875rem; }
.ck-content h2 { font-size: 1.5rem; }
.ck-content h3 { font-size: 1.25rem; }
.ck-content p { margin-bottom: 1.25em; }
.ck-content ul,.ck-content ol { margin-bottom:1.25em; padding-left:1.5rem; }
.ck-content li { margin-bottom:.5em; }
.ck-content blockquote { border-left:4px solid var(--pf-primary); margin:1.5em 0; color: var(--pf-gray-600); background: var(--pf-primary-light); padding:1rem 1.5rem; border-radius:0 8px 8px 0; font-style:italic; }
.ck-content a { color: var(--pf-primary); text-decoration: underline; }
.ck-content strong { font-weight: 700; color: var(--pf-gray-900); }
.ck-content .table { margin-bottom:1.5em; width:100%; border-collapse:collapse; }
.ck-content .table td,.ck-content .table th { border:1px solid var(--pf-gray-200); padding:.75rem; }
.ck-content .table th { background: var(--pf-gray-50); font-weight:600; }

/* Submit btn loading state */
.pf-btn-primary.loading { opacity: .75; pointer-events: none; }
.pf-btn-primary.loading::before {
    content:''; display: inline-block; width: 15px; height: 15px;
    border: 2px solid rgba(255,255,255,.4); border-top-color: #fff;
    border-radius: 50%; animation: pf-spin .7s linear infinite;
    margin-right: 0.5rem;
}
@keyframes pf-spin { to { transform: rotate(360deg); } }
</style>
@endpush

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ----------------------------------------------------------------
       1. CKEditor
     ---------------------------------------------------------------- */
    ClassicEditor.create(document.querySelector('#editor'), {
        toolbar: [
            'heading', '|',
            'bold', 'italic', 'underline', 'link', '|',
            'bulletedList', 'numberedList', 'blockQuote', '|',
            'insertTable', 'mediaEmbed', '|',
            'undo', 'redo'
        ],
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
            ]
        }
    }).catch(console.error);

    /* ----------------------------------------------------------------
       2. Auto-slug from title
     ---------------------------------------------------------------- */
    const titleInput = document.getElementById('title');
    const slugInput  = document.getElementById('slug');
    const btnRegen   = document.getElementById('btn-regen-slug');
    let slugManual   = slugInput.value.length > 0;

    function toSlug(str) {
        return str.toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
            .replace(/đ/g,'d').replace(/[^a-z0-9\s-]/g,'')
            .trim().replace(/\s+/g,'-').replace(/-+/g,'-');
    }

    titleInput.addEventListener('input', function () {
        if (!slugManual) slugInput.value = toSlug(this.value);
    });
    slugInput.addEventListener('input', function () {
        slugManual = this.value.length > 0;
    });
    if (btnRegen) {
        btnRegen.addEventListener('click', function () {
            slugInput.value = toSlug(titleInput.value);
            slugManual = false;
            slugInput.focus();
        });
    }

    /* ----------------------------------------------------------------
       3. Status radio styling
     ---------------------------------------------------------------- */
    document.querySelectorAll('.pf-status-option').forEach(function(label) {
        label.addEventListener('click', function() {
            document.querySelectorAll('.pf-status-option').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });

    /* ----------------------------------------------------------------
       4. Drag & Drop image upload
     ---------------------------------------------------------------- */
    const dropzone     = document.getElementById('pf-dropzone');
    const fileInput    = document.getElementById('pf-file-input');
    const thumbWrapper = document.getElementById('pf-thumb-wrapper');
    const thumbImg     = document.getElementById('pf-thumb');
    const placeholder  = document.getElementById('pf-dropzone-placeholder');
    const removeBtn    = document.getElementById('pf-thumb-remove');

    function showPreview(file) {
        if (!file || !file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            thumbImg.src = e.target.result;
            thumbWrapper.style.display = '';
            placeholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }

    dropzone.addEventListener('click', (e) => { if (!e.target.closest('.pf-thumb-remove')) fileInput.click(); });
    dropzone.addEventListener('dragover', (e) => { e.preventDefault(); dropzone.classList.add('dragover'); });
    dropzone.addEventListener('dragleave', () => dropzone.classList.remove('dragover'));
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault(); dropzone.classList.remove('dragover');
        const file = e.dataTransfer.files[0];
        if (file) { fileInput.files = e.dataTransfer.files; showPreview(file); }
    });
    fileInput.addEventListener('change', () => showPreview(fileInput.files[0]));
    if (removeBtn) {
        removeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            fileInput.value = '';
            thumbWrapper.style.display = 'none';
            placeholder.style.display = '';
            thumbImg.src = '';
        });
    }

    /* ----------------------------------------------------------------
       5. Tag cloud click toggles
     ---------------------------------------------------------------- */
    const tagsInput = document.getElementById('tags_input');
    document.querySelectorAll('.pf-tag-chip').forEach(function(chip) {
        chip.addEventListener('click', function() {
            const id = this.dataset.id;
            let ids = tagsInput.value ? tagsInput.value.split(',').map(s=>s.trim()).filter(Boolean) : [];
            const idx = ids.indexOf(id);
            if (idx === -1) { ids.push(id); this.classList.add('selected'); }
            else            { ids.splice(idx,1); this.classList.remove('selected'); }
            tagsInput.value = ids.join(', ');
        });

        // Mark pre-selected tags
        const ids = tagsInput.value ? tagsInput.value.split(',').map(s=>s.trim()) : [];
        if (ids.includes(chip.dataset.id)) chip.classList.add('selected');
    });

    /* ----------------------------------------------------------------
       6. SEO character counters
     ---------------------------------------------------------------- */
    const metaTitle = document.getElementById('meta_title');
    const metaDesc  = document.getElementById('meta_description');
    const mtCount   = document.getElementById('meta-title-count');
    const mdCount   = document.getElementById('meta-desc-count');
    function updateCount(input, display) {
        const len = input.value.length;
        const max = parseInt(input.maxLength);
        display.textContent = len;
        display.style.color = len > max * 0.9 ? '#ef4444' : len > max * 0.7 ? '#f59e0b' : '#94a3b8';
    }
    if (metaTitle) {
        updateCount(metaTitle, mtCount);
        metaTitle.addEventListener('input', () => updateCount(metaTitle, mtCount));
    }
    if (metaDesc) {
        updateCount(metaDesc, mdCount);
        metaDesc.addEventListener('input', () => updateCount(metaDesc, mdCount));
    }

    /* ----------------------------------------------------------------
       7. Submit button loading state
     ---------------------------------------------------------------- */
    const form      = document.getElementById('pf-form');
    const submitBtn = document.getElementById('pf-submit-btn');
    form.addEventListener('submit', function() {
        submitBtn.classList.add('loading');
        submitBtn.innerHTML = '<span>{{ __("Saving...") }}</span>';
    });

    /* ----------------------------------------------------------------
       8. Auto-dismiss success toast (from layout)
     ---------------------------------------------------------------- */
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        setTimeout(() => { successAlert.style.opacity = '0'; successAlert.style.transition = 'opacity .5s'; setTimeout(() => successAlert.remove(), 500); }, 4000);
    }
});
</script>
@endpush
@endsection