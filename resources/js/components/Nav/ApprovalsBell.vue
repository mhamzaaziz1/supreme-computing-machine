<script setup>
/**
 * The approvals inbox, as a popover on the bell. Every exception a manager
 * decides lands here; the requester's screen is polling, so approving here
 * lets the seller carry on straight away.
 */
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Icon from '../Icon.vue';
import Money from '../Money.vue';
import Popover from '../Popover.vue';
import { api } from '../../overlays/api';
import { openOverlay } from '../../overlays/store';
import { toast } from '../../overlays/toast';

const page = usePage();
const count = ref(page.props.ops?.pendingApprovals ?? 0);
const items = ref([]);
const view = ref('pending');
const loading = ref(false);
const notes = ref({});
let timer = null;

const refreshCount = async () => {
    try {
        count.value = (await api('approvals/count')).pending;
    } catch {
        // Offline or logged out: keep the last number.
    }
};

const load = async () => {
    loading.value = true;
    try {
        const r = await api('approvals', { query: { status: view.value === 'decided' ? 'decided' : null } });
        items.value = r.items;
        count.value = r.pending;
    } catch (e) {
        toast(e.message, { tone: 'danger' });
    } finally {
        loading.value = false;
    }
};

const decide = async (item, approve) => {
    try {
        const r = await api(`approvals/${item.id}/decide`, { method: 'POST', body: { approve, note: notes.value[item.id] || null } });
        toast(`${item.type_label}: ${r.message.toLowerCase().replace('.', '')}`);
        count.value = r.pending;
        load();
    } catch (e) {
        toast(e.message, { tone: 'danger' });
    }
};

const switchView = (v) => {
    view.value = v;
    load();
};

onMounted(() => (timer = setInterval(refreshCount, 30000)));
onBeforeUnmount(() => clearInterval(timer));

const reasonText = (item) => item.payload?.reason || (item.payload?.reasons ?? []).map((r) => r.message).join(' ');
</script>

<template>
    <Popover :width="400" @open="load">
        <template #trigger="{ toggle }">
            <button
                type="button"
                class="relative grid h-8 w-8 place-items-center rounded-md text-content-secondary transition-colors hover:bg-surface-sunken hover:text-content-primary"
                :aria-label="`Approvals${count ? `, ${count} waiting` : ''}`"
                @click="toggle"
            >
                <Icon name="bell" :size="17" />
                <span v-if="count" class="absolute -right-0.5 -top-0.5 grid h-4 min-w-4 place-items-center rounded-full bg-danger px-1 text-[10px] font-semibold text-white">
                    {{ count > 9 ? '9+' : count }}
                </span>
            </button>
        </template>

        <div class="flex items-center justify-between border-b border-edge-subtle px-4 py-2.5">
            <h3 class="text-sm font-semibold text-content-primary">Approvals</h3>
            <div class="flex gap-1 text-xs">
                <button type="button" class="rounded px-2 py-1" :class="view === 'pending' ? 'bg-surface-sunken font-medium text-content-primary' : 'text-content-muted'" @click="switchView('pending')">Waiting</button>
                <button type="button" class="rounded px-2 py-1" :class="view === 'decided' ? 'bg-surface-sunken font-medium text-content-primary' : 'text-content-muted'" @click="switchView('decided')">Decided</button>
            </div>
        </div>

        <div class="scrollbar-slim max-h-[60vh] overflow-y-auto">
            <p v-if="loading && !items.length" class="px-4 py-8 text-center text-sm text-content-muted">Loading…</p>
            <p v-else-if="!items.length" class="px-4 py-8 text-center text-sm text-content-muted">
                {{ view === 'pending' ? 'Nothing is waiting for you.' : 'No decisions yet.' }}
            </p>

            <article v-for="item in items" :key="item.id" class="border-b border-edge-subtle px-4 py-3 last:border-0">
                <div class="flex items-start gap-2">
                    <span class="shrink-0 rounded bg-surface-sunken px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-content-secondary">{{ item.type_label }}</span>
                    <span class="ml-auto shrink-0 text-xs text-content-muted">{{ item.ago }}</span>
                </div>
                <p class="mt-1.5 text-[13px] text-content-primary">{{ item.summary }}</p>
                <p class="mt-0.5 text-xs text-content-muted">
                    {{ item.requester ?? 'Someone' }}
                    <template v-if="item.amount !== null"> · <Money :value="item.amount" compact /></template>
                    <template v-if="item.contact_id"> · <button type="button" class="hover:underline" @click="openOverlay('outlet', { id: item.contact_id })">open outlet</button></template>
                </p>
                <p v-if="reasonText(item)" class="mt-1 text-xs italic text-content-secondary">“{{ reasonText(item) }}”</p>

                <div v-if="item.status === 'pending'" class="mt-2 flex gap-1.5">
                    <input v-model="notes[item.id]" type="text" placeholder="Note (optional)"
                        class="min-w-0 flex-1 rounded border border-edge-subtle bg-surface-page px-2 py-1 text-xs text-content-primary focus:outline-none" />
                    <button type="button" class="rounded bg-success px-2.5 py-1 text-xs font-semibold text-white" @click="decide(item, true)">Approve</button>
                    <button type="button" class="rounded border border-edge-strong px-2.5 py-1 text-xs font-medium text-danger" @click="decide(item, false)">Reject</button>
                </div>
                <p v-else class="mt-1 text-xs" :class="item.status === 'approved' ? 'text-success' : 'text-danger'">
                    {{ item.status === 'approved' ? 'Approved' : 'Rejected' }} by {{ item.decider ?? '—' }}<template v-if="item.note"> — {{ item.note }}</template>
                </p>
            </article>
        </div>
    </Popover>
</template>
