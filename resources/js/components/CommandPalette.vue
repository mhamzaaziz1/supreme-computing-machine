<script setup>
/**
 * Ctrl/Cmd-K palette: screens, records and verbs.
 *
 * The real cost of the old navigation was traversal: five clicks through
 * three menus to reach a screen you visit twenty times a day. This makes
 * every destination one keystroke away, and goes further than navigation:
 *
 *   - records: outlets, invoices, products, vehicles, vans and cheques,
 *     each opening the drawer that belongs to it
 *   - verbs: "collect madina", "sell madina", "visit madina",
 *     "statement madina", "load aa", "settle aa" open the task directly
 *   - actions: the drawers that have no page of their own (vans, cheques,
 *     oil changes due, schemes…)
 */
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Icon from './Icon.vue';
import Money from './Money.vue';
import { api } from '../overlays/api';
import { openOverlay } from '../overlays/store';

const page = usePage();
const open = ref(false);
const query = ref('');
const cursor = ref(0);
const input = ref(null);
const listEl = ref(null);
const records = ref([]);
const searching = ref(false);

const appBase = () => (document.querySelector('meta[name="app-base"]')?.content ?? window.location.origin).replace(/\/$/, '');

/** Verb → which records it acts on. */
const VERBS = {
    sell: 'outlets',
    collect: 'outlets',
    visit: 'outlets',
    statement: 'outlets',
    load: 'vans',
    settle: 'vans',
};

const VERB_LABEL = { sell: 'Sell to', collect: 'Collect from', visit: 'Log a visit at', statement: 'Statement for', load: 'Load', settle: 'Settle' };

const parsed = computed(() => {
    const q = query.value.trim();
    const m = q.match(/^(\w+)(?:\s+(.*))?$/);
    const verb = m?.[1]?.toLowerCase();
    if (verb && VERBS[verb] && (m[2] !== undefined || VERBS[verb] === 'vans')) {
        return { verb, term: (m[2] ?? '').trim() };
    }
    return { verb: null, term: q };
});

/** Drawers that have no page of their own. */
const actions = [
    { label: 'Vans — load and settle', keywords: 'van vans load settle stock truck unload', icon: 'truck', run: () => openOverlay('vans') },
    { label: 'Cheques in hand', keywords: 'cheque cheques pdc deposit bounce post dated', icon: 'cheque', run: () => openOverlay('cheques') },
    { label: 'Oil changes due', keywords: 'oil change due reminder service vehicle bay', icon: 'oil', run: () => openOverlay('serviceDue') },
    { label: 'Trade schemes', keywords: 'scheme schemes promotion discount slab free rebate target', icon: 'target', run: () => openOverlay('schemes') },
    { label: 'Cost to serve by route', keywords: 'route margin profit cost serve van economics', icon: 'insight', run: () => openOverlay('routeEconomics') },
    { label: 'Principal sales file & targets', keywords: 'principal secondary sales export csv target litres', icon: 'download', run: () => openOverlay('principal') },
];

/** Flatten the nav tree into one searchable list, keeping the group name. */
const destinations = computed(() => {
    const out = [];
    const go = (item) => () => (item.spa ? router.visit(item.url) : (window.location.href = item.url));

    for (const group of page.props.nav ?? []) {
        if (group.url) out.push({ key: group.url, label: group.label, group: null, icon: group.icon, run: go(group) });
        for (const item of group.items) {
            out.push({ key: item.url + item.label, label: item.label, group: group.label, icon: group.icon, run: go(item) });
        }
    }
    for (const item of page.props.settingsNav ?? []) {
        out.push({ key: item.url, label: item.label, group: 'Settings', icon: 'settings', run: go(item) });
    }

    return out;
});

/**
 * Subsequence match, so "prodl" finds "Product List" and "vexp" finds
 * "Vehicle Expenses". Scores earlier and more contiguous matches higher.
 */
const score = (text, q) => {
    const haystack = text.toLowerCase();
    let i = 0;
    let points = 0;
    let streak = 0;

    for (const ch of q) {
        const at = haystack.indexOf(ch, i);
        if (at === -1) return -1;
        streak = at === i ? streak + 1 : 0;
        points += streak * 2 + (at === 0 ? 5 : 0) - Math.min(at - i, 4);
        i = at + 1;
    }

    // Shorter labels win ties: "Brands" should beat "Brands Import Template".
    return points - haystack.length * 0.05;
};

