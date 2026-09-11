<script setup>
/**
 * Everything a seller records at an outlet besides the order: how the visit
 * went, damaged stock, used oil collected, and returns.
 *
 * One modal with four tabs rather than four screens: the seller is standing
 * in the forecourt and should not have to navigate.
 */
import { computed, onMounted, ref } from 'vue';
import Modal from '../ui/Modal.vue';
import Money from '../Money.vue';
import { api, currentPosition } from '../../overlays/api';
import { openOverlay } from '../../overlays/store';
import { toast } from '../../overlays/toast';

const props = defineProps({
    contactId: { type: Number, required: true },
    tab: { type: String, default: 'visit' },
    onDone: { type: Function, default: null },
});

const emit = defineEmits(['close']);

const data = ref(null);
const loadError = ref(null);
const active = ref(props.tab);
const busy = ref(false);
const error = ref(null);

const visit = ref({ outcome: 'order_taken', notes: '', followup_date: '', photo: null });
const damage = ref({ query: '', hits: [], variation_id: null, product: '', quantity: '', notes: '', photo: null });
const oil = ref({ litres: '', rate: '', notes: '' });
const ret = ref({ transaction_id: null, qty: {}, reason: '' });

onMounted(async () => {
    try {
        data.value = await api(`outlets/${props.contactId}/visit`);
        ret.value.transaction_id = data.value.invoices[0]?.id ?? null;
    } catch (e) {
        loadError.value = e.message;
    }
});

const tabs = computed(() => [
    ['visit', 'Visit'],
    ['damage', 'Damage'],
    ['oil', 'Used oil'],
    ...(data.value?.can_return ? [['return', 'Return']] : []),
]);

const finish = (message) => {
    toast(message);
    props.onDone?.();
    emit('close');
};

const form = (fields) => {
    const fd = new FormData();
    for (const [k, v] of Object.entries(fields)) {
        if (v !== null && v !== undefined && v !== '') fd.append(k, v);
    }
    return fd;
};

// -- visit -------------------------------------------------------------------

const sendVisit = async (reason = null, position = undefined) => {
    busy.value = true;
    error.value = null;
    let pos = position;
    if (pos === undefined) {
        try {
            pos = await currentPosition();
        } catch {
            pos = null;
        }
    }
    try {
        const v = visit.value;
        const r = await api('visits', {
            method: 'POST',
            body: form({
                contact_id: props.contactId, outcome: v.outcome, notes: v.notes, followup_date: v.followup_date,
                lat: pos?.lat, lng: pos?.lng, accuracy: pos?.accuracy, reason, photo: v.photo,
            }),
        });
        finish(r.message);
    } catch (e) {
        if (e.status === 422 && e.body?.checkin) {
            openOverlay('checkIn', {
                contactId: props.contactId,
                contactName: data.value.outlet.name,
                action: 'mark_visit_done',
                result: e.body.checkin,
                position: pos,
                onResolve: () => toast('Visit logged with your reason.'),
            });
        } else {
            error.value = e.message;
        }
    } finally {
        busy.value = false;
    }
};

// -- damage ------------------------------------------------------------------

