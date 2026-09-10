<script setup>
/**
 * Trade schemes: what is running, for whom, and what it has cost so far.
 */
import { onMounted, ref } from 'vue';
import Drawer from '../ui/Drawer.vue';
import Money from '../Money.vue';
import { api } from '../../overlays/api';
import { openOverlay } from '../../overlays/store';
import { toast } from '../../overlays/toast';

const emit = defineEmits(['close']);

const data = ref(null);
const loading = ref(true);
const error = ref(null);

const load = async () => {
    loading.value = true;
    error.value = null;
    try {
        data.value = await api('schemes');
    } catch (e) {
        error.value = e.message;
    } finally {
        loading.value = false;
    }
};

onMounted(load);

const typeLabel = { free_goods: 'Free goods', slab_discount: 'Slab discount', target_rebate: 'Target rebate' };

const rule = (s) => {
    const r = s.rules;
    const unit = r.unit_label || 'units';
    if (s.type === 'free_goods') return `Buy ${r.buy_qty} get ${r.free_qty} free`;
    if (s.type === 'slab_discount') return (r.slabs ?? []).map((x) => `${x.percent}% at ${x.min_qty}+`).join(' · ') + ` ${unit}`;
    return (r.tiers ?? []).map((x) => `${x.name} ${x.min_qty} ${unit} → ${x.rebate_percent}%`).join(' · ') + ` per ${r.period}`;
};

const scopeText = (s) => {
    const o = data.value.options;
    const names = (ids, list) => ids.map((id) => list.find((x) => x.id === id)?.name ?? `#${id}`);
    const parts = [
        ...names(s.scope.category_ids ?? [], o.categories),
        ...names(s.scope.brand_ids ?? [], o.brands),
        ...names(s.scope.variation_ids ?? [], o.variations),
    ];
    return parts.length ? parts.join(', ') : 'All products';
};

const audienceText = (s) => {
    const o = data.value.options;
    const parts = [
        ...(s.audience.customer_group_ids ?? []).map((id) => o.groups.find((g) => g.id === id)?.name),
        ...(s.audience.route_ids ?? []).map((id) => o.routes.find((r) => r.id === id)?.name),
    ].filter(Boolean);
    return parts.length ? parts.join(', ') : 'All outlets';
};

const toggle = async (s) => {
    try {
        toast((await api(`schemes/${s.id}/toggle`, { method: 'POST' })).message);
        load();
    } catch (e) {
        toast(e.message, { tone: 'danger' });
    }
};

const edit = (s = null) => openOverlay('schemeEdit', { scheme: s, options: data.value.options, onDone: load });

const fmt = (d) => (d ? new Date(d).toLocaleDateString(undefined, { day: 'numeric', month: 'short' }) : 'open');
</script>

<template>
    <Drawer title="Trade schemes" subtitle="Applied automatically in the sale form" width="lg" :loading="loading" :error="error" @close="emit('close')" @retry="load">
        <template #header-actions>
            <button v-if="data" type="button" class="rounded-md bg-accent-500 px-3 py-1.5 text-sm font-semibold text-brand-950 hover:bg-accent-400" @click="edit()">New scheme</button>
        </template>

        <ul v-if="data" class="divide-y divide-edge-subtle">
            <li v-for="s in data.schemes" :key="s.id" class="px-5 py-4" :class="s.is_active ? '' : 'opacity-60'">
                <div class="flex items-start gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" class="font-semibold text-content-primary hover:underline" @click="edit(s)">{{ s.name }}</button>
                            <span class="rounded bg-surface-sunken px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-content-secondary">{{ typeLabel[s.type] }}</span>
                            <span v-if="!s.is_active" class="text-[11px] text-content-muted">paused</span>
                        </div>
                        <p class="mt-0.5 text-[13px] text-content-primary">{{ rule(s) }}</p>
                        <p class="text-xs text-content-muted">{{ scopeText(s) }} · {{ audienceText(s) }} · {{ fmt(s.starts_on) }} – {{ fmt(s.ends_on) }}</p>
                        <p v-if="s.used" class="mt-1 text-xs text-content-secondary">Used on {{ s.used }} invoices · <Money :value="s.benefit" compact /> given</p>
                    </div>
                    <button type="button" class="shrink-0 rounded-md border border-edge-strong px-2.5 py-1 text-xs font-medium text-content-primary hover:bg-surface-sunken" @click="toggle(s)">
                        {{ s.is_active ? 'Pause' : 'Run' }}
                    </button>
                </div>
            </li>
            <li v-if="!data.schemes.length" class="px-5 py-12 text-center text-sm text-content-muted">
                No schemes yet. Free goods, slab discounts and target rebates all start from "New scheme".
            </li>
        </ul>
    </Drawer>
</template>
