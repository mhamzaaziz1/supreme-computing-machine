<script setup>
/**
 * One customer vehicle: when it is next due, what was done last time, and
 * the two things the bay does with that — remind the owner, or repeat the
 * last job as a new sale.
 */
import { onMounted, ref } from 'vue';
import Drawer from '../ui/Drawer.vue';
import Icon from '../Icon.vue';
import Money from '../Money.vue';
import { api } from '../../overlays/api';
import { openOverlay } from '../../overlays/store';
import { toast } from '../../overlays/toast';

const props = defineProps({ id: { type: Number, required: true } });
const emit = defineEmits(['close']);

const data = ref(null);
const loading = ref(true);
const error = ref(null);

const load = async () => {
    loading.value = true;
    error.value = null;
    try {
        data.value = await api(`vehicles/${props.id}`);
    } catch (e) {
        error.value = e.message;
    } finally {
        loading.value = false;
    }
};

onMounted(load);

const remind = async () => {
    try {
        const r = await api('service-due/remind', { method: 'POST', body: { vehicle_ids: [props.id], channel: 'whatsapp' } });
        r.links.forEach((l) => window.open(l.url, '_blank', 'noopener'));
        toast(r.failed.length ? 'This owner has no mobile number.' : 'Reminder opened in WhatsApp.', { tone: r.failed.length ? 'danger' : 'success' });
        load();
    } catch (e) {
        toast(e.message, { tone: 'danger' });
    }
};

const fmt = (d) => (d ? new Date(d).toLocaleDateString(undefined, { day: 'numeric', month: 'short', year: 'numeric' }) : '—');
const tone = { overdue: 'border-danger/40 bg-danger/5', due_soon: 'border-warning/40 bg-warning/5', ok: 'border-edge-subtle bg-surface-raised' };
</script>

<template>
    <Drawer :title="data?.vehicle.plate || 'Vehicle'" :subtitle="data ? [data.vehicle.name, data.owner.name].filter(Boolean).join(' · ') : ''"
        width="md" :loading="loading" :error="error" @close="emit('close')" @retry="load">
        <div v-if="data" class="space-y-5 p-5">
            <section v-if="data.service" class="rounded-lg border p-4" :class="tone[data.service.status]">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-content-muted">Next oil change</p>
                <p class="mt-1 text-xl font-semibold text-content-primary">≈ {{ fmt(data.service.due_on) }}</p>
                <p class="text-[13px] text-content-secondary">
                    at {{ data.service.next_mileage.toLocaleString() }} km ·
                    <template v-if="data.service.days_until < 0">{{ -data.service.days_until }} days overdue</template>
                    <template v-else>in {{ data.service.days_until }} days</template>
                </p>
                <p class="mt-2 text-xs text-content-muted">
                    Estimated {{ data.service.estimated_reading.toLocaleString() }} km now, driving {{ data.service.km_per_day }} km/day
                    {{ data.service.basis === 'default' ? '(assumed until a second visit)' : `(from ${data.service.visits} visits)` }}.
                </p>
                <p v-if="data.last_reminder" class="mt-1 text-xs text-content-muted">Last reminded {{ fmt(data.last_reminder.sent_at) }} by {{ data.last_reminder.channel }}.</p>
            </section>
            <p v-else class="text-[13px] text-content-muted">No mileage recorded yet, so no due date.</p>

            <section>
                <h3 class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-content-muted">Service history</h3>
                <ol class="space-y-2">
                    <li v-for="h in data.history" :key="h.id" class="rounded-md border border-edge-subtle p-3 text-[13px]">
                        <div class="flex justify-between gap-2">
                            <span class="font-medium text-content-primary">{{ fmt(h.date) }}</span>
                            <span class="text-content-muted numeric">{{ h.reading?.toLocaleString() ?? '—' }} → {{ h.next?.toLocaleString() ?? '—' }} km</span>
                        </div>
                        <p v-if="h.items.length" class="mt-1 text-content-secondary">{{ h.items.join(', ') }}</p>
                        <p v-if="h.invoice_no" class="mt-0.5 text-xs text-content-muted">{{ h.invoice_no }} · <Money :value="h.total" compact /></p>
                    </li>
                    <li v-if="!data.history.length" class="text-[13px] text-content-muted">Nothing recorded.</li>
                </ol>
            </section>
        </div>

        <template #footer>
            <div v-if="data" class="grid grid-cols-3 gap-2">
                <a v-if="data.links.repeat" :href="data.links.repeat" class="rounded-md bg-accent-500 px-2 py-2 text-center text-sm font-semibold text-brand-950 hover:bg-accent-400">Repeat last job</a>
                <button v-else type="button" disabled class="rounded-md bg-accent-500 px-2 py-2 text-sm font-semibold text-brand-950 opacity-40">Repeat last job</button>
                <button type="button" :disabled="!data.owner.mobile" class="flex items-center justify-center gap-1.5 rounded-md border border-edge-strong px-2 py-2 text-sm font-medium text-content-primary hover:bg-surface-sunken disabled:opacity-40" @click="remind">
                    <Icon name="whatsapp" :size="15" /> Remind
                </button>
                <button type="button" class="rounded-md border border-edge-strong px-2 py-2 text-sm font-medium text-content-primary hover:bg-surface-sunken" @click="openOverlay('outlet', { id: data.owner.id })">Owner</button>
            </div>
        </template>
    </Drawer>
</template>
