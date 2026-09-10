<script setup>
/**
 * For the lubricant principal: the monthly secondary-sales file (one row per
 * invoice line, net of returns, with litres) and progress on their targets.
 */
import { computed, onMounted, ref } from 'vue';
import Modal from '../ui/Modal.vue';
import Icon from '../Icon.vue';
import Money from '../Money.vue';
import { api } from '../../overlays/api';
import { toast } from '../../overlays/toast';

const props = defineProps({ tab: { type: String, default: 'sales' } });
const emit = defineEmits(['close']);

const active = ref(props.tab);
const month = ref(null);
const data = ref(null);
const error = ref(null);
const loading = ref(true);
const adding = ref(false);

const today = new Date();
const iso = (d) => d.toISOString().slice(0, 10);
const qStart = new Date(today.getFullYear(), Math.floor(today.getMonth() / 3) * 3, 1);
const qEnd = new Date(today.getFullYear(), Math.floor(today.getMonth() / 3) * 3 + 3, 0);
const blank = () => ({ name: '', starts_on: iso(qStart), ends_on: iso(qEnd), measure: 'litres', target: '', scope: { brand_ids: [], category_ids: [] } });
const form = ref(blank());

const load = async () => {
    loading.value = true;
    error.value = null;
    try {
        data.value = await api('principal', { query: { month: month.value } });
        month.value = data.value.month;
    } catch (e) {
        error.value = e.message;
    } finally {
        loading.value = false;
    }
};

onMounted(load);

const totals = computed(() => (data.value?.brands ?? []).reduce((t, b) => ({ qty: t.qty + b.qty, litres: t.litres + b.litres, value: t.value + b.value }), { qty: 0, litres: 0, value: 0 }));

const download = () => (window.location.href = data.value.export_url);

const toggle = (list, id) => {
    const i = list.indexOf(id);
    i === -1 ? list.push(id) : list.splice(i, 1);
};

const addTarget = async () => {
    try {
        toast((await api('principal/targets', { method: 'POST', body: form.value })).message);
        form.value = blank();
        adding.value = false;
        load();
    } catch (e) {
        toast(e.message, { tone: 'danger' });
    }
};

const removeTarget = async (t) => {
    if (!window.confirm(`Remove "${t.name}"?`)) return;
    toast((await api(`principal/targets/${t.id}`, { method: 'DELETE' })).message);
    load();
};

const unit = { qty: 'units', litres: 'L', value: '' };
const fmt = (n, m) => (m === 'value' ? null : `${Number(n).toLocaleString(undefined, { maximumFractionDigits: 1 })} ${unit[m]}`);
const monthLabel = (m) => new Date(`${m}-01T00:00:00`).toLocaleDateString(undefined, { month: 'long', year: 'numeric' });

const input = 'rounded-md border border-edge-subtle bg-surface-page px-2.5 py-1.5 text-sm text-content-primary focus:border-accent-500 focus:outline-none';
const chip = (on) => ['rounded-full border px-2.5 py-0.5 text-xs', on ? 'border-brand-600 bg-brand-600 text-white' : 'border-edge-subtle text-content-secondary'];
</script>

