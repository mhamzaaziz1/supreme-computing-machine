<script setup>
/**
 * A route's day: its stops in visit order, which are due an order, which
 * have been visited, what each owes — and the rules the route runs under.
 * Drag stops to change the order; the map shows the same order.
 */
import { computed, onMounted, ref } from 'vue';
import Drawer from '../ui/Drawer.vue';
import Icon from '../Icon.vue';
import Money from '../Money.vue';
import Popover from '../Popover.vue';
import RouteMap from '../ui/RouteMap.vue';
import { api } from '../../overlays/api';
import { openOverlay } from '../../overlays/store';
import { toast } from '../../overlays/toast';

const props = defineProps({ id: { type: Number, required: true } });
const emit = defineEmits(['close']);

const data = ref(null);
const loading = ref(true);
const error = ref(null);
const tab = ref('stops');
const stops = ref([]);
const dirty = ref(false);
const dragFrom = ref(null);
const rules = ref({});

const load = async () => {
    loading.value = true;
    error.value = null;
    try {
        data.value = await api(`routes/${props.id}/plan`);
        stops.value = [...data.value.stops];
        dirty.value = false;
    } catch (e) {
        error.value = e.message;
    } finally {
        loading.value = false;
    }
};

onMounted(load);

const drop = (to) => {
    const from = dragFrom.value;
    dragFrom.value = null;
    if (from === null || from === to) return;
    const list = [...stops.value];
    const [moved] = list.splice(from, 1);
    list.splice(to, 0, moved);
    stops.value = list;
    dirty.value = true;
};

const saveOrder = async () => {
    try {
        toast((await api(`routes/${props.id}/sequence`, { method: 'PUT', body: { contact_ids: stops.value.map((s) => s.id) } })).message);
        load();
    } catch (e) {
        toast(e.message, { tone: 'danger' });
    }
};

const openRules = () => (rules.value = { ...data.value.rules });
const saveRules = async (close) => {
    try {
        const r = rules.value;
        toast(
            (
                await api(`routes/${props.id}/rules`, {
                    method: 'PATCH',
                    body: { ...r, minimum_order_value: r.minimum_order_value === '' ? null : r.minimum_order_value, allowed_start_time: r.allowed_start_time || null, allowed_end_time: r.allowed_end_time || null },
                })
            ).message,
        );
        close();
        load();
    } catch (e) {
        toast(e.message, { tone: 'danger' });
    }
};

const modes = [
    ['off', 'Off', 'Visits recorded, nothing checked'],
    ['log', 'Log', 'Checked; failures logged, not blocked'],
    ['enforce', 'Enforce', 'Failures need a reason before billing'],
];

const points = computed(() =>
    stops.value
        .map((s, i) => ({ id: s.id, lat: s.lat, lng: s.lng, number: i + 1, label: `${i + 1}. ${s.name}`, tone: s.visited_today ? 'visited' : s.due ? 'due' : 'pending' }))
        .filter((p) => p.lat !== null),
);

const ago = (d) => {
    if (!d) return 'never visited';
    const days = Math.round((Date.now() - new Date(d).getTime()) / 864e5);
    return days <= 0 ? 'visited today' : `visited ${days}d ago`;
};

const input = 'w-full rounded-md border border-edge-subtle bg-surface-page px-2.5 py-1.5 text-sm text-content-primary focus:border-accent-500 focus:outline-none';
</script>

