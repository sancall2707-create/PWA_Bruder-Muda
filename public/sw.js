/* Cache PWA Bruder FIC.
 * Naikkan CACHE_VERSION setiap kali aset statis berubah agar cache lama dibuang.
 */
const CACHE_VERSION = '20261107a';
const STATIC_CACHE = `pwa-static-${CACHE_VERSION}`;
const IMAGE_CACHE = `pwa-images-${CACHE_VERSION}`;
const PAGE_CACHE = `pwa-pages-${CACHE_VERSION}`;
const IMAGE_CACHE_LIMIT = 80;

const PRECACHE_URLS = [
    '/css/fic-pwa.css?v=20261104a',
    '/images/logo-fic.png',
    '/images/logo-tppbm.png?v=2',
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

    if (request.destination === 'image' || isImagePath(url.pathname) || url.pathname.endsWith('/foto')) {
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
        const finalPath = response.url ? new URL(response.url).pathname : url.pathname;
        if (response.ok && response.type === 'basic' && !response.redirected && !mustNotStore(response) && url.pathname !== '/login' && finalPath !== '/login') {
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

/* ---- Web Push ------------------------------------------------------------ */
self.addEventListener('push', (event) => {
    let data = { title: 'Bruder FIC', body: 'Notifikasi baru', url: '/', icon: '/images/logo-fic.png' };
    try { data = Object.assign(data, event.data.json()); } catch (e) { /* biar default */ }

    const promise = self.registration.showNotification(data.title, {
        body: data.body,
        icon: data.icon,
        badge: '/images/icons/icon-192.png',
        tag: data.tag || 'fic-default',
        renotify: true,
        data: { url: data.url },
    }).then(() => {
        // Beri tahu halaman yang terbuka agar menyegarkan angka penanda & lencana.
        // Lencana (setAppBadge) hanya tersedia di konteks halaman, bukan Service Worker.
        return self.clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((list) => {
                list.forEach((c) => c.postMessage({ jenis: 'notifikasi-baru' }));
            })
            .catch(() => {});
    });

    event.waitUntil(promise);
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = event.notification.data?.url || '/';
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((list) => {
            for (const c of list) {
                if (new URL(c.url).pathname === url && 'focus' in c) {
                    return c.focus();
                }
            }
            return clients.openWindow(url);
        })
    );
});

/* Token push dirotasi oleh browser/FCM: daftar ulang lalu kirim token baru ke server,
 * supaya notifikasi tidak berhenti mendadak tanpa sebab yang terlihat pengguna. */
self.addEventListener('pushsubscriptionchange', (event) => {
    const daftarUlang = (async () => {
        try {
            const lama = event.oldSubscription || await self.registration.pushManager.getSubscription();

            const res = await fetch('/push/vapid-key', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
            if (!res.ok) { return; }
            const { key } = await res.json();
            if (!key) { return; }

            const baru = event.newSubscription || await self.registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: base64ToUint8Array(key),
            });

            if (!lama) { return; }

            const k = baru.toJSON();
            await fetch('/push/rotate', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({
                    endpoint_lama: lama.endpoint,
                    endpoint: k.endpoint,
                    keys: { p256dh: k.keys.p256dh, auth: k.keys.auth },
                }),
            });
        } catch (e) { /* diam: rotasi gagal tidak boleh mematikan Service Worker */ }
    })();

    event.waitUntil(daftarUlang);
});

function base64ToUint8Array(base64) {
    const p = base64.replace(/-/g, '+').replace(/_/g, '/');
    const raw = atob(p);
    const arr = new Uint8Array(raw.length);
    for (let i = 0; i < raw.length; i++) { arr[i] = raw.charCodeAt(i); }
    return arr;
}