<template>
    <Modal title="Principal reporting" eyebrow="Secondary sales & targets" width="lg" @close="emit('close')">
        <div class="mb-4 flex gap-1 rounded-lg bg-surface-sunken p-1">
            <button v-for="[key, name] in [['sales', 'Monthly sales file'], ['targets', 'Targets']]" :key="key" type="button"
                class="flex-1 rounded-md px-2 py-1.5 text-sm font-medium"
                :class="active === key ? 'bg-surface-raised text-content-primary shadow-raised' : 'text-content-secondary'" @click="active = key">{{ name }}</button>
        </div>

        <p v-if="error" class="rounded-md bg-danger/10 px-3 py-2 text-sm text-danger">{{ error }}</p>
        <div v-else-if="loading && !data" class="h-40 animate-pulse rounded-lg bg-surface-sunken"></div>

        <template v-else-if="data">
            <!-- Monthly file -->
            <div v-if="active === 'sales'" class="space-y-3">
                <div class="flex items-center gap-2">
                    <select v-model="month" :class="input" @change="load">
                        <option v-for="m in data.months" :key="m" :value="m">{{ monthLabel(m) }}</option>
                    </select>
                    <button type="button" class="ml-auto inline-flex items-center gap-1.5 rounded-md bg-accent-500 px-3 py-2 text-sm font-semibold text-brand-950 hover:bg-accent-400" @click="download">
                        <Icon name="download" :size="15" /> Download CSV
                    </button>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-content-muted">
                            <th class="py-1.5 font-medium">Brand</th><th class="py-1.5 text-right font-medium">Units</th>
                            <th class="py-1.5 text-right font-medium">Litres</th><th class="py-1.5 text-right font-medium">Net value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="b in data.brands" :key="b.brand" class="border-t border-edge-subtle">
                            <td class="py-1.5 text-content-primary">{{ b.brand }}</td>
                            <td class="py-1.5 text-right numeric">{{ b.qty.toLocaleString() }}</td>
                            <td class="py-1.5 text-right numeric">{{ b.litres.toLocaleString() }}<span v-if="b.missing_pack" class="text-warning" title="Some products have no pack size">*</span></td>
                            <td class="py-1.5 text-right"><Money :value="b.value" compact /></td>
                        </tr>
                        <tr v-if="!data.brands.length"><td colspan="4" class="py-6 text-center text-[13px] text-content-muted">No sales in {{ monthLabel(month) }}.</td></tr>
                    </tbody>
                    <tfoot v-if="data.brands.length">
                        <tr class="border-t-2 border-edge-strong font-semibold">
                            <td class="py-1.5">Total</td><td class="py-1.5 text-right numeric">{{ totals.qty.toLocaleString() }}</td>
                            <td class="py-1.5 text-right numeric">{{ totals.litres.toLocaleString() }}</td><td class="py-1.5 text-right"><Money :value="totals.value" compact /></td>
                        </tr>
                    </tfoot>
                </table>
                <p v-if="data.products_without_pack" class="text-xs text-warning">
                    {{ data.products_without_pack }} products have no pack size, so their litres count as zero. Set it in the product's drawer in the catalogue.
                </p>
                <p class="text-xs text-content-muted">One row per invoice line: outlet, route, SKU, quantity, litres, net, tax and gross — net of returns.</p>
            </div>

            <!-- Targets -->
            <div v-else class="space-y-3">
                <article v-for="t in data.targets" :key="t.id" class="rounded-lg border border-edge-subtle p-3">
                    <div class="flex items-start gap-2">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-content-primary">{{ t.name }}</p>
                            <p class="text-xs text-content-muted">{{ t.starts_on }} → {{ t.ends_on }} · {{ t.ended ? 'ended' : `${t.days_left} days left` }}</p>
                        </div>
                        <button v-if="data.can_manage" type="button" class="text-content-muted hover:text-danger" :aria-label="`Remove ${t.name}`" @click="removeTarget(t)"><Icon name="close" :size="14" /></button>
                    </div>
                    <div class="relative my-2 h-2.5 overflow-hidden rounded-full bg-surface-sunken">
                        <div class="h-full rounded-full" :class="t.percent >= 100 ? 'bg-success' : 'bg-brand-600'" :style="{ width: `${Math.min(100, t.percent)}%` }"></div>
                        <div v-if="!t.ended" class="absolute inset-y-0 w-0.5 bg-content-primary/60" :style="{ left: `${t.elapsed_percent}%` }" title="Time elapsed"></div>
                    </div>
                    <p class="text-[13px] text-content-secondary">
                        <template v-if="t.measure === 'value'"><Money :value="t.achieved" compact /> of <Money :value="t.target" compact /></template>
                        <template v-else>{{ fmt(t.achieved, t.measure) }} of {{ fmt(t.target, t.measure) }}</template>
                        · {{ t.percent }}%
                        <span v-if="t.projected_percent !== null && !t.ended" :class="t.projected_percent >= 100 ? 'text-success' : 'text-warning'">
                            · on pace for {{ Math.round(t.projected_percent) }}%
                        </span>
                    </p>
                </article>
                <p v-if="!data.targets.length && !adding" class="text-[13px] text-content-muted">No principal targets yet.</p>

                <form v-if="adding" class="space-y-2 rounded-lg border border-edge-subtle bg-surface-page p-3" @submit.prevent="addTarget">
                    <input v-model="form.name" type="text" maxlength="120" placeholder="Q3 PCMO volume" :class="[input, 'w-full']" required />
                    <div class="flex flex-wrap items-center gap-2 text-sm text-content-primary">
                        <input v-model="form.starts_on" type="date" :class="input" required /> to <input v-model="form.ends_on" type="date" :class="input" required />
                    </div>
                    <div class="flex items-center gap-2 text-sm text-content-primary">
                        Target <input v-model.number="form.target" type="number" min="0" step="any" :class="[input, 'w-32']" required />
                        <select v-model="form.measure" :class="input"><option value="litres">litres</option><option value="qty">units</option><option value="value">value</option></select>
                    </div>
                    <div class="flex flex-wrap gap-1.5">
                        <button v-for="b in data.options.brands" :key="`b${b.id}`" type="button" :class="chip(form.scope.brand_ids.includes(b.id))" @click="toggle(form.scope.brand_ids, b.id)">{{ b.name }}</button>
                        <button v-for="c in data.options.categories" :key="`c${c.id}`" type="button" :class="chip(form.scope.category_ids.includes(c.id))" @click="toggle(form.scope.category_ids, c.id)">{{ c.name }}</button>
                    </div>
                    <p class="text-xs text-content-muted">No brand or category picked = every product counts.</p>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="rounded-md px-3 py-1.5 text-sm text-content-secondary hover:bg-surface-sunken" @click="adding = false">Cancel</button>
                        <button type="submit" class="rounded-md bg-brand-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-brand-700">Add target</button>
                    </div>
                </form>
                <button v-else-if="data.can_manage" type="button" class="text-sm font-medium text-brand-600 hover:underline dark:text-brand-300" @click="adding = true">Add a target</button>
            </div>
        </template>
    </Modal>
</template>
