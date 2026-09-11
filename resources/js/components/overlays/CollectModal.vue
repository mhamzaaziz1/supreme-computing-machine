<script setup>
/**
 * Collect a payment, oldest invoice first.
 *
 * The preview shows exactly how the amount will be spread before it is
 * saved, because "which invoice did this pay?" is the argument every
 * collector has with every outlet. Cheques carry their bank and date into
 * the post-dated cheque register.
 */
import { computed, onMounted, ref } from 'vue';
import Modal from '../ui/Modal.vue';
import Money from '../Money.vue';
import { api } from '../../overlays/api';
import { toast } from '../../overlays/toast';

const props = defineProps({
    contactId: { type: Number, required: true },
    onDone: { type: Function, default: null },
});

const emit = defineEmits(['close']);

const data = ref(null);
const loadError = ref(null);
const busy = ref(false);
const error = ref(null);

const form = ref({ amount: '', method: 'cash', paid_on: '', note: '', cheque_number: '', cheque_bank: '', cheque_date: '' });

onMounted(async () => {
    try {
        data.value = await api(`outlets/${props.contactId}/collect`);
        form.value.paid_on = data.value.now;
    } catch (e) {
        loadError.value = e.message;
    }
});

const amount = computed(() => Number(form.value.amount) || 0);

const quick = computed(() => {
    if (!data.value?.invoices.length) return [];
    const inv = data.value.invoices;
    const overdue = inv.filter((i) => i.overdue_days > 0).reduce((s, i) => s + i.due, 0);
    const out = [{ label: 'Oldest invoice', value: inv[0].due }];
    if (overdue > 0 && Math.abs(overdue - inv[0].due) > 0.01) out.push({ label: 'All overdue', value: overdue });
    out.push({ label: 'Full balance', value: data.value.outstanding });
    return out;
});

/** Mirrors TransactionUtil::payAtOnce: oldest first, remainder to advance. */
const allocation = computed(() => {
    let left = amount.value;
    const rows = [];
    for (const inv of data.value?.invoices ?? []) {
        if (left <= 0) break;
        const applied = Math.min(left, inv.due);
        rows.push({ ...inv, applied, settles: applied >= inv.due - 0.005 });
        left -= applied;
    }
    return { rows, advance: Math.max(0, left) };
});

const isCheque = computed(() => form.value.method === 'cheque');
const dirty = computed(() => amount.value > 0);

const submit = async () => {
    if (amount.value <= 0 || busy.value) return;
    busy.value = true;
    error.value = null;
    try {
        const f = form.value;
        const r = await api('collect', {
            method: 'POST',
            body: {
                contact_id: props.contactId,
                amount: amount.value,
                method: f.method,
                paid_on: f.paid_on || null,
                note: f.note || null,
                cheque_number: isCheque.value ? f.cheque_number : null,
                cheque_bank: isCheque.value ? f.cheque_bank || null : null,
                cheque_date: isCheque.value ? f.cheque_date : null,
            },
        });
        toast(r.message, { action: r.whatsapp ? { label: 'Send receipt', href: r.whatsapp } : null, timeout: 10000 });
        props.onDone?.(r);
        emit('close');
    } catch (e) {
        error.value = e.message;
    } finally {
        busy.value = false;
    }
};

const input = 'w-full rounded-md border border-edge-subtle bg-surface-page px-3 py-2 text-sm text-content-primary focus:border-accent-500 focus:outline-none';
</script>

