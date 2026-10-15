const CACHE_NAME = 'kreasi-rpl-v1';
const STATIC_ASSETS = [
    '/',
    '/login',
    '/offline',
    '/assets/css/bootstrap.min.css',
    '/assets/css/main.css',
    '/assets/css/kreasi-custom.css',
    '/assets/js/jquery-3.7.1.min.js',
    '/assets/js/boostrap.bundle.min.js',
    '/assets/js/phosphor-icon.js',
    '/assets/js/main.js',
    '/assets/images/logo/kreasi-logo.png',
    '/assets/images/logo/icon-192.png',
    '/manifest.json'
];

// Install Event
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(STATIC_ASSETS);
        })
    );
    self.skipWaiting();
});

// Activate Event
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys.map(key => {
                    if (key !== CACHE_NAME) {
                        return caches.delete(key);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// Fetch Event
self.addEventListener('fetch', event => {
    // Only handle GET requests
    if (event.request.method !== 'GET') return;

    // Handle static assets with Cache-First
    if (event.request.url.includes('/assets/')) {
        event.respondWith(
            caches.match(event.request).then(cachedResponse => {
                return cachedResponse || fetch(event.request).then(networkResponse => {
                    return caches.open(CACHE_NAME).then(cache => {
                        cache.put(event.request, networkResponse.clone());
                        return networkResponse;
                    });
                });
            })
        );
        return;
    }

    // Handle HTML pages with Network-First, fallback to cached offline page
    event.respondWith(
        fetch(event.request).catch(() => {
            return caches.match(event.request).then(response => {
                if (response) return response;
                if (event.request.mode === 'navigate') {
                    return caches.match('/offline');
                }
            });
        })
    );
});
