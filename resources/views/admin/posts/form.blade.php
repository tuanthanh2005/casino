@extends('layouts.admin')

@php
    $isEdit = isset($post);
    $action = $isEdit ? route('admin.posts.update', $post) : route('admin.posts.store');
    $title = $isEdit ? __('Edit Post') . ': ' . $post->title : __('Create New Post');
@endphp

@section('page_title', $title)

@section('admin_content')
<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="admin-form" style="display: grid; grid-template-columns: 1fr 320px; gap: 2rem;">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="card" style="padding: 2rem;">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">{{ __('Post Title') }}</label>
                <input type="text" name="title" value="{{ old('title', $post->title ?? '') }}" placeholder="{{ __('Enter catchy title...') }}" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 1rem; font-weight: 600;" required>
                @error('title') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">{{ __('Slug (URL)') }}</label>
                <input type="text" name="slug" value="{{ old('slug', $post->slug ?? '') }}" placeholder="{{ __('best-fish-for-beginners') }}" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.8125rem;">
                <p style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.25rem;">{{ __('Leave empty to auto-generate from title.') }}</p>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">{{ __('Excerpt / Summary') }}</label>
                <textarea name="excerpt" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">{{ __('Content') }}</label>
                <textarea name="content" id="editor" rows="20" style="width: 100%; padding: 1rem; border: 1px solid #e2e8f0; border-radius: 8px; font-family: monospace; font-size: 0.9375rem;" required>{{ old('content', $post->content ?? '') }}</textarea>
                <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.5rem;">{{ __('Tip: Use Markdown or HTML for formatting.') }}</p>
            </div>
        </div>

        <div class="card" style="padding: 2rem;">
            <h3 style="font-size: 1.125rem; margin-bottom: 1.5rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1rem;">{{ __('SEO Settings') }}</h3>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">{{ __('Meta title') }}</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $post->meta_title ?? '') }}" style="width: 100%; padding: 0.625rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.8125rem;">
            </div>

            <div>
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">{{ __('Meta description') }}</label>
                <textarea name="meta_description" rows="3" style="width: 100%; padding: 0.625rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.8125rem;">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <!-- Sidebar actions -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem; height: fit-content; position: sticky; top: 1.5rem;">
        <div class="card" style="padding: 1.5rem;">
            <h3 style="font-size: 0.875rem; text-transform: uppercase; color: #94a3b8; margin-bottom: 1.25rem;">{{ __('Publish Action') }}</h3>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 0.5rem; font-size: 0.75rem;">{{ __('Status') }}</label>
                <select name="status" style="width: 100%; padding: 0.625rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.8125rem;">
                    <option value="draft" {{ old('status', $post->status ?? '') == 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                    <option value="published" {{ old('status', $post->status ?? '') == 'published' ? 'selected' : '' }}>{{ __('Published') }}</option>
                    <option value="scheduled" {{ old('status', $post->status ?? '') == 'scheduled' ? 'selected' : '' }}>{{ __('Scheduled') }}</option>
                </select>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 0.5rem; font-size: 0.75rem;">{{ __('Language') }}</label>
                <select name="lang" style="width: 100%; padding: 0.625rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.8125rem;" required>
                    <option value="en" {{ (old('lang', $post->lang ?? 'en') == 'en') ? 'selected' : '' }}>{{ __('English') }}</option>
                    <option value="vi" {{ (old('lang', $post->lang ?? '') == 'vi') ? 'selected' : '' }}>{{ __('Tiếng Việt') }}</option>
                </select>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.8125rem; font-weight: 600;">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $post->is_featured ?? false) ? 'checked' : '' }}>
                    {{ __('Make Featured Post') }}
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1rem;">
                {{ $isEdit ? __('Update Post') : __('Publish Post') }}
            </button>

            @if($isEdit)
                <a href="/blog/{{ $post->slug }}" target="_blank" class="btn" style="width: 100%; justify-content: center; background: #e0f2fe; color: #0369a1; margin-top: 0.75rem; border: none;">{{ __('View Live Page') }}</a>
            @endif
        </div>

        <div class="card" style="padding: 1.5rem;">
            <h3 style="font-size: 0.875rem; text-transform: uppercase; color: #94a3b8; margin-bottom: 1.25rem;">{{ __('Organization') }}</h3>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 0.5rem; font-size: 0.75rem;">{{ __('Category') }}</label>
                <select name="category_id" style="width: 100%; padding: 0.625rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.8125rem;" required>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $post->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 0.5rem; font-size: 0.75rem;">{{ __('Tags (ID comma separated)') }}</label>
                <input type="text" name="tags_input" placeholder="{{ __('1, 2, 5') }}" style="width: 100%; padding: 0.625rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.8125rem;">
            </div>
        </div>

        <div class="card" style="padding: 1.5rem;">
            <h3 style="font-size: 0.875rem; text-transform: uppercase; color: #94a3b8; margin-bottom: 1.25rem;">{{ __('Featured Image') }}</h3>
            @if($isEdit && $post->featured_image)
                <img src="{{ asset('uploads/posts/' . $post->featured_image) }}" style="width: 100%; border-radius: 8px; margin-bottom: 1rem; border: 1px solid #e2e8f0;">
            @endif
            <input type="file" name="featured_image" style="font-size: 0.75rem; color: #64748b; width: 100%;">
            <p style="font-size: 0.625rem; color: #94a3b8; margin-top: 0.5rem;">{{ __('Recommended: 1200x630px. Max 2MB.') }}</p>
        </div>
    </div>
</form>

@push('styles')
<style>
    .ck-editor__editable_inline {
        min-height: 400px;
        font-family: 'Inter', sans-serif !important;
        font-size: 1rem !important;
        line-height: 1.6 !important;
    }
    .ck.ck-editor__main>.ck-editor__editable {
        background: #fff !important;
        border: none !important;
        box-shadow: none !important;
    }
    .ck.ck-toolbar {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px 8px 0 0 !important;
    }
    .ck.ck-content {
        border: 1px solid #e2e8f0 !important;
        border-top: none !important;
        border-radius: 0 0 8px 8px !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: [
                'heading', '|', 
                'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|',
                'insertTable', 'mediaEmbed', 'undo', 'redo'
            ],
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                ]
            }
        })
        .catch(error => {
            console.error(error);
        });
</script>
@endpush
@endsection