// -- records ------------------------------------------------------------------

const TYPE = {
    outlet: { label: 'Outlet', icon: 'customers' },
    invoice: { label: 'Invoice', icon: 'receipt' },
    product: { label: 'Product', icon: 'stock' },
    vehicle: { label: 'Vehicle', icon: 'truck' },
    van: { label: 'Van', icon: 'truck' },
    cheque: { label: 'Cheque', icon: 'cheque' },
};

const statementUrl = (id) => {
    const end = new Date();
    const start = new Date(Date.now() - 90 * 864e5);
    const d = (x) => x.toISOString().slice(0, 10);
    return `${appBase()}/contacts/ledger?contact_id=${id}&start_date=${d(start)}&end_date=${d(end)}&format=format_1&action=pdf`;
};

const runRecord = (r, verb) => {
    if (r.type === 'outlet') {
        if (verb === 'sell') return (window.location.href = `${appBase()}/sales/create?contact_id=${r.id}`);
        if (verb === 'collect') return openOverlay('collect', { contactId: r.id });
        if (verb === 'visit') return openOverlay('visit', { contactId: r.id });
        if (verb === 'statement') return window.open(statementUrl(r.id), '_blank', 'noopener');
        return openOverlay('outlet', { id: r.id });
    }
    if (r.type === 'invoice') return openOverlay('invoice', { id: r.id });
    if (r.type === 'product') return (window.location.href = r.url);
    if (r.type === 'vehicle') return openOverlay('vehicle', { id: r.id });
    if (r.type === 'van') return openOverlay(verb === 'settle' ? 'settleVan' : 'loadVan', { vanId: r.id });
    if (r.type === 'cheque') return openOverlay('cheques');
};

let timer;
let controller;
watch(parsed, ({ verb, term }) => {
    clearTimeout(timer);
    controller?.abort();
    const only = verb ? VERBS[verb] : null;
    if (term.length < 2 && only !== 'vans') {
        records.value = [];
        searching.value = false;
        return;
    }
    searching.value = true;
    timer = setTimeout(async () => {
        controller = new AbortController();
        try {
            const r = await api('search', { query: { q: term, only }, signal: controller.signal });
            records.value = r.results;
        } catch (e) {
            if (e.name !== 'AbortError') records.value = [];
        } finally {
            searching.value = false;
        }
    }, 180);
});

// -- the list -------------------------------------------------------------------

const results = computed(() => {
    const { verb, term } = parsed.value;

    const recordItems = records.value.map((r) => ({
        key: `${r.type}-${r.id}`,
        label: verb ? `${VERB_LABEL[verb]} ${r.label}` : r.label,
        sub: r.sublabel,
        group: TYPE[r.type]?.label,
        icon: TYPE[r.type]?.icon ?? 'search',
        amount: r.amount,
        run: () => runRecord(r, verb),
    }));

    if (verb) return recordItems;

    const q = term.toLowerCase();
    if (!q) return [...actions.map((a) => ({ ...a, key: a.label, group: 'Action' })), ...destinations.value.slice(0, 6)];

    const ranked = [...actions.map((a) => ({ ...a, key: a.label, group: 'Action', haystack: `${a.label} ${a.keywords}` })), ...destinations.value]
        .map((d) => ({ d, s: Math.max(score(d.haystack ?? d.label, q), score(`${d.group ?? ''} ${d.label}`, q) - 2) }))
        .filter((r) => r.s > -1)
        .sort((a, b) => b.s - a.s)
        .slice(0, 10)
        .map((r) => r.d);

    return [...recordItems, ...ranked];
});

watch(query, () => (cursor.value = 0));
watch(cursor, async () => {
    await nextTick();
    listEl.value?.querySelector('[data-active]')?.scrollIntoView({ block: 'nearest' });
});

const show = async () => {
    open.value = true;
    query.value = '';
    records.value = [];
    cursor.value = 0;
    await nextTick();
    input.value?.focus();
};

const hide = () => (open.value = false);

const go = (item) => {
    if (!item) return;
    hide();
    item.run();
};

