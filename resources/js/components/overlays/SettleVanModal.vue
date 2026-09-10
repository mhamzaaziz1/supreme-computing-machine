<script setup>
/**
 * Settle van: count what came back, hand in the money, see the difference.
 *
 * Counted quantities default to what the system expects, so the store
 * keeper only touches the lines that disagree. Differences never post on
 * their own; they go to the approvals inbox with the count attached.
 */
import { computed, onMounted, ref } from 'vue';
import Modal from '../ui/Modal.vue';
import Money from '../Money.vue';
import { api } from '../../overlays/api';
import { toast } from '../../overlays/toast';

const props = defineProps({
    vanId: { type: Number, required: true },
    onDone: { type: Function, default: null },
});

const emit = defineEmits(['close']);

const data = ref(null);
const error = ref(null);
const busy = ref(false);
const counted = ref({});
const form = ref({ counted_cash: '', counted_cheques: '', unload: true, to_location_id: null, odometer_end: '', notes: '' });

onMounted(async () => {
    try {
        const r = await api(`vans/${props.vanId}/settle`);
        data.value = r;
        counted.value = Object.fromEntries(r.lines.map((l) => [l.variation_id, l.qty]));
        form.value.counted_cash = r.money.cash;
        form.value.counted_cheques = r.money.cheque;
        form.value.to_location_id = r.warehouses[0]?.id ?? null;
        form.value.odometer_end = r.odometer?.end ?? '';
    } catch (e) {
        error.value = e.message;
    }
});

const diff = (l) => Number((Number(counted.value[l.variation_id] ?? 0) - l.qty).toFixed(4));

const totals = computed(() => {
    if (!data.value) return null;
    let short = 0;
    let over = 0;
    for (const l of data.value.lines) {
        const d = diff(l);
        if (d < 0) short += -d * l.cost;
        if (d > 0) over += d * l.cost;
    }
    const cash = Number(form.value.counted_cash || 0) - data.value.money.cash;
    const cheques = Number(form.value.counted_cheques || 0) - data.value.money.cheque;
    return { short, over, cash, cheques, clean: short < 0.005 && over < 0.005 && Math.abs(cash) < 0.005 && Math.abs(cheques) < 0.005 };
});

const submit = async () => {
    busy.value = true;
    error.value = null;
    try {
        const f = form.value;
        const r = await api(`vans/${props.vanId}/settle`, {
            method: 'POST',
            body: {
                date: data.value.date,
                lines: data.value.lines.map((l) => ({ variation_id: l.variation_id, counted: Number(counted.value[l.variation_id] ?? 0) })),
                counted_cash: Number(f.counted_cash || 0),
                counted_cheques: Number(f.counted_cheques || 0),
                unload: !!f.unload,
                to_location_id: f.unload ? f.to_location_id : null,
                odometer_end: f.odometer_end === '' ? null : Number(f.odometer_end),
                notes: f.notes || null,
            },
        });
        toast(r.message, { tone: r.status === 'settled' ? 'success' : 'info', action: { label: 'Print slip', href: r.slip_url }, timeout: 12000 });
        props.onDone?.(r);
        emit('close');
    } catch (e) {
        error.value = e.message;
    } finally {
        busy.value = false;
    }
};

const input = 'rounded-md border border-edge-subtle bg-surface-page px-2.5 py-1.5 text-sm text-content-primary focus:border-accent-500 focus:outline-none';
const n = (v) => Number(v).toLocaleString(undefined, { maximumFractionDigits: 2 });
</script>

