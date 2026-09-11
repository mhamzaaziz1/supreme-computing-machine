<script setup>
/**
 * The field app: a seller's day on a phone.
 *
 * Built for a forecourt with one bar of signal. The route, balances, price
 * list and van stock arrive with the page (and the service worker keeps the
 * last copy, so it opens offline). Orders, collections and visits are saved
 * to the phone first and synced when there is signal; each carries its own
 * id, so a retry after a dropped connection can never create a second
 * invoice. Prices and credit rules are applied by the server at sync time.
 */
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import Icon from '../../components/Icon.vue';
import Money from '../../components/Money.vue';
import { api, currentPosition } from '../../overlays/api';

const props = defineProps({
    date: { type: String, required: true },
    fieldUser: { type: Boolean, default: false },
    routes: { type: Array, default: () => [] },
    routeId: { type: [Number, null], default: null },
    stops: { type: Array, default: () => [] },
    location: { type: Object, default: null },
    products: { type: Array, default: () => [] },
    methods: { type: Array, default: () => [] },
    outcomes: { type: Array, default: () => [] },
    syncedAt: { type: String, default: null },
});

const appBase = (document.querySelector('meta[name="app-base"]')?.content ?? window.location.origin).replace(/\/$/, '');

// -- The phone-side queue (IndexedDB) ------------------------------------------

const DB_NAME = 'gj-field';
const STORE = 'queue';
let dbPromise = null;

const idb = () =>
    (dbPromise ??= new Promise((resolve, reject) => {
        const req = indexedDB.open(DB_NAME, 1);
        req.onupgradeneeded = () => req.result.createObjectStore(STORE, { keyPath: 'client_id' });
        req.onsuccess = () => resolve(req.result);
        req.onerror = () => reject(req.error);
    }));

const idbRun = async (mode, work) => {
    const db = await idb();
    return new Promise((resolve, reject) => {
        const t = db.transaction(STORE, mode);
        const req = work(t.objectStore(STORE));
        t.oncomplete = () => resolve(req?.result);
        t.onerror = () => reject(t.error);
    });
};

const saveItem = (item) => idbRun('readwrite', (s) => s.put(JSON.parse(JSON.stringify(item)))).catch(() => null);
const dropItem = (id) => idbRun('readwrite', (s) => s.delete(id)).catch(() => null);
const loadItems = () => idbRun('readonly', (s) => s.getAll()).catch(() => []);

const queue = ref([]);
const online = ref(navigator.onLine);
const syncing = ref(false);
const lastSync = ref(props.syncedAt);
const notice = ref(null);
let noticeTimer;

const say = (text, tone = 'ok') => {
    notice.value = { text, tone };
    clearTimeout(noticeTimer);
    noticeTimer = setTimeout(() => (notice.value = null), 5000);
};

const uuid = () => (crypto.randomUUID ? crypto.randomUUID() : `${Date.now()}-${Math.random().toString(16).slice(2)}`);

const enqueue = async (kind, payload, label) => {
    const item = { client_id: uuid(), kind, payload, label, happened_at: new Date().toISOString(), status: 'pending', message: null, created: Date.now() };
    queue.value.push(item);
    await saveItem(item);
    say(online.value ? 'Saved. Sending now…' : 'Saved on the phone. It will send when there is signal.');
    sync();
};

const sync = async () => {
    if (syncing.value || !navigator.onLine) return;
    syncing.value = true;
    let changed = false;

    try {
        for (const item of [...queue.value].sort((a, b) => a.created - b.created)) {
            if (item.status === 'failed') continue;

            let r;
            try {
                r = await api('field/sync', {
                    method: 'POST',
                    body: { client_id: item.client_id, kind: item.kind, happened_at: item.happened_at, payload: item.payload },
                });
            } catch (e) {
                if (e.status === 0 || e.status === 419) break; // offline or logged out: try later
                r = { status: 'failed', message: e.message };
            }

            if (r.status === 'done') {
                queue.value = queue.value.filter((q) => q.client_id !== item.client_id);
                await dropItem(item.client_id);
                changed = true;
                say(r.message);
            } else {
                Object.assign(item, { status: 'failed', message: r.message, needs_reason: !!r.needs_reason });
                await saveItem(item);
            }
        }
    } finally {
        syncing.value = false;
    }

    if (changed) {
        lastSync.value = new Date().toISOString();
        router.reload({ only: ['stops', 'products', 'syncedAt'], preserveScroll: true });
    }
};

