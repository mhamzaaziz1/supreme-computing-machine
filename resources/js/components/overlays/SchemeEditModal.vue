<script setup>
/**
 * Create or edit a trade scheme. The rules panel changes with the type:
 * buy-N-get-M, quantity slabs, or period target tiers.
 */
import { computed, ref } from 'vue';
import Modal from '../ui/Modal.vue';
import Icon from '../Icon.vue';
import { api } from '../../overlays/api';
import { toast } from '../../overlays/toast';

const props = defineProps({
    scheme: { type: Object, default: null },
    options: { type: Object, required: true },
    onDone: { type: Function, default: null },
});

const emit = defineEmits(['close']);

const today = new Date().toISOString().slice(0, 10);
const s = props.scheme;

const form = ref({
    name: s?.name ?? '',
    type: s?.type ?? 'free_goods',
    starts_on: s?.starts_on?.slice(0, 10) ?? today,
    ends_on: s?.ends_on?.slice(0, 10) ?? '',
    is_active: s?.is_active ?? true,
    scope: {
        category_ids: [...(s?.scope.category_ids ?? [])],
        brand_ids: [...(s?.scope.brand_ids ?? [])],
        variation_ids: [...(s?.scope.variation_ids ?? [])],
    },
    audience: {
        customer_group_ids: [...(s?.audience.customer_group_ids ?? [])],
        route_ids: [...(s?.audience.route_ids ?? [])],
    },
    rules: {
        unit_label: s?.rules.unit_label ?? '',
        buy_qty: s?.rules.buy_qty ?? 10,
        free_qty: s?.rules.free_qty ?? 1,
        slabs: s?.rules.slabs ? s.rules.slabs.map((x) => ({ ...x })) : [{ min_qty: 10, percent: 2 }],
        period: s?.rules.period ?? 'quarter',
        tiers: s?.rules.tiers ? s.rules.tiers.map((x) => ({ ...x })) : [{ name: 'Silver', min_qty: 10, rebate_percent: 1 }, { name: 'Gold', min_qty: 20, rebate_percent: 2 }],
    },
});

const productNames = ref(Object.fromEntries((props.options.variations ?? []).map((v) => [v.id, v.name])));
const query = ref('');
const hits = ref([]);
const busy = ref(false);
const error = ref(null);
let timer;

const appBase = () => (document.querySelector('meta[name="app-base"]')?.content ?? window.location.origin).replace(/\/$/, '');

