<script setup>
/**
 * Oil changes coming due. Each vehicle's own km/day turns "next at 49,600
 * km" into a date, so the bay can remind owners before they drift to the
 * garage down the road.
 */
import { computed, onMounted, ref } from 'vue';
import Drawer from '../ui/Drawer.vue';
import Icon from '../Icon.vue';
import { api } from '../../overlays/api';
import { openOverlay } from '../../overlays/store';
import { toast } from '../../overlays/toast';

const emit = defineEmits(['close']);

const within = ref(7);
const data = ref(null);
const loading = ref(true);
const error = ref(null);
const selected = ref(new Set());
const sending = ref(false);

const load = async () => {
    loading.value = true;
    error.value = null;
    try {
        data.value = await api('service-due', { query: { within: within.value } });
        selected.value = new Set();
    } catch (e) {
        error.value = e.message;
    } finally {
        loading.value = false;
    }
};

onMounted(load);

const pick = (d) => {
    within.value = d;
    load();
};

const toggle = (id) => {
    const s = new Set(selected.value);
    s.has(id) ? s.delete(id) : s.add(id);
    selected.value = s;
};

const allSelected = computed(() => data.value?.items.length && selected.value.size === data.value.items.length);
const toggleAll = () => (selected.value = allSelected.value ? new Set() : new Set(data.value.items.map((i) => i.vehicle_id)));

const remind = async (ids, channel) => {
    sending.value = true;
    try {
        const r = await api('service-due/remind', { method: 'POST', body: { vehicle_ids: ids, channel } });
        if (channel === 'whatsapp') r.links.forEach((l) => window.open(l.url, '_blank', 'noopener'));
        toast(r.failed.length ? `${r.message} No mobile number for ${r.failed.join(', ')}.` : r.message, { tone: r.failed.length ? 'info' : 'success' });
        load();
    } catch (e) {
        toast(e.message, { tone: 'danger' });
    } finally {
        sending.value = false;
    }
};

const chip = (i) => {
    if (i.days_until < 0) return { text: `${-i.days_until}d overdue`, tone: 'bg-danger/10 text-danger' };
    if (i.days_until === 0) return { text: 'due today', tone: 'bg-accent-500/15 text-accent-700 dark:text-accent-300' };
    return { text: `in ${i.days_until}d`, tone: 'bg-warning/10 text-warning' };
};

const ago = (d) => (d ? new Date(String(d).replace(' ', 'T')).toLocaleDateString(undefined, { day: 'numeric', month: 'short' }) : null);
</script>

<template>
    <Drawer title="Oil changes due" subtitle="Predicted from each vehicle's own mileage" width="xl" :error="error" @close="emit('close')" @retry="load">
        <div class="flex flex-wrap items-center gap-2 border-b border-edge-subtle bg-surface-raised px-5 py-2">
            <button v-for="d in [7, 14, 30]" :key="d" type="button" class="rounded-md px-3 py-1.5 text-sm font-medium"
                :class="within === d ? 'bg-brand-600 text-white' : 'text-content-secondary hover:bg-surface-sunken'" @click="pick(d)">
                Next {{ d }} days
            </button>
            <div v-if="selected.size" class="ml-auto flex gap-2">
                <button type="button" :disabled="sending" class="rounded-md border border-edge-strong px-3 py-1.5 text-[13px] font-medium text-content-primary hover:bg-surface-sunken" @click="remind([...selected], 'whatsapp')">
                    WhatsApp {{ selected.size }}
                </button>
                <button v-if="data?.sms_available" type="button" :disabled="sending" class="rounded-md bg-brand-600 px-3 py-1.5 text-[13px] font-semibold text-white hover:bg-brand-700" @click="remind([...selected], 'sms')">
                    SMS {{ selected.size }}
                </button>
            </div>
        </div>

        <div v-if="loading" class="space-y-2 p-5"><div v-for="n in 4" :key="n" class="h-12 animate-pulse rounded bg-surface-sunken"></div></div>

        <div v-else-if="data" class="overflow-x-auto">
            <table class="w-full min-w-[680px] text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-content-muted">
                        <th class="w-10 px-5 py-2"><input type="checkbox" :checked="allSelected" aria-label="Select all" @change="toggleAll" /></th>
                        <th class="px-2 py-2 font-medium">Vehicle</th>
                        <th class="px-3 py-2 font-medium">Owner</th>
                        <th class="px-3 py-2 font-medium">Due</th>
                        <th class="px-3 py-2 text-right font-medium">Est. now / due at</th>
                        <th class="px-5 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="i in data.items" :key="i.vehicle_id" class="border-t border-edge-subtle">
                        <td class="px-5 py-2.5"><input type="checkbox" :checked="selected.has(i.vehicle_id)" :aria-label="`Select ${i.plate}`" @change="toggle(i.vehicle_id)" /></td>
                        <td class="px-2 py-2.5">
                            <button type="button" class="text-left font-medium text-content-primary hover:text-brand-600 hover:underline" @click="openOverlay('vehicle', { id: i.vehicle_id })">
                                {{ i.plate || 'No plate' }}
                            </button>
                            <p class="text-xs text-content-muted">{{ i.vehicle }}</p>
                        </td>
                        <td class="px-3 py-2.5">
                            <button type="button" class="text-left text-content-primary hover:underline" @click="openOverlay('outlet', { id: i.contact_id })">{{ i.owner }}</button>
                            <p class="text-xs text-content-muted">{{ i.mobile || 'no mobile' }}</p>
                        </td>
                        <td class="px-3 py-2.5">
                            <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold" :class="chip(i).tone">{{ chip(i).text }}</span>
                            <p v-if="i.reminded_at" class="mt-0.5 text-[11px] text-content-muted">reminded {{ ago(i.reminded_at) }}</p>
                        </td>
                        <td class="whitespace-nowrap px-3 py-2.5 text-right text-content-secondary numeric">
                            {{ i.estimated_reading.toLocaleString() }} / {{ i.next_mileage.toLocaleString() }} km
                            <p class="text-[11px] text-content-muted">{{ i.km_per_day }} km/day{{ i.basis === 'default' ? ' (assumed)' : '' }}</p>
                        </td>
                        <td class="px-5 py-2.5 text-right">
                            <button type="button" :disabled="sending || !i.mobile" class="rounded p-1.5 text-success hover:bg-surface-sunken disabled:opacity-30" :title="`Remind ${i.owner} on WhatsApp`" :aria-label="`Remind ${i.owner} on WhatsApp`" @click="remind([i.vehicle_id], 'whatsapp')">
                                <Icon name="whatsapp" :size="17" />
                            </button>
                        </td>
                    </tr>
                    <tr v-if="!data.items.length">
                        <td colspan="6" class="px-5 py-12 text-center text-sm text-content-muted">No vehicles due in the next {{ within }} days.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </Drawer>
</template>
