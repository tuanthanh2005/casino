<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('page_title', __('Dashboard')) - Aquahub Admin</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('av.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style>
        :root {
            --sidebar-width: 280px;
        }

        body {
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
        }

        .admin-sidebar {
            width: var(--sidebar-width);
            background: #0f172a;
            color: white;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding: 2rem 1.5rem;
            z-index: 100;
        }

        .admin-main {
            flex-grow: 1;
            margin-left: var(--sidebar-width);
            padding: 2rem 3rem;
            min-width: 0;
        }

        .nav-item-admin {
            display: block;
            padding: 0.75rem 1rem;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .nav-item-admin:hover,
        .nav-item-admin.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
        }

        @media (max-width: 991px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }

            .admin-main {
                margin-left: 0;
                padding: 1.5rem;
            }
        }
    </style>
    @stack('styles')
    <style>
        /* ============================================================
           GLOBAL ADMIN TOAST
           ============================================================ */
        .admin-toast-container {
            position: fixed; top: 1.5rem; right: 1.5rem;
            z-index: 9999; display: flex; flex-direction: column; gap: .75rem;
            pointer-events: none;
        }
        .admin-toast {
            display: flex; align-items: flex-start; gap: .875rem;
            padding: 1rem 1.25rem; border-radius: 12px;
            min-width: 300px; max-width: 420px;
            box-shadow: 0 10px 25px rgba(0,0,0,.12), 0 4px 10px rgba(0,0,0,.08);
            pointer-events: all;
            animation: admin-toast-in .4s cubic-bezier(.22,1,.36,1);
        }
        @keyframes admin-toast-in {
            from { opacity: 0; transform: translateX(60px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .admin-toast.out {
            animation: admin-toast-out .3s ease forwards;
        }
        @keyframes admin-toast-out {
            to { opacity: 0; transform: translateX(60px); }
        }
        .admin-toast-success { background: #fff; border-left: 4px solid #22c55e; }
        .admin-toast-error   { background: #fff; border-left: 4px solid #ef4444; }
        .admin-toast-icon { flex-shrink: 0; margin-top: 1px; }
        .admin-toast-success .admin-toast-icon { color: #22c55e; }
        .admin-toast-error   .admin-toast-icon { color: #ef4444; }
        .admin-toast-body { flex: 1; }
        .admin-toast-title { font-size: .8125rem; font-weight: 700; color: #0f172a; margin: 0 0 .15rem; }
        .admin-toast-msg   { font-size: .8125rem; color: #475569; margin: 0; }
        .admin-toast-progress {
            position: absolute; bottom: 0; left: 0; height: 3px; border-radius: 0 0 12px 12px;
            width: 100%; transform-origin: left;
        }
        .admin-toast { position: relative; overflow: hidden; }
        .admin-toast-success .admin-toast-progress { background: #22c55e; }
        .admin-toast-error   .admin-toast-progress { background: #ef4444; }
        .admin-toast-close {
            flex-shrink: 0; background: none; border: none; cursor: pointer;
            color: #94a3b8; display: flex; align-items: center; transition: color .2s;
            padding: 0;
        }
        .admin-toast-close:hover { color: #0f172a; }
    </style>
</head>

<body>
    <div class="admin-sidebar">
        <div style="margin-bottom: 3rem; padding-left: 1rem; display: flex; align-items: center; gap: 0.75rem;">
            <img src="{{ asset('av.png') }}" style="width: 32px; height: 32px; border-radius: 6px;">
            <h1 style="font-size: 1.25rem; font-weight: 800; color: white; margin-bottom: 0;">AQUAHUB</h1>
        </div>

        <nav>
            <a href="/admin" class="nav-item-admin {{ request()->is('admin') ? 'active' : '' }}">{{ __('Dashboard') }}</a>
            <a href="{{ route('admin.posts.index') }}" class="nav-item-admin {{ request()->is('admin/posts*') ? 'active' : '' }}">{{ __('All Posts') }}</a>
            <a href="{{ route('admin.messages.index') }}" class="nav-item-admin {{ request()->is('admin/messages*') ? 'active' : '' }} d-flex justify-content-between align-items-center">
                <span>{{ __('Support Messages') }}</span>
                @if(($unread_admin_messages ?? 0) > 0)
                    <span class="badge bg-danger rounded-pill" style="font-size: 0.65rem;">{{ $unread_admin_messages }}</span>
                @endif
            </a>
            <a href="{{ route('admin.settings.footer') }}" class="nav-item-admin {{ request()->is('admin/settings/footer*') ? 'active' : '' }}">{{ __('Footer Settings') }}</a>
            <a href="{{ route('admin.settings.adsense') }}" class="nav-item-admin {{ request()->is('admin/settings/adsense*') ? 'active' : '' }}">{{ __('Quảng cáo Adsense') }}</a>
            <a href="{{ route('admin.countries.index') }}" class="nav-item-admin {{ request()->is('admin/countries*') ? 'active' : '' }}">{{ __('Countries Management') }}</a>
            <a href="/admin/categories" class="nav-item-admin {{ request()->is('admin/categories*') ? 'active' : '' }}">{{ __('Category Management') }}</a>
            <a href="/admin/tags" class="nav-item-admin {{ request()->is('admin/tags*') ? 'active' : '' }}">{{ __('Tags') }}</a>
            <a href="{{ route('admin.pages.index') }}" class="nav-item-admin {{ request()->is('admin/pages*') ? 'active' : '' }}">{{ __('Static Pages') }}</a>
            <div style="margin: 2rem 1rem 1rem; font-size: 0.75rem; text-transform: uppercase; color: #475569; letter-spacing: 0.1em;">{{ __('External') }}</div>
            <a href="/" target="_blank" class="nav-item-admin">{{ __('Visit Website') }}</a>
            <a href="/logout" class="nav-item-admin" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: #fca5a5;">{{ __('Log Out') }}</a>

            <form id="logout-form" action="/logout" method="POST" style="display: none;">
                @csrf
            </form>
        </nav>
    </div>

    <div class="admin-main">
        <header class="admin-header">
            <h2 style="font-size: 1.875rem; font-weight: 700; color: #0f172a;">@yield('page_title')</h2>
            <div style="display: flex; gap: 1rem; align-items: center;">
                @yield('header_actions')
                <div style="width: 40px; height: 40px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #0284c7;">A</div>
            </div>
        </header>

        @if(session('success') || session('error'))
        <div class="admin-toast-container" id="admin-toast-container">
            @if(session('success'))
            <div class="admin-toast admin-toast-success" id="toast-success">
                <div class="admin-toast-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div class="admin-toast-body">
                    <p class="admin-toast-title">{{ __('Thành công!') }}</p>
                    <p class="admin-toast-msg">{{ session('success') }}</p>
                </div>
                <button class="admin-toast-close" onclick="closeToast('toast-success')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
                <div class="admin-toast-progress" id="toast-success-bar" style="animation: toast-shrink 5s linear forwards;"></div>
            </div>
            @endif
            @if(session('error'))
            <div class="admin-toast admin-toast-error" id="toast-error">
                <div class="admin-toast-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div class="admin-toast-body">
                    <p class="admin-toast-title">{{ __('Có lỗi xảy ra!') }}</p>
                    <p class="admin-toast-msg">{{ session('error') }}</p>
                </div>
                <button class="admin-toast-close" onclick="closeToast('toast-error')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
                <div class="admin-toast-progress" id="toast-error-bar" style="animation: toast-shrink 7s linear forwards;"></div>
            </div>
            @endif
        </div>
        <style>
            @keyframes toast-shrink { from { width: 100%; } to { width: 0%; } }
        </style>
        <script>
            function closeToast(id) {
                const el = document.getElementById(id);
                if (!el) return;
                el.classList.add('out');
                setTimeout(() => el.remove(), 300);
            }
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(() => closeToast('toast-success'), 5000);
                setTimeout(() => closeToast('toast-error'), 7000);
            });
        <\/script>
        @endif

        @yield('admin_content')
    </div>

    <!-- Bootstrap 5 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    @stack('scripts')
</body>

</html>
