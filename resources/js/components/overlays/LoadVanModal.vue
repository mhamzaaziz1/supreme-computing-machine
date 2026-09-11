<script setup>
/**
 * Load van: a stock transfer from a warehouse to the van, pre-filled with
 * the van's previous load so a regular route is one check-and-confirm.
 */
import { computed, onMounted, ref, watch } from 'vue';
import Modal from '../ui/Modal.vue';
import Icon from '../Icon.vue';
import { api } from '../../overlays/api';
import { toast } from '../../overlays/toast';

const props = defineProps({
    vanId: { type: Number, required: true },
    onDone: { type: Function, default: null },
});

const emit = defineEmits(['close']);

const data = ref(null);
const from = ref(null);
const lines = ref([]);
const note = ref('');
const busy = ref(false);
const error = ref(null);

const query = ref('');
const hits = ref([]);
let timer;

const appBase = () => (document.querySelector('meta[name="app-base"]')?.content ?? window.location.origin).replace(/\/$/, '');

const fetchForm = async (fromId = null, keepLines = false) => {
    error.value = null;
    try {
        const r = await api(`vans/${props.vanId}/load`, { query: { from: fromId } });
        data.value = r;
        from.value = r.from;
        if (!keepLines) {
            lines.value = r.suggested.map((s) => ({ ...s }));
        } else {
            const avail = Object.fromEntries(r.suggested.map((s) => [s.variation_id, s.available]));
            lines.value.forEach((l) => (l.available = avail[l.variation_id] ?? l.available));
        }
    } catch (e) {
        error.value = e.message;
    }
};

onMounted(() => fetchForm());

watch(from, (v, old) => {
    if (old !== null && v !== old) fetchForm(v, true);
});

