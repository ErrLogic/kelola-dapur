const CACHE_NAME = 'kelola-dapur-v2';
const APP_SHELL = ['/manifest.json', '/icon.svg'];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(APP_SHELL)).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key)))
        ).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    // Only intercept requests for our own origin
    if (url.origin !== self.location.origin) {
        return;
    }

    // Never intercept Livewire requests (component updates, file uploads, etc.)
    if (url.pathname.startsWith('/livewire')) {
        return;
    }

    // Never intercept non-GET requests
    if (event.request.method !== 'GET') {
        return;
    }

    // Never intercept navigation requests — always fetch fresh from server
    // so dynamic Laravel pages (auth, CSRF, etc.) always work correctly
    if (event.request.mode === 'navigate') {
        return;
    }

    // For static assets: cache-first strategy
    event.respondWith(
        fetch(event.request).catch(async () => {
            const cachedResponse = await caches.match(event.request);
            if (cachedResponse) {
                return cachedResponse;
            }
            return new Response('', { status: 404, statusText: 'Not Found' });
        })
    );
});
