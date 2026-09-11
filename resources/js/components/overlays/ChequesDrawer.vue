<script setup>
/**
 * Post-dated cheque register. Cheques due today or earlier float to the top
 * of "In hand" so they get deposited; a bounce reverses the payment and puts
 * the outlet on hold in one step.
 */
import { onMounted, ref } from 'vue';
import Drawer from '../ui/Drawer.vue';
import Money from '../Money.vue';
import { api } from '../../overlays/api';
import { openOverlay } from '../../overlays/store';
import { toast } from '../../overlays/toast';

const emit = defineEmits(['close']);

const tabs = [
    ['open', 'In hand'],
    ['cleared', 'Cleared'],
    ['bounced', 'Bounced'],
];

const status = ref('open');
const data = ref(null);
const loading = ref(true);
const error = ref(null);
const confirming = ref(null);
const note = ref('');

const load = async () => {
    loading.value = true;
    error.value = null;
    try {
        data.value = await api('cheques', { query: { status: status.value } });
    } catch (e) {
        error.value = e.message;
    } finally {
        loading.value = false;
    }
};

onMounted(load);

const pick = (s) => {
    status.value = s;
    confirming.value = null;
    load();
};

const setStatus = async (item, next) => {
    try {
        const r = await api(`cheques/${item.id}/status`, { method: 'POST', body: { status: next, note: note.value || null } });
        toast(r.message, { tone: next === 'bounced' ? 'danger' : 'success' });
        confirming.value = null;
        note.value = '';
        load();
    } catch (e) {
        toast(e.message, { tone: 'danger' });
    }
};

const when = (c) => {
    if (c.days === null || c.days === undefined) return '';
    if (c.days < 0) return `${-c.days}d past date`;
    if (c.days === 0) return 'dated today';
    return `in ${c.days}d`;
};

const fmt = (d) => (d ? new Date(d).toLocaleDateString(undefined, { day: 'numeric', month: 'short', year: 'numeric' }) : '—');
</script>

<template>
    <Drawer title="Cheques" subtitle="Post-dated cheques received from outlets" width="xl" :error="error" @close="emit('close')" @retry="load">
        <div class="flex items-center gap-1 border-b border-edge-subtle bg-surface-raised px-5 py-2">
            <button
                v-for="[key, label] in tabs"
                :key="key"
                type="button"
                class="rounded-md px-3 py-1.5 text-sm font-medium"
                :class="status === key ? 'bg-brand-600 text-white' : 'text-content-secondary hover:bg-surface-sunken'"
                @click="pick(key)"
            >
                {{ label }}
            </button>
            <span v-if="data" class="ml-auto text-[13px] text-content-muted">
                {{ data.totals.count }} · <Money :value="data.totals.amount" compact />
                <template v-if="data.totals.due_now > 0"> · <span class="font-medium text-accent-700 dark:text-accent-300"><Money :value="data.totals.due_now" compact /> ready to deposit</span></template>
            </span>
        </div>

        <div v-if="loading" class="space-y-2 p-5">
            <div v-for="n in 4" :key="n" class="h-12 animate-pulse rounded bg-surface-sunken"></div>
        </div>

        <div v-else-if="data" class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-content-muted">
                        <th class="px-5 py-2 font-medium">Outlet</th>
                        <th class="px-3 py-2 font-medium">Cheque</th>
                        <th class="px-3 py-2 font-medium">Date</th>
                        <th class="px-3 py-2 text-right font-medium">Amount</th>
                        <th class="px-5 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="c in data.items" :key="c.id">
                        <tr class="border-t border-edge-subtle">
                            <td class="px-5 py-2.5">
                                <button v-if="c.contact_id" type="button" class="text-left text-content-primary hover:text-brand-600 hover:underline" @click="openOverlay('outlet', { id: c.contact_id })">
                                    {{ c.contact ?? '—' }}
                                </button>
                            </td>
                            <td class="px-3 py-2.5 text-content-secondary">#{{ c.number || '—' }}<span v-if="c.bank" class="text-content-muted"> · {{ c.bank }}</span></td>
                            <td class="whitespace-nowrap px-3 py-2.5">
                                <span class="text-content-primary">{{ fmt(c.date) }}</span>
                                <span v-if="c.status === 'pending'" class="ml-1.5 text-xs" :class="c.days <= 0 ? 'font-medium text-accent-700 dark:text-accent-300' : 'text-content-muted'">{{ when(c) }}</span>
                                <span v-else-if="c.status === 'deposited'" class="ml-1.5 rounded-full bg-info/10 px-1.5 py-0.5 text-[11px] text-info">deposited</span>
                            </td>
                            <td class="whitespace-nowrap px-3 py-2.5 text-right font-medium text-content-primary"><Money :value="c.amount" /></td>
                            <td class="whitespace-nowrap px-5 py-2.5 text-right">
                                <template v-if="status === 'open'">
                                    <button v-if="c.status === 'pending'" type="button" class="rounded px-2 py-1 text-xs font-medium text-brand-600 hover:bg-surface-sunken dark:text-brand-300" @click="setStatus(c, 'deposited')">Deposited</button>
                                    <button type="button" class="rounded px-2 py-1 text-xs font-medium text-success hover:bg-surface-sunken" @click="setStatus(c, 'cleared')">Cleared</button>
                                    <button type="button" class="rounded px-2 py-1 text-xs font-medium text-danger hover:bg-surface-sunken" @click="confirming = c.id">Bounced</button>
                                </template>
                                <span v-else class="text-xs text-content-muted">{{ fmt(c.status_at) }}</span>
                            </td>
                        </tr>
                        <tr v-if="confirming === c.id" class="bg-danger/5">
                            <td colspan="5" class="px-5 py-3">
                                <p class="text-[13px] text-content-primary">
                                    Reverse this <Money :value="c.amount" compact /> payment and put {{ c.contact }} on credit hold?
                                </p>
                                <div class="mt-2 flex gap-2">
                                    <input v-model="note" type="text" placeholder="Bank's reason (optional)"
                                        class="flex-1 rounded-md border border-edge-subtle bg-surface-raised px-2.5 py-1.5 text-sm text-content-primary focus:outline-none" />
                                    <button type="button" class="rounded-md bg-danger px-3 py-1.5 text-sm font-semibold text-white" @click="setStatus(c, 'bounced')">Record bounce</button>
                                    <button type="button" class="rounded-md px-3 py-1.5 text-sm text-content-secondary hover:bg-surface-sunken" @click="confirming = null">Cancel</button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr v-if="!data.items.length">
                        <td colspan="5" class="px-5 py-12 text-center text-sm text-content-muted">No cheques here.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </Drawer>
</template>