<template>
    <Drawer :title="data?.route.name ?? 'Route'" :subtitle="data ? [data.sellers.join(', ') || 'No seller assigned', data.van ? `van ${data.van.plate}` : null].filter(Boolean).join(' · ') : ''"
        width="xl" :loading="loading" :error="error" @close="emit('close')" @retry="load">
        <template v-if="data?.can_edit" #header-actions>
            <Popover :width="320" @open="openRules">
                <template #trigger="{ toggle }">
                    <button type="button" class="rounded-md border border-edge-strong px-2.5 py-1.5 text-xs font-medium text-content-primary hover:bg-surface-sunken" @click="toggle">
                        Rules · {{ data.rules.geofence_mode }}
                    </button>
                </template>
                <template #default="{ close }">
                    <form class="space-y-3 p-4" @submit.prevent="saveRules(close)">
                        <div>
                            <p class="mb-1 text-xs font-medium text-content-muted">Geofence</p>
                            <div class="space-y-1">
                                <label v-for="[key, name, hint] in modes" :key="key" class="flex cursor-pointer items-start gap-2 rounded-md border px-2.5 py-1.5"
                                    :class="rules.geofence_mode === key ? 'border-brand-600 bg-brand-600/5' : 'border-edge-subtle'">
                                    <input v-model="rules.geofence_mode" type="radio" :value="key" class="mt-1" />
                                    <span><span class="block text-sm font-medium text-content-primary">{{ name }}</span><span class="block text-xs text-content-muted">{{ hint }}</span></span>
                                </label>
                            </div>
                        </div>
                        <label class="block">
                            <span class="mb-1 block text-xs font-medium text-content-muted">Minimum order for field sellers</span>
                            <input v-model="rules.minimum_order_value" type="number" min="0" step="any" placeholder="No minimum" :class="input" />
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="block"><span class="mb-1 block text-xs font-medium text-content-muted">Selling from</span><input v-model="rules.allowed_start_time" type="time" :class="input" /></label>
                            <label class="block"><span class="mb-1 block text-xs font-medium text-content-muted">until</span><input v-model="rules.allowed_end_time" type="time" :class="input" /></label>
                        </div>
                        <label class="flex items-center gap-2 text-sm text-content-primary"><input v-model="rules.enable_collections" type="checkbox" /> Sellers may collect payments</label>
                        <label class="flex items-center gap-2 text-sm text-content-primary"><input v-model="rules.enable_returns" type="checkbox" /> Sellers may take returns</label>
                        <button type="submit" class="w-full rounded-md bg-brand-600 px-3 py-2 text-sm font-semibold text-white hover:bg-brand-700">Save rules</button>
                    </form>
                </template>
            </Popover>
        </template>

        <div v-if="data">
            <div class="grid grid-cols-2 gap-px border-b border-edge-subtle bg-edge-subtle sm:grid-cols-5">
                <div v-for="[label, value, tone] in [
                    ['Stops', data.summary.stops, ''],
                    ['Visited today', `${data.summary.visited} / ${data.summary.stops}`, 'text-success'],
                    ['Due an order', data.summary.due, 'text-accent-700 dark:text-accent-300'],
                    ['Pinned on map', `${data.summary.pinned} / ${data.summary.stops}`, ''],
                ]" :key="label" class="bg-surface-raised px-4 py-2.5">
                    <p class="text-[11px] uppercase tracking-wide text-content-muted">{{ label }}</p>
                    <p class="text-lg font-semibold numeric" :class="tone || 'text-content-primary'">{{ value }}</p>
                </div>
                <div class="bg-surface-raised px-4 py-2.5">
                    <p class="text-[11px] uppercase tracking-wide text-content-muted">Owed on route</p>
                    <p class="text-lg font-semibold text-content-primary"><Money :value="data.summary.outstanding" compact /></p>
                </div>
            </div>

            <div class="flex items-center gap-1 border-b border-edge-subtle px-5 py-2">
                <button v-for="[key, name] in [['stops', 'Stops'], ['map', 'Map']]" :key="key" type="button" class="rounded-md px-3 py-1.5 text-sm font-medium"
                    :class="tab === key ? 'bg-brand-600 text-white' : 'text-content-secondary hover:bg-surface-sunken'" @click="tab = key">{{ name }}</button>
                <button v-if="dirty" type="button" class="ml-auto rounded-md bg-accent-500 px-3 py-1.5 text-sm font-semibold text-brand-950 hover:bg-accent-400" @click="saveOrder">Save visit order</button>
                <span v-else-if="data.can_edit && tab === 'stops'" class="ml-auto text-xs text-content-muted">Drag to reorder</span>
            </div>

            <ol v-if="tab === 'stops'" class="divide-y divide-edge-subtle">
                <li
                    v-for="(s, i) in stops"
                    :key="s.id"
                    :draggable="data.can_edit"
                    class="flex items-center gap-3 px-5 py-2.5"
                    :class="dragFrom === i ? 'opacity-40' : ''"
                    @dragstart="dragFrom = i"
                    @dragover.prevent
                    @drop="drop(i)"
                >
                    <Icon v-if="data.can_edit" name="grip" :size="16" class="shrink-0 cursor-grab text-content-muted" />
                    <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full text-[11px] font-semibold"
                        :class="s.visited_today ? 'bg-success text-white' : s.due ? 'bg-accent-500 text-brand-950' : 'bg-surface-sunken text-content-secondary'">{{ i + 1 }}</span>
                    <div class="min-w-0 flex-1">
                        <button type="button" class="truncate text-left text-sm font-medium text-content-primary hover:underline" @click="openOverlay('outlet', { id: s.id })">{{ s.name }}</button>
                        <p class="text-xs text-content-muted">
                            {{ ago(s.last_visit) }}
                            <template v-if="s.interval_days"> · orders every ~{{ Math.round(s.interval_days) }}d</template>
                            <template v-if="s.days_since_order !== null"> · last {{ s.days_since_order }}d ago</template>
                            <template v-if="s.lat === null"> · not pinned</template>
                        </p>
                    </div>
                    <div class="flex shrink-0 items-center gap-1.5">
                        <span v-if="s.on_hold" class="rounded-full bg-danger/10 px-2 py-0.5 text-[11px] font-semibold text-danger">hold</span>
                        <span v-if="s.due && !s.visited_today" class="rounded-full bg-accent-500/15 px-2 py-0.5 text-[11px] font-semibold text-accent-700 dark:text-accent-300">due</span>
                        <span v-if="s.ordered_today" class="rounded-full bg-success/10 px-2 py-0.5 text-[11px] font-semibold text-success"><Money :value="s.ordered_today" compact /></span>
                        <span v-if="s.outstanding > 0" class="w-24 text-right text-xs text-content-secondary"><Money :value="s.outstanding" compact /> owed</span>
                        <button type="button" class="rounded p-1 text-content-muted hover:bg-surface-sunken hover:text-content-primary" :aria-label="`Log a visit at ${s.name}`" @click="openOverlay('visit', { contactId: s.id, onDone: load })">
                            <Icon name="geofence" :size="15" />
                        </button>
                    </div>
                </li>
                <li v-if="!stops.length" class="px-5 py-12 text-center text-sm text-content-muted">No outlets are on this route yet.</li>
            </ol>

            <div v-else class="p-5">
                <RouteMap :points="points" path height="420px" @select="(p) => openOverlay('outlet', { id: p.id })" />
                <p class="mt-2 text-xs text-content-muted">Numbers follow the visit order. Green: visited today · amber: due an order.</p>
            </div>
        </div>
    </Drawer>
</template>
