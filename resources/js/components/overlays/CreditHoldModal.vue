<script setup>
/**
 * Credit hold.
 *
 * The one place the app deliberately blocks the screen. It does the maths
 * the seller would otherwise argue about, and offers three ways forward,
 * with taking a payment first because it turns the problem into cash.
 * Number keys pick an option so the counter never needs the mouse.
 */
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import Modal from '../ui/Modal.vue';
import Money from '../Money.vue';
import { api } from '../../overlays/api';
import { isTyping } from '../../overlays/store';

const props = defineProps({
    hold: { type: Object, required: true },
    /** Called with {action: 'pay', amount} | {action: 'reduce'} | {action: 'override', token, approver} */
    onResolve: { type: Function, required: true },
});

const emit = defineEmits(['close']);

const mode = ref(null); // null | 'override' | 'waiting'
const pin = ref('');
const reason = ref('');
const busy = ref(false);
const error = ref(null);
let poll = null;

const reasonOf = (code) => props.hold.reasons.find((r) => r.code === code);
const overLimit = computed(() => reasonOf('over_limit'));
const overdue = computed(() => reasonOf('overdue'));
const onHold = computed(() => reasonOf('on_hold'));

const resolve = (result) => {
    props.onResolve(result);
    emit('close');
};

const takePayment = () => resolve({ action: 'pay', amount: props.hold.minimum_payment });
const reduce = () => resolve({ action: 'reduce', available: props.hold.available });

const approveWithPin = async () => {
    if (!pin.value) return;
    busy.value = true;
    error.value = null;
    try {
        const r = await api('credit/override', {
            method: 'POST',
            body: { contact_id: props.hold.contact_id, amount_due: props.hold.order_due, pin: pin.value, reason: reason.value || null },
        });
        resolve({ action: 'override', token: r.token, approver: r.approver });
    } catch (e) {
        error.value = e.message;
        pin.value = '';
    } finally {
        busy.value = false;
    }
};

const sendToInbox = async () => {
    busy.value = true;
    error.value = null;
    try {
        const r = await api('credit/request', {
            method: 'POST',
            body: { contact_id: props.hold.contact_id, amount_due: props.hold.order_due, reason: reason.value || null },
        });
        mode.value = 'waiting';
        poll = setInterval(async () => {
            try {
                const s = await api(`approvals/${r.approval_id}`);
                if (s.status === 'approved' && s.token) {
                    clearInterval(poll);
                    resolve({ action: 'override', token: s.token, approver: 'a manager' });
                } else if (s.status === 'rejected') {
                    clearInterval(poll);
                    mode.value = 'override';
                    error.value = `A manager declined the override${s.note ? `: ${s.note}` : '.'}`;
                }
            } catch {
                // Keep polling through a dropped request.
            }
        }, 4000);
    } catch (e) {
        error.value = e.message;
    } finally {
        busy.value = false;
    }
};

const onKey = (e) => {
    if (isTyping(e) || mode.value === 'waiting') return;
    if (e.key === '1') takePayment();
    else if (e.key === '2') reduce();
    else if (e.key === '3') mode.value = 'override';
};

onMounted(() => document.addEventListener('keydown', onKey));
onBeforeUnmount(() => {
    document.removeEventListener('keydown', onKey);
    clearInterval(poll);
});
</script>

