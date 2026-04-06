<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
    <title>{{ __('Register') }} - Aquahub.pro</title>
    <link rel="icon" type="image/png" href="{{ asset('av.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <style>
        body { background: #fdfdfd; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .auth-card { width: 100%; max-width: 440px; background: white; padding: 2.5rem; border-radius: 24px; box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08); border: 1px solid rgba(15, 23, 42, 0.05); }
    </style>
</head>

<body>
    <div class="auth-card">
        <div class="text-center mb-5">
            <a href="/" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; color: #020617; font-family: 'Outfit'; font-weight: 800; font-size: 1.25rem;">
                <img src="{{ asset('av.png') }}" alt="" style="height: 36px; border-radius: 8px;">
                <span>AQUAHUB</span>
            </a>
            <h1 class="h4 mt-4 fw-bold mb-1">{{ __('Create an account') }}</h1>
            <p class="text-secondary small">{{ __('Join our community of aquarium beginners') }}</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger py-2 small border-0 mb-4" style="background: #fef2f2; color: #991b1b;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold">{{ __('Your full name') }}</label>
                <input type="text" name="name" class="form-control" placeholder="John Doe" value="{{ old('name') }}" required autofocus style="background: #f1f5f9; border: none; padding: 0.75rem 1rem; border-radius: 12px; font-size: 1rem;">
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">{{ __('Email address') }}</label>
                <input type="email" name="email" class="form-control" placeholder="name@example.com" value="{{ old('email') }}" required style="background: #f1f5f9; border: none; padding: 0.75rem 1rem; border-radius: 12px; font-size: 1rem;">
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label small fw-bold">{{ __('Password') }}</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required style="background: #f1f5f9; border: none; padding: 0.75rem 1rem; border-radius: 12px; font-size: 1rem;">
                </div>
                <div class="col-md-6 mt-3 mt-md-0">
                    <label class="form-label small fw-bold">{{ __('Confirm Password') }}</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required style="background: #f1f5f9; border: none; padding: 0.75rem 1rem; border-radius: 12px; font-size: 1rem;">
                </div>
            </div>

            <div class="mb-4 form-check">
                <input type="checkbox" name="terms" class="form-check-input" id="terms" required>
                <label class="form-check-label small opacity-75" for="terms">{{ __('I agree to the') }} <a href="/terms" class="text-primary text-decoration-none fw-bold">{{ __('Terms of Service') }}</a> & <a href="/privacy" class="text-primary text-decoration-none fw-bold">{{ __('Privacy Policy') }}</a></label>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-3 mb-4" style="border-radius: 12px; font-weight: 700;">{{ __('Create Free Account') }}</button>
            
            <p class="text-center small text-secondary mb-0">{{ __('Already have an account?') }} <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-bold">{{ __('Log in here') }}</a></p>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
