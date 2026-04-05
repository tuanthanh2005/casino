<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('page_title', 'Dashboard') - Aquahub Admin</title>

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
</head>

<body>
    <div class="admin-sidebar">
        <div style="margin-bottom: 3rem; padding-left: 1rem; display: flex; align-items: center; gap: 0.75rem;">
            <img src="{{ asset('av.png') }}" style="width: 32px; height: 32px; border-radius: 6px;">
            <h1 style="font-size: 1.25rem; font-weight: 800; color: white; margin-bottom: 0;">AQUAHUB</h1>
        </div>

        <nav>
            <a href="/admin" class="nav-item-admin {{ request()->is('admin') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('admin.posts.index') }}" class="nav-item-admin {{ request()->is('admin/posts*') ? 'active' : '' }}">All Posts</a>
            <a href="{{ route('admin.messages.index') }}" class="nav-item-admin {{ request()->is('admin/messages*') ? 'active' : '' }}">Support Messages</a>
            <a href="/admin/categories" class="nav-item-admin {{ request()->is('admin/categories*') ? 'active' : '' }}">Categories</a>
            <a href="/admin/tags" class="nav-item-admin {{ request()->is('admin/tags*') ? 'active' : '' }}">Tags</a>
            <div style="margin: 2rem 1rem 1rem; font-size: 0.75rem; text-transform: uppercase; color: #475569; letter-spacing: 0.1em;">External</div>
            <a href="/" target="_blank" class="nav-item-admin">Visit Website</a>
            <a href="/logout" class="nav-item-admin" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: #fca5a5;">Logout</a>

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

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-bottom: 2rem; border-radius: 12px; border: none; background: #dcfce7; color: #166534;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @yield('admin_content')
    </div>

    <!-- Bootstrap 5 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    @stack('scripts')
</body>

</html>
