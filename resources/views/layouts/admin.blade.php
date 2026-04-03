<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') | AquaHub Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('av.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: #06b6d4;
            --primary-dark: #0891b2;
            --accent: #10b981;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --bg: #060912;
            --bg-card: #0d1117;
            --bg-card2: #161b27;
            --sidebar-w: 250px;
            --border: rgba(99,102,241,0.15);
            --text: #f9fafb;
            --text-muted: #6b7280;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); display: flex; min-height: 100vh; }

        /* SIDEBAR */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--bg-card);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 100;
            overflow-y: auto;
        }

        .sidebar-logo {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .sidebar-logo span {
            font-size: 1.2rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .sidebar-logo small {
            display: block;
            font-size: 0.7rem;
            color: var(--text-muted);
            margin-top: 0.15rem;
        }

        .sidebar-menu {
            list-style: none;
            padding: 1rem 0;
            flex: 1;
        }

        .sidebar-menu li {}

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.15s;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            color: var(--text);
            background: rgba(99,102,241,0.1);
        }

        .sidebar-menu a.active {
            border-left: 3px solid var(--primary);
        }

        .sidebar-menu a .bi { font-size: 1rem; }

        .sidebar-section-title {
            padding: 0.5rem 1.5rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-muted);
            margin-top: 0.5rem;
        }

        .sidebar-bottom {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border);
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .sidebar-user img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
        }

        .sidebar-user .info .name {
            font-size: 0.8rem;
            font-weight: 600;
        }

        .sidebar-user .info .role {
            font-size: 0.7rem;
            color: var(--primary);
        }

        .btn-logout-sidebar {
            width: 100%;
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.3);
            color: #ef4444;
            border-radius: 8px;
            padding: 0.5rem;
            font-size: 0.8rem;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-logout-sidebar:hover { background: rgba(239,68,68,0.2); }

        /* ADMIN BADGE */
        .admin-badge {
            display: inline-block;
            padding: 0.15rem 0.5rem;
            background: rgba(99,102,241,0.2);
            border: 1px solid rgba(99,102,241,0.4);
            color: #a5b4fc;
            border-radius: 4px;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-left: 0.5rem;
        }

        /* MAIN CONTENT */
        .admin-main {
            margin-left: var(--sidebar-w);
            flex: 1;
            padding: 2rem;
            max-width: calc(100% - var(--sidebar-w));
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 800;
        }

        .page-header p {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* STAT CARDS */
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s;
        }

        .stat-card:hover { transform: translateY(-2px); }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 900;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
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
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-body { padding: 1.5rem; }

        /* BUTTONS */
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.8rem;
            cursor: pointer;
            border: none;
            transition: all 0.15s;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            text-decoration: none;
            font-family: 'Inter', sans-serif;
        }

        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-success { background: var(--success); color: white; }
        .btn-success:hover { background: #059669; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-danger:hover { background: #dc2626; }
        .btn-warning { background: var(--warning); color: #000; }
        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text); }
        .btn-outline:hover { background: var(--bg-card2); }
        .btn-sm { padding: 0.35rem 0.75rem; font-size: 0.75rem; }

        /* TABLE */
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
        th { padding: 0.75rem 1rem; text-align: left; color: var(--text-muted); font-weight: 500; border-bottom: 1px solid var(--border); white-space: nowrap; }
        td { padding: 0.8rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.04); vertical-align: middle; }
        tr:hover td { background: rgba(99,102,241,0.04); }

        /* FORM */
        .form-control {
            background: var(--bg-card2);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            padding: 0.575rem 0.875rem;
            width: 100%;
            font-size: 16px; /* Fix auto-zoom on Mobile */
            font-family: 'Inter', sans-serif;
        }
        .form-control:focus { outline: none; border-color: var(--primary); }

        /* BADGES */
        .badge { padding: 0.25rem 0.6rem; border-radius: 6px; font-size: 0.72rem; font-weight: 600; }
        .badge-success { background: rgba(16,185,129,0.15); color: #10b981; }
        .badge-danger { background: rgba(239,68,68,0.15); color: #ef4444; }
        .badge-warning { background: rgba(245,158,11,0.15); color: #f59e0b; }
        .badge-primary { background: rgba(99,102,241,0.15); color: #818cf8; }

        /* ALERT */
        .alert { padding: 0.875rem 1rem; border-radius: 10px; margin-bottom: 1rem; font-size: 0.875rem; }
        .alert-success { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); color: #10b981; }
        .alert-danger { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #ef4444; }

        /* MODAL */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.8);
            z-index: 9900;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .modal-overlay.active { display: flex; }
        .modal-box {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2rem;
            max-width: 460px;
            width: 100%;
        }
        .modal-title { font-size: 1.2rem; font-weight: 700; margin-bottom: 1rem; }

        /* UTILS */
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 1rem; }
        .mb-4 { margin-bottom: 1.5rem; }
        .mt-3 { margin-top: 1rem; }
        .mt-4 { margin-top: 1.5rem; }
        .text-muted { color: var(--text-muted); }
        .text-center { text-align: center; }
        .d-flex { display: flex; }
        .align-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-2 { gap: 0.5rem; }
        .gap-3 { gap: 1rem; }
        .fw-bold { font-weight: 700; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .w-100 { width: 100%; }

        /* TOAST */
        .toast-container { position: fixed; bottom: 2rem; right: 2rem; z-index: 9999; display: flex; flex-direction: column; gap: 0.5rem; }
        .toast { background: var(--bg-card); border: 1px solid var(--border); border-radius: 12px; padding: 1rem 1.25rem; min-width: 280px; display: flex; align-items: center; gap: 0.75rem; animation: slideIn 0.3s ease; font-size: 0.875rem; }
        .toast.success { border-left: 3px solid var(--success); }
        .toast.error { border-left: 3px solid var(--danger); }
        @keyframes slideIn { from { opacity: 0; transform: translateX(80px); } to { opacity: 1; transform: translateX(0); } }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .admin-main { margin-left: 0; max-width: 100%; }
        }
    </style>
    @stack('admin-styles')
</head>
<body>
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <span>🌊 AquaHub</span>
            <small>Admin Panel <span class="admin-badge">ADMIN</span></small>
        </div>

        <ul class="sidebar-menu">
            <li class="sidebar-section-title">Tổng quan</li>
            <li>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            <li class="sidebar-section-title">Quản lý Game</li>
            <li>
                <a href="{{ route('admin.sessions') }}" class="{{ request()->routeIs('admin.sessions') ? 'active' : '' }}">
                    <i class="bi bi-controller"></i> Phiên cược BTC
                </a>
            </li>
            <li>
                <a href="{{ route('admin.casino') }}" class="{{ request()->routeIs('admin.casino') ? 'active' : '' }}"
                   style="{{ request()->routeIs('admin.casino') ? '' : '' }}">
                    <i class="bi bi-dice-5"></i> 🎰 Casino Stats
                </a>
            </li>
            <li>
                <a href="{{ route('admin.support.chat') }}" class="{{ request()->routeIs('admin.support.chat*') ? 'active' : '' }}">
                    <i class="bi bi-chat-square-dots"></i> 💬 Chat khách hàng
                </a>
            </li>
            <li>
                <a href="{{ route('admin.support.contacts') }}" class="{{ request()->routeIs('admin.support.contacts*') ? 'active' : '' }}">
                    <i class="bi bi-headset"></i> 📞 Liên hệ hỗ trợ
                </a>
            </li>


            <li class="sidebar-section-title">Quản lý User</li>
            <li>
                <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Người dùng
                </a>
            </li>
            <li>
                <a href="{{ route('admin.exchanges') }}" class="{{ request()->routeIs('admin.exchanges') ? 'active' : '' }}">
                    <i class="bi bi-gift"></i> Yêu cầu đổi quà
                    @php $pendingCount = \App\Models\ExchangeRequest::where('status','pending')->count(); @endphp
                    @if($pendingCount > 0)
                        <span style="margin-left:auto; background:var(--danger); color:white; border-radius:100px; padding:0.1rem 0.5rem; font-size:0.7rem; font-weight:700">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>
            </li>

            <li class="sidebar-section-title">Cửa hàng</li>
            <li>
                <a href="{{ route('admin.rewards.index') }}" class="{{ request()->routeIs('admin.rewards.*') ? 'active' : '' }}">
                    <i class="bi bi-shop"></i> Phần thưởng
                </a>
            </li>
            <li>
                <a href="{{ route('admin.blog-posts.index') }}" class="{{ request()->routeIs('admin.blog-posts.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-richtext"></i> Blog SEO
                </a>
            </li>

            <li class="sidebar-section-title">💳 Tài Chính</li>
            <li>
                <a href="{{ route('admin.deposits') }}" class="{{ request()->routeIs('admin.deposits*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-down-circle"></i> Nạp tiền
                    @php $depPending = \App\Models\DepositOrder::where('status','pending')->count(); @endphp
                    @if($depPending > 0)
                        <span style="margin-left:auto; background:var(--success); color:white; border-radius:100px; padding:0.1rem 0.5rem; font-size:0.7rem; font-weight:700">{{ $depPending }}</span>
                    @endif
                </a>
            </li>
            <li>
                <a href="{{ route('admin.withdrawals') }}" class="{{ request()->routeIs('admin.withdrawals*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-up-circle"></i> Rút tiền
                    @php $wdPending = \App\Models\WithdrawalOrder::where('status','pending')->count(); @endphp
                    @if($wdPending > 0)
                        <span style="margin-left:auto; background:var(--danger); color:white; border-radius:100px; padding:0.1rem 0.5rem; font-size:0.7rem; font-weight:700">{{ $wdPending }}</span>
                    @endif
                </a>
            </li>
            <li>
                <a href="{{ route('admin.finance.summary') }}" class="{{ request()->routeIs('admin.finance.summary') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line"></i> Tổng thống kê doanh thu
                </a>
            </li>
            <li>
                <a href="{{ route('admin.finance.loss') }}" class="{{ request()->routeIs('admin.finance.loss') ? 'active' : '' }}">
                    <i class="bi bi-graph-down-arrow"></i> Doanh thu lỗ
                </a>
            </li>
            <li class="sidebar-section-title">🌾 Nông Trại</li>
            <li>
                <a href="{{ route('admin.farm') }}" class="{{ request()->routeIs('admin.farm') ? 'active' : '' }}">
                    <i class="bi bi-graph-up"></i> Dashboard Farm
                </a>
            </li>
            <li>
                <a href="{{ route('admin.farm.seeds') }}" class="{{ request()->routeIs('admin.farm.seeds') ? 'active' : '' }}">
                    <i class="bi bi-flower1"></i> Quản lý hạt giống
                </a>
            </li>
            <li>
                <a href="{{ route('admin.farm.transactions') }}" class="{{ request()->routeIs('admin.farm.transactions') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i> Lịch sử giao dịch
                </a>
            </li>

            <li class="sidebar-section-title">🛡️ Hỗ Trợ MXH</li>
            <li>
                <a href="{{ route('admin.nav.orders') }}" class="{{ request()->routeIs('admin.nav.orders*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-check"></i> Đơn Hàng NAV
                    @php $navPending = \App\Models\NavOrder::where('status', 'paid')->count(); @endphp
                    @if($navPending > 0)
                        <span style="margin-left:auto; background:var(--primary); color:white; border-radius:100px; padding:0.1rem 0.5rem; font-size:0.7rem; font-weight:700">{{ $navPending }}</span>
                    @endif
                </a>
            </li>
            <li>
                <a href="{{ route('admin.nav.services') }}" class="{{ request()->routeIs('admin.nav.services*') ? 'active' : '' }}">
                    <i class="bi bi-grid-3x2-gap"></i> Dịch Vụ
                </a>
            </li>
            <li>
                <a href="{{ route('admin.nav.settings') }}" class="{{ request()->routeIs('admin.nav.settings*') ? 'active' : '' }}">
                    <i class="bi bi-bank"></i> Cài Đặt TT
                </a>
            </li>

            <li>
                <a href="{{ route('home') }}" target="_blank">
                    <i class="bi bi-box-arrow-up-right"></i> Xem trang chính
                </a>
            </li>
        </ul>

        <div class="sidebar-bottom">
            <div class="sidebar-user">
                <img src="{{ auth()->user()->avatar_url }}" alt="Admin">
                <div class="info">
                    <div class="name">{{ auth()->user()->name }}</div>
                    <div class="role">Administrator</div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout-sidebar">
                    <i class="bi bi-box-arrow-right"></i> Đăng xuất
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN -->
    <main class="admin-main">
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

        @yield('admin-content')
    </main>

    <!-- TOAST -->
    <div class="toast-container" id="toast-container"></div>

    <script>
        function showToast(msg, type = 'success') {
            const icons = { success: 'bi-check-circle-fill', error: 'bi-x-circle-fill', info: 'bi-info-circle-fill' };
            const colors = { success: '#10b981', error: '#ef4444', info: '#6366f1' };
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `<i class="bi ${icons[type]}" style="color:${colors[type]}; font-size:1.2rem"></i><span>${msg}</span>`;
            document.getElementById('toast-container').appendChild(toast);
            setTimeout(() => toast.remove(), 5000);
        }
    </script>

    @stack('admin-scripts')
</body>
</html>