<template>
    <Modal :title="`This order puts ${hold.contact_name} over its terms`" eyebrow="Credit hold" tone="danger" width="md" :busy="busy" @close="emit('close')">
        <div class="space-y-3 text-[13.5px] text-content-secondary">
            <dl class="grid grid-cols-[1fr_auto] gap-x-4 gap-y-1 rounded-lg bg-surface-sunken px-3.5 py-2.5 numeric">
                <dt>Outstanding now</dt>
                <dd class="text-right text-content-primary"><Money :value="hold.outstanding" compact /></dd>
                <dt>This order on credit</dt>
                <dd class="text-right text-content-primary">+ <Money :value="hold.order_due" compact /></dd>
                <template v-if="hold.limit !== null">
                    <dt>Credit limit</dt>
                    <dd class="text-right text-content-primary"><Money :value="hold.limit" compact /></dd>
                </template>
                <template v-if="overLimit">
                    <dt class="border-t border-edge-strong pt-1 font-semibold text-danger">Over by</dt>
                    <dd class="border-t border-edge-strong pt-1 text-right font-semibold text-danger"><Money :value="overLimit.over_by" compact /></dd>
                </template>
            </dl>

            <p v-if="onHold" class="rounded-md bg-danger/10 px-3 py-2 text-danger">{{ onHold.message }}</p>

            <div v-if="overdue">
                <p>{{ overdue.message }}</p>
                <ul class="mt-1 space-y-0.5 text-xs">
                    <li v-for="inv in overdue.invoices.slice(0, 4)" :key="inv.id" class="flex justify-between">
                        <span>{{ inv.invoice_no }} · {{ inv.overdue_days }} days overdue</span>
                        <Money :value="inv.due" compact />
                    </li>
                </ul>
            </div>
        </div>

        <div v-if="mode !== 'waiting'" class="mt-4 space-y-2">
            <button type="button" class="flex w-full items-center gap-3 rounded-lg border border-accent-500 bg-accent-500/10 px-3 py-2.5 text-left hover:bg-accent-500/15" @click="takePayment">
                <kbd class="rounded border border-edge-strong bg-surface-raised px-1.5 text-xs text-content-primary">1</kbd>
                <span>
                    <span class="block text-sm font-semibold text-content-primary">Take a payment now</span>
                    <span class="block text-xs text-content-muted">At least <Money :value="hold.minimum_payment" compact /> {{ overLimit && hold.reasons.length === 1 ? 'clears the hold' : 'makes this a paid sale' }}</span>
                </span>
            </button>
            <button type="button" class="flex w-full items-center gap-3 rounded-lg border border-edge-subtle px-3 py-2.5 text-left hover:bg-surface-sunken" @click="reduce">
                <kbd class="rounded border border-edge-strong bg-surface-raised px-1.5 text-xs text-content-primary">2</kbd>
                <span>
                    <span class="block text-sm font-semibold text-content-primary">Reduce the order</span>
                    <span class="block text-xs text-content-muted">
                        <template v-if="!onHold && !overdue && hold.available > 0">You can still sell <Money :value="hold.available" compact /> on credit</template>
                        <template v-else>Back to the order; only cash sales until this is cleared</template>
                    </span>
                </span>
            </button>
            <button
                type="button"
                class="flex w-full items-center gap-3 rounded-lg border px-3 py-2.5 text-left hover:bg-surface-sunken"
                :class="mode === 'override' ? 'border-brand-500' : 'border-edge-subtle'"
                @click="mode = 'override'"
            >
                <kbd class="rounded border border-edge-strong bg-surface-raised px-1.5 text-xs text-content-primary">3</kbd>
                <span>
                    <span class="block text-sm font-semibold text-content-primary">Ask a manager to override</span>
                    <span class="block text-xs text-content-muted">Manager's staff PIN here, or send it to their inbox · the reason is logged</span>
                </span>
            </button>

            <form v-if="mode === 'override'" class="space-y-2 rounded-lg border border-edge-subtle bg-surface-page p-3" @submit.prevent="approveWithPin">
                <input v-model="reason" type="text" maxlength="190" placeholder="Why should this go through? (optional)"
                    class="w-full rounded-md border border-edge-subtle bg-surface-raised px-2.5 py-1.5 text-sm text-content-primary focus:border-accent-500 focus:outline-none" />
                <div class="flex gap-2">
                    <input v-model="pin" type="password" inputmode="numeric" autocomplete="off" placeholder="Manager PIN" aria-label="Manager PIN"
                        class="w-36 rounded-md border border-edge-subtle bg-surface-raised px-2.5 py-1.5 text-sm text-content-primary focus:border-accent-500 focus:outline-none" />
                    <button type="submit" :disabled="!pin || busy" class="rounded-md bg-brand-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-brand-700 disabled:opacity-50">
                        Approve
                    </button>
                    <button type="button" :disabled="busy" class="ml-auto rounded-md px-2 py-1.5 text-sm font-medium text-brand-600 hover:underline dark:text-brand-300" @click="sendToInbox">
                        Send to inbox instead
                    </button>
                </div>
            </form>
        </div>

        <div v-else class="mt-4 flex items-center gap-3 rounded-lg border border-edge-subtle bg-surface-page px-3 py-3 text-sm text-content-primary">
            <span class="h-3 w-3 animate-pulse rounded-full bg-accent-500"></span>
            Waiting for a manager to decide in their inbox. This closes by itself when they approve.
        </div>

        <p v-if="error" class="mt-3 rounded-md bg-danger/10 px-3 py-2 text-[13px] text-danger">{{ error }}</p>
    </Modal>
</template>
