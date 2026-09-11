/*
 * Service worker for the field app (/field).
 *
 * Keeps the field screen openable with no signal:
 *   - the /field page itself: network first, falling back to the last copy
 *     (which carries the route, prices and van stock as Inertia props);
 *   - built assets (/build/assets/*): cache first — their names are content
 *     hashes, so a cached file is never stale;
 *   - everything else, including every /ops write, goes straight to the
 *     network. Writes made offline are queued by the page in IndexedDB, not
 *     here, so they can be shown, edited and retried.
 */
const VERSION = 'field-v1';
const PAGE_CACHE = `${VERSION}-page`;
const ASSET_CACHE = `${VERSION}-assets`;

self.addEventListener('install', () => self.skipWaiting());

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches
            .keys()
            .then((keys) => Promise.all(keys.filter((k) => !k.startsWith(VERSION)).map((k) => caches.delete(k))))
            .then(() => self.clients.claim()),
    );
});

const isFieldPage = (url) => /\/field\/?$/.test(url.pathname);
const isAsset = (url) => url.pathname.includes('/build/assets/') || url.pathname.endsWith('/field.webmanifest');

self.addEventListener('fetch', (event) => {
    const req = event.request;
    if (req.method !== 'GET') return;

    const url = new URL(req.url);
    if (url.origin !== self.location.origin) return;

    // Inertia's own visits to /field ask for JSON; cache those too, keyed apart.
    if (isFieldPage(url)) {
        event.respondWith(
            fetch(req)
                .then((res) => {
                    if (res.ok) {
                        const copy = res.clone();
                        caches.open(PAGE_CACHE).then((c) => c.put(req, copy));
                    }
                    return res;
                })
                .catch(() => caches.match(req).then((hit) => hit || caches.match(url.pathname))),
        );
        return;
    }

    if (isAsset(url)) {
        event.respondWith(
            caches.match(req).then(
                (hit) =>
                    hit ||
                    fetch(req).then((res) => {
                        if (res.ok) {
                            const copy = res.clone();
                            caches.open(ASSET_CACHE).then((c) => c.put(req, copy));
                        }
                        return res;
                    }),
            ),
        );
    }
});
