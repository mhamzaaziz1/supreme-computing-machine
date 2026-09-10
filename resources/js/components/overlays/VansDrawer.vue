<script setup>
/**
 * Every van, what is on it, and where it is in its day: not loaded,
 * loaded, settled, or settled with differences waiting for a manager.
 */
import { onMounted, ref } from 'vue';
import Drawer from '../ui/Drawer.vue';
import Icon from '../Icon.vue';
import Money from '../Money.vue';
import { api, opsUrl } from '../../overlays/api';
import { openOverlay } from '../../overlays/store';

const emit = defineEmits(['close']);

const data = ref(null);
const loading = ref(true);
const error = ref(null);

const load = async () => {
    loading.value = true;
    error.value = null;
    try {
        data.value = await api('vans');
    } catch (e) {
        error.value = e.message;
    } finally {
        loading.value = false;
    }
};

onMounted(load);

const state = (v) => {
    if (v.settlement?.status === 'pending_approval') return { label: 'Settled · differences', tone: 'bg-warning/10 text-warning' };
    if (v.settlement?.status === 'rejected') return { label: 'Recount needed', tone: 'bg-danger/10 text-danger' };
    if (v.settlement) return { label: 'Settled', tone: 'bg-success/10 text-success' };
    if (v.loaded_today) return { label: 'On the road', tone: 'bg-info/10 text-info' };
    return { label: 'Not loaded', tone: 'bg-surface-sunken text-content-secondary' };
};

const loadVan = (v) => openOverlay('loadVan', { vanId: v.id, onDone: load });
const settleVan = (v) => openOverlay('settleVan', { vanId: v.id, onDone: load });
const slip = (v) => window.open(opsUrl(`vans/settlements/${v.settlement.id}/slip`), '_blank', 'noopener');
</script>

<template>
    <Drawer title="Vans" :subtitle="data ? `Stock on each van · ${new Date(data.date).toLocaleDateString(undefined, { weekday: 'long', day: 'numeric', month: 'short' })}` : ''"
        width="lg" :loading="loading" :error="error" @close="emit('close')" @retry="load">
        <ul v-if="data" class="divide-y divide-edge-subtle">
            <li v-for="v in data.vans" :key="v.id" class="px-5 py-4">
                <div class="flex items-start gap-3">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-surface-sunken text-content-secondary">
                        <Icon name="truck" :size="18" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-semibold text-content-primary">{{ v.plate }}</p>
                            <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold" :class="state(v).tone">{{ state(v).label }}</span>
                        </div>
                        <p class="text-[13px] text-content-muted">
                            {{ v.route ?? 'No route' }}<template v-if="v.sellers.length"> · {{ v.sellers.join(', ') }}</template>
                        </p>
                        <p class="mt-1 text-[13px] text-content-secondary">
                            <template v-if="v.location_id">{{ v.units.toLocaleString() }} units on board · <Money :value="v.value" compact /> at cost</template>
                            <template v-else>Not set up as a stock van yet — the first load creates its stock location.</template>
                        </p>
                    </div>
                </div>
                <div v-if="data.can_move_stock" class="mt-3 flex flex-wrap gap-2 pl-12">
                    <button type="button" class="rounded-md border border-edge-strong px-3 py-1.5 text-[13px] font-medium text-content-primary hover:bg-surface-sunken" @click="loadVan(v)">
                        Load
                    </button>
                    <button type="button" :disabled="!v.location_id" class="rounded-md border border-edge-strong px-3 py-1.5 text-[13px] font-medium text-content-primary hover:bg-surface-sunken disabled:opacity-40" @click="settleVan(v)">
                        Settle
                    </button>
                    <button v-if="v.settlement" type="button" class="rounded-md px-3 py-1.5 text-[13px] font-medium text-brand-600 hover:bg-surface-sunken dark:text-brand-300" @click="slip(v)">
                        Print slip
                    </button>
                </div>
            </li>
            <li v-if="!data.vans.length" class="px-5 py-12 text-center text-sm text-content-muted">
                No supply-chain vehicles yet. Add one under Supply chain vehicles.
            </li>
        </ul>
    </Drawer>
</template>
