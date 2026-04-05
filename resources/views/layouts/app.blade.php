<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Aquahub.pro — The Modern Aquarium Resource')</title>
    <meta name="description" content="@yield('meta_description', 'Elite guides, setup tutorials, and species care for aquarium owners worldwide.')">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('av.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
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
                
                @auth
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle py-1 px-3" type="button" data-bs-toggle="dropdown" style="font-size: 0.8125rem; border-radius: 99px;">
                            {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-2" style="border-radius: 12px; font-size: 0.875rem;">
                            @if(Auth::user()->is_admin)
                                <li><a class="dropdown-item py-2" href="/admin">Dashboard</a></li>
                                <li><hr class="dropdown-divider opacity-50"></li>
                            @endif
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">{{ __('Dashboard') }}</a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <!-- Chat Bubble -->
    <div id="chat-bubble" onclick="toggleChat()" style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; background: var(--dark); border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 10px 25px rgba(0,0,0,0.2); z-index: 10000; transition: transform 0.3s ease;">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m3 21 1.9-1.9a9 9 0 1 1 3.8 3.8z"></path></svg>
        <div id="msg-dot" style="position: absolute; top: 0; right: 0; width: 15px; height: 15px; background: #22c55e; border-radius: 50%; border: 2px solid white; display: none;"></div>
    </div>

    <!-- Chat Window -->
    <div id="chat-window" style="position: fixed; bottom: 100px; right: 30px; width: 350px; height: 500px; background: white; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); z-index: 10000; display: none; flex-direction: column; overflow: hidden; border: 1px solid var(--border);">
        <div style="background: var(--dark); color: white; padding: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h5 class="mb-0 h6">Chat with Admin</h5>
                <p class="mb-0" style="font-size: 0.7rem; opacity: 0.7;">We usually reply in minutes</p>
            </div>
            <button onclick="toggleChat()" style="background: none; border: none; color: white; opacity: 0.5;">✕</button>
        </div>
        <div id="chat-messages" style="flex-grow: 1; padding: 1.5rem; overflow-y: auto; display: flex; flex-direction: column; gap: 1rem; background: #f8fafc;">
            <!-- Messages load here -->
        </div>
        <div style="padding: 1rem; border-top: 1px solid var(--border);">
            @guest
                <div class="mb-2">
                    <input type="text" id="guest_name" placeholder="Your Name" style="width: 100%; border: 1px solid var(--border); padding: 0.5rem; border-radius: 8px; font-size: 0.75rem; margin-bottom: 0.5rem;">
                </div>
            @endguest
            <div style="display: flex; gap: 0.5rem;">
                <input type="text" id="chat-input" placeholder="Type a message..." style="flex-grow: 1; border: 1px solid var(--border); padding: 0.6rem 1rem; border-radius: 12px; font-size: 0.8125rem;" onkeypress="if(event.key === 'Enter') sendMessage()">
                <button onclick="sendMessage()" style="background: var(--dark); border: none; color: white; width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 7-7 7 7"></path><path d="M12 19V5"></path></svg>
                </button>
            </div>
        </div>
    </div>

    <footer class="footer">
        <!-- Footer Content -->
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let chatOpen = false;
        function toggleChat() {
            chatOpen = !chatOpen;
            document.getElementById('chat-window').style.display = chatOpen ? 'flex' : 'none';
            if(chatOpen) {
                loadMessages();
                document.getElementById('chat-bubble').style.transform = 'scale(0.9)';
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
                    : 'align-self: flex-end; background: var(--dark); color: white; padding: 0.75rem 1rem; border-radius: 15px 15px 0 15px; font-size: 0.8125rem; max-width: 80%; shadow: var(--shadow);';
                div.innerText = msg.message;
                container.appendChild(div);
            });
            container.scrollTop = container.scrollHeight;
        }

        async function sendMessage() {
            const input = document.getElementById('chat-input');
            const msg = input.value.trim();
            if(!msg) return;

            const guestName = document.getElementById('guest_name')?.value || null;
            
            input.value = '';
            
            await fetch('{{ route('chat.send') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ message: msg, guest_name: guestName })
            });

            loadMessages();
        }

        // Auto-poll for new messages if open
        setInterval(() => {
            if(chatOpen) loadMessages();
        }, 10000);
    </script>
    @stack('scripts')
</body>
</html>
