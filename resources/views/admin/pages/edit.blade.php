@extends('layouts.admin')

@section('page_title', __('Edit Page'))

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush

@section('header_actions')
<a href="{{ route('admin.pages.index') }}" class="btn btn-light border fw-bold" style="border-radius: 8px;">
    {{ __('Cancel') }}
</a>
@endsection

@section('admin_content')
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <form action="{{ route('admin.pages.update', $page) }}" method="POST" id="pageForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">{{ __('Page Title') }}</label>
                        <input type="text" name="title" value="{{ $page->title }}" class="form-control" style="font-size: 1.25rem; font-weight: 600;" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">{{ __('Page Content') }}</label>
                        <input type="hidden" name="content" id="content-input">
                        <div id="editor" style="height: 400px; font-size: 1rem;">{!! $page->content !!}</div>
                    </div>

            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">SEO Settings</h5>
                <div class="mb-3">
                    <label class="form-label fw-bold">{{ __('SEO Title') }}</label>
                    <input type="text" name="seo_title" value="{{ $page->seo_title }}" class="form-control" placeholder="Optional">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">{{ __('SEO Description') }}</label>
                    <textarea name="seo_description" rows="3" class="form-control" placeholder="Optional">{{ $page->seo_description }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">{{ __('Publish') }}</h5>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">{{ __('Language') }}</label>
                    <select name="lang" class="form-select">
                        <option value="vi" {{ $page->lang == 'vi' ? 'selected' : '' }}>Tiếng Việt</option>
                        <option value="en" {{ $page->lang == 'en' ? 'selected' : '' }}>English</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">{{ __('Custom URL / Slug') }}</label>
                    <input type="text" name="slug" value="{{ $page->slug }}" class="form-control" placeholder="leave blank to auto-generate">
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="isActive" value="1" {{ $page->is_active ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold" for="isActive">{{ __('Active (Visible)') }}</label>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold py-2">{{ __('Save Changes') }}</button>
            </div>
        </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    var quill = new Quill('#editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [2, 3, 4, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                ['link', 'blockquote', 'code-block'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['clean']
            ]
        }
    });

    document.getElementById('pageForm').onsubmit = function() {
        var html = quill.root.innerHTML;
        document.getElementById('content-input').value = html;
        if(html === '<p><br></p>') {
            alert('Content cannot be empty');
            return false;
        }
    };
</script>
@endpush
