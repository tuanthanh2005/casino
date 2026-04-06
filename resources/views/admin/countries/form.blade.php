@extends('layouts.admin')

@section('page_title', isset($country) ? __('Edit Country') : __('Add New Country'))

@section('admin_content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form action="{{ isset($country) ? route('admin.countries.update', $country) : route('admin.countries.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($country))
                @method('PUT')
            @endif

            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px;">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase opacity-50">{{ __('Country Name') }}</label>
                        <input type="text" name="name" class="form-control border-0 bg-light py-2" value="{{ old('name', $country->name ?? '') }}" placeholder="{{ __('e.g. United States') }}" required style="border-radius: 10px;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase opacity-50">{{ __('Country Code') }}</label>
                        <input type="text" name="code" class="form-control border-0 bg-light py-2" value="{{ old('code', $country->code ?? '') }}" placeholder="{{ __('e.g. US') }}" required style="border-radius: 10px;">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold small text-uppercase opacity-50">{{ __('Status') }}</label>
                        <select name="status" class="form-select border-0 bg-light py-2" required style="border-radius: 10px;">
                            <option value="1" {{ (old('status', $country->status ?? 1)) == 1 ? 'selected' : '' }}>{{ __('Active') }}</option>
                            <option value="0" {{ (old('status', $country->status ?? 1)) == 0 ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold small text-uppercase opacity-50">{{ __('Country Icon (Flag)') }}</label>
                        @if(isset($country) && $country->icon)
                            <div class="mb-3">
                                <img src="{{ asset('uploads/countries/' . $country->icon) }}" alt="{{ $country->name }}" style="width: 60px; height: 38px; object-fit: cover; border-radius: 8px; border: 2px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                            </div>
                        @endif
                        <input type="file" name="icon" class="form-control border-0 bg-light py-2" style="border-radius: 10px;">
                        <div class="text-secondary smaller mt-2">{{ __('Recommended size: 32x20px. JPG, PNG, SVG supported.') }}</div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-5">
                <a href="{{ route('admin.countries.index') }}" class="text-secondary text-decoration-none small fw-bold">{{ __('Cancel and go back') }}</a>
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold" style="border-radius: 99px; background: #0f172a; border: none; box-shadow: 0 10px 20px rgba(0,0,0,0.1);">
                    {{ isset($country) ? __('Save Changes') : __('Create Country') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