const retry = async (item) => {
    if (item.reason) item.payload.reason = item.reason;
    Object.assign(item, { status: 'pending', message: null });
    await saveItem(item);
    sync();
};

const discard = async (item) => {
    if (!window.confirm(`Delete this ${item.kind} from the phone? It has not been sent.`)) return;
    queue.value = queue.value.filter((q) => q.client_id !== item.client_id);
    await dropItem(item.client_id);
};

const goOnline = () => {
    online.value = true;
    sync();
};
const goOffline = () => (online.value = false);
let timer;

onMounted(async () => {
    queue.value = (await loadItems()) ?? [];
    window.addEventListener('online', goOnline);
    window.addEventListener('offline', goOffline);
    timer = setInterval(sync, 30000);
    sync();

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register(`${appBase}/field-sw.js`, { scope: `${appBase}/` }).catch(() => null);
    }
});

onBeforeUnmount(() => {
    window.removeEventListener('online', goOnline);
    window.removeEventListener('offline', goOffline);
    clearInterval(timer);
});

// -- Stops ----------------------------------------------------------------------

const filter = ref('all');
const pendingFor = (id) => queue.value.filter((q) => q.payload.contact_id === id);

const shown = computed(() =>
    props.stops.filter((s) => (filter.value === 'due' ? s.due : filter.value === 'todo' ? !s.visited_today && !pendingFor(s.id).length : true)),
);

const counts = computed(() => ({
    visited: props.stops.filter((s) => s.visited_today || pendingFor(s.id).some((q) => q.kind === 'visit')).length,
    due: props.stops.filter((s) => s.due).length,
}));

const switchRoute = (e) => router.visit(`${appBase}/field?route=${e.target.value}`);

// -- The stop sheet ---------------------------------------------------------------

const open = ref(null);
const tab = ref('order');
const busy = ref(false);

const cart = ref({});
const query = ref('');
const pay = ref({ amount: '', method: 'cash' });
const collect = ref({ amount: '', method: 'cash', cheque_number: '', cheque_bank: '', cheque_date: '', note: '' });
const visit = ref({ outcome: 'order_taken', notes: '' });

const openStop = (s) => {
    open.value = s;
    tab.value = 'order';
    cart.value = {};
    query.value = '';
    pay.value = { amount: '', method: 'cash' };
    collect.value = { amount: '', method: 'cash', cheque_number: '', cheque_bank: '', cheque_date: '', note: '' };
    visit.value = { outcome: 'order_taken', notes: '' };
};

const matches = computed(() => {
    const q = query.value.trim().toLowerCase();
    const inCart = props.products.filter((p) => cart.value[p.variation_id]);
    const rest = props.products.filter((p) => !cart.value[p.variation_id] && (!q || p.name.toLowerCase().includes(q) || p.sku?.toLowerCase().includes(q)));
    return [...inCart, ...rest.slice(0, q ? 40 : 15)];
});

const bump = (p, d) => {
    const next = Math.max(0, (cart.value[p.variation_id] ?? 0) + d);
    const c = { ...cart.value };
    if (next) c[p.variation_id] = next;
    else delete c[p.variation_id];
    cart.value = c;
};

const cartTotal = computed(() => props.products.reduce((s, p) => s + (cart.value[p.variation_id] ?? 0) * p.price, 0));
const cartCount = computed(() => Object.values(cart.value).reduce((s, n) => s + n, 0));

const where = async () => {
    try {
        return await currentPosition({ timeout: 8000 });
    } catch {
        return null;
    }
};

