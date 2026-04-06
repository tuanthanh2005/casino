@extends('layouts.admin')

@section('page_title', __('Footer Settings'))

@section('admin_content')
    <div class="card p-4 border-0 shadow-sm" style="border-radius: 20px;">
        <div class="mb-5">
            <h5 class="fw-bold mb-1">{{ __('Global Website Management') }}</h5>
            <p class="text-secondary small">{{ __('Maintain professional international standards for your website footer, including legal notices and global markers.') }}</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 py-3 mb-4"
                style="background: #f0fdf4; color: #166534; border-radius: 12px;">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.settings.footer') }}" method="POST">
            @csrf
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-4 mb-4">
                        <label class="form-label fw-bold small text-uppercase opacity-50 d-flex justify-content-between">
                            {{ __('About Company (English)') }} <span class="badge bg-primary">EN</span>
                        </label>
                        <textarea name="footer_about_en" class="form-control border-0 bg-white" rows="3"
                            style="border-radius: 10px;">{{ $settings['footer_about_en'] ?? 'The elite aquarium resource dedicated to helping beginners build and maintain stunning worlds.' }}</textarea>
                    </div>
                    <div class="p-3 bg-light rounded-4">
                        <label class="form-label fw-bold small text-uppercase opacity-50 d-flex justify-content-between">
                            {{ __('Về công ty (Tiếng Việt)') }} <span class="badge bg-danger">VI</span>
                        </label>
                        <textarea name="footer_about_vi" class="form-control border-0 bg-white" rows="3"
                            style="border-radius: 10px;">{{ $settings['footer_about_vi'] ?? 'Cổng thông tin chuyên biệt giúp xây dựng đam mê thủy sinh một cách bền vững.' }}</textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-4 mb-4">
                        <label class="form-label fw-bold small text-uppercase opacity-50">{{ __('Copyright Text') }}</label>
                        <input type="text" name="footer_copyright" class="form-control border-0 bg-white"
                            value="{{ $settings['footer_copyright'] ?? '© 2026 AQUAHUB PRO. ALL RIGHTS RESERVED.' }}"
                            style="border-radius: 8px;">
                    </div>
                    <div class="p-3 bg-light rounded-4">
                        <label class="form-label fw-bold small text-uppercase opacity-50">{{ __('Operational Regions (Global Markers)') }}</label>
                        <input type="text" name="footer_regions" class="form-control border-0 bg-white"
                            value="{{ $settings['footer_regions'] ?? __('USA, ENGLAND, CANADA, AUSTRALIA') }}"
                            style="border-radius: 8px;">
                    </div>
                </div>
            </div>

            <h6 class="fw-bold mb-4 border-bottom pb-2">{{ __('Social Links & External Connections') }}</h6>
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <label class="form-label fw-bold small">{{ __('Facebook URL') }}</label>
                    <input type="text" name="social_facebook" class="form-control bg-light border-0"
                        value="{{ $settings['social_facebook'] ?? '#' }}" style="border-radius: 8px;">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small">{{ __('Instagram URL') }}</label>
                    <input type="text" name="social_instagram" class="form-control bg-light border-0"
                        value="{{ $settings['social_instagram'] ?? '#' }}" style="border-radius: 8px;">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small">{{ __('Twitter/X URL') }}</label>
                    <input type="text" name="social_x_twitter" class="form-control bg-light border-0"
                        value="{{ $settings['social_x_twitter'] ?? '#' }}" style="border-radius: 8px;">
                </div>
            </div>

            <h6 class="fw-bold mb-4 border-bottom pb-2">{{ __('Resource Management') }}</h6>
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold small">{{ __('Newsletter Community Count (displayed)') }}</label>
                    <input type="text" name="newsletter_count" class="form-control bg-light border-0"
                        value="{{ $settings['newsletter_count'] ?? '15,000+' }}" style="border-radius: 8px;">
                </div>
            </div>

            <div class="mt-5 pt-4 border-top">
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold"
                    style="border-radius: 99px; background: #0f172a; border: none;">{{ __('Apply Professional Settings') }}</button>
            </div>
        </form>
    </div>
@endsection