@extends('layouts.app')

@section('title', __('My Profile') . ' - Aquahub')

@section('content')
<div class="py-lg-5 py-4" style="background: #f8fafc; min-height: 100vh;">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- User Header Card -->
                <div class="card h-auto border-0 shadow-sm mb-4" style="border-radius: 24px; overflow: hidden;">
                    <div class="p-4 p-md-5 text-center text-md-start" style="background: white;">
                        <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                            <div style="width: 100px; height: 100px; background: var(--dark); color: white; border-radius: 30px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 800; box-shadow: 0 10px 20px rgba(0,0,0,0.1);">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <h1 class="h3 fw-black mb-1" style="letter-spacing: -0.02em;">{{ $user->name }}</h1>
                                <p class="text-secondary mb-3">{{ $user->email }}</p>
                                <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2">
                                    <span class="badge" style="background: #ecfeff; color: #0891b2; font-size: 0.75rem; padding: 0.5rem 1rem; border-radius: 99px;">
                                        {{ $user->is_admin ? __('Elite Admin') : __('Community Member') }}
                                    </span>
                                    <span class="badge" style="background: #f1f5f9; color: #475569; font-size: 0.75rem; padding: 0.5rem 1rem; border-radius: 99px;">
                                        {{ __('Joined') }} {{ $user->created_at?->format('M Y') ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                            @if($user->is_admin)
                            <div>
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary px-4">{{ __('Admin Panel') }}</a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if(session('status') === 'profile-updated')
                <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 16px; background: #dcfce7; color: #166534;">
                    <div class="d-flex align-items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        <span>{{ __('Profile successfully updated!') }}</span>
                    </div>
                </div>
                @endif

                <div class="row g-4">
                    <!-- Personal Info -->
                    <div class="col-12">
                        <div class="card h-auto border-0 shadow-sm" style="border-radius: 20px;">
                            <div class="card-body p-4 p-md-5">
                                <h2 class="h5 fw-bold mb-4">{{ __('Personal Information') }}</h2>
                                <form action="{{ route('profile') }}" method="POST">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-secondary text-uppercase">{{ __('Full Name') }}</label>
                                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" style="border-radius: 12px; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0;">
                                            @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-secondary text-uppercase">{{ __('Email Address') }}</label>
                                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" style="border-radius: 12px; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0;">
                                            @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <hr class="my-4 opacity-50">
                                        
                                        <h2 class="h5 fw-bold mb-2">{{ __('Security Settings') }}</h2>
                                        <p class="text-secondary small mb-4">{{ __('Leave password fields empty to keep your current password.') }}</p>
                                        
                                        <div class="col-12">
                                            <label class="form-label small fw-bold text-secondary text-uppercase">{{ __('Current Password') }}</label>
                                            <input type="password" name="current_password" class="form-control" style="border-radius: 12px; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0;">
                                            @error('current_password') <span class="text-danger small">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-secondary text-uppercase">{{ __('New Password') }}</label>
                                            <input type="password" name="new_password" class="form-control" style="border-radius: 12px; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0;">
                                            @error('new_password') <span class="text-danger small">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-secondary text-uppercase">{{ __('Confirm New Password') }}</label>
                                            <input type="password" name="new_password_confirmation" class="form-control" style="border-radius: 12px; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0;">
                                        </div>
                                        
                                        <div class="col-12 mt-5">
                                            <button type="submit" class="btn btn-primary w-100 py-3" style="border-radius: 16px; font-weight: 700; letter-spacing: 0.05em;">{{ __('Save Changes') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Sign Out Button -->
                    <div class="col-12">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100 py-3 mt-2" style="border-radius: 16px; font-weight: 700; border-width: 2px;">{{ __('Log Out') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .form-control:focus {
        background: white !important;
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 4px rgba(6, 182, 212, 0.1) !important;
    }
</style>
@endsection