const saveOrder = async () => {
    if (!cartCount.value || !props.location) return;
    busy.value = true;
    const pos = await where();
    await enqueue(
        'order',
        {
            contact_id: open.value.id,
            location_id: props.location.id,
            lines: Object.entries(cart.value).map(([variation_id, quantity]) => ({ variation_id: Number(variation_id), quantity })),
            payment: { amount: Number(pay.value.amount) || 0, method: pay.value.method },
            ...(pos ?? {}),
        },
        `Order · ${open.value.name} · ${cartCount.value} items`,
    );
    busy.value = false;
    open.value = null;
};

const saveCollect = async () => {
    if (!(Number(collect.value.amount) > 0)) return;
    if (collect.value.method === 'cheque' && (!collect.value.cheque_number || !collect.value.cheque_date)) {
        say('Enter the cheque number and date.', 'bad');
        return;
    }
    await enqueue('collect', { contact_id: open.value.id, ...collect.value, amount: Number(collect.value.amount) }, `Collection · ${open.value.name}`);
    open.value = null;
};

const saveVisit = async () => {
    busy.value = true;
    const pos = await where();
    await enqueue('visit', { contact_id: open.value.id, ...visit.value, ...(pos ?? {}) }, `Visit · ${open.value.name}`);
    busy.value = false;
    open.value = null;
};

const mapsUrl = (s) =>
    s.lat !== null ? `https://www.google.com/maps/dir/?api=1&destination=${s.lat},${s.lng}` : s.address ? `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(s.address)}` : null;

const showQueue = ref(false);
const failed = computed(() => queue.value.filter((q) => q.status === 'failed'));

