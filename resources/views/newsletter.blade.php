@extends('layouts.app')

@section('title', __('Newsletter - Aquahub'))

@push('styles')
<style>
    .newsletter-wrapper {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: radial-gradient(circle at 50% 0%, #f8fafc 0%, #e2e8f0 100%);
        padding: 4rem 1rem;
    }
    .newsletter-card {
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        max-width: 1000px;
        width: 100%;
        display: flex;
        flex-direction: column;
    }
    @media (min-width: 992px) {
        .newsletter-card {
            flex-direction: row;
        }
    }
    .newsletter-content {
        padding: 4rem 3rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .newsletter-image {
        flex: 1;
        background: url('https://images.unsplash.com/photo-1522069169874-c58ec4b76be5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80') center/cover no-repeat;
        min-height: 300px;
        position: relative;
    }
    .newsletter-image::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, transparent, rgba(15, 23, 42, 0.8));
    }
    .badge-premium {
        background: rgba(37, 99, 235, 0.1);
        color: #2563eb;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.75rem;
        letter-spacing: 1px;
        text-transform: uppercase;
        display: inline-block;
        margin-bottom: 2rem;
    }
    .input-group-custom {
        background: #f1f5f9;
        border-radius: 16px;
        padding: 0.5rem;
        display: flex;
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }
    .input-group-custom:focus-within {
        border-color: #2563eb;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }
    .input-custom {
        background: transparent;
        border: none;
        padding: 1rem 1.5rem;
        font-size: 1rem;
        flex: 1;
        color: #0f172a;
        outline: none;
    }
    .btn-custom {
        background: #0f172a;
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 0 2rem;
        font-weight: 700;
        transition: all 0.3s ease;
    }
    .btn-custom:hover {
        background: #2563eb;
        transform: translateY(-2px);
    }
    .features-list {
        margin-top: 2.5rem;
        padding-top: 2.5rem;
        border-top: 1px solid #e2e8f0;
    }
    .feature-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
        color: #475569;
        font-weight: 500;
    }
    .feature-icon {
        width: 24px;
        height: 24px;
        background: #dcfce7;
        color: #16a34a;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@section('content')
<div class="newsletter-wrapper">
    <div class="newsletter-card">
        <div class="newsletter-content">
            <div class="badge-premium">{{ __('VIP Access') }}</div>
            <h1 style="font-size: 2.75rem; font-weight: 900; line-height: 1.1; color: #0f172a; margin-bottom: 1rem; letter-spacing: -0.04em;">
                {{ __('Join 15,000+ Happy Fishkeepers') }}
            </h1>
            <p style="font-size: 1.125rem; color: #64748b; margin-bottom: 2.5rem; line-height: 1.6;">
                {{ __('Get the ultimate aquarium guides, exclusive product discounts, and secret tips delivered straight to your inbox. No spam, only pure value.') }}
            </p>

            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center mb-4" style="border-radius: 12px; border: none; background: #dcfce7; color: #166534; font-weight: 600;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger d-flex align-items-center mb-4" style="border-radius: 12px; border: none; background: #fee2e2; color: #b91c1c; font-weight: 600;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    {{ session('error') }}
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger mb-4" style="border-radius: 12px; border: none; background: #fee2e2; color: #b91c1c; font-weight: 600;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('newsletter.subscribe') }}" method="POST">
                @csrf
                <div class="input-group-custom">
                    <input type="email" name="email" class="input-custom" placeholder="{{ __('Enter your best email address') }}" required>
                    <button type="submit" class="btn-custom">{{ __('Subscribe Now') }}</button>
                </div>
                <p style="font-size: 0.85rem; color: #94a3b8; margin-top: 1rem; text-align: center;">
                    {{ __('By joining, you agree to our Privacy Policy. Unsubscribe anytime.') }}
                </p>
            </form>

            <div class="features-list">
                <div class="feature-item">
                    <div class="feature-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                    {{ __('Weekly step-by-step setup guides') }}
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                    {{ __('Exclusive deals on aquarium gear') }}
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                    {{ __('Early access to our premium content') }}
                </div>
            </div>
        </div>
        <div class="newsletter-image d-none d-lg-block">
            <div style="position: absolute; bottom: 2rem; left: 2rem; right: 2rem; color: white;">
                <div style="font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem;">"The best resource for aquarium enthusiasts."</div>
                <div style="opacity: 0.8; font-weight: 500;">— Aquatic Monthly</div>
            </div>
        </div>
    </div>
</div>
@endsection
