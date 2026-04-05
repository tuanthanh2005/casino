<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name', 'Aquahub.pro'))</title>
    <meta name="description" content="@yield('meta_description', 'Expert guides, species care, and setup tutorials for aquarium beginners.')">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('av.png') }}">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', config('app.name'))">
    <meta property="og:image" content="@yield('meta_image', asset('av.png'))">

    <!-- Premium Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Standard CSS (Bootstrap + Custom) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    @stack('styles')
</head>

<body>
    <!-- Transparent Glassmorphism Header -->
    <header class="header">
        <div class="container nav-container">
            <a href="/" class="logo">
                <img src="{{ asset('av.png') }}" alt="">
                <span>AQUAHUB</span>
            </a>
            
            <nav class="nav-links d-none d-lg-flex">
                <a href="/" class="{{ request()->is('/') ? 'active' : '' }}">Home</a>
                <a href="/category/beginners">Beginners</a>
                <a href="/category/setup-guides">Setup Guides</a>
                <a href="/category/product-reviews">Reviews</a>
            </nav>

            <div class="d-flex align-items-center gap-3">
                <div class="d-none d-sm-block">
                    <form action="/search" method="GET" class="position-relative">
                        <input type="text" name="q" placeholder="Search guides..." style="background: rgba(241, 245, 249, 0.5); border: 1px solid var(--border); padding: 0.5rem 1rem 0.5rem 2.5rem; border-radius: 99px; font-size: 0.8125rem;">
                        <svg class="position-absolute translate-middle-y" style="left: 0.75rem; top: 50%; opacity: 0.4;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg>
                    </form>
                </div>
                <a href="/admin" class="btn btn-primary btn-sm d-none d-md-flex" style="border-radius: 99px; font-size: 0.8125rem; padding: 0.5rem 1.25rem;">Admin Login</a>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <a href="/" class="logo mb-4" style="filter: brightness(2);">
                        <img src="{{ asset('av.png') }}" alt="" style="height: 48px;">
                        <span style="color: white; -webkit-text-fill-color: white;">AQUAHUB</span>
                    </a>
                    <p style="color: #94a3b8; font-size: 0.9375rem; line-height: 1.8; margin-top: 1.5rem;">Dedicated to making aquarium fishkeeping successful and stress-free for beginners worldwide. Practical advice backed by research and experience.</p>
                </div>
                <div class="col-6 col-lg-2 offset-lg-2">
                    <h5 class="text-white mb-4">Explore</h5>
                    <ul class="list-unstyled" style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <li><a href="/category/beginners" class="text-secondary text-decoration-none small">Fish for Beginners</a></li>
                        <li><a href="/category/setup-guides" class="text-secondary text-decoration-none small">Tank Setup</a></li>
                        <li><a href="/category/fish-care" class="text-secondary text-decoration-none small">Disease Guide</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-2">
                    <h5 class="text-white mb-4">Legal</h5>
                    <ul class="list-unstyled" style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <li><a href="/legal/privacy" class="text-secondary text-decoration-none small">Privacy Policy</a></li>
                        <li><a href="/legal/disclaimer" class="text-secondary text-decoration-none small">Disclaimer</a></li>
                        <li><a href="/legal/affiliate-disclosure" class="text-secondary text-decoration-none small">Affiliate Disclosure</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h5 class="text-white mb-4">Social</h5>
                    <div style="display: flex; gap: 1rem;">
                        <div style="width: 32px; height: 32px; background: #1e293b; border-radius: 8px;"></div>
                        <div style="width: 32px; height: 32px; background: #1e293b; border-radius: 8px;"></div>
                        <div style="width: 32px; height: 32px; background: #1e293b; border-radius: 8px;"></div>
                    </div>
                </div>
            </div>
            
            <div class="border-top border-secondary mt-5 pt-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <p class="text-secondary small mb-0">© {{ date('Y') }} Aquahub.pro. Expert Fish Keeping Resource.</p>
                <div class="d-flex gap-3">
                    <span class="text-secondary small">US</span>
                    <span class="text-secondary small">UK</span>
                    <span class="text-secondary small">CA</span>
                    <span class="text-secondary small">AU</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>

</html>
