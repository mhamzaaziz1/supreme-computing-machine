<script setup>
/**
 * Cost to serve: each route's gross margin against what its van costs,
 * ending in net margin per unit (or per litre, where pack sizes are set).
 * A busy route that loses money shows up red here.
 */
import { computed, onMounted, ref } from 'vue';
import Drawer from '../ui/Drawer.vue';
import Money from '../Money.vue';
import { api } from '../../overlays/api';
import { openOverlay } from '../../overlays/store';

const emit = defineEmits(['close']);

const days = ref(30);
const data = ref(null);
const loading = ref(true);
const error = ref(null);

const load = async () => {
    loading.value = true;
    error.value = null;
    try {
        data.value = await api('routes/economics', { query: { days: days.value } });
    } catch (e) {
        error.value = e.message;
    } finally {
        loading.value = false;
    }
};

onMounted(load);

const pick = (d) => {
    days.value = d;
    load();
};

const perLitre = computed(() => data.value?.has_litres && data.value.totals.litres > 0);
const maxAbsNet = computed(() => Math.max(1, ...(data.value?.routes ?? []).map((r) => Math.abs(r.net))));
</script>

<template>
    <Drawer title="Cost to serve" subtitle="Margin per route after the van's costs" width="xl" :error="error" @close="emit('close')" @retry="load">
        <div class="flex items-center gap-2 border-b border-edge-subtle bg-surface-raised px-5 py-2">
            <button v-for="d in [7, 30, 90]" :key="d" type="button" class="rounded-md px-3 py-1.5 text-sm font-medium"
                :class="days === d ? 'bg-brand-600 text-white' : 'text-content-secondary hover:bg-surface-sunken'" @click="pick(d)">Last {{ d }} days</button>
        </div>

        <div v-if="loading" class="space-y-2 p-5"><div v-for="n in 4" :key="n" class="h-10 animate-pulse rounded bg-surface-sunken"></div></div>

        <div v-else-if="data" class="overflow-x-auto">
            <table class="w-full min-w-[820px] text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-content-muted">
                        <th class="px-5 py-2 font-medium">Route</th>
                        <th class="px-3 py-2 text-right font-medium">Sales</th>
                        <th class="px-3 py-2 text-right font-medium">Gross</th>
                        <th class="px-3 py-2 text-right font-medium">Van cost</th>
                        <th class="w-48 px-3 py-2 font-medium">Net</th>
                        <th class="px-3 py-2 text-right font-medium">{{ perLitre ? 'Net / litre' : 'Net / unit' }}</th>
                        <th class="px-3 py-2 text-right font-medium">Collected</th>
                        <th class="px-5 py-2 text-right font-medium">Owed</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="r in data.routes" :key="r.id" class="cursor-pointer border-t border-edge-subtle hover:bg-surface-page" @click="openOverlay('route', { id: r.id })">
                        <td class="px-5 py-2.5">
                            <p class="font-medium text-content-primary">{{ r.name }}</p>
                            <p class="text-xs text-content-muted">{{ r.buyers }}/{{ r.outlets }} outlets bought · {{ r.visits }} visits<template v-if="r.km"> · {{ r.km.toLocaleString() }} km</template></p>
                        </td>
                        <td class="px-3 py-2.5 text-right text-content-primary"><Money :value="r.revenue" compact /></td>
                        <td class="px-3 py-2.5 text-right text-content-secondary">
                            <Money :value="r.gross" compact /><span v-if="r.gross_pct !== null" class="block text-[11px] text-content-muted">{{ r.gross_pct }}%</span>
                        </td>
                        <td class="px-3 py-2.5 text-right text-content-secondary"><Money :value="r.van_cost" compact /></td>
                        <td class="px-3 py-2.5">
                            <div class="flex items-center gap-2">
                                <div class="h-2 flex-1 overflow-hidden rounded-full bg-surface-sunken">
                                    <div class="h-full rounded-full" :class="r.net < 0 ? 'bg-danger' : 'bg-success'" :style="{ width: `${(Math.abs(r.net) / maxAbsNet) * 100}%` }"></div>
                                </div>
                                <Money :value="r.net" compact class="w-20 text-right font-medium" :class="r.net < 0 ? 'text-danger' : 'text-content-primary'" />
                            </div>
                        </td>
                        <td class="px-3 py-2.5 text-right" :class="(perLitre ? r.net_per_litre : r.net_per_unit) < 0 ? 'text-danger' : 'text-content-secondary'">
                            <Money v-if="(perLitre ? r.net_per_litre : r.net_per_unit) !== null" :value="perLitre ? r.net_per_litre : r.net_per_unit" />
                            <span v-else class="text-content-muted">—</span>
                        </td>
                        <td class="px-3 py-2.5 text-right text-content-secondary"><Money :value="r.collected" compact /></td>
                        <td class="px-5 py-2.5 text-right" :class="r.outstanding > 0 ? 'text-danger' : 'text-content-muted'"><Money :value="r.outstanding" compact /></td>
                    </tr>
                    <tr v-if="!data.routes.length"><td colspan="8" class="px-5 py-12 text-center text-sm text-content-muted">No routes yet.</td></tr>
                </tbody>
                <tfoot v-if="data.routes.length">
                    <tr class="border-t-2 border-edge-strong font-semibold">
                        <td class="px-5 py-2.5 text-content-primary">All routes</td>
                        <td class="px-3 py-2.5 text-right"><Money :value="data.totals.revenue" compact /></td>
                        <td class="px-3 py-2.5 text-right"><Money :value="data.totals.gross" compact /></td>
                        <td class="px-3 py-2.5 text-right"><Money :value="data.totals.van_cost" compact /></td>
                        <td class="px-3 py-2.5 text-right" :class="data.totals.net < 0 ? 'text-danger' : ''"><Money :value="data.totals.net" compact /></td>
                        <td></td>
                        <td class="px-3 py-2.5 text-right"><Money :value="data.totals.collected" compact /></td>
                        <td class="px-5 py-2.5 text-right"><Money :value="data.totals.outstanding" compact /></td>
                    </tr>
                </tfoot>
            </table>
            <p class="px-5 py-3 text-xs text-content-muted">
                Gross = sell price less the purchase cost of the stock each sale drew from. Van cost = vehicle expenses for the route's van.
                <template v-if="!perLitre"> Set a pack size in litres on products to see margin per litre.</template>
            </p>
        </div>
    </Drawer>
</template>
