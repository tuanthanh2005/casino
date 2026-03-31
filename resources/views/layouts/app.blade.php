<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CryptoBet - Dự đoán BTC') | CryptoBet</title>
    <meta name="description" content="Webgame dự đoán giá Bitcoin Long/Short - Đổi Point lấy quà tặng Premium">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --accent: #f59e0b;
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
            min-width: 220px;
            padding: 0.5rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            z-index: 200;
            backdrop-filter: blur(20px);
        }
        .nav-dropdown-menu.open { display: block; animation: dropDown 0.2s ease; }
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
            font-size: 0.9rem;
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
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="{{ route('home') }}" class="navbar-brand">
                ₿ CryptoBet
            </a>
            
            @auth
            <ul class="nav-menu">
                {{-- Dropdown Games --}}
                <li class="nav-dropdown">
                    <a href="#" class="nav-link nav-dropdown-toggle {{ request()->routeIs('home') || request()->routeIs('spin') || request()->routeIs('dice') ? 'active' : '' }}"
                       onclick="toggleNavDropdown(event)">
                        <i class="bi bi-controller"></i> Games
                        <i class="bi bi-chevron-down" style="font-size:0.65rem; margin-left:2px; transition:transform 0.2s" id="nav-chevron"></i>
                    </a>
                    <div class="nav-dropdown-menu" id="nav-games-dropdown">
                        <a href="{{ route('home') }}" class="nav-dropdown-item {{ request()->routeIs('home') ? 'active' : '' }}">
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
                    </div>
                </li>
                <li><a href="{{ route('shop') }}" class="nav-link {{ request()->routeIs('shop') ? 'active' : '' }}">
                    <i class="bi bi-shop"></i> Cửa hàng
                </a></li>
                <li><a href="{{ route('payment.deposit') }}" class="nav-link {{ request()->routeIs('payment.deposit*') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle"></i> Nạp tiền
                </a></li>
                <li><a href="{{ route('payment.withdraw') }}" class="nav-link {{ request()->routeIs('payment.withdraw*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-up-right-circle"></i> Rút / Đổi
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
                    <span id="nav-balance">{{ number_format(auth()->user()->balance_point, 2) }}</span> PT
                </div>
                <img class="avatar" src="{{ auth()->user()->avatar_url }}" alt="Avatar">
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
                <a href="{{ route('login') }}" class="btn btn-outline">Đăng nhập</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Đăng ký</a>
            </div>
            @endguest
        </div>
    </nav>

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
            if (dropdown && !dropdown.contains(e.target)) {
                document.getElementById('nav-games-dropdown')?.classList.remove('open');
                const chevron = document.getElementById('nav-chevron');
                if (chevron) chevron.style.transform = '';
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
