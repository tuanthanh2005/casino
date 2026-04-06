@extends('layouts.admin')

@section('page_title', isset($category) ? __('Edit Category') : __('Create Category'))

@section('admin_content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}" method="POST">
            @csrf
            @if(isset($category))
                @method('PUT')
            @endif

            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px;">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase opacity-50">{{ __('Language') }}</label>
                        <select name="lang" class="form-select border-0 bg-light py-2" required style="border-radius: 10px;">
                            <option value="en" {{ (old('lang', $category->lang ?? '')) == 'en' ? 'selected' : '' }}>{{ __('English') }}</option>
                            <option value="vi" {{ (old('lang', $category->lang ?? '')) == 'vi' ? 'selected' : '' }}>{{ __('Tiếng Việt') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase opacity-50">{{ __('Category Name') }}</label>
                        <input type="text" name="name" class="form-control border-0 bg-light py-2" value="{{ old('name', $category->name ?? '') }}" placeholder="{{ __('e.g. Beginners Guide') }}" required style="border-radius: 10px;">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold small text-uppercase opacity-50">{{ __('Slug (URL Path)') }}</label>
                        <input type="text" name="slug" class="form-control border-0 bg-light py-2" value="{{ old('slug', $category->slug ?? '') }}" placeholder="{{ __('e.g. beginners-guide') }}" style="border-radius: 10px;">
                        <div class="text-secondary smaller mt-2">{{ __('Leave blank to auto-generate from name.') }}</div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold small text-uppercase opacity-50">{{ __('Description') }}</label>
                        <textarea name="description" class="form-control border-0 bg-light py-2" rows="3" placeholder="{{ __('Describe the focus of this category...') }}" style="border-radius: 10px;">{{ old('description', $category->description ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm p-4 mb-5" style="border-radius: 20px; background: #fafbfc;">
                <h6 class="fw-bold mb-4">{{ __('SEO & Metadata (Search Appearance)') }}</h6>
                <div class="row g-4">
                    <div class="col-md-12">
                        <label class="form-label fw-bold small text-uppercase opacity-50">{{ __('Meta title') }}</label>
                        <input type="text" name="meta_title" class="form-control border-0 bg-white shadow-sm py-2" value="{{ old('meta_title', $category->meta_title ?? '') }}" placeholder="{{ __('Browser title...') }}" style="border-radius: 10px;">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold small text-uppercase opacity-50">{{ __('Meta description') }}</label>
                        <textarea name="meta_description" class="form-control border-0 bg-white shadow-sm py-2" rows="2" placeholder="{{ __('Search engine description...') }}" style="border-radius: 10px;">{{ old('meta_description', $category->meta_description ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-5">
                <a href="{{ route('admin.categories.index') }}" class="text-secondary text-decoration-none small fw-bold">{{ __('Cancel and go back') }}</a>
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold" style="border-radius: 99px; background: #0f172a; border: none; box-shadow: 0 10px 20px rgba(0,0,0,0.1);">
                    {{ isset($category) ? __('Save Changes') : __('Create Category') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
