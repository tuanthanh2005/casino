<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Aquahub.pro — The Modern Aquarium Resource')</title>
    <meta name="description" content="@yield('meta_description', 'Elite guides, setup tutorials, and species care for aquarium owners worldwide.')">

    <!-- Global Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('av.png') }}">

    <!-- Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">

    <!-- CSS Stack (Bootstrapless approach: utility only) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    @stack('styles')
</head>

<body>
    <header class="header">
        <div class="container nav-container">
            <a href="/" class="logo">
                <img src="{{ asset('av.png') }}" alt="">
                <span>AQUAHUB</span>
            </a>
            
            <nav class="nav-links">
                <a href="/" class="{{ request()->is('/') ? 'active' : '' }}">{{ __('Home') }}</a>
                <a href="/category/beginners">{{ __('Beginners') }}</a>
                <a href="/category/setup-guides">{{ __('Setup Guides') }}</a>
                <a href="/category/product-reviews">{{ __('Reviews') }}</a>
            </nav>

            <div class="d-flex align-items-center gap-3">
                <div class="d-none d-sm-flex gap-2 me-2">
                    <a href="{{ route('lang.switch', 'en') }}" class="text-decoration-none small {{ app()->getLocale() == 'en' ? 'fw-bold text-dark' : 'text-secondary opacity-50' }}">EN</a>
                    <span class="opacity-20 text-secondary">|</span>
                    <a href="{{ route('lang.switch', 'vi') }}" class="text-decoration-none small {{ app()->getLocale() == 'vi' ? 'fw-bold text-dark' : 'text-secondary opacity-50' }}">VI</a>
                </div>
                <div class="d-none d-lg-block">
                    <form action="/search" method="GET" class="position-relative">
                        <input type="text" name="q" placeholder="{{ __('Search guides...') }}" style="background: transparent; border: 1px solid var(--border); padding: 0.4rem 1rem 0.4rem 2.2rem; border-radius: 99px; font-size: 0.8125rem; width: 180px;">
                        <svg class="position-absolute" style="left: 0.8rem; top: 50%; translate: 0 -50%; color: #94a3b8;" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg>
                    </form>
                </div>
                <a href="/admin" class="btn btn-primary">{{ __('Dashboard') }}</a>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <a href="/" class="logo mb-4">
                        <img src="{{ asset('av.png') }}" alt="">
                        <span>AQUAHUB</span>
                    </a>
                    <p style="color: #64748b; font-size: 0.875rem; max-width: 320px; line-height: 1.7; margin-top: 1.5rem;">The leading digital resource for aquarium beginners. Helping thousands across the US, UK, and Australia build successful underwater ecosystems since 2024.</p>
                </div>
                <div>
                    <h4>Resources</h4>
                    <a href="/category/beginners">Beginner Guides</a>
                    <a href="/category/setup-guides">Setup Guides</a>
                    <a href="/category/fish-care">Fish Care</a>
                    <a href="/category/comparisons">Comparisons</a>
                </div>
                <div>
                    <h4>Company</h4>
                    <a href="/about">Our Story</a>
                    <a href="/contact">Get in Touch</a>
                    <a href="/newsletter">Newsletter</a>
                </div>
                <div>
                    <h4>Legal</h4>
                    <a href="/legal/privacy">Privacy Policy</a>
                    <a href="/legal/disclaimer">Disclaimer</a>
                    <a href="/legal/terms">Terms of Service</a>
                </div>
            </div>
            
            <div style="border-top: 1px solid var(--border); pt-4; padding-top: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <p style="font-size: 0.75rem; color: #94a3b8; font-weight: 500; margin-bottom: 0;">© 2024 AQUAHUB PRO. ALL RIGHTS RESERVED.</p>
                <div style="display: flex; gap: 1.5rem;">
                    <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 600;">USA</span>
                    <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 600;">ENGLAND</span>
                    <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 600;">AUSTRALIA</span>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