const search = () => {
    clearTimeout(timer);
    if (!query.value.trim()) return (hits.value = []);
    timer = setTimeout(async () => {
        try {
            const url = new URL(`${appBase()}/products/list`);
            url.searchParams.set('term', query.value.trim());
            const res = await fetch(url, { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
            hits.value = await res.json();
        } catch {
            hits.value = [];
        }
    }, 250);
};

const addProduct = (h) => {
    if (!form.value.scope.variation_ids.includes(h.variation_id)) form.value.scope.variation_ids.push(h.variation_id);
    productNames.value[h.variation_id] = h.name;
    query.value = '';
    hits.value = [];
};

const toggleId = (list, id) => {
    const i = list.indexOf(id);
    i === -1 ? list.push(id) : list.splice(i, 1);
};

const types = [
    ['free_goods', 'Free goods', 'Buy N, get M free'],
    ['slab_discount', 'Slab discount', '% off above a quantity'],
    ['target_rebate', 'Target rebate', 'Tiers over a month or quarter'],
];

const valid = computed(() => form.value.name.trim() && form.value.starts_on);

const save = async () => {
    busy.value = true;
    error.value = null;
    try {
        const f = form.value;
        const body = { ...f, ends_on: f.ends_on || null, rules: { ...f.rules, unit_label: f.rules.unit_label || null } };
        const r = s ? await api(`schemes/${s.id}`, { method: 'PUT', body }) : await api('schemes', { method: 'POST', body });
        toast(r.message);
        props.onDone?.();
        emit('close');
    } catch (e) {
        error.value = e.message;
    } finally {
        busy.value = false;
    }
};

const remove = async () => {
    if (!window.confirm('Delete this scheme?')) return;
    try {
        toast((await api(`schemes/${s.id}`, { method: 'DELETE' })).message);
        props.onDone?.();
        emit('close');
    } catch (e) {
        error.value = e.message;
    }
};

const input = 'rounded-md border border-edge-subtle bg-surface-page px-2.5 py-1.5 text-sm text-content-primary focus:border-accent-500 focus:outline-none';
const label = 'mb-1 block text-xs font-medium uppercase tracking-wide text-content-muted';
const chip = (on) => ['rounded-full border px-2.5 py-0.5 text-xs', on ? 'border-brand-600 bg-brand-600 text-white' : 'border-edge-subtle text-content-secondary hover:border-edge-strong'];
</script>

<template>
    <Modal :title="s ? `Edit ${s.name}` : 'New scheme'" eyebrow="Trade scheme" width="lg" dirty :busy="busy" @close="emit('close')">
        <form id="scheme-form" class="space-y-4" @submit.prevent="save">
            <div class="grid gap-3 sm:grid-cols-[1fr_9rem_9rem]">
                <label class="block"><span :class="label">Name</span><input v-model="form.name" type="text" maxlength="120" placeholder="Q3 20W-50 drum push" :class="[input, 'w-full']" required /></label>
                <label class="block"><span :class="label">Starts</span><input v-model="form.starts_on" type="date" :class="[input, 'w-full']" required /></label>
                <label class="block"><span :class="label">Ends</span><input v-model="form.ends_on" type="date" :class="[input, 'w-full']" /></label>
            </div>

            <div class="grid grid-cols-3 gap-2">
                <button v-for="[key, name, hint] in types" :key="key" type="button" class="rounded-lg border px-3 py-2 text-left"
                    :class="form.type === key ? 'border-brand-600 bg-brand-600/5' : 'border-edge-subtle hover:bg-surface-sunken'" @click="form.type = key">
                    <span class="block text-sm font-semibold text-content-primary">{{ name }}</span>
                    <span class="block text-xs text-content-muted">{{ hint }}</span>
                </button>
            </div>

            <!-- Rules -->
            <div class="rounded-lg border border-edge-subtle bg-surface-page p-3">
                <div v-if="form.type === 'free_goods'" class="flex flex-wrap items-center gap-2 text-sm text-content-primary">
                    Buy <input v-model.number="form.rules.buy_qty" type="number" min="1" :class="[input, 'w-20']" />
                    get <input v-model.number="form.rules.free_qty" type="number" min="1" :class="[input, 'w-20']" /> free,
                    counted in <input v-model="form.rules.unit_label" type="text" placeholder="units" maxlength="20" :class="[input, 'w-28']" />
                    <p class="w-full text-xs text-content-muted">Free units go on the qualifying item bought in the largest quantity.</p>
                </div>

                <div v-else-if="form.type === 'slab_discount'" class="space-y-2">
                    <div v-for="(slab, i) in form.rules.slabs" :key="i" class="flex items-center gap-2 text-sm text-content-primary">
                        From <input v-model.number="slab.min_qty" type="number" min="0" step="any" :class="[input, 'w-24']" /> {{ form.rules.unit_label || 'units' }},
                        <input v-model.number="slab.percent" type="number" min="0" max="100" step="any" :class="[input, 'w-20']" /> % off
                        <button type="button" class="ml-auto text-content-muted hover:text-danger" :aria-label="`Remove slab ${i + 1}`" @click="form.rules.slabs.splice(i, 1)"><Icon name="close" :size="14" /></button>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" class="text-xs font-medium text-brand-600 hover:underline dark:text-brand-300" @click="form.rules.slabs.push({ min_qty: '', percent: '' })">Add slab</button>
                        <input v-model="form.rules.unit_label" type="text" placeholder="unit, e.g. cartons" maxlength="20" :class="[input, 'w-40 text-xs']" />
                    </div>
                </div>

                <div v-else class="space-y-2">
                    <div class="flex items-center gap-2 text-sm text-content-primary">
                        Measured per
                        <select v-model="form.rules.period" :class="input">
                            <option value="month">month</option><option value="quarter">quarter</option><option value="scheme">scheme period</option>
                        </select>
                        in <input v-model="form.rules.unit_label" type="text" placeholder="units" maxlength="20" :class="[input, 'w-28']" />
                    </div>
                    <div v-for="(t, i) in form.rules.tiers" :key="i" class="flex items-center gap-2 text-sm text-content-primary">
                        <input v-model="t.name" type="text" maxlength="30" placeholder="Tier" :class="[input, 'w-28']" />
                        at <input v-model.number="t.min_qty" type="number" min="0" step="any" :class="[input, 'w-24']" />
                        earns <input v-model.number="t.rebate_percent" type="number" min="0" max="100" step="any" :class="[input, 'w-20']" /> % rebate
                        <button type="button" class="ml-auto text-content-muted hover:text-danger" :aria-label="`Remove tier ${t.name}`" @click="form.rules.tiers.splice(i, 1)"><Icon name="close" :size="14" /></button>
                    </div>
                    <button type="button" class="text-xs font-medium text-brand-600 hover:underline dark:text-brand-300" @click="form.rules.tiers.push({ name: '', min_qty: '', rebate_percent: '' })">Add tier</button>
                </div>
            </div>

            <!-- Scope -->
            <div>
                <span :class="label">Products that count <span class="normal-case tracking-normal text-content-muted">(none picked = all products)</span></span>
                <div class="flex flex-wrap gap-1.5">
                    <button v-for="c in options.categories" :key="`c${c.id}`" type="button" :class="chip(form.scope.category_ids.includes(c.id))" @click="toggleId(form.scope.category_ids, c.id)">{{ c.name }}</button>
                    <button v-for="b in options.brands" :key="`b${b.id}`" type="button" :class="chip(form.scope.brand_ids.includes(b.id))" @click="toggleId(form.scope.brand_ids, b.id)">{{ b.name }}</button>
                    <button v-for="id in form.scope.variation_ids" :key="`v${id}`" type="button" :class="chip(true)" @click="toggleId(form.scope.variation_ids, id)">{{ productNames[id] ?? `#${id}` }} ✕</button>
                </div>
                <div class="relative mt-2">
                    <input v-model="query" type="search" placeholder="Add a specific product" :class="[input, 'w-full']" @input="search" />
                    <ul v-if="hits.length" class="absolute left-0 right-0 z-10 mt-1 max-h-48 overflow-y-auto rounded-lg border border-edge-subtle bg-surface-raised shadow-overlay">
                        <li v-for="h in hits" :key="h.variation_id">
                            <button type="button" class="w-full px-3 py-1.5 text-left text-[13px] text-content-primary hover:bg-surface-sunken" @click="addProduct(h)">{{ h.name }} <span class="text-content-muted">{{ h.sub_sku }}</span></button>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Audience -->
            <div v-if="options.groups.length || options.routes.length">
                <span :class="label">Outlets <span class="normal-case tracking-normal text-content-muted">(none picked = all outlets)</span></span>
                <div class="flex flex-wrap gap-1.5">
                    <button v-for="g in options.groups" :key="`g${g.id}`" type="button" :class="chip(form.audience.customer_group_ids.includes(g.id))" @click="toggleId(form.audience.customer_group_ids, g.id)">{{ g.name }}</button>
                    <button v-for="r in options.routes" :key="`r${r.id}`" type="button" :class="chip(form.audience.route_ids.includes(r.id))" @click="toggleId(form.audience.route_ids, r.id)">{{ r.name }}</button>
                </div>
            </div>
        </form>

        <p v-if="error" class="mt-3 rounded-md bg-danger/10 px-3 py-2 text-[13px] text-danger">{{ error }}</p>

        <template #footer>
            <button v-if="s" type="button" class="mr-auto rounded-md px-3 py-2 text-sm font-medium text-danger hover:bg-surface-sunken" @click="remove">Delete</button>
            <label class="mr-auto flex items-center gap-2 text-sm text-content-secondary" :class="s ? 'ml-0' : ''">
                <input v-model="form.is_active" type="checkbox" class="rounded border-edge-strong" /> Running
            </label>
            <button type="button" class="rounded-md px-3 py-2 text-sm font-medium text-content-secondary hover:bg-surface-sunken" :disabled="busy" @click="emit('close')">Cancel</button>
            <button type="submit" form="scheme-form" :disabled="!valid || busy" class="rounded-md bg-accent-500 px-4 py-2 text-sm font-semibold text-brand-950 hover:bg-accent-400 disabled:opacity-50">
                {{ busy ? 'Saving…' : 'Save scheme' }}
            </button>
        </template>
    </Modal>
</template>
