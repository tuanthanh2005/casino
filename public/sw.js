const CACHE_NAME = 'aquahub-v1';
const STATIC_ASSETS = [
    '/',
    '/manifest.json',
    '/av.png',
];

// Install: cache static assets
self.addEventListener('install', event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(STATIC_ASSETS).catch(() => {});
        })
    );
});

// Activate: clean old caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
        ).then(() => self.clients.claim())
    );
});

// Fetch: network first, fallback to cache
self.addEventListener('fetch', event => {
    // Chỉ xử lý GET requests
    if (event.request.method !== 'GET') return;

    // Bỏ qua API calls
    const url = new URL(event.request.url);
    if (url.pathname.startsWith('/api/') || url.pathname.startsWith('/admin/')) return;

    event.respondWith(
        fetch(event.request)
            .then(response => {
                // Cache page responses
                if (response.ok && response.type === 'basic') {
                    const cloned = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(event.request, cloned));
                }
                return response;
            })
            .catch(() => caches.match(event.request))
    );
});