const onKeydown = (e) => {
    const combo = (e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k';

    if (combo) {
        e.preventDefault();
        open.value ? hide() : show();
        return;
    }

    if (!open.value) return;

    if (e.key === 'Escape') {
        e.preventDefault();
        hide();
    } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        cursor.value = (cursor.value + 1) % Math.max(results.value.length, 1);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        cursor.value = (cursor.value - 1 + results.value.length) % Math.max(results.value.length, 1);
    } else if (e.key === 'Enter') {
        e.preventDefault();
        go(results.value[cursor.value]);
    }
};

onMounted(() => window.addEventListener('keydown', onKeydown));
onUnmounted(() => window.removeEventListener('keydown', onKeydown));

defineExpose({ show });
</script>

<template>
    <div>
        <!-- Global, so the shortcut works from any page including Blade-rendered ones. -->
        <teleport to="body">
            <div
                v-if="open"
                class="fixed inset-0 z-50 flex items-start justify-center bg-neutral-950/50 px-4 pt-[12vh] backdrop-blur-[2px]"
                @click.self="hide"
            >
                <div
                    class="w-full max-w-xl overflow-hidden rounded-xl bg-surface-raised shadow-overlay ring-1 ring-edge-subtle"
                    role="dialog"
                    aria-modal="true"
                    aria-label="Search"
                >
                    <div class="flex items-center gap-3 border-b border-edge-subtle px-4">
                        <Icon name="search" :size="18" class="shrink-0 text-content-muted" />
                        <input
                            ref="input"
                            v-model="query"
                            type="text"
                            placeholder="Find a screen, outlet or invoice — or type collect, sell, visit, load…"
                            class="w-full bg-transparent py-3.5 text-[15px] text-content-primary placeholder:text-content-muted focus:outline-none"
                            autocomplete="off"
                            spellcheck="false"
                        />
                        <span v-if="searching" class="h-2 w-2 shrink-0 animate-pulse rounded-full bg-accent-500" aria-label="Searching"></span>
                        <kbd
                            class="shrink-0 rounded border border-edge-subtle px-1.5 py-0.5 text-[10px] font-medium text-content-muted"
                        >
                            ESC
                        </kbd>
                    </div>

                    <p v-if="parsed.verb" class="border-b border-edge-subtle bg-surface-page px-4 py-1.5 text-xs text-content-muted">
                        {{ VERB_LABEL[parsed.verb] }}… {{ parsed.term ? '' : VERBS[parsed.verb] === 'vans' ? 'pick a van' : 'keep typing a name' }}
                    </p>

                    <ul
                        v-if="results.length"
                        ref="listEl"
                        class="scrollbar-slim max-h-96 overflow-y-auto p-1.5"
                    >
                        <li v-for="(r, i) in results" :key="r.key">
                            <button
                                type="button"
                                :data-active="i === cursor ? '' : null"
                                class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left transition-colors"
                                :class="
                                    i === cursor
                                        ? 'bg-brand-600 text-white'
                                        : 'text-content-primary hover:bg-surface-sunken'
                                "
                                @click="go(r)"
                                @mousemove="cursor = i"
                            >
                                <Icon
                                    :name="r.icon"
                                    :size="17"
                                    class="shrink-0"
                                    :class="i === cursor ? 'text-white/80' : 'text-content-muted'"
                                />
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm">{{ r.label }}</span>
                                    <span v-if="r.sub" class="block truncate text-xs" :class="i === cursor ? 'text-white/70' : 'text-content-muted'">{{ r.sub }}</span>
                                </span>
                                <Money v-if="r.amount" :value="r.amount" compact class="shrink-0 text-xs" :class="i === cursor ? 'text-white/80' : 'text-content-secondary'" />
                                <span
                                    v-if="r.group"
                                    class="ml-2 shrink-0 truncate text-xs"
                                    :class="i === cursor ? 'text-white/70' : 'text-content-muted'"
                                >
                                    {{ r.group }}
                                </span>
                            </button>
                        </li>
                    </ul>

                    <p v-else-if="!searching" class="px-4 py-8 text-center text-sm text-content-muted">
                        Nothing matches “{{ query }}”.
                    </p>
                </div>
            </div>
        </teleport>
    </div>
</template>
