<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
    <title>@yield('title', 'Aquahub.pro — The Modern Aquarium Resource')</title>
    <meta name="description"
        content="@yield('meta_description', 'Elite guides, setup tutorials, and species care for aquarium owners worldwide.')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('av.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <!-- Google AdSense -->
    @php
        $adsenseEnabled = \App\Models\Setting::get('adsense_enabled', '0');
        $adsenseCode = \App\Models\Setting::get('adsense_code', '');
    @endphp
    @if($adsenseEnabled == '1' && !empty($adsenseCode))
        {!! $adsenseCode !!}
    @endif

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
                <a href="/category/{{ app()->getLocale() == 'vi' ? 'cho-nguoi-moi' : 'beginners' }}">{{ __('Beginners') }}</a>
                <a href="/category/{{ app()->getLocale() == 'vi' ? 'huong-dan-lap-dat' : 'setup-guides' }}">{{ __('Setup Guides') }}</a>
                <a href="/category/{{ app()->getLocale() == 'vi' ? 'danh-gia-san-pham' : 'product-reviews' }}">{{ __('Reviews') }}</a>
                @if(auth()->check() && auth()->user()->is_admin)
                    <a href="/admin" class="text-danger fw-bold">{{ __('Admin') }}</a>
                @endif
                <a href="{{ auth()->check() ? route('profile') : route('login') }}" class="{{ request()->is('profile') || request()->is('login') ? 'active' : '' }}">
                    {{ auth()->check() ? __('Account') : __('Login') }}
                </a>
            </nav>
            <div class="d-flex align-items-center gap-2 gap-sm-3">
                <div class="dropdown country-dropdown">
                    @php
                        $loc = app()->getLocale();
                        $currentCountry = ($active_countries ?? collect())->filter(function($c) use ($loc) {
                            $code = strtolower($c->code);
                            if ($loc == 'vi') return in_array($code, ['vi', 'vn']);
                            if ($loc == 'en') return in_array($code, ['en', 'us', 'uk']);
                            return $code == strtolower($loc);
                        })->first() ?? ($active_countries ?? collect())->first();
                    @endphp
                    
                    <a class="d-flex align-items-center dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration: none; padding: 5px;">
                        @if($currentCountry && isset($currentCountry->icon) && $currentCountry->icon)
                            <img src="{{ asset('uploads/countries/' . $currentCountry->icon) }}" alt="{{ $currentCountry->name ?? '' }}" style="width: 26px; height: 17px; object-fit: cover; border-radius: 3px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border: 2px solid white;">
                        @else
                            <span class="small fw-bold text-dark border px-2 py-1 rounded bg-light">{{ strtoupper(app()->getLocale()) }}</span>
                        @endif
                    </a>
                    
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-2" style="border-radius: 15px; min-width: 170px; font-size: 0.85rem; z-index: 99999;">
                        <li class="px-3 py-2">
                            <span class="text-secondary smaller text-uppercase fw-800 opacity-50" style="font-size: 0.65rem; letter-spacing: 0.1em;">{{ __('Select Country') }}</span>
                        </li>
                        @foreach($active_countries ?? [] as $country)
                            <li>
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center justify-content-between {{ (isset($country->code) && (strtolower(app()->getLocale()) == strtolower($country->code) || ($country->code == 'VI' && app()->getLocale() == 'vi') || ($country->code == 'en' && app()->getLocale() == 'en'))) ? 'active' : '' }}" href="{{ isset($country->code) ? route('lang.switch', $country->code) : '#' }}">
                                    <div class="d-flex align-items-center gap-2">
                                        @if(isset($country->icon) && $country->icon)
                                            <img src="{{ asset('uploads/countries/' . $country->icon) }}" style="width: 20px; height: 13px; object-fit: cover; border-radius: 2px;">
                                        @endif
                                        <span class="fw-500">{{ $country->name ?? '' }}</span>
                                    </div>
                                    @if(isset($country->code) && app()->getLocale() == $country->code)
                                        <div style="width: 6px; height: 6px; background: var(--primary); border-radius: 50%;"></div>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <style>
                    /* Hiện mũi tên nhỏ */
                    .country-dropdown .dropdown-toggle::after { 
                        margin-left: 0.35em;
                        vertical-align: 0.15em;
                        border-top: .3em solid;
                        border-right: .3em solid transparent;
                        border-bottom: 0;
                        border-left: .3em solid transparent;
                        opacity: 0.5;
                    }
                    .country-dropdown .dropdown-item.active { background-color: #f1f5f9; color: var(--dark); font-weight: 700; }
                    .country-dropdown .dropdown-item:hover { background-color: #f8fafc; }
                </style>
                <!-- Search Icon for Mobile -->
                <button class="btn p-1 border-0 d-md-none text-dark" onclick="toggleSearch()" id="mobile-search-trigger">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </button>

                @if(auth()->check())
                    <!-- Removed Account from header action area as it's now in menu/nav-links -->
                @endif
            </div>
        </div>
        <!-- Mobile Search Dropdown -->
        <div id="mobile-search-bar" class="d-md-none" style="display: none; position: absolute; top: 60px; left: 0; right: 0; background: white; padding: 12px 20px; border-bottom: 1px solid var(--border); box-shadow: 0 10px 20px rgba(0,0,0,0.05); z-index: 999;">
            <form action="{{ route('search') }}" method="GET">
                <input type="text" name="q" class="form-control border-0 bg-light" placeholder="{{ __('Search guides and tutorials...') }}" 
                    style="border-radius: 12px; padding: 0.75rem 1.25rem; font-size: 16px;" id="mobile-search-input">
            </form>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <div id="chat-bubble" onclick="toggleChat()"
        style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; background: var(--dark); border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 10px 25px rgba(0,0,0,0.2); z-index: 10000; transition: transform 0.3s ease;">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white"
            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="m3 21 1.9-1.9a9 9 0 1 1 3.8 3.8z"></path>
        </svg>
        <div id="msg-dot"
            style="position: absolute; top: 0; right: 0; width: 18px; height: 18px; background: #ef4444; border-radius: 50%; border: 2px solid white; display: {{ ($unread_user_messages ?? 0) > 0 ? 'flex' : 'none' }}; align-items: center; justify-content: center; color: white; font-size: 0.6rem; font-weight: 800;">
            {{ $unread_user_messages ?? '' }}
        </div>
    </div>

    <div id="chat-window"
        style="position: fixed; bottom: 100px; right: 30px; width: 350px; height: 500px; background: white; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); z-index: 10000; display: none; flex-direction: column; overflow: hidden; border: 1px solid var(--border);">
        <div
            style="background: var(--dark); color: white; padding: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h5 class="mb-0 h6">{{ __('Chat with Admin') }}</h5>
                <p class="mb-0" style="font-size: 0.7rem; opacity: 0.7;">{{ __('We usually reply in minutes') }}</p>
            </div>
            <button onclick="toggleChat()"
                style="background: none; border: none; color: white; opacity: 0.5;">✕</button>
        </div>
        <div id="chat-messages"
            style="flex-grow: 1; padding: 1.5rem; overflow-y: auto; display: flex; flex-direction: column; gap: 1rem; background: #f8fafc;">
        </div>
        <div style="padding: 1rem; border-top: 1px solid var(--border);">
            <div style="display: flex; gap: 0.5rem;">
                <input type="text" id="chat-input" placeholder="{{ __('Type a message...') }}"
                    style="flex-grow: 1; border: 1px solid var(--border); padding: 0.6rem 1rem; border-radius: 12px; font-size: 16px;"
                    onkeypress="if(event.key === 'Enter') sendMessage()">
                <button onclick="sendMessage()"
                    style="background: var(--dark); border: none; color: white; width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m5 12 7-7 7 7"></path>
                        <path d="M12 19V5"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <footer class="footer bg-white pt-5 pb-4 border-top">
        <div class="container overflow-hidden">
            <div class="row g-4 g-lg-5">
                <div class="col-lg-4">
                    <a href="/" class="logo mb-4 d-inline-flex">
                        <img src="{{ asset('av.png') }}" alt="" style="height: 36px; border-radius: 8px;">
                        <span>AQUAHUB</span>
                    </a>
                    <p class="text-secondary small mb-4" style="line-height: 1.8; max-width: 320px;">
                        {{ $footer_settings['footer_about'] ?? __('The elite aquarium resource dedicated to helping beginners build and maintain stunning, healthy underwater worlds.') }}
                    </p>
                    <div class="d-flex gap-3 mb-4">
                        @if(isset($footer_settings['social_facebook']))
                            <a href="{{ $footer_settings['social_facebook'] }}" class="text-dark opacity-50"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                                </svg></a>
                        @endif
                        @if(isset($footer_settings['social_instagram']))
                            <a href="{{ $footer_settings['social_instagram'] }}" class="text-dark opacity-50"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                                </svg></a>
                        @endif
                        @if(isset($footer_settings['social_x_twitter']))
                            <a href="{{ $footer_settings['social_x_twitter'] }}" class="text-dark opacity-50"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M4 4l11.733 16h4.267l-11.733-16zM4 20l6.768-6.768m2.464-2.464L20 4"></path>
                                </svg></a>
                        @endif
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <h5 class="fw-bold small text-uppercase mb-4">{{ __('Explore') }}</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                        <li><a href="/" class="text-secondary text-decoration-none small">{{ __('Home Resource') }}</a></li>
                        <li><a href="/blog" class="text-secondary text-decoration-none small">{{ __('Latest Guides') }}</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-3">
                    <h5 class="fw-bold small text-uppercase mb-4">{{ __('Categories') }}</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                        @if(isset($footer_categories))
                            @foreach($footer_categories as $cat)
                                <li><a href="/category/{{ $cat->slug }}"
                                        class="text-secondary text-decoration-none small d-flex justify-content-between align-items-center">
                                        {{ $cat->name }} <span class="opacity-25"
                                            style="font-size: 0.65rem;">({{ $cat->posts_count }})</span>
                                    </a></li>
                            @endforeach
                        @endif
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h5 class="fw-bold small text-uppercase mb-4">{{ __('Global Access') }}</h5>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        @if(isset($footer_settings['footer_regions']))
                            @foreach(explode(',', $footer_settings['footer_regions']) as $region)
                                <span class="badge border text-secondary fw-normal px-2 py-1"
                                    style="font-size: 0.6rem;">{{ trim($region) }}</span>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div
                class="mt-5 pt-4 border-top text-center d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <p class="mb-0 text-secondary extra-small fw-bold">
                    {{ $footer_settings['footer_copyright'] ?? '© 2026 AQUAHUB PRO. ' . __('All Rights Reserved') . '.' }}
                </p>
                <div class="d-flex gap-4">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/ba/Stripe_Logo%2C_revised_2016.svg"
                        alt="Payments" style="height: 18px; filter: grayscale(1); opacity: 0.5;">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" alt="Payments"
                        style="height: 18px; filter: grayscale(1); opacity: 0.5;">
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile Bottom Navigation -->
    <div class="mobile-bottom-nav d-md-none">
        <a href="/" class="mobile-nav-item {{ request()->is('/') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span>{{ __('Home') }}</span>
        </a>
        <a href="/category/{{ app()->getLocale() == 'vi' ? 'cho-nguoi-moi' : 'beginners' }}" class="mobile-nav-item {{ request()->is('category/beginners') || request()->is('category/cho-nguoi-moi') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            <span>{{ __('Beginners') }}</span>
        </a>
        <a href="/category/{{ app()->getLocale() == 'vi' ? 'huong-dan-lap-dat' : 'setup-guides' }}" class="mobile-nav-item {{ request()->is('category/setup-guides') || request()->is('category/huong-dan-lap-dat') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            <span>{{ __('Guides') }}</span>
        </a>
        <a href="/category/{{ app()->getLocale() == 'vi' ? 'danh-gia-san-pham' : 'product-reviews' }}" class="mobile-nav-item {{ request()->is('category/product-reviews') || request()->is('category/danh-gia-san-pham') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <span>{{ __('Reviews') }}</span>
        </a>
        <a href="{{ auth()->check() ? route('profile') : route('login') }}" class="mobile-nav-item {{ request()->is('profile') || request()->is('login') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span>{{ auth()->check() ? __('Profile') : __('Login') }}</span>
        </a>
        @if(auth()->check() && auth()->user()->is_admin)
            <a href="/admin" class="mobile-nav-item text-danger">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span>{{ __('Admin') }}</span>
            </a>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let searchOpen = false;
        function toggleSearch() {
            searchOpen = !searchOpen;
            const bar = document.getElementById('mobile-search-bar');
            bar.style.display = searchOpen ? 'block' : 'none';
            if (searchOpen) {
                document.getElementById('mobile-search-input').focus();
            }
        }

        let chatOpen = false;
        function toggleChat() {
            chatOpen = !chatOpen;
            document.getElementById('chat-window').style.display = chatOpen ? 'flex' : 'none';
            if (chatOpen) {
                loadMessages();
                document.getElementById('chat-bubble').style.transform = 'scale(0.9)';
                document.getElementById('msg-dot').style.display = 'none';
            } else {
                document.getElementById('chat-bubble').style.transform = 'scale(1)';
            }
        }
        async function loadMessages() {
            const res = await fetch('{{ route('chat.get') }}');
            const data = await res.json();
            const container = document.getElementById('chat-messages');
            container.innerHTML = '';
            data.messages.forEach(msg => {
                const div = document.createElement('div');
                div.style.cssText = msg.is_from_admin
                    ? 'align-self: flex-start; background: white; padding: 0.75rem 1rem; border-radius: 0 15px 15px 15px; font-size: 0.8125rem; max-width: 80%; border: 1px solid var(--border);'
                    : 'align-self: flex-end; background: var(--dark); color: white; padding: 0.75rem 1rem; border-radius: 15px 15px 0 15px; font-size: 0.8125rem; max-width: 80%;';
                div.innerText = msg.message;
                container.appendChild(div);
            });
            container.scrollTop = container.scrollHeight;
        }
        async function sendMessage() {
            const input = document.getElementById('chat-input');
            const msg = input.value.trim();
            if (!msg) return;
            input.value = '';
            await fetch('{{ route('chat.send') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ message: msg })
            });
            loadMessages();
        }
        setInterval(() => {
            if (chatOpen) { loadMessages(); } else { updateUnreadCount(); }
        }, 10000);
        async function updateUnreadCount() {
            const res = await fetch('{{ route('chat.unread') }}');
            const data = await res.json();
            const dot = document.getElementById('msg-dot');
            if (data.count > 0) { dot.innerText = data.count; dot.style.display = 'flex'; } else { dot.style.display = 'none'; }
        }
    </script>
    @stack('scripts')
</body>

</html>