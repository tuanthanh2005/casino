<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AquaHub Pro - Dự Đoán BTC, Mini Games & Nông Trại Kiếm Point')</title>
    <meta name="description" content="@yield('meta_description', 'AquaHub Pro - Nền tảng giải trí đa năng: Dự đoán giá Bitcoin Long/Short, Mini Games hấp dẫn và Nông trại ảo. Tích lũy Point đổi quà tặng Premium ngay hôm nay.')">
    <meta name="keywords" content="@yield('meta_keywords', 'aquahub, aquahub pro, dự đoán btc, game nông trại, mini games, đổi thưởng, bitcoin prediction')">
    <meta name="robots" content="@yield('meta_robots', 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1')">
    <meta name="googlebot" content="@yield('meta_googlebot', 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1')">
    <meta name="theme-color" content="#0a0a0f">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <link rel="sitemap" type="application/xml" title="Sitemap" href="{{ url('/sitemap.xml') }}">
    @if(config('services.google.site_verification'))
    <meta name="google-site-verification" content="{{ config('services.google.site_verification') }}">
    @endif
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('av.png') }}">
    <link rel="shortcut icon" href="{{ asset('av.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('av.png') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="AquaHub Pro">
    <meta property="og:locale" content="vi_VN">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="AquaHub Pro - Kết Nối Dịch Vụ & Giải Trí Đỉnh Cao">
    <meta property="og:description" content="Dự đoán BTC, Mini Games & Nông Trại. Hệ thống giải trí minh bạch, uy tín tại AquaHub Pro.">
    <meta property="og:image" content="{{ asset('av.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="AquaHub Pro - Mini Games & BTC Prediction">
    <meta property="twitter:description" content="Nền tảng giải trí đa năng tích hợp nông trại và dự đoán giá Crypto.">
    <meta property="twitter:image" content="{{ asset('av.png') }}">

    <!-- Schema.org JSON-LD -->
    <script type="application/ld+json">
    {
    "@@context": "https://schema.org",
      "@type": "WebSite",
      "name": "AquaHub Pro",
            "url": "{{ config('app.url') }}",
            "description": "Nền tảng Mini Games, Dự đoán giá BTC và Nông trại giải trí tích hợp."
    }
    </script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {}
            }
        }
    </script>
    
    <style>
        :root {
            --primary: #06b6d4;
            --primary-dark: #0891b2;
            --accent: #10b981;
            --success: #10b981;
            --danger: #ef4444;
            --bg: #0a0a0f;
            --bg-card: #111827;
            --bg-card2: #1f2937;
            --border: rgba(99,102,241,0.2);
            --text: #f9fafb;
            --text-muted: #9ca3af;
            --long: #10b981;
            --short: #ef4444;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* NAVBAR */
        .navbar {
            background: rgba(17,24,39,0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 0.75rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-weight: 900;
            font-size: 1.4rem;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
        }

        /* Modal Base */
    .profile-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(10px);
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        opacity: 0;
        transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .profile-modal.open {
        display: flex;
        opacity: 1;
    }
    .profile-modal-card {
        background: var(--bg-card);
        width: 100%;
        max-width: 440px;
        border: 1px solid var(--border);
        border-radius: 24px;
        overflow: hidden;
        animation: modalScale 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    @keyframes modalScale {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .profile-modal-header {
        padding: 1.5rem;
        background: linear-gradient(to right, rgba(99,102,241,0.08), transparent);
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .profile-modal-body {
        padding: 1.5rem;
    }
    .btn-close-modal {
        background: var(--bg-body);
        color: var(--text-muted);
        border: 1px solid var(--border);
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-close-modal:hover {
        background: var(--danger);
        color: white;
    }

    .nav-link {
            color: var(--text-muted) !important;
            font-weight: 500;
            transition: color 0.2s;
            text-decoration: none;
            padding: 0.5rem 1rem;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--text) !important;
        }

        .balance-badge {
            background: linear-gradient(135deg, #1f2937, #374151);
            border: 1px solid var(--border);
            border-radius: 50px;
            padding: 0.4rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--accent);
            display: flex;
            align-items: center;
            gap: 0.4rem;
            white-space: nowrap;
        }

        .balance-badge .bi {
            color: var(--accent);
        }

        .btn-nav-logout {
            background: transparent;
            border: 1px solid rgba(239,68,68,0.3);
            color: #ef4444;
            border-radius: 8px;
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-nav-logout:hover {
            background: rgba(239,68,68,0.1);
        }

        .nav-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            list-style: none;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* NAV DROPDOWN */
        .nav-dropdown { position: relative; }
        .nav-dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            width: min(860px, 84vw);
            padding: 0.5rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            z-index: 200;
            backdrop-filter: blur(20px);
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.45rem;
        }
        .nav-dropdown-menu.open { display: grid; animation: dropDown 0.2s ease; }
        @keyframes dropDown {
            from { opacity:0; transform:translateY(-8px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .nav-dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 0.75rem;
            border-radius: 10px;
            color: var(--text);
            text-decoration: none;
            transition: background 0.15s;
            font-size: 0.875rem;
            min-height: 64px;
        }
        .nav-dropdown-item:hover, .nav-dropdown-item.active {
            background: rgba(99,102,241,0.1);
        }
        .nav-dropdown-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        @media (max-width: 1200px) {
            .nav-dropdown-menu {
                width: min(760px, 92vw);
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        /* MAIN CONTENT */
        .main-content {
            max-width: 1280px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }

        /* CARDS */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* BUTTONS */
        .btn {
            padding: 0.6rem 1.25rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-long {
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
            font-size: 1rem;
            padding: 0.875rem 2rem;
        }
        .btn-long:hover {
            background: linear-gradient(135deg, #047857, #059669);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16,185,129,0.4);
        }

        .btn-short {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: white;
            font-size: 1rem;
            padding: 0.875rem 2rem;
        }
        .btn-short:hover {
            background: linear-gradient(135deg, #b91c1c, #dc2626);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(239,68,68,0.4);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
        }
        .btn-outline:hover {
            background: var(--bg-card2);
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }
        .btn-danger:hover { background: #dc2626; }

        .btn-success {
            background: var(--success);
            color: white;
        }
        .btn-success:hover { background: #059669; }

        /* BADGES */
        .badge {
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-success { background: rgba(16,185,129,0.15); color: #10b981; border: 1px solid rgba(16,185,129,0.3); }
        .badge-danger { background: rgba(239,68,68,0.15); color: #ef4444; border: 1px solid rgba(239,68,68,0.3); }
        .badge-warning { background: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.3); }
        .badge-primary { background: rgba(99,102,241,0.15); color: #818cf8; border: 1px solid rgba(99,102,241,0.3); }

        /* FORM INPUTS */
        .form-control {
            background: var(--bg-card2);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            padding: 0.65rem 1rem;
            width: 100%;
            font-family: 'Inter', sans-serif;
            font-size: 16px; /* Fix auto-zoom on Mobile */
            transition: border-color 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
        }
        .form-control::placeholder { color: var(--text-muted); }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 0.4rem;
        }

        .form-group { margin-bottom: 1rem; }

        /* ALERT */
        .alert {
            padding: 0.875rem 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .alert-success { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); color: #10b981; }
        .alert-danger { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #ef4444; }
        .alert-warning { background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.3); color: #f59e0b; }

        /* TABLE */
        .table-wrapper { overflow-x: auto; }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }
        th {
            padding: 0.75rem 1rem;
            text-align: left;
            color: var(--text-muted);
            font-weight: 500;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }
        td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            vertical-align: middle;
        }
        tr:hover td { background: rgba(99,102,241,0.04); }

        /* GRID */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; }

        @media (max-width: 768px) {
            .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr; }
            .nav-menu { display: none; }
        }

        /* TOAST */
        .toast-container {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .toast {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            min-width: 280px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideIn 0.3s ease;
            font-size: 0.9rem;
        }

        .toast.success { border-left: 3px solid var(--success); }
        .toast.error { border-left: 3px solid var(--danger); }
        .toast.info { border-left: 3px solid var(--primary); }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(100px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* AVATAR */
        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }

        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 1rem; }
        .mb-4 { margin-bottom: 1.5rem; }
        .mt-1 { margin-top: 0.25rem; }
        .mt-2 { margin-top: 0.5rem; }
        .mt-3 { margin-top: 1rem; }
        .mt-4 { margin-top: 1.5rem; }
        .text-muted { color: var(--text-muted); }
        .text-center { text-align: center; }
        .d-flex { display: flex; }
        .align-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-1 { gap: 0.25rem; }
        .gap-2 { gap: 0.5rem; }
        .gap-3 { gap: 1rem; }
        .fw-bold { font-weight: 700; }
        .fs-small { font-size: 0.8rem; }
        .w-100 { width: 100%; }

        /* ── MOBILE ELEMENTS (HIDDEN ON DESKTOP) ── */
        .mobile-header, .mobile-bottom-nav {
            display: none !important;
        }

        /* ── GLOBAL MOBILE APP STYLE ── */
        @media (max-width: 768px) {
            .mobile-header { display: flex !important; }
            .mobile-bottom-nav { display: flex !important; }
            
            :root {
                --mobile-header-h: 60px;
                --mobile-nav-h: 70px;
            }
            
            body {
                padding-top: var(--mobile-header-h) !important;
                padding-bottom: var(--mobile-nav-h) !important;
                background: #000 !important; /* True black background for Premium feel */
            }

            .main-content {
                padding: 1rem 0.75rem !important;
                margin: 0 !important;
                max-width: 100% !important;
            }

            /* Hide Desktop Elements */
            .navbar, .desktop-only, header:not(.mobile-header) {
                display: none !important;
            }

            /* Mobile Utility Classes */
            .m-card {
                background: rgba(255, 255, 255, 0.05) !important;
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.1) !important;
                border-radius: 16px !important;
                padding: 1rem !important;
                margin-bottom: 1rem !important;
            }
            .m-text-shadow { text-shadow: 0 2px 4px rgba(0,0,0,0.5); }
            .m-full { width: 100% !important; margin-left: 0 !important; margin-right: 0 !important; }
            
            /* Typography Scale-down */
            h1 { font-size: 1.5rem !important; }
            .card { background: rgba(31, 41, 55, 0.5) !important; border: 1px solid rgba(255,255,255,0.1) !important; }
            .card-header { font-size: 0.9rem !important; padding: 0.75rem 1rem !important; display: flex !important; }
        }

        /* MOBILE OVERRIDES */
        @media (max-width: 768px) {
            .navbar { display: none !important; }
            .main-content { padding: 1rem 0.75rem calc(7.25rem + env(safe-area-inset-bottom)) 0.75rem !important; }
            
            .mobile-header {
                position: fixed;
                top: 0; left: 0; right: 0;
                z-index: 2000;
                background: rgba(10, 10, 15, 0.7);
                backdrop-filter: blur(15px);
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
                padding: 0.6rem 1rem;
                display: flex;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 4px 30px rgba(0,0,0,0.3);
            }
            .mobile-status {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 50px;
                padding: 0.3rem 0.75rem;
                font-size: 0.82rem;
                font-weight: 700;
                color: var(--accent);
            }
            .mobile-logo {
                font-weight: 900;
                font-size: 1.1rem;
                background: linear-gradient(135deg, var(--primary), var(--accent));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }

            .mobile-bottom-nav {
                position: fixed;
                bottom: 0; left: 0; right: 0;
                z-index: 2000;
                background: rgba(17, 24, 39, 0.85);
                backdrop-filter: blur(20px);
                border-top: 1px solid rgba(255, 255, 255, 0.08);
                display: flex;
                justify-content: space-around;
                padding: 0.5rem 0.2rem calc(0.5rem + env(safe-area-inset-bottom));
                border-radius: 20px 20px 0 0;
                box-shadow: 0 -10px 40px rgba(0,0,0,0.5);
            }
            .m-nav-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 0.2rem;
                color: var(--text-muted);
                text-decoration: none;
                font-size: 0.65rem;
                font-weight: 600;
                flex: 1;
                transition: transform 0.2s;
            }
            .m-nav-item i { font-size: 1.4rem; }
            .m-nav-item.active {
                color: var(--primary);
            }
            .m-nav-item.active i {
                transform: translateY(-5px);
                text-shadow: 0 0 15px var(--primary);
            }

            .mobile-nav-loader {
                position: fixed;
                inset: 0;
                z-index: 3500;
                display: none;
                align-items: center;
                justify-content: center;
                background: rgba(3, 8, 22, 0.76);
                backdrop-filter: blur(8px);
            }

            .mobile-nav-loader.show {
                display: flex;
            }

            .mobile-nav-loader-card {
                width: min(86vw, 260px);
                border: 1px solid rgba(34, 211, 238, 0.34);
                border-radius: 18px;
                background: linear-gradient(145deg, rgba(9, 17, 34, 0.95), rgba(8, 14, 28, 0.92));
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.45);
                padding: 1rem 1.1rem;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 0.6rem;
            }

            .mobile-nav-loader-brand {
                display: inline-flex;
                align-items: center;
                gap: 0.45rem;
                color: #22d3ee;
                font-weight: 800;
                letter-spacing: 0.01em;
            }

            .mobile-nav-loader-brand i {
                font-size: 1.1rem;
            }

            .mobile-nav-loader-text {
                font-size: 0.82rem;
                color: #b6c5d8;
            }

            .mobile-nav-loader-spinner {
                width: 28px;
                height: 28px;
                border: 2px solid rgba(34, 211, 238, 0.25);
                border-top-color: #22d3ee;
                border-radius: 50%;
                animation: mobileLoaderSpin 0.85s linear infinite;
            }

            body.is-mobile-nav-loading {
                overflow: hidden;
            }

            @keyframes mobileLoaderSpin {
                to { transform: rotate(360deg); }
            }
        }
        @media (min-width: 769px) {
            .mobile-header, .mobile-bottom-nav, .mobile-nav-loader { display: none !important; }
        }

        /* ── PAGE CONTENT TRANSITION ── */
        .page-enter { animation: pageFade 0.4s ease; }
        @keyframes pageFade { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
    
    @stack('styles')
</head>
<body class="{{ request()->routeIs('farm*') ? 'is-farm' : '' }}">
    <!-- MOBILE HEADER (STATUS BAR) -->
    @auth
    <div class="mobile-header">
        <div class="mobile-logo">🌊 AquaHub</div>
        <div style="display:flex; align-items:center; gap:0.6rem">
            <div class="mobile-status">
                <i class="bi bi-coin"></i>
                <span id="m-nav-balance">{{ number_format((float) auth()->user()->balance_point, 0) }}</span>
            </div>
            <img class="avatar" src="{{ auth()->user()->avatar_url }}" alt="Avatar" onclick="openProfileModal()" style="border: 2px solid var(--primary); width:34px; height:34px">
        </div>
    </div>
    @endauth

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="{{ route('home') }}" class="navbar-brand">🌊 AquaHub</a>
            
            @auth
            <ul class="nav-menu">
                {{-- Dropdown Games --}}
                <li class="nav-dropdown">
                    <a href="#" class="nav-link nav-dropdown-toggle {{ request()->routeIs('home') || request()->routeIs('games.catalog') || request()->routeIs('prediction') || request()->routeIs('spin') || request()->routeIs('dice') || request()->routeIs('rps') || request()->routeIs('farm*') ? 'active' : '' }}"
                       onclick="toggleNavDropdown(event)">
                        <i class="bi bi-controller"></i> Games
                        <i class="bi bi-chevron-down" style="font-size:0.65rem; margin-left:2px; transition:transform 0.2s" id="nav-chevron"></i>
                    </a>
                    <div class="nav-dropdown-menu" id="nav-games-dropdown">
                        <a href="{{ route('games.catalog') }}" class="nav-dropdown-item {{ request()->routeIs('games.catalog') ? 'active' : '' }}">
                            <span class="nav-dropdown-icon" style="background:rgba(14,165,233,0.15)">🧩</span>
                            <div>
                                <div style="font-weight:600">Danh mục game</div>
                                <div style="font-size:0.72rem; color:var(--text-muted)">Tất cả game trong hệ thống</div>
                            </div>
                        </a>
                        <a href="{{ route('prediction') }}" class="nav-dropdown-item {{ request()->routeIs('prediction') ? 'active' : '' }}">
                            <span class="nav-dropdown-icon" style="background:rgba(99,102,241,0.15)">📈</span>
                            <div>
                                <div style="font-weight:600">BTC Long / Short</div>
                                <div style="font-size:0.72rem; color:var(--text-muted)">Dự đoán giá Bitcoin</div>
                            </div>
                        </a>
                        <a href="{{ route('spin') }}" class="nav-dropdown-item {{ request()->routeIs('spin') ? 'active' : '' }}">
                            <span class="nav-dropdown-icon" style="background:rgba(245,158,11,0.15)">🎡</span>
                            <div>
                                <div style="font-weight:600">Vòng Quay May Mắn</div>
                                <div style="font-size:0.72rem; color:var(--text-muted)">Quay để nhân điểm</div>
                            </div>
                        </a>
                        <a href="{{ route('dice') }}" class="nav-dropdown-item {{ request()->routeIs('dice') ? 'active' : '' }}">
                            <span class="nav-dropdown-icon" style="background:rgba(16,185,129,0.15)">🎲</span>
                            <div>
                                <div style="font-weight:600">Tài Xỉu</div>
                                <div style="font-size:0.72rem; color:var(--text-muted)">Xúc xắc — Tài hay Xỉu</div>
                            </div>
                        </a>
                        <a href="{{ route('rps') }}" class="nav-dropdown-item {{ request()->routeIs('rps') ? 'active' : '' }}">
                            <span class="nav-dropdown-icon" style="background:rgba(244,63,94,0.15)">✊</span>
                            <div>
                                <div style="font-weight:600">Kéo Búa Bao</div>
                                <div style="font-size:0.72rem; color:var(--text-muted)">1 click · Single / BO3</div>
                            </div>
                        </a>
                        <a href="{{ route('farm') }}" class="nav-dropdown-item {{ request()->routeIs('farm*') ? 'active' : '' }}">
                            <span class="nav-dropdown-icon" style="background:rgba(34,197,94,0.15)">🌾</span>
                            <div>
                                <div style="font-weight:600">Nông Trại</div>
                                <div style="font-size:0.72rem; color:var(--text-muted)">Trồng cây, thu hoạch</div>
                            </div>
                        </a>
                    </div>
                </li>
                <li><a href="{{ route('shop') }}" class="nav-link {{ request()->routeIs('shop') ? 'active' : '' }}">
                    <i class="bi bi-shop"></i> Cửa hàng
                </a></li>
                <li><a href="{{ route('nav.index') }}" class="nav-link {{ request()->routeIs('nav.*') ? 'active' : '' }}"
                    style="{{ request()->routeIs('nav.*') ? 'color:#69C9D0!important' : '' }}">
                    🛡️ Hỗ Trợ MXH
                </a></li>
                <li><a href="{{ route('payment.deposit') }}" class="nav-link {{ request()->routeIs('payment.deposit*') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle"></i> Nạp tiền
                </a></li>
                <li><a href="{{ route('payment.withdraw') }}" class="nav-link {{ request()->routeIs('payment.withdraw*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-up-right-circle"></i> Rút / Đổi
                </a></li>
                <li><a href="{{ route('support.chat') }}" class="nav-link {{ request()->routeIs('support.chat*') ? 'active' : '' }}">
                    <i class="bi bi-chat-dots"></i> Hỗ trợ
                </a></li>
                <li><a href="{{ route('blog.index') }}" class="nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i> Blog
                </a></li>
                @if(auth()->user()->isAdmin())
                <li><a href="{{ route('admin.dashboard') }}" class="nav-link">
                    <i class="bi bi-shield-lock"></i> Admin
                </a></li>
                @endif
            </ul>


            <div class="nav-right">
                <div class="balance-badge">
                    <i class="bi bi-coin"></i>
                    <span id="nav-balance">{{ number_format((float) auth()->user()->balance_point, 2) }}</span> PT
                </div>
                <img class="avatar" src="{{ auth()->user()->avatar_url }}" alt="Avatar" onclick="openProfileModal()" style="cursor:pointer">
                <form action="{{ route('logout') }}" method="POST" style="display:inline">
                    @csrf
                    <button type="submit" class="btn-nav-logout">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
            @endauth

            @guest
            <div class="nav-right">
                <a href="{{ route('blog.index') }}" class="btn btn-outline">Blog</a>
                <a href="{{ route('login') }}" class="btn btn-outline">Đăng nhập</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Đăng ký</a>
            </div>
            @endguest
        </div>
    </nav>

    <!-- MOBILE BOTTOM NAV -->
    @auth
    <div class="mobile-bottom-nav">
        <a href="{{ route('games.catalog') }}" class="m-nav-item {{ request()->routeIs('games.catalog') || request()->routeIs('prediction') || request()->routeIs('spin') || request()->routeIs('dice') || request()->routeIs('rps') || request()->routeIs('farm*') ? 'active' : '' }}">
            <i class="bi bi-controller"></i>
            <span>Game</span>
        </a>
        <a href="{{ route('shop') }}" class="m-nav-item {{ request()->routeIs('shop') ? 'active' : '' }}">
            <i class="bi bi-shop"></i>
            <span>Cửa Hàng</span>
        </a>
        <a href="{{ route('home') }}" class="m-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i>
            <span>Trang chủ</span>
        </a>
        <a href="{{ route('payment.deposit') }}" class="m-nav-item {{ request()->routeIs('payment.deposit*') || request()->routeIs('payment.withdraw*') ? 'active' : '' }}">
            <i class="bi bi-wallet2"></i>
            <span>Nạp/Rút</span>
        </a>
        <a href="javascript:void(0)" onclick="openContactModal()" class="m-nav-item">
            <i class="bi bi-chat-dots"></i>
            <span>Liên hệ</span>
        </a>
    </div>

    <div class="mobile-nav-loader" id="mobile-nav-loader" aria-hidden="true">
        <div class="mobile-nav-loader-card">
            <div class="mobile-nav-loader-brand">
                <i class="bi bi-water"></i>
                <span>AquaHub</span>
            </div>
            <div class="mobile-nav-loader-spinner"></div>
            <div class="mobile-nav-loader-text">Đang tải trang...</div>
        </div>
    </div>
    @endauth

    <!-- MAIN CONTENT -->
    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success mb-3">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger mb-3">
                <i class="bi bi-x-circle-fill"></i> {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <!-- TOAST -->
    <div class="toast-container" id="toast-container"></div>

    @auth
    @php
        $supportSettings = \App\Models\GameSetting::getMany([
            'support_title',
            'support_subtitle',
            'support_center_label',
            'support_phone',
            'support_email',
            'support_zalo_url',
            'support_messenger_url',
            'support_working_hours',
        ]);

        $supportTitle = $supportSettings['support_title'] ?? 'Liên hệ hỗ trợ';
        $supportSubtitle = $supportSettings['support_subtitle'] ?? 'Hỗ trợ nhanh khi cần xử lý giao dịch / game';
        $supportCenterLabel = $supportSettings['support_center_label'] ?? 'Trung tâm hỗ trợ MXH';
        $supportPhone = $supportSettings['support_phone'] ?? '0900000000';
        $supportEmail = $supportSettings['support_email'] ?? 'support@aquahub.vn';
        $supportZaloUrl = $supportSettings['support_zalo_url'] ?? 'https://zalo.me';
        $supportMessengerUrl = $supportSettings['support_messenger_url'] ?? 'https://m.me';
        $supportWorkingHours = $supportSettings['support_working_hours'] ?? '08:00 - 22:00 mỗi ngày';
    @endphp

    <!-- PROFILE MODAL -->
    <div class="profile-modal" id="profile-modal">
        <div class="profile-modal-card">
            <div class="profile-modal-header">
                <div style="display:flex; align-items:center; gap:0.75rem">
                    <span style="font-size:1.5rem">👤</span>
                    <div>
                        <div style="font-weight:800; font-size:1.1rem">Thông tin tài khoản</div>
                        <div style="font-size:0.75rem; color:var(--text-muted)">Cập nhật tên và mật khẩu của bạn</div>
                    </div>
                </div>
                <button class="btn-close-modal" onclick="closeProfileModal()"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="profile-modal-body">
                <form id="profile-update-form" onsubmit="event.preventDefault(); submitProfileUpdate();">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Tên hiển thị</label>
                        <div class="auth-input-group">
                            <i class="bi bi-person"></i>
                            <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu mới (Bỏ trống nếu không đổi)</label>
                        <div class="auth-input-group">
                            <i class="bi bi-lock"></i>
                            <input type="password" name="password" class="form-control" placeholder="Mật khẩu tối thiểu 8 ký tự">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Xác nhận mật khẩu mới</label>
                        <div class="auth-input-group">
                            <i class="bi bi-shield-check"></i>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu mới">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100" id="btn-profile-submit" style="padding:0.875rem">
                        <i class="bi bi-save"></i> Cập nhật ngay
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- CONTACT SUPPORT MODAL -->
    <div class="profile-modal" id="contact-modal">
        <div class="profile-modal-card">
            <div class="profile-modal-header">
                <div style="display:flex; align-items:center; gap:0.75rem">
                    <span style="font-size:1.5rem">📞</span>
                    <div>
                        <div style="font-weight:800; font-size:1.1rem">{{ $supportTitle }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted)">{{ $supportSubtitle }}</div>
                    </div>
                </div>
                <button class="btn-close-modal" onclick="closeContactModal()"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="profile-modal-body" style="display:grid; gap:0.7rem">
                <a href="{{ route('support.chat') }}" class="btn btn-primary" style="justify-content:flex-start">
                    <i class="bi bi-chat-dots"></i> Chat trực tiếp với admin
                </a>
                <a href="{{ route('nav.index') }}" class="btn btn-outline" style="justify-content:flex-start">
                    <i class="bi bi-shield-check"></i> {{ $supportCenterLabel }}
                </a>
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $supportPhone) }}" class="btn btn-outline" style="justify-content:flex-start">
                    <i class="bi bi-telephone"></i> Hotline: {{ $supportPhone }}
                </a>
                <a href="mailto:{{ $supportEmail }}" class="btn btn-outline" style="justify-content:flex-start">
                    <i class="bi bi-envelope"></i> Email: {{ $supportEmail }}
                </a>
                <a href="{{ $supportZaloUrl }}" target="_blank" rel="noopener" class="btn btn-outline" style="justify-content:flex-start">
                    <i class="bi bi-chat-left-dots"></i> Zalo hỗ trợ
                </a>
                <a href="{{ $supportMessengerUrl }}" target="_blank" rel="noopener" class="btn btn-outline" style="justify-content:flex-start">
                    <i class="bi bi-messenger"></i> Facebook Messenger
                </a>
                <div style="font-size:0.78rem; color:var(--text-muted); margin-top:0.35rem">
                    Thời gian hỗ trợ: {{ $supportWorkingHours }}.
                </div>
            </div>
        </div>
    </div>
    @endauth

    <script>
        // Global toast function
        function showToast(msg, type = 'success') {
            const icons = { success: 'bi-check-circle-fill', error: 'bi-x-circle-fill', info: 'bi-info-circle-fill' };
            const colors = { success: '#10b981', error: '#ef4444', info: '#6366f1' };
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `<i class="bi ${icons[type]}" style="color:${colors[type]}; font-size:1.2rem"></i><span>${msg}</span>`;
            document.getElementById('toast-container').appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        }

        // Update nav balance
        function updateNavBalance(balance) {
            const el = document.getElementById('nav-balance');
            if (el) el.textContent = balance;
        }

        // Nav dropdown toggle
        function toggleNavDropdown(e) {
            e.preventDefault();
            const menu    = document.getElementById('nav-games-dropdown');
            const chevron = document.getElementById('nav-chevron');
            const isOpen  = menu.classList.toggle('open');
            if (chevron) chevron.style.transform = isOpen ? 'rotate(180deg)' : '';
        }
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.querySelector('.nav-dropdown');
            const chevron = document.getElementById('nav-chevron');
            if (dropdown && !dropdown.contains(e.target)) {
                document.getElementById('nav-games-dropdown')?.classList.remove('open');
                if (chevron) chevron.style.transform = '';
            }
        });

        // Profile Modal Logic
        function openProfileModal() {
            document.getElementById('profile-modal').classList.add('open');
        }
        function closeProfileModal() {
            document.getElementById('profile-modal').classList.remove('open');
        }

        function openContactModal() {
            document.getElementById('contact-modal').classList.add('open');
        }

        function closeContactModal() {
            document.getElementById('contact-modal').classList.remove('open');
        }

        async function submitProfileUpdate() {
            const form = document.getElementById('profile-update-form');
            const btn  = document.getElementById('btn-profile-submit');
            const data = new FormData(form);
            
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang lưu...';

            try {
                const resp = await fetch('{{ route("profile.update") }}', {
                    method: 'POST',
                    body: data,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const result = await resp.json();

                if (result.success) {
                    showToast(result.message, 'success');
                    // Update UI name if changed
                    if (result.user && result.user.name) {
                        // Refresh to sync avatar if needed, or just toast
                        setTimeout(() => location.reload(), 1500);
                    }
                    closeProfileModal();
                } else {
                    showToast(result.message || 'Có lỗi xảy ra!', 'error');
                }
            } catch (err) {
                showToast('Lỗi kết nối máy chủ!', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-save"></i> Cập nhật ngay';
            }
        }
        
        // Close modal on escape or background click
        document.getElementById('profile-modal')?.addEventListener('click', function(e) {
            if (e.target === this) closeProfileModal();
        });
        document.getElementById('contact-modal')?.addEventListener('click', function(e) {
            if (e.target === this) closeContactModal();
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeProfileModal();
            if (e.key === 'Escape') closeContactModal();
        });

        // Mobile bottom-nav loading indicator
        function initMobileNavLoader() {
            if (!window.matchMedia('(max-width: 768px)').matches) return;

            const loader = document.getElementById('mobile-nav-loader');
            if (!loader) return;

            document.querySelectorAll('.mobile-bottom-nav .m-nav-item[href]').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    const href = (link.getAttribute('href') || '').trim();

                    // Skip non-navigation links and special click actions.
                    if (!href || href.startsWith('javascript:') || href.startsWith('#')) return;
                    if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button !== 0) return;
                    if (link.getAttribute('target') === '_blank') return;

                    const targetUrl = new URL(link.href, window.location.origin);
                    const currentUrl = new URL(window.location.href);
                    if (targetUrl.pathname === currentUrl.pathname && targetUrl.search === currentUrl.search) return;

                    loader.classList.add('show');
                    document.body.classList.add('is-mobile-nav-loading');
                });
            });

            window.addEventListener('pageshow', function() {
                loader.classList.remove('show');
                document.body.classList.remove('is-mobile-nav-loading');
            });
        }

        initMobileNavLoader();
    </script>

    @stack('scripts')
</body>
</html>