const time = (iso) => (iso ? new Date(iso).toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' }) : '');
const input = 'w-full rounded-lg border border-edge-subtle bg-surface-raised px-3 py-2.5 text-base text-content-primary focus:border-accent-500 focus:outline-none';
</script>

<template>
    <Head title="Field">
        <link rel="manifest" :href="`${appBase}/field.webmanifest`" />
        <meta name="theme-color" content="#0b2a3b" />
        <meta name="mobile-web-app-capable" content="yes" />
    </Head>

    <div class="min-h-screen bg-surface-page pb-24 text-content-primary">
        <!-- Status bar -->
        <header class="sticky top-0 z-20 bg-nav-bg px-4 pb-3 pt-3 text-white shadow-overlay">
            <div class="flex items-center gap-2">
                <a :href="`${appBase}/today`" class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-white/10" aria-label="Back to the office screens">
                    <Icon name="home" :size="18" />
                </a>
                <select v-if="routes.length > 1" :value="routeId" class="min-w-0 flex-1 truncate rounded-lg bg-white/10 px-2 py-2 text-sm font-semibold text-white focus:outline-none" @change="switchRoute">
                    <option v-for="r in routes" :key="r.id" :value="r.id" class="text-neutral-900">{{ r.name }}</option>
                </select>
                <p v-else class="min-w-0 flex-1 truncate text-base font-semibold">{{ routes[0]?.name ?? 'No route' }}</p>
                <button type="button" class="flex shrink-0 items-center gap-1.5 rounded-lg px-2.5 py-2 text-xs font-medium"
                    :class="!online ? 'bg-danger/80' : queue.length ? 'bg-accent-500 text-brand-950' : 'bg-white/10'" @click="showQueue = !showQueue">
                    <Icon :name="online ? 'check' : 'wifiOff'" :size="14" />
                    <template v-if="!online">Offline · {{ queue.length }}</template>
                    <template v-else-if="queue.length">{{ syncing ? 'Sending…' : `${queue.length} waiting` }}</template>
                    <template v-else>Synced {{ time(lastSync) }}</template>
                </button>
            </div>
            <p class="mt-1.5 text-xs text-white/70">
                {{ counts.visited }} of {{ stops.length }} visited · {{ counts.due }} due an order
                <template v-if="location"> · selling from {{ location.name }}</template>
            </p>
        </header>

        <p v-if="notice" class="sticky top-[84px] z-10 mx-3 mt-2 rounded-lg px-3 py-2 text-sm shadow-raised" :class="notice.tone === 'bad' ? 'bg-danger text-white' : 'bg-surface-inverse text-content-inverse'">
            {{ notice.text }}
        </p>

        <!-- Waiting / failed actions -->
        <section v-if="showQueue || failed.length" class="mx-3 mt-3 overflow-hidden rounded-xl border border-edge-subtle bg-surface-raised">
            <p class="border-b border-edge-subtle px-4 py-2 text-xs font-semibold uppercase tracking-wider text-content-muted">On this phone, not yet sent</p>
            <ul class="divide-y divide-edge-subtle">
                <li v-for="q in queue" :key="q.client_id" class="px-4 py-3">
                    <div class="flex items-start gap-2">
                        <span class="mt-1 h-2 w-2 shrink-0 rounded-full" :class="q.status === 'failed' ? 'bg-danger' : 'bg-accent-500'"></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm text-content-primary">{{ q.label }}</p>
                            <p class="text-xs text-content-muted">{{ time(q.happened_at) }}<template v-if="q.message"> · <span class="text-danger">{{ q.message }}</span></template></p>
                        </div>
                        <button type="button" class="text-xs text-content-muted" @click="discard(q)">Delete</button>
                    </div>
                    <div v-if="q.status === 'failed'" class="mt-2 flex gap-2 pl-4">
                        <input v-if="q.needs_reason" v-model="q.reason" type="text" placeholder="Reason, e.g. order by phone" class="min-w-0 flex-1 rounded-md border border-edge-subtle bg-surface-page px-2 py-1.5 text-sm text-content-primary" />
                        <button type="button" class="rounded-md bg-brand-600 px-3 py-1.5 text-sm font-semibold text-white" @click="retry(q)">Retry</button>
                    </div>
                </li>
                <li v-if="!queue.length" class="px-4 py-4 text-sm text-content-muted">Nothing waiting. Everything is on the server.</li>
            </ul>
        </section>

        <p v-if="!location" class="mx-3 mt-3 rounded-lg bg-warning/10 px-3 py-2 text-sm text-warning">
            No selling location for this route. Ask the office to set up your van or give you a location.
        </p>

        <!-- Filter -->
        <div class="mx-3 mt-3 flex gap-1.5">
            <button v-for="[key, label] in [['all', 'All stops'], ['todo', 'Not visited'], ['due', 'Due']]" :key="key" type="button"
                class="rounded-full px-3 py-1.5 text-sm font-medium" :class="filter === key ? 'bg-brand-600 text-white' : 'bg-surface-raised text-content-secondary ring-1 ring-edge-subtle'"
                @click="filter = key">{{ label }}</button>
        </div>

        <!-- Stops -->
        <ol class="mx-3 mt-3 space-y-2">
            <li v-for="(s, i) in shown" :key="s.id">
                <button type="button" class="flex w-full items-start gap-3 rounded-xl border border-edge-subtle bg-surface-raised p-3.5 text-left active:bg-surface-sunken" @click="openStop(s)">
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full text-sm font-semibold"
                        :class="s.visited_today ? 'bg-success text-white' : s.due ? 'bg-accent-500 text-brand-950' : 'bg-surface-sunken text-content-secondary'">
                        {{ s.sequence ?? i + 1 }}
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-base font-semibold text-content-primary">{{ s.name }}</span>
                        <span v-if="s.address" class="block truncate text-xs text-content-muted">{{ s.address }}</span>
                        <span class="mt-1.5 flex flex-wrap gap-1">
                            <span v-if="s.on_hold" class="rounded-full bg-danger/10 px-2 py-0.5 text-[11px] font-semibold text-danger">credit hold</span>
                            <span v-if="s.due" class="rounded-full bg-accent-500/15 px-2 py-0.5 text-[11px] font-semibold text-accent-700 dark:text-accent-300">due</span>
                            <span v-if="s.visited_today" class="rounded-full bg-success/10 px-2 py-0.5 text-[11px] font-semibold text-success">visited</span>
                            <span v-if="s.outstanding > 0" class="rounded-full bg-surface-sunken px-2 py-0.5 text-[11px] text-content-secondary">owes <Money :value="s.outstanding" compact /></span>
                            <span v-for="q in pendingFor(s.id)" :key="q.client_id" class="rounded-full px-2 py-0.5 text-[11px] font-semibold"
                                :class="q.status === 'failed' ? 'bg-danger/10 text-danger' : 'bg-info/10 text-info'">{{ q.kind }} {{ q.status === 'failed' ? 'failed' : 'waiting' }}</span>
                        </span>
                    </span>
                    <Icon name="chevronRight" :size="18" class="mt-2 shrink-0 text-content-muted" />
                </button>
            </li>
            <li v-if="!shown.length" class="rounded-xl border border-dashed border-edge-strong px-4 py-10 text-center text-sm text-content-muted">
                {{ stops.length ? 'Nothing in this filter.' : 'No outlets on this route yet.' }}
            </li>
        </ol>

        <!-- Stop sheet -->
        <div v-if="open" class="fixed inset-0 z-30 bg-neutral-950/50" @click.self="open = null">
            <section class="absolute inset-x-0 bottom-0 flex max-h-[88vh] flex-col rounded-t-2xl bg-surface-page shadow-overlay">
                <header class="shrink-0 border-b border-edge-subtle px-4 pb-3 pt-2">
                    <div class="mx-auto mb-2 h-1 w-10 rounded-full bg-edge-strong"></div>
                    <div class="flex items-start gap-2">
                        <div class="min-w-0 flex-1">
                            <h2 class="truncate text-lg font-semibold text-content-primary">{{ open.name }}</h2>
                            <p class="text-xs text-content-muted">
                                <template v-if="open.outstanding > 0">Owes <Money :value="open.outstanding" compact /></template>
                                <template v-else>Nothing owed</template>
                                <template v-if="open.days_since_order !== null"> · last order {{ open.days_since_order }}d ago</template>
                            </p>
                        </div>
                        <a v-if="open.mobile" :href="`tel:${open.mobile}`" class="grid h-10 w-10 place-items-center rounded-full bg-surface-raised ring-1 ring-edge-subtle" aria-label="Call"><Icon name="phone" :size="18" /></a>
                        <a v-if="mapsUrl(open)" :href="mapsUrl(open)" target="_blank" rel="noopener" class="grid h-10 w-10 place-items-center rounded-full bg-surface-raised ring-1 ring-edge-subtle" aria-label="Directions"><Icon name="map" :size="18" /></a>
                        <button type="button" class="grid h-10 w-10 place-items-center rounded-full" aria-label="Close" @click="open = null"><Icon name="close" :size="18" /></button>
                    </div>
                    <p v-if="open.on_hold" class="mt-2 rounded-lg bg-danger/10 px-3 py-2 text-sm text-danger">On credit hold. Orders need full payment or a manager's override.</p>
                    <div class="mt-3 grid grid-cols-3 gap-1 rounded-xl bg-surface-sunken p-1">
                        <button v-for="[key, label] in [['order', 'Order'], ['collect', 'Collect'], ['visit', 'Visit']]" :key="key" type="button"
                            class="rounded-lg py-2 text-sm font-semibold" :class="tab === key ? 'bg-surface-raised text-content-primary shadow-raised' : 'text-content-secondary'" @click="tab = key">{{ label }}</button>
                    </div>
                </header>

                <div class="min-h-0 flex-1 overflow-y-auto px-4 py-3">
                    <!-- Order -->
                    <div v-if="tab === 'order'">
                        <input v-model="query" type="search" placeholder="Search products" :class="input" />
                        <ul class="mt-2 divide-y divide-edge-subtle">
                            <li v-for="p in matches" :key="p.variation_id" class="flex items-center gap-3 py-2.5">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-content-primary">{{ p.name }}</p>
                                    <p class="text-xs text-content-muted"><Money :value="p.price" /><template v-if="p.stock !== null"> · {{ p.stock }} on hand</template></p>
                                </div>
                                <div class="flex shrink-0 items-center gap-1">
                                    <button type="button" class="grid h-9 w-9 place-items-center rounded-full bg-surface-sunken text-lg" :aria-label="`Fewer ${p.name}`" @click="bump(p, -1)">−</button>
                                    <span class="w-8 text-center text-base font-semibold numeric">{{ cart[p.variation_id] ?? 0 }}</span>
                                    <button type="button" class="grid h-9 w-9 place-items-center rounded-full bg-brand-600 text-lg text-white" :aria-label="`More ${p.name}`" @click="bump(p, 1)">+</button>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Collect -->
                    <div v-else-if="tab === 'collect'" class="space-y-3">
                        <input v-model="collect.amount" type="number" inputmode="decimal" min="0" placeholder="Amount received" :class="[input, 'text-lg font-semibold']" />
                        <div class="grid grid-cols-3 gap-1.5">
                            <button v-for="m in methods" :key="m.value" type="button" class="rounded-lg border py-2 text-sm font-medium"
                                :class="collect.method === m.value ? 'border-brand-600 bg-brand-600 text-white' : 'border-edge-subtle bg-surface-raised text-content-secondary'" @click="collect.method = m.value">{{ m.label }}</button>
                        </div>
                        <template v-if="collect.method === 'cheque'">
                            <input v-model="collect.cheque_number" type="text" placeholder="Cheque number" :class="input" />
                            <div class="grid grid-cols-2 gap-2">
                                <input v-model="collect.cheque_bank" type="text" placeholder="Bank" :class="input" />
                                <input v-model="collect.cheque_date" type="date" :class="input" />
                            </div>
                        </template>
                        <input v-model="collect.note" type="text" placeholder="Note (optional)" :class="input" />
                    </div>

                    <!-- Visit -->
                    <div v-else class="space-y-3">
                        <div class="grid grid-cols-2 gap-1.5">
                            <button v-for="o in outcomes" :key="o.value" type="button" class="rounded-lg border px-2 py-2.5 text-left text-sm"
                                :class="visit.outcome === o.value ? 'border-brand-600 bg-brand-600/10 font-semibold text-content-primary' : 'border-edge-subtle bg-surface-raised text-content-secondary'"
                                @click="visit.outcome = o.value">{{ o.label }}</button>
                        </div>
                        <textarea v-model="visit.notes" rows="3" placeholder="Notes (optional)" :class="input"></textarea>
                        <p class="text-xs text-content-muted">Your location is recorded with the visit.</p>
                    </div>
                </div>

                <footer class="shrink-0 border-t border-edge-subtle bg-surface-raised px-4 py-3">
                    <template v-if="tab === 'order'">
                        <div v-if="cartCount" class="mb-2 flex items-center gap-2">
                            <input v-model="pay.amount" type="number" inputmode="decimal" min="0" placeholder="Paid now" class="min-w-0 flex-1 rounded-lg border border-edge-subtle bg-surface-page px-3 py-2 text-base text-content-primary" />
                            <select v-model="pay.method" class="rounded-lg border border-edge-subtle bg-surface-page px-2 py-2 text-sm text-content-primary">
                                <option v-for="m in methods" :key="m.value" :value="m.value">{{ m.label }}</option>
                            </select>
                            <button type="button" class="rounded-lg px-2 py-2 text-xs font-medium text-brand-600 dark:text-brand-300" @click="pay.amount = cartTotal.toFixed(2)">Full</button>
                        </div>
                        <button type="button" :disabled="!cartCount || busy || !location" class="w-full rounded-xl bg-accent-500 py-3.5 text-base font-semibold text-brand-950 disabled:opacity-40" @click="saveOrder">
                            {{ cartCount ? `Save order · ${cartCount} items · ` : 'Add products to the order' }}<Money v-if="cartCount" :value="cartTotal" compact />
                        </button>
                    </template>
                    <button v-else-if="tab === 'collect'" type="button" :disabled="!(Number(collect.amount) > 0)" class="w-full rounded-xl bg-accent-500 py-3.5 text-base font-semibold text-brand-950 disabled:opacity-40" @click="saveCollect">
                        Save collection
                    </button>
                    <button v-else type="button" :disabled="busy" class="w-full rounded-xl bg-accent-500 py-3.5 text-base font-semibold text-brand-950 disabled:opacity-40" @click="saveVisit">
                        {{ busy ? 'Getting location…' : 'Log visit' }}
                    </button>
                </footer>
            </section>
        </div>
    </div>
</template>