<template>
    <Modal :title="data ? `Settle ${data.van.plate}` : 'Settle van'" eyebrow="End of day" width="xl" :busy="busy" dirty @close="emit('close')">
        <div v-if="!data && !error" class="h-56 animate-pulse rounded-lg bg-surface-sunken"></div>

        <div v-if="data" class="grid gap-5 lg:grid-cols-[1fr_17rem]">
            <div class="space-y-2">
                <p class="text-[13px] text-content-muted">
                    {{ data.sellers.join(', ') || 'No seller on this route' }} · {{ data.sales.count }} sales today · <Money :value="data.sales.total" compact />
                    <span v-if="data.previous" class="ml-1 text-warning">· already settled once today ({{ data.previous.status.replace('_', ' ') }})</span>
                </p>
                <div class="max-h-[50vh] overflow-y-auto rounded-lg border border-edge-subtle">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 bg-surface-sunken">
                            <tr class="text-left text-xs uppercase tracking-wide text-content-muted">
                                <th class="px-3 py-2 font-medium">Product</th>
                                <th class="px-2 py-2 text-right font-medium">Loaded</th>
                                <th class="px-2 py-2 text-right font-medium">Sold</th>
                                <th class="px-2 py-2 text-right font-medium">System</th>
                                <th class="w-28 px-2 py-2 font-medium">Counted</th>
                                <th class="px-3 py-2 text-right font-medium">Diff</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="l in data.lines" :key="l.variation_id" class="border-t border-edge-subtle" :class="diff(l) !== 0 ? 'bg-warning/5' : ''">
                                <td class="px-3 py-1.5 text-content-primary">{{ l.name }}</td>
                                <td class="px-2 py-1.5 text-right text-content-muted numeric">{{ n(l.loaded_today) }}</td>
                                <td class="px-2 py-1.5 text-right text-content-muted numeric">{{ n(l.sold_today) }}</td>
                                <td class="px-2 py-1.5 text-right text-content-primary numeric">{{ n(l.qty) }}</td>
                                <td class="px-2 py-1.5"><input v-model.number="counted[l.variation_id]" type="number" min="0" step="any" :class="[input, 'w-24']" /></td>
                                <td class="px-3 py-1.5 text-right font-medium numeric" :class="diff(l) < 0 ? 'text-danger' : diff(l) > 0 ? 'text-warning' : 'text-content-muted'">
                                    {{ diff(l) === 0 ? '—' : (diff(l) > 0 ? '+' : '') + n(diff(l)) }}
                                </td>
                            </tr>
                            <tr v-if="!data.lines.length">
                                <td colspan="6" class="px-3 py-8 text-center text-[13px] text-content-muted">The system shows nothing on this van.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <aside class="space-y-3 text-[13px]">
                <div class="rounded-lg border border-edge-subtle p-3">
                    <p class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-content-muted">Money handed in</p>
                    <label class="mb-2 block">
                        <span class="flex justify-between text-content-muted">Cash <span>expected <Money :value="data.money.cash" compact /></span></span>
                        <input v-model.number="form.counted_cash" type="number" min="0" step="any" :class="[input, 'mt-1 w-full']" />
                    </label>
                    <label class="block">
                        <span class="flex justify-between text-content-muted">Cheques <span>expected <Money :value="data.money.cheque" compact /></span></span>
                        <input v-model.number="form.counted_cheques" type="number" min="0" step="any" :class="[input, 'mt-1 w-full']" />
                    </label>
                    <p v-if="data.money.other" class="mt-2 text-xs text-content-muted">Plus <Money :value="data.money.other" compact /> by card/transfer (not handed in).</p>
                </div>

                <div class="rounded-lg border p-3" :class="totals.clean ? 'border-success/40 bg-success/5' : 'border-warning/40 bg-warning/5'">
                    <p class="mb-1 text-[11px] font-semibold uppercase tracking-wider text-content-muted">Differences</p>
                    <p v-if="totals.clean" class="font-medium text-success">Everything matches.</p>
                    <dl v-else class="space-y-0.5">
                        <div v-if="totals.short > 0.004" class="flex justify-between"><dt>Stock short</dt><dd class="font-medium text-danger"><Money :value="totals.short" compact /></dd></div>
                        <div v-if="totals.over > 0.004" class="flex justify-between"><dt>Stock over</dt><dd class="font-medium text-warning"><Money :value="totals.over" compact /></dd></div>
                        <div v-if="Math.abs(totals.cash) > 0.004" class="flex justify-between"><dt>Cash {{ totals.cash < 0 ? 'short' : 'over' }}</dt><dd class="font-medium" :class="totals.cash < 0 ? 'text-danger' : 'text-warning'"><Money :value="Math.abs(totals.cash)" compact /></dd></div>
                        <div v-if="Math.abs(totals.cheques) > 0.004" class="flex justify-between"><dt>Cheques {{ totals.cheques < 0 ? 'short' : 'over' }}</dt><dd class="font-medium" :class="totals.cheques < 0 ? 'text-danger' : 'text-warning'"><Money :value="Math.abs(totals.cheques)" compact /></dd></div>
                        <p class="pt-1 text-xs text-content-muted">These go to a manager for approval before anything is written off.</p>
                    </dl>
                </div>

                <label class="flex items-center gap-2 text-content-primary">
                    <input v-model="form.unload" type="checkbox" class="rounded border-edge-strong" /> Unload counted stock to
                </label>
                <select v-if="form.unload" v-model.number="form.to_location_id" :class="[input, 'w-full']">
                    <option v-for="w in data.warehouses" :key="w.id" :value="w.id">{{ w.name }}</option>
                </select>
                <input v-model="form.odometer_end" type="number" min="0" placeholder="Odometer at return (km)" :class="[input, 'w-full']" />
                <textarea v-model="form.notes" rows="2" maxlength="500" placeholder="Explain any difference" :class="[input, 'w-full']"></textarea>
            </aside>
        </div>

        <p v-if="error" class="mt-3 rounded-md bg-danger/10 px-3 py-2 text-[13px] text-danger">{{ error }}</p>

        <template #footer>
            <button type="button" class="rounded-md px-3 py-2 text-sm font-medium text-content-secondary hover:bg-surface-sunken" :disabled="busy" @click="emit('close')">Cancel</button>
            <button type="button" :disabled="!data || busy" class="rounded-md bg-accent-500 px-4 py-2 text-sm font-semibold text-brand-950 hover:bg-accent-400 disabled:opacity-50" @click="submit">
                {{ busy ? 'Settling…' : totals?.clean ? 'Settle van' : 'Settle and send differences' }}
            </button>
        </template>
    </Modal>
</template>