let timer;
const appBase = () => (document.querySelector('meta[name="app-base"]')?.content ?? window.location.origin).replace(/\/$/, '');
const searchProduct = () => {
    clearTimeout(timer);
    if (!damage.value.query.trim()) return (damage.value.hits = []);
    timer = setTimeout(async () => {
        const url = new URL(`${appBase()}/products/list`);
        url.searchParams.set('term', damage.value.query.trim());
        try {
            const res = await fetch(url, { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
            damage.value.hits = await res.json();
        } catch {
            damage.value.hits = [];
        }
    }, 250);
};
const pickProduct = (h) => Object.assign(damage.value, { variation_id: h.variation_id, product: h.name, query: '', hits: [] });

const sendDamage = async () => {
    busy.value = true;
    error.value = null;
    try {
        const d = damage.value;
        const r = await api('visits/report', {
            method: 'POST',
            body: form({ contact_id: props.contactId, kind: 'damage', variation_id: d.variation_id, quantity: d.quantity, notes: [d.product, d.notes].filter(Boolean).join(' — '), photo: d.photo }),
        });
        finish(r.message);
    } catch (e) {
        error.value = e.message;
    } finally {
        busy.value = false;
    }
};

// -- used oil ----------------------------------------------------------------

const oilAmount = computed(() => (Number(oil.value.litres) || 0) * (Number(oil.value.rate) || 0));
const sendOil = async () => {
    busy.value = true;
    error.value = null;
    try {
        const r = await api('visits/report', { method: 'POST', body: form({ contact_id: props.contactId, kind: 'used_oil', ...oil.value }) });
        finish(r.message);
    } catch (e) {
        error.value = e.message;
    } finally {
        busy.value = false;
    }
};

// -- return ------------------------------------------------------------------

const invoice = computed(() => data.value?.invoices.find((i) => i.id === ret.value.transaction_id));
const returnValue = computed(() => (invoice.value?.lines ?? []).reduce((s, l) => s + (Number(ret.value.qty[l.sell_line_id]) || 0) * l.unit_price_inc_tax, 0));
const sendReturn = async () => {
    busy.value = true;
    error.value = null;
    try {
        const r = await api('visits/return', {
            method: 'POST',
            body: {
                contact_id: props.contactId,
                transaction_id: ret.value.transaction_id,
                lines: invoice.value.lines.map((l) => ({ sell_line_id: l.sell_line_id, quantity: Number(ret.value.qty[l.sell_line_id]) || 0 })),
                reason: ret.value.reason,
            },
        });
        finish(r.message);
    } catch (e) {
        error.value = e.message;
    } finally {
        busy.value = false;
    }
};

const submit = () => ({ visit: () => sendVisit(), damage: sendDamage, oil: sendOil, return: sendReturn })[active.value]();

const canSubmit = computed(() => {
    if (active.value === 'damage') return damage.value.quantity > 0 && damage.value.photo;
    if (active.value === 'oil') return oil.value.litres > 0;
    if (active.value === 'return') return returnValue.value > 0 && ret.value.reason.trim();
    return true;
});

const submitLabel = { visit: 'Log visit', damage: 'Save damage report', oil: 'Record used oil', return: 'Send for approval' };

const input = 'w-full rounded-md border border-edge-subtle bg-surface-page px-2.5 py-1.5 text-sm text-content-primary focus:border-accent-500 focus:outline-none';
const label = 'mb-1 block text-xs font-medium uppercase tracking-wide text-content-muted';
</script>

<template>
    <Modal :title="data ? data.outlet.name : 'Visit'" eyebrow="At the outlet" width="md" :busy="busy" @close="emit('close')">
        <p v-if="loadError" class="rounded-md bg-danger/10 px-3 py-2 text-sm text-danger">{{ loadError }}</p>
        <div v-else-if="!data" class="h-40 animate-pulse rounded-lg bg-surface-sunken"></div>

        <template v-else>
            <div class="mb-4 flex gap-1 rounded-lg bg-surface-sunken p-1" role="tablist">
                <button v-for="[key, name] in tabs" :key="key" type="button" role="tab" :aria-selected="active === key"
                    class="flex-1 rounded-md px-2 py-1.5 text-sm font-medium"
                    :class="active === key ? 'bg-surface-raised text-content-primary shadow-raised' : 'text-content-secondary hover:text-content-primary'"
                    @click="active = key; error = null">
                    {{ name }}
                </button>
            </div>

            <!-- Visit -->
            <div v-if="active === 'visit'" class="space-y-3">
                <div>
                    <span :class="label">How did it go?</span>
                    <div class="grid grid-cols-2 gap-1.5">
                        <label v-for="o in data.outcomes" :key="o.value" class="flex cursor-pointer items-center gap-2 rounded-md border px-2.5 py-1.5 text-[13px]"
                            :class="visit.outcome === o.value ? 'border-brand-600 bg-brand-600/5 text-content-primary' : 'border-edge-subtle text-content-secondary'">
                            <input v-model="visit.outcome" type="radio" :value="o.value" class="sr-only" /> {{ o.label }}
                        </label>
                    </div>
                </div>
                <textarea v-model="visit.notes" rows="2" maxlength="400" placeholder="Notes (optional)" :class="input"></textarea>
                <div class="grid grid-cols-2 gap-3">
                    <label class="block"><span :class="label">Follow up on</span><input v-model="visit.followup_date" type="date" :class="input" /></label>
                    <label class="block"><span :class="label">Photo</span><input type="file" accept="image/*" capture="environment" class="w-full text-xs text-content-secondary" @change="visit.photo = $event.target.files[0] ?? null" /></label>
                </div>
                <p v-if="!data.outlet.has_route" class="text-xs text-content-muted">This outlet is not on a route, so the visit is saved without a GPS log.</p>
                <p v-else-if="data.field_user" class="text-xs text-content-muted">Your location is checked against the outlet when you log the visit.</p>
            </div>

            <!-- Damage -->
            <div v-else-if="active === 'damage'" class="space-y-3">
                <div class="relative">
                    <span :class="label">Product</span>
                    <input v-model="damage.query" type="search" :placeholder="damage.product || 'Search the damaged product'" :class="input" @input="searchProduct" />
                    <ul v-if="damage.hits.length" class="absolute left-0 right-0 z-10 mt-1 max-h-48 overflow-y-auto rounded-lg border border-edge-subtle bg-surface-raised shadow-overlay">
                        <li v-for="h in damage.hits" :key="h.variation_id">
                            <button type="button" class="w-full px-3 py-1.5 text-left text-[13px] text-content-primary hover:bg-surface-sunken" @click="pickProduct(h)">{{ h.name }}</button>
                        </li>
                    </ul>
                    <p v-if="damage.product" class="mt-1 text-xs text-content-secondary">{{ damage.product }}</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <label class="block"><span :class="label">Quantity</span><input v-model.number="damage.quantity" type="number" min="0" step="any" :class="input" /></label>
                    <label class="block"><span :class="label">Photo (required)</span><input type="file" accept="image/*" capture="environment" class="w-full text-xs text-content-secondary" @change="damage.photo = $event.target.files[0] ?? null" /></label>
                </div>
                <input v-model="damage.notes" type="text" maxlength="300" placeholder="What is wrong — leaking, dented, expired batch…" :class="input" />
            </div>

            <!-- Used oil -->
            <div v-else-if="active === 'oil'" class="space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <label class="block"><span :class="label">Litres collected</span><input v-model.number="oil.litres" type="number" min="0" step="any" :class="input" /></label>
                    <label class="block"><span :class="label">Rate per litre</span><input v-model.number="oil.rate" type="number" min="0" step="any" placeholder="optional" :class="input" /></label>
                </div>
                <p v-if="oilAmount" class="text-[13px] text-content-secondary">Value <Money :value="oilAmount" /></p>
                <input v-model="oil.notes" type="text" maxlength="300" placeholder="Drums, condition, anything else" :class="input" />
            </div>

            <!-- Return -->
            <div v-else class="space-y-3">
                <p v-if="!data.invoices.length" class="text-[13px] text-content-muted">No recent invoices with anything left to return.</p>
                <template v-else>
                    <label class="block">
                        <span :class="label">From invoice</span>
                        <select v-model.number="ret.transaction_id" :class="input">
                            <option v-for="i in data.invoices" :key="i.id" :value="i.id">{{ i.invoice_no }} · {{ i.date }}</option>
                        </select>
                    </label>
                    <table class="w-full text-[13px]">
                        <tr v-for="l in invoice?.lines ?? []" :key="l.sell_line_id" class="border-t border-edge-subtle">
                            <td class="py-1.5 text-content-primary">{{ l.name }}<span class="block text-xs text-content-muted">up to {{ l.returnable }}</span></td>
                            <td class="w-24 py-1.5"><input v-model.number="ret.qty[l.sell_line_id]" type="number" min="0" :max="l.returnable" step="any" :class="input" /></td>
                        </tr>
                    </table>
                    <input v-model="ret.reason" type="text" maxlength="190" placeholder="Why is it coming back? (required)" :class="input" />
                    <p v-if="returnValue" class="text-[13px] text-content-secondary">Credit value <Money :value="returnValue" /> · a manager approves before stock and balance change.</p>
                </template>
            </div>

            <p v-if="error" class="mt-3 rounded-md bg-danger/10 px-3 py-2 text-[13px] text-danger">{{ error }}</p>
        </template>

        <template #footer>
            <button type="button" class="rounded-md px-3 py-2 text-sm font-medium text-content-secondary hover:bg-surface-sunken" :disabled="busy" @click="emit('close')">Cancel</button>
            <button type="button" :disabled="!data || !canSubmit || busy" class="rounded-md bg-accent-500 px-4 py-2 text-sm font-semibold text-brand-950 hover:bg-accent-400 disabled:opacity-50" @click="submit">
                {{ busy ? 'Saving…' : submitLabel[active] }}
            </button>
        </template>
    </Modal>
</template>
