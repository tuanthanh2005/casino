@extends('layouts.admin')

@php
    $isEdit = isset($post);
    $action = $isEdit ? route('admin.posts.update', $post) : route('admin.posts.store');
    $title = $isEdit ? __('Edit Post') . ': ' . $post->title : __('Create New Post');
@endphp

@section('page_title', $title)

@section('admin_content')
    <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="admin-form"
        >
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="admin-form-layout">
            <div class="main-content">
                <div class="card card-lg">
                    <div class="form-group">
                        <label class="form-label">{{ __('Post Title') }}</label>
                        <input type="text" name="title" class="form-control form-control-lg" value="{{ old('title', $post->title ?? '') }}" placeholder="{{ __('Enter catchy title...') }}" required>
                        @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __('Slug (URL)') }}</label>
                        <input type="text" name="slug" class="form-control" value="{{ old('slug', $post->slug ?? '') }}" placeholder="{{ __('best-fish-for-beginners') }}">
                        <p class="form-text">{{ __('Leave empty to auto-generate from title.') }}</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __('Excerpt / Summary') }}</label>
                        <textarea name="excerpt" class="form-control" rows="3">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">{{ __('Content') }}</label>
                        <textarea name="content" id="editor" class="form-control font-mono" rows="20" required>{{ old('content', $post->content ?? '') }}</textarea>
                        <p class="form-text">{{ __('Tip: Use Markdown or HTML for formatting.') }}</p>
                    </div>
                </div>

                <div class="card card-lg">
                    <h3 class="card-header">{{ __('SEO Settings') }}</h3>

                    <div class="form-group">
                        <label class="form-label">{{ __('Meta title') }}</label>
                        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $post->meta_title ?? '') }}">
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">{{ __('Meta description') }}</label>
                        <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="sidebar-content">
                <div class="card">
                    <h3 class="card-subheader">{{ __('Publish Action') }}</h3>

                    <div class="form-group">
                        <label class="form-label text-sm">{{ __('Status') }}</label>
                        <select name="status" class="form-control">
                            <option value="draft" {{ old('status', $post->status ?? '') == 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                            <option value="published" {{ old('status', $post->status ?? '') == 'published' ? 'selected' : '' }}>{{ __('Published') }}</option>
                            <option value="scheduled" {{ old('status', $post->status ?? '') == 'scheduled' ? 'selected' : '' }}>{{ __('Scheduled') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label text-sm">{{ __('Language') }}</label>
                        <select name="lang" class="form-control" required>
                            <option value="en" {{ (old('lang', $post->lang ?? 'en') == 'en') ? 'selected' : '' }}>{{ __('English') }}</option>
                            <option value="vi" {{ (old('lang', $post->lang ?? '') == 'vi') ? 'selected' : '' }}>{{ __('Tiếng Việt') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $post->is_featured ?? false) ? 'checked' : '' }}>
                            {{ __('Make Featured Post') }}
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        {{ $isEdit ? __('Update Post') : __('Publish Post') }}
                    </button>

                    @if($isEdit)
                        <a href="/blog/{{ $post->slug }}" target="_blank" class="btn btn-outline-info btn-block mt-3">{{ __('View Live Page') }}</a>
                    @endif
                </div>

                <div class="card">
                    <h3 class="card-subheader">{{ __('Organization') }}</h3>

                    <div class="form-group">
                        <label class="form-label text-sm">{{ __('Category') }}</label>
                        <select name="category_id" class="form-control" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $post->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label text-sm">{{ __('Tags (ID comma separated)') }}</label>
                        <input type="text" name="tags_input" class="form-control" placeholder="{{ __('1, 2, 5') }}">
                    </div>
                </div>

                <div class="card">
                    <h3 class="card-subheader">{{ __('Featured Image') }}</h3>

                    @if($isEdit && $post->featured_image)
                        <div class="image-preview">
                            <img src="{{ asset('uploads/posts/' . $post->featured_image) }}" alt="Featured Image">
                        </div>
                    @endif

                    <input type="file" name="featured_image" class="form-control-file">
                    <p class="form-text">{{ __('Recommended: 1200x630px. Max 2MB.') }}</p>
                </div>
            </div>
        </div>
    </form>

    @push('styles')
        <style>
            /* ==========================================================================
               1. LAYOUT & RESPONSIVE GRID
               ========================================================================== */
            .admin-form-layout {
                display: grid;
                grid-template-columns: 1fr 320px;
                gap: 2rem;
                align-items: start;
            }
            .main-content {
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
            }
            .sidebar-content {
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
                position: sticky;
                top: 1.5rem;
            }

            /* Responsive cho màn hình nhỏ (Tablet/Mobile) */
            @media (max-width: 992px) {
                .admin-form-layout {
                    grid-template-columns: 1fr; /* Tràn 1 cột */
                }
                .sidebar-content {
                    position: static; /* Tắt sticky trên mobile */
                }
            }

            /* ==========================================================================
               2. CARDS & TYPOGRAPHY
               ========================================================================== */
            .card {
                background: #ffffff;
                border-radius: 12px;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
                padding: 1.5rem;
                border: 1px solid #f1f5f9;
            }
            .card-lg {
                padding: 2rem;
            }
            .card-header {
                font-size: 1.125rem;
                font-weight: 600;
                margin-top: 0;
                margin-bottom: 1.5rem;
                border-bottom: 1px solid #f1f5f9;
                padding-bottom: 1rem;
                color: #1e293b;
            }
            .card-subheader {
                font-size: 0.875rem;
                text-transform: uppercase;
                color: #64748b;
                margin-top: 0;
                margin-bottom: 1.25rem;
                letter-spacing: 0.05em;
                font-weight: 700;
            }

            /* ==========================================================================
               3. FORMS & INPUTS
               ========================================================================== */
            .form-group {
                margin-bottom: 1.5rem;
            }
            .mb-0 { margin-bottom: 0 !important; }
            .mt-3 { margin-top: 1rem !important; }

            .form-label {
                display: block;
                font-weight: 600;
                color: #334155;
                margin-bottom: 0.5rem;
                font-size: 0.875rem;
            }
            .text-sm {
                font-size: 0.8125rem;
            }

            .form-control {
                display: block;
                width: 100%;
                padding: 0.625rem 0.75rem;
                font-size: 0.875rem;
                font-family: inherit;
                line-height: 1.5;
                color: #1e293b;
                background-color: #fff;
                border: 1px solid #cbd5e1;
                border-radius: 8px;
                transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
                box-sizing: border-box;
            }
            .form-control:focus {
                outline: none;
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
            }
            .form-control-lg {
                padding: 0.875rem 1rem;
                font-size: 1rem;
                font-weight: 600;
            }
            .font-mono {
                font-family: monospace;
            }

            .form-control-file {
                display: block;
                width: 100%;
                font-size: 0.875rem;
                color: #64748b;
            }
            .form-control-file::file-selector-button {
                padding: 0.5rem 1rem;
                border-radius: 6px;
                border: 1px solid #e2e8f0;
                background: #f8fafc;
                color: #475569;
                font-weight: 500;
                cursor: pointer;
                transition: background 0.2s;
                margin-right: 1rem;
            }
            .form-control-file::file-selector-button:hover {
                background: #e2e8f0;
            }

            .form-text {
                font-size: 0.75rem;
                color: #64748b;
                margin-top: 0.5rem;
                margin-bottom: 0;
            }
            .text-danger {
                color: #ef4444;
                font-size: 0.8125rem;
                margin-top: 0.25rem;
                display: block;
            }
            .checkbox-label {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                cursor: pointer;
                font-size: 0.875rem;
                font-weight: 600;
                color: #334155;
                user-select: none;
            }

            /* ==========================================================================
               4. BUTTONS & IMAGES
               ========================================================================== */
            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0.75rem 1.5rem;
                font-size: 0.875rem;
                font-weight: 600;
                border-radius: 8px;
                cursor: pointer;
                transition: all 0.2s ease;
                border: 1px solid transparent;
                text-decoration: none;
                text-align: center;
            }
            .btn-block {
                width: 100%;
                box-sizing: border-box;
            }
            .btn-primary {
                background-color: #0f172a;
                color: #ffffff;
            }
            .btn-primary:hover {
                background-color: #1e293b;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }
            .btn-outline-info {
                background-color: #e0f2fe;
                color: #0369a1;
                border-color: #bae6fd;
            }
            .btn-outline-info:hover {
                background-color: #bae6fd;
                color: #0c4a6e;
            }

            .image-preview {
                border-radius: 8px;
                overflow: hidden;
                border: 1px solid #e2e8f0;
                margin-bottom: 1rem;
                background: #f8fafc;
            }
            .image-preview img {
                display: block;
                width: 100%;
                height: auto;
                object-fit: cover;
            }

            /* ==========================================================================
               5. CKEDITOR OVERRIDES (Giữ nguyên của bạn)
               ========================================================================== */
            .ck-editor__editable_inline {
                min-height: 500px;
                height: auto !important; 
                font-family: 'Inter', system-ui, sans-serif !important;
                font-size: 1.05rem !important; 
                line-height: 1.7 !important;
                color: #334155 !important; 
            }
            .ck.ck-editor__main>.ck-editor__editable {
                background: #fff !important;
                border: none !important;
                box-shadow: none !important;
            }
            .ck.ck-toolbar {
                background: #f8fafc !important;
                border: 1px solid #cbd5e1 !important;
                border-bottom: none !important;
                border-radius: 8px 8px 0 0 !important;
                padding: 0.5rem !important;
            }
            .ck.ck-content {
                border: 1px solid #cbd5e1 !important;
                border-radius: 0 0 8px 8px !important;
                padding: 2.5rem !important; 
            }
            /* Các thuộc tính Typography của Editor */
            .ck-content h1, .ck-content h2, .ck-content h3, .ck-content h4 { color: #0f172a; font-weight: 700; margin-top: 1.5em; margin-bottom: 0.75em; line-height: 1.3; }
            .ck-content h1 { font-size: 1.875rem; }
            .ck-content h2 { font-size: 1.5rem; }
            .ck-content h3 { font-size: 1.25rem; }
            .ck-content p { margin-bottom: 1.25em; }
            .ck-content ul, .ck-content ol { margin-bottom: 1.25em; padding-left: 1.5rem; }
            .ck-content li { margin-bottom: 0.5em; }
            .ck-content blockquote { border-left: 4px solid #94a3b8; margin: 1.5em 0; font-style: italic; color: #475569; background: #f8fafc; padding: 1rem 1.5rem; border-radius: 0 8px 8px 0; }
            .ck-content a { color: #0284c7; text-decoration: underline; }
            .ck-content strong { font-weight: 700; color: #1e293b; }
            .ck-content .table { margin-bottom: 1.5em; width: 100%; border-collapse: collapse; }
            .ck-content .table td, .ck-content .table th { border: 1px solid #e2e8f0; padding: 0.75rem; }
            .ck-content .table th { background: #f8fafc; font-weight: 600; }
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