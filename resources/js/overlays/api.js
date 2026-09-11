/**
 * JSON client for the /ops endpoints the overlays talk to.
 *
 * The app is often served from a subdirectory (/POS/public), so the base URL
 * comes from the page's <meta name="app-base"> instead of being assumed. The
 * CSRF token rides along on every write; Laravel's 422 bodies are flattened
 * into one readable message so a modal can show it as-is.
 */

export class ApiError extends Error {
    constructor(message, status, body) {
        super(message);
        this.status = status;
        this.body = body;
    }
}

const base = () => {
    const meta = document.querySelector('meta[name="app-base"]')?.content ?? window.location.origin;
    return `${meta.replace(/\/$/, '')}/ops`;
};

export const opsUrl = (path, query) => {
    const url = new URL(`${base()}/${String(path).replace(/^\//, '')}`, window.location.origin);
    for (const [k, v] of Object.entries(query ?? {})) {
        if (v !== undefined && v !== null && v !== '') url.searchParams.set(k, v);
    }
    return url.toString();
};

const messageFrom = (body, status) => {
    if (body?.errors) {
        const first = Object.values(body.errors).flat()[0];
        if (first) return first;
    }
    if (body?.message) return body.message;
    if (status === 403) return 'You do not have permission to do that.';
    if (status === 419) return 'Your session expired. Reload the page and try again.';
    return 'The server could not complete that. Try again.';
};

export async function api(path, { method = 'GET', body, query, signal } = {}) {
    const headers = { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' };
    const init = { method, headers, credentials: 'same-origin', signal };

    if (method !== 'GET') {
        headers['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        if (body instanceof FormData) {
            init.body = body;
        } else if (body !== undefined) {
            headers['Content-Type'] = 'application/json';
            init.body = JSON.stringify(body);
        }
    }

    let res;
    try {
        res = await fetch(opsUrl(path, query), init);
    } catch (e) {
        if (e.name === 'AbortError') throw e;
        throw new ApiError('No connection to the server.', 0, null);
    }

    const text = await res.text();
    let data = null;
    try {
        data = text ? JSON.parse(text) : null;
    } catch {
        data = null;
    }

    if (!res.ok) throw new ApiError(messageFrom(data, res.status), res.status, data);

    return data;
}

/** Current position, for check-in and anything else that must prove presence. */
export function currentPosition({ timeout = 12000 } = {}) {
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) {
            reject(new Error('This device cannot report its location.'));
            return;
        }
        navigator.geolocation.getCurrentPosition(
            (p) => resolve({ lat: p.coords.latitude, lng: p.coords.longitude, accuracy: Math.round(p.coords.accuracy) }),
            (e) =>
                reject(
                    new Error(
                        e.code === 1
                            ? 'Location permission is blocked for this site.'
                            : 'Could not get a GPS fix. Move to open sky and try again.',
                    ),
                ),
            { enableHighAccuracy: true, timeout, maximumAge: 30000 },
        );
    });
}