<template>
    <Modal :title="data ? `Collect from ${data.outlet.name}` : 'Collect payment'" eyebrow="Collection" width="lg" :dirty="dirty" :busy="busy" @close="emit('close')">
        <p v-if="loadError" class="rounded-md bg-danger/10 px-3 py-2 text-sm text-danger">{{ loadError }}</p>
        <div v-else-if="!data" class="h-40 animate-pulse rounded-lg bg-surface-sunken"></div>

        <form v-else id="collect-form" class="grid gap-5 md:grid-cols-[1fr_16rem]" @submit.prevent="submit">
            <div class="space-y-3">
                <div>
                    <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-content-muted" for="collect-amount">Amount</label>
                    <input id="collect-amount" v-model="form.amount" type="number" min="0" step="any" autofocus :class="[input, 'text-lg font-semibold']" />
                    <div class="mt-1.5 flex flex-wrap gap-1.5">
                        <button
                            v-for="q in quick"
                            :key="q.label"
                            type="button"
                            class="rounded-full border border-edge-subtle px-2.5 py-0.5 text-xs text-content-secondary hover:border-accent-500 hover:text-content-primary"
                            @click="form.amount = Number(q.value.toFixed(2))"
                        >
                            {{ q.label }} · <Money :value="q.value" compact />
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <label class="block">
                        <span class="mb-1 block text-xs font-medium uppercase tracking-wide text-content-muted">Method</span>
                        <select v-model="form.method" :class="input">
                            <option v-for="m in data.methods" :key="m.value" :value="m.value">{{ m.label }}</option>
                        </select>
                    </label>
                    <label class="block">
                        <span class="mb-1 block text-xs font-medium uppercase tracking-wide text-content-muted">Received on</span>
                        <input v-model="form.paid_on" type="datetime-local" :class="input" />
                    </label>
                </div>

                <div v-if="isCheque" class="grid grid-cols-3 gap-3 rounded-lg border border-edge-subtle bg-surface-page p-3">
                    <label class="block">
                        <span class="mb-1 block text-xs font-medium text-content-muted">Cheque no.</span>
                        <input v-model="form.cheque_number" type="text" :class="input" required />
                    </label>
                    <label class="block">
                        <span class="mb-1 block text-xs font-medium text-content-muted">Bank</span>
                        <input v-model="form.cheque_bank" type="text" placeholder="HBL, MCB…" :class="input" />
                    </label>
                    <label class="block">
                        <span class="mb-1 block text-xs font-medium text-content-muted">Cheque date</span>
                        <input v-model="form.cheque_date" type="date" :class="input" required />
                    </label>
                </div>

                <input v-model="form.note" type="text" maxlength="190" placeholder="Note (optional)" :class="input" />
            </div>

            <aside class="rounded-lg border border-edge-subtle bg-surface-page p-3 text-[13px]">
                <p class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-content-muted">Goes to</p>
                <p v-if="!data.invoices.length" class="text-content-muted">Nothing is owed. The full amount is held as an advance.</p>
                <ul v-else class="space-y-1.5">
                    <li v-for="row in allocation.rows" :key="row.id" class="flex justify-between gap-2">
                        <span class="min-w-0 truncate text-content-primary">
                            {{ row.invoice_no }}
                            <span class="text-xs" :class="row.settles ? 'text-success' : 'text-content-muted'">{{ row.settles ? 'settled' : 'part' }}</span>
                        </span>
                        <Money :value="row.applied" compact class="shrink-0" />
                    </li>
                    <li v-if="!allocation.rows.length" class="text-content-muted">Enter an amount to see where it goes.</li>
                    <li v-if="allocation.advance > 0.004" class="flex justify-between gap-2 border-t border-edge-subtle pt-1.5 text-success">
                        <span>Held as advance</span><Money :value="allocation.advance" compact />
                    </li>
                </ul>
                <p class="mt-3 border-t border-edge-subtle pt-2 text-xs text-content-muted">
                    Outstanding <Money :value="data.outstanding" compact /> → <Money :value="Math.max(0, data.outstanding - amount)" compact />
                </p>
            </aside>
        </form>

        <p v-if="error" class="mt-3 rounded-md bg-danger/10 px-3 py-2 text-[13px] text-danger">{{ error }}</p>

        <template #footer>
            <button type="button" class="rounded-md px-3 py-2 text-sm font-medium text-content-secondary hover:bg-surface-sunken" :disabled="busy" @click="emit('close')">Cancel</button>
            <button type="submit" form="collect-form" :disabled="amount <= 0 || busy"
                class="rounded-md bg-accent-500 px-4 py-2 text-sm font-semibold text-brand-950 hover:bg-accent-400 disabled:opacity-50">
                {{ busy ? 'Saving…' : amount > 0 ? 'Record payment' : 'Enter an amount' }}
            </button>
        </template>
    </Modal>
</template>
