/**
 * FoxDesk Service Worker
 * Minimal SW for PWA installability. Network-first strategy
 * so the app always shows fresh data from the server.
 */

const CACHE_NAME = 'foxdesk-v1';

// Install — just activate immediately
self.addEventListener('install', function(e) {
    self.skipWaiting();
});

// Activate — clean old caches
self.addEventListener('activate', function(e) {
    e.waitUntil(
        caches.keys().then(function(names) {
            return Promise.all(
                names.filter(function(n) { return n !== CACHE_NAME; })
                     .map(function(n) { return caches.delete(n); })
            );
        }).then(function() {
            return self.clients.claim();
        })
    );
});

// Fetch — network first, cache fallback for static assets only
self.addEventListener('fetch', function(e) {
    var url = new URL(e.request.url);

    // Never cache API calls, POST requests, or external resources
    if (e.request.method !== 'GET' ||
        url.search.indexOf('page=api') !== -1 ||
        url.origin !== self.location.origin) {
        return;
    }

    // Cache static assets (CSS, JS, fonts, images) with network-first
    var isStatic = /\.(css|js|woff2?|ttf|png|jpg|jpeg|svg|webp|ico)(\?|$)/.test(url.pathname);
    if (isStatic) {
        e.respondWith(
            fetch(e.request).then(function(response) {
                var clone = response.clone();
                caches.open(CACHE_NAME).then(function(cache) {
                    cache.put(e.request, clone);
                });
                return response;
            }).catch(function() {
                return caches.match(e.request);
            })
        );
    }
    // All other requests (PHP pages) go straight to network, no caching
});
