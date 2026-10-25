/* Cache PWA Bruder FIC.
 * Naikkan CACHE_VERSION setiap kali aset statis berubah agar cache lama dibuang.
 */
const CACHE_VERSION = '20261025a';
const STATIC_CACHE = `pwa-static-${CACHE_VERSION}`;
const IMAGE_CACHE = `pwa-images-${CACHE_VERSION}`;
const PAGE_CACHE = `pwa-pages-${CACHE_VERSION}`;
const IMAGE_CACHE_LIMIT = 80;

const PRECACHE_URLS = [
    '/css/fic-pwa.css?v=20261025a',
    '/images/logo-fic.png',
    '/images/logo-tppbm.png',
    '/images/icons/favicon-32.png',
    '/images/icons/icon-192.png',
    '/images/icons/icon-512.png',
    '/apple-touch-icon.png',
    '/favicon.ico',
    '/manifest.webmanifest',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => cache.addAll(PRECACHE_URLS))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    const keep = new Set([STATIC_CACHE, IMAGE_CACHE, PAGE_CACHE]);
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.filter((key) => !keep.has(key)).map((key) => caches.delete(key))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('message', (event) => {
    if (event.data !== 'clear-private') {
        return;
    }

    event.waitUntil(Promise.all([
        caches.delete(PAGE_CACHE),
        caches.delete(IMAGE_CACHE),
    ]));
});

self.addEventListener('fetch', (event) => {
    const request = event.request;

    if (request.method !== 'GET' || request.headers.has('range')) {
        return;
    }

    const url = new URL(request.url);
    if (url.origin !== self.location.origin) {
        return;
    }

    if (isPrivateAsset(url.pathname)) {
        return;
    }

    if (request.mode === 'navigate' || acceptsHtml(request)) {
        event.respondWith(networkFirstPage(request));
        return;
    }

    if (request.destination === 'image' || isImagePath(url.pathname)) {
        event.respondWith(staleWhileRevalidate(request, IMAGE_CACHE, IMAGE_CACHE_LIMIT));
        return;
    }

    if (request.destination === 'style' || request.destination === 'script' || request.destination === 'font' || isStaticPath(url.pathname)) {
        event.respondWith(cacheFirst(request, STATIC_CACHE));
    }
});

function isPrivateAsset(pathname) {
    return pathname.startsWith('/super-admin')
        || pathname.startsWith('/admin')
        || pathname === '/profil/foto'
        || pathname.endsWith('/foto')
        || pathname.includes('/bukti');
}

function acceptsHtml(request) {
    return (request.headers.get('accept') || '').includes('text/html');
}

function isImagePath(pathname) {
    return /\.(?:png|jpe?g|webp|gif|svg|ico)$/i.test(pathname);
}

function isStaticPath(pathname) {
    return /\.(?:css|js|woff2?)$/i.test(pathname) || pathname === '/manifest.webmanifest';
}

function mustNotStore(response) {
    const cacheControl = response.headers.get('Cache-Control') || '';

    return /no-store/i.test(cacheControl);
}

async function cacheFirst(request, cacheName) {
    const cache = await caches.open(cacheName);
    const cached = await cache.match(request);
    if (cached) {
        return cached;
    }

    const response = await fetch(request);
    await storeIfUseful(cache, request, response);
    return response;
}

async function staleWhileRevalidate(request, cacheName, limit) {
    const cache = await caches.open(cacheName);
    const cached = await cache.match(request);
    const network = fetch(request)
        .then(async (response) => {
            if (await storeIfUseful(cache, request, response)) {
                await trimCache(cache, limit);
            }
            return response;
        })
        .catch(() => cached);

    return cached || network;
}

async function networkFirstPage(request) {
    const cache = await caches.open(PAGE_CACHE);
    const url = new URL(request.url);

    try {
        const response = await fetch(request);
        if (response.ok && response.type === 'basic' && !mustNotStore(response) && url.pathname !== '/login') {
            await cache.put(request, response.clone());
        }
        return response;
    } catch (error) {
        const cached = await cache.match(request);
        if (cached) {
            return cached;
        }
        throw error;
    }
}

async function storeIfUseful(cache, request, response) {
    if (!response || !response.ok || response.type !== 'basic' || mustNotStore(response)) {
        return false;
    }

    const disposition = response.headers.get('Content-Disposition') || '';
    if (/attachment/i.test(disposition)) {
        return false;
    }

    await cache.put(request, response.clone());
    return true;
}

async function trimCache(cache, limit) {
    const keys = await cache.keys();
    if (keys.length <= limit) {
        return;
    }

    await Promise.all(keys.slice(0, keys.length - limit).map((key) => cache.delete(key)));
}