const search = () => {
    clearTimeout(timer);
    const term = query.value.trim();
    if (!term) {
        hits.value = [];
        return;
    }
    timer = setTimeout(async () => {
        try {
            const url = new URL(`${appBase()}/products/list`);
            url.searchParams.set('term', term);
            url.searchParams.set('location_id', from.value);
            const res = await fetch(url, { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
            hits.value = (await res.json()).filter((h) => Number(h.enable_stock));
        } catch {
            hits.value = [];
        }
    }, 250);
};

const add = (h) => {
    const existing = lines.value.find((l) => l.variation_id === h.variation_id);
    if (existing) existing.quantity += 1;
    else lines.value.push({ variation_id: h.variation_id, name: h.name, sku: h.sub_sku, unit: h.unit, available: Number(h.qty_available) || 0, quantity: 1 });
    query.value = '';
    hits.value = [];
};

const short = (l) => Number(l.quantity) > Number(l.available) + 0.0001;
const valid = computed(() => lines.value.some((l) => Number(l.quantity) > 0) && !lines.value.some(short));
const units = computed(() => lines.value.reduce((s, l) => s + (Number(l.quantity) || 0), 0));

const submit = async () => {
    if (!valid.value || busy.value) return;
    busy.value = true;
    error.value = null;
    try {
        const r = await api(`vans/${props.vanId}/load`, {
            method: 'POST',
            body: {
                from_location_id: from.value,
                lines: lines.value.filter((l) => Number(l.quantity) > 0).map((l) => ({ variation_id: l.variation_id, quantity: Number(l.quantity) })),
                note: note.value || null,
            },
        });
        toast(r.message);
        props.onDone?.(r);
        emit('close');
    } catch (e) {
        error.value = e.message;
    } finally {
        busy.value = false;
    }
};

const input = 'rounded-md border border-edge-subtle bg-surface-page px-2.5 py-1.5 text-sm text-content-primary focus:border-accent-500 focus:outline-none';
</script>

<template>
    <Modal :title="data ? `Load ${data.van.plate}` : 'Load van'" eyebrow="Van stock" width="lg" :dirty="lines.length > 0" :busy="busy" @close="emit('close')">
        <div v-if="!data && !error" class="h-48 animate-pulse rounded-lg bg-surface-sunken"></div>

        <div v-if="data" class="space-y-3">
            <div class="flex flex-wrap items-center gap-3 text-[13px] text-content-secondary">
                <label class="flex items-center gap-2">
                    From
                    <select v-model.number="from" :class="input">
                        <option v-for="w in data.warehouses" :key="w.id" :value="w.id">{{ w.name }}</option>
                    </select>
                </label>
                <span v-if="data.van.sellers.length">· for {{ data.van.sellers.join(', ') }}</span>
                <span v-if="data.on_van.length" class="ml-auto">Already on board: {{ data.on_van.reduce((s, x) => s + x.qty, 0) }} units</span>
            </div>

            <div class="relative">
                <Icon name="search" :size="15" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-content-muted" />
                <input v-model="query" type="search" placeholder="Add a product by name or SKU" :class="[input, 'w-full pl-9']" @input="search" />
                <ul v-if="hits.length" class="absolute left-0 right-0 z-10 mt-1 max-h-60 overflow-y-auto rounded-lg border border-edge-subtle bg-surface-raised shadow-overlay">
                    <li v-for="h in hits" :key="h.variation_id">
                        <button type="button" class="flex w-full items-center justify-between gap-3 px-3 py-2 text-left text-[13px] hover:bg-surface-sunken" @click="add(h)">
                            <span class="text-content-primary">{{ h.name }} <span class="text-content-muted">{{ h.sub_sku }}</span></span>
                            <span class="shrink-0 text-content-muted">{{ Number(h.qty_available) }} at warehouse</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="max-h-[42vh] overflow-y-auto rounded-lg border border-edge-subtle">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 bg-surface-sunken">
                        <tr class="text-left text-xs uppercase tracking-wide text-content-muted">
                            <th class="px-3 py-2 font-medium">Product</th>
                            <th class="px-3 py-2 text-right font-medium">At warehouse</th>
                            <th class="w-32 px-3 py-2 font-medium">Load</th>
                            <th class="w-8"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(l, i) in lines" :key="l.variation_id" class="border-t border-edge-subtle">
                            <td class="px-3 py-1.5 text-content-primary">{{ l.name }} <span class="text-xs text-content-muted">{{ l.sku }}</span></td>
                            <td class="px-3 py-1.5 text-right numeric" :class="short(l) ? 'font-medium text-danger' : 'text-content-muted'">{{ l.available }}</td>
                            <td class="px-3 py-1.5"><input v-model.number="l.quantity" type="number" min="0" step="any" :class="[input, 'w-24']" /></td>
                            <td class="pr-2 text-right">
                                <button type="button" class="rounded p-1 text-content-muted hover:text-danger" :aria-label="`Remove ${l.name}`" @click="lines.splice(i, 1)">
                                    <Icon name="close" :size="14" />
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!lines.length">
                            <td colspan="4" class="px-3 py-8 text-center text-[13px] text-content-muted">Search above to add products to this load.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p v-if="lines.some(short)" class="text-[13px] text-danger">Some lines ask for more than the warehouse holds.</p>
            <input v-model="note" type="text" maxlength="190" placeholder="Note (optional)" :class="[input, 'w-full']" />
        </div>

        <p v-if="error" class="mt-3 rounded-md bg-danger/10 px-3 py-2 text-[13px] text-danger">{{ error }}</p>

        <template #footer>
            <span class="mr-auto text-[13px] text-content-muted">{{ units.toLocaleString() }} units</span>
            <button type="button" class="rounded-md px-3 py-2 text-sm font-medium text-content-secondary hover:bg-surface-sunken" :disabled="busy" @click="emit('close')">Cancel</button>
            <button type="button" :disabled="!valid || busy" class="rounded-md bg-accent-500 px-4 py-2 text-sm font-semibold text-brand-950 hover:bg-accent-400 disabled:opacity-50" @click="submit">
                {{ busy ? 'Loading…' : 'Load van' }}
            </button>
        </template>
    </Modal>
</template>
