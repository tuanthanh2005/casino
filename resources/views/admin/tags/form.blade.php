@extends('layouts.admin')

@section('page_title', isset($tag) ? __('Edit Tag') : __('Create Tag'))

@section('admin_content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <form action="{{ isset($tag) ? route('admin.tags.update', $tag) : route('admin.tags.store') }}" method="POST">
            @csrf
            @if(isset($tag))
                @method('PUT')
            @endif

            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px;">
                <div class="row g-4">
                    <div class="col-md-12">
                        <label class="form-label fw-bold small text-uppercase opacity-50">{{ __('Tag Name') }}</label>
                        <input type="text" name="name" class="form-control border-0 bg-light py-2" value="{{ old('name', $tag->name ?? '') }}" placeholder="{{ __('e.g. Technology') }}" required style="border-radius: 10px;">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold small text-uppercase opacity-50">{{ __('Slug (URL Path)') }}</label>
                        <input type="text" name="slug" class="form-control border-0 bg-light py-2" value="{{ old('slug', $tag->slug ?? '') }}" placeholder="{{ __('e.g. technology') }}" style="border-radius: 10px;">
                        <div class="text-secondary smaller mt-2">{{ __('Leave blank to auto-generate.') }}</div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-5">
                <a href="{{ route('admin.tags.index') }}" class="text-secondary text-decoration-none small fw-bold">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold" style="border-radius: 99px; background: #0f172a; border: none;">
                    {{ isset($tag) ? __('Update Tag') : __('Create Tag') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
