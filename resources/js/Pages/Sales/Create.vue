<script setup>
/**
 * Add Sale.
 *
 * The form gathers input and posts to the legacy SellPosController@store with
 * is_direct_sale = 1 and the field names it already understands, so stock,
 * invoice numbering, tax and credit-limit checks stay in one place. Submission
 * is a real form post rather than an Inertia visit: that endpoint answers a
 * direct sale with a redirect to a Blade page, which Inertia would reject.
 *
 * Line rows are filled from the server (pos/variation/...) rather than from
 * the search result, so price, unit and tax are whatever the backend says
 * they are for that location.
 */
import { computed, onMounted, ref, watch } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AppShell from '../../Layouts/AppShell.vue';
import Icon from '../../components/Icon.vue';
import Money from '../../components/Money.vue';
import Popover from '../../components/Popover.vue';
import { api, currentPosition } from '../../overlays/api';
import { openOverlay } from '../../overlays/store';
import { toast } from '../../overlays/toast';

const props = defineProps({
    locations: { type: Array, default: () => [] },
    defaultLocationId: { type: [Number, null], default: null },
    customers: { type: Array, default: () => [] },
    /** Opened from an overlay: { contact_id, lines: [{variation_id, quantity}], source } */
    prefill: { type: Object, default: () => ({ contact_id: null, lines: [], source: null }) },
    walkInCustomerId: { type: [Number, null], default: null },
    taxRates: { type: Array, default: () => [] },
    paymentTypes: { type: Array, default: () => [] },
    today: { type: String, required: true },
    dateFormat: { type: String, default: 'm/d/Y' },
    timeFormat: { type: Number, default: 24 },
    links: { type: Object, required: true },
});

/**
 * Render the picked datetime in the business's configured format.
 *
 * The store endpoint parses transaction_date with Carbon::createFromFormat
 * against that format, so an ISO string fails to parse and the whole sale is
 * swallowed by a generic "something went wrong". Only the tokens the app's
 * own date-format options use are supported.
 */
const toBusinessDate = (isoLocal) => {
    const d = new Date(isoLocal);
    if (Number.isNaN(d.getTime())) return '';

    const pad = (n) => String(n).padStart(2, '0');
    const hours24 = d.getHours();
    const hours12 = hours24 % 12 || 12;

    const tokens = {
        d: pad(d.getDate()),
        j: String(d.getDate()),
        m: pad(d.getMonth() + 1),
        n: String(d.getMonth() + 1),
        Y: String(d.getFullYear()),
        y: pad(d.getFullYear() % 100),
    };

    const datePart = props.dateFormat.replace(/[djmnYy]/g, (t) => tokens[t] ?? t);
    const timePart =
        props.timeFormat === 12
            ? `${pad(hours12)}:${pad(d.getMinutes())} ${hours24 < 12 ? 'AM' : 'PM'}`
            : `${pad(hours24)}:${pad(d.getMinutes())}`;

    return `${datePart} ${timePart}`;
};

// -----------------------------------------------------------------
// Form state
// -----------------------------------------------------------------

const locationId = ref(props.defaultLocationId);
const contactId = ref(props.prefill?.contact_id ?? props.walkInCustomerId);
const transactionDate = ref(props.today);
const status = ref('final');

const lines = ref([]);
const discountType = ref('fixed');
const discountAmount = ref(0);
const taxRateId = ref('');
const shippingCharges = ref(0);

const payAmount = ref(0);
const payMethod = ref('cash');
const payNote = ref('');

const saving = ref(false);
const error = ref(null);

// -----------------------------------------------------------------
// Customer picker
// -----------------------------------------------------------------

const customerQuery = ref('');
const customerOpen = ref(false);

const selectedCustomer = computed(() => props.customers.find((c) => c.id === contactId.value) ?? null);

const customerMatches = computed(() => {
    const q = customerQuery.value.trim().toLowerCase();
    const pool = props.customers;
    if (!q) return pool.slice(0, 50);

    return pool
        .filter(
            (c) =>
                c.name?.toLowerCase().includes(q) ||
                c.mobile?.toLowerCase().includes(q) ||
                c.code?.toLowerCase().includes(q),
        )
        .slice(0, 50);
});

const pickCustomer = (c) => {
    contactId.value = c.id;
    customerQuery.value = '';
    customerOpen.value = false;
};

// -----------------------------------------------------------------
// What this outlet usually buys, and what it has stopped buying
// -----------------------------------------------------------------

const pattern = ref(null);

const loadPattern = async (id) => {
    pattern.value = null;
    if (!id || id === props.walkInCustomerId) return;
    try {
        const r = await api(`outlets/${id}/pattern`);
        if (contactId.value === id) pattern.value = r;
    } catch {
        // Optional help; the sale works without it.
    }
};

watch(contactId, loadPattern, { immediate: true });

const inCart = (variationId) => lines.value.some((l) => l.variation_id === variationId);
const basketMissing = computed(() => (pattern.value?.basket ?? []).filter((b) => !inCart(b.variation_id)));

const addBasket = async () => {
    for (const b of basketMissing.value) {
        await addProduct({ variation_id: b.variation_id, name: b.name }, Number(b.quantity) || 1);
    }
};

// -----------------------------------------------------------------
// Product search
// -----------------------------------------------------------------

const productQuery = ref('');
const results = ref([]);
const searching = ref(false);
let searchTimer;

const search = () => {
    clearTimeout(searchTimer);
    const term = productQuery.value.trim();

    if (!term) {
        results.value = [];
        return;
    }

    searchTimer = setTimeout(async () => {
        searching.value = true;
        try {
            const url = new URL(props.links.searchProducts, window.location.origin);
            url.searchParams.set('term', term);
            if (locationId.value) url.searchParams.set('location_id', locationId.value);

            const res = await fetch(url, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            results.value = await res.json();
        } catch {
            results.value = [];
        } finally {
            searching.value = false;
        }
    }, 250);
};

// Prices and stock are per location, so a change invalidates what is shown.
watch(locationId, () => {
    results.value = [];
    if (productQuery.value) search();
});

/**
 * Ask the server for the row rather than trusting the search payload: it is
 * the same endpoint the legacy screen used, and it returns the unit, tax and
 * location-specific price the backend will price the line at.
 */
const addProduct = async (hit, quantity = 1) => {
    error.value = null;

    try {
        const res = await fetch(`${props.links.variation}/${hit.variation_id}/${locationId.value}`, {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        });

        if (!res.ok) throw new Error('Could not load that product.');
        const d = await res.json();

        const existing = lines.value.find((l) => l.variation_id === d.variation_id);
        if (existing) {
            existing.quantity += quantity;
        } else {
            const excl = Number(d.default_sell_price) || 0;
            const incl = Number(d.sell_price_inc_tax) || excl;

            lines.value.push({
                product_id: d.product_id,
                variation_id: d.variation_id,
                name: d.product_name ?? hit.name,
                sku: d.sub_sku ?? hit.sub_sku,
                unit: d.unit ?? hit.unit,
                unit_id: d.unit_id ?? '',
                enable_stock: d.enable_stock ?? 0,
                product_type: d.product_type ?? 'single',
                tax_id: d.tax_id ?? '',
                qty_available: Number(d.qty_available) || 0,
                quantity,
                unit_price: excl,
                unit_price_inc_tax: incl,
                // Per-unit tax, which is what the backend expects in item_tax.
                item_tax: Math.max(0, incl - excl),
            });
        }

        productQuery.value = '';
        results.value = [];
    } catch (e) {
        error.value = e.message ?? 'Could not add that product.';
    }
};

const removeLine = (i) => lines.value.splice(i, 1);

/** Lines handed over by an overlay (usual basket, repeat of an invoice). */
const prefillNote = ref(null);
onMounted(async () => {
    const wanted = props.prefill?.lines ?? [];
    if (!wanted.length || !locationId.value) return;

    for (const w of wanted) {
        await addProduct({ variation_id: w.variation_id, name: '' }, Number(w.quantity) || 1);
    }
    prefillNote.value =
        props.prefill.source === 'repeat'
            ? 'Filled from the earlier invoice. Check quantities before saving.'
            : "Filled with this outlet's usual order. Adjust quantities to what they need today.";
});

// -----------------------------------------------------------------
// Totals
// -----------------------------------------------------------------

// -----------------------------------------------------------------
// Trade schemes: evaluated on the server, applied to the lines here
// -----------------------------------------------------------------

const schemes = ref({ applied: [], hints: [], targets: [] });
let schemeTimer;

const evaluateSchemes = () => {
    clearTimeout(schemeTimer);
    schemeTimer = setTimeout(async () => {
        try {
            schemes.value = await api('schemes/evaluate', {
                method: 'POST',
                body: {
                    contact_id: contactId.value && contactId.value !== props.walkInCustomerId ? contactId.value : null,
                    lines: lines.value.map((l) => ({
                        variation_id: l.variation_id,
                        quantity: Number(l.quantity) || 0,
                        unit_price_inc_tax: Number(l.unit_price_inc_tax) || 0,
                    })),
                },
            });
        } catch {
            // Schemes are a bonus; the sale works without them.
        }
    }, 350);
};

watch(() => [contactId.value, lines.value.map((l) => `${l.variation_id}:${l.quantity}:${l.unit_price_inc_tax}`).join('|')], evaluateSchemes, {
    immediate: true,
});

/** Free units and the combined % off a line gets from the schemes applied. */
const effectsFor = (variationId) => {
    let free = 0;
    let keep = 1;
    const names = [];
    for (const a of schemes.value.applied) {
        for (const e of a.effects) {
            if (e.variation_id !== variationId) continue;
            if (e.kind === 'free') free += e.quantity;
            if (e.kind === 'discount') keep *= 1 - e.percent / 100;
            if (!names.includes(a.summary)) names.push(a.summary);
        }
    }
    return { free, off: 1 - keep, names };
};

// A "get a different item free" scheme needs that item on the invoice.
watch(
    () => schemes.value.applied,
    async (applied) => {
        for (const a of applied) {
            for (const e of a.effects) {
                if (e.kind === 'free' && !lines.value.some((l) => l.variation_id === e.variation_id)) {
                    await addProduct({ variation_id: e.variation_id, name: '' }, 0);
                }
            }
        }
    },
);

const schemeSavings = computed(() =>
    lines.value.reduce((sum, l) => {
        const e = effectsFor(l.variation_id);
        const incl = Number(l.unit_price_inc_tax) || 0;
        return sum + (Number(l.quantity) || 0) * incl * e.off + e.free * incl;
    }, 0),
);

const lineTotal = (l) => (Number(l.quantity) || 0) * (Number(l.unit_price_inc_tax) || 0) * (1 - effectsFor(l.variation_id).off);

const subtotal = computed(() => lines.value.reduce((sum, l) => sum + lineTotal(l), 0));

const discountValue = computed(() => {
    const amount = Number(discountAmount.value) || 0;
    return discountType.value === 'percentage' ? (subtotal.value * amount) / 100 : amount;
});

const taxValue = computed(() => {
    const rate = props.taxRates.find((t) => String(t.id) === String(taxRateId.value))?.rate ?? 0;
    return ((subtotal.value - discountValue.value) * rate) / 100;
});

const finalTotal = computed(
    () => subtotal.value - discountValue.value + taxValue.value + (Number(shippingCharges.value) || 0),
);

const balanceDue = computed(() => finalTotal.value - (Number(payAmount.value) || 0));

/** How many adjustments are actually in play, for the popover's badge. */
const adjustmentCount = computed(
    () =>
        [Number(discountAmount.value) > 0, Boolean(taxRateId.value), Number(shippingCharges.value) > 0].filter(
            Boolean,
        ).length,
);

/** Paying in full is the common case, so make it one click. */
const payFull = () => (payAmount.value = Number(finalTotal.value.toFixed(2)));

// -----------------------------------------------------------------
// Submit
// -----------------------------------------------------------------

const canSave = computed(() => lines.value.length > 0 && locationId.value && contactId.value);

/** A manager's credit override, redeemed by the store endpoint. */
const overrideToken = ref(null);

/**
 * Run the credit rules before posting. The store endpoint enforces the same
 * rules, but it answers this screen with a redirect that loses the details;
 * checking first lets the credit-hold modal show the maths and a way out.
 */
const creditCleared = async () => {
    if (overrideToken.value || balanceDue.value <= 0.004 || contactId.value === props.walkInCustomerId) return true;

    let r;
    try {
        r = await api('credit/check', { method: 'POST', body: { contact_id: contactId.value, amount_due: Number(balanceDue.value.toFixed(2)) } });
    } catch {
        return true; // Let the server decide rather than block on a failed check.
    }
    if (r.ok) return true;

    openOverlay('creditHold', {
        hold: r.hold,
        onResolve: (res) => {
            if (res.action === 'pay') {
                payAmount.value = Number(Math.min(finalTotal.value, (Number(payAmount.value) || 0) + res.amount).toFixed(2));
                error.value = null;
                toast('Payment amount raised. Check the method, then save again.', { tone: 'info' });
            } else if (res.action === 'reduce') {
                error.value = res.available > 0
                    ? `This outlet can take up to ${res.available.toFixed(0)} more on credit. Reduce the order or take a payment.`
                    : 'This outlet can only buy for cash until its overdue invoices are paid.';
            } else if (res.action === 'override') {
                overrideToken.value = res.token;
                toast(`Override approved by ${res.approver}. Saving the sale.`);
                submit('final');
            }
        },
    });
    return false;
};

/** Proof of presence for a field seller; the store endpoint checks it on enforced routes. */
const checkinToken = ref(null);
const page = usePage();

const checkedIn = async () => {
    if (checkinToken.value || !page.props.ops?.fieldUser || contactId.value === props.walkInCustomerId) return true;

    let position = null;
    try {
        position = await currentPosition();
    } catch {
        position = null; // The server records "no location" and asks for a reason.
    }

    let r;
    try {
        r = await api('checkin', {
            method: 'POST',
            body: { contact_id: contactId.value, action: 'place_order', lat: position?.lat ?? null, lng: position?.lng ?? null, accuracy: position?.accuracy ?? null },
        });
    } catch {
        return true;
    }

    if (r.allowed) {
        checkinToken.value = r.token;
        if (r.pinned) toast('Outlet location saved from this visit.', { tone: 'info' });
        return true;
    }

    openOverlay('checkIn', {
        contactId: contactId.value,
        contactName: selectedCustomer.value?.name,
        action: 'place_order',
        result: r,
        position,
        onResolve: (token) => {
            checkinToken.value = token;
            submit('final');
        },
    });
    return false;
};

// A different outlet needs its own check-in.
watch(contactId, () => {
    checkinToken.value = null;
    overrideToken.value = null;
});

const submit = async (saveAs) => {
    if (!canSave.value || saving.value) return;

    if (saveAs === 'final') {
        saving.value = true;
        const cleared = (await checkedIn()) && (await creditCleared());
        saving.value = false;
        if (!cleared) return;
    }

    saving.value = true;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = props.links.store;
    form.style.display = 'none';

    const add = (name, value) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value ?? '';
        form.appendChild(input);
    };

    add('_token', document.querySelector('meta[name="csrf-token"]')?.content ?? '');
    add('is_direct_sale', 1);
    add('status', saveAs);
    add('sale_type', 'sell');
    add('location_id', locationId.value);
    add('contact_id', contactId.value);
    add('transaction_date', toBusinessDate(transactionDate.value));
    add('discount_type', discountType.value);
    add('discount_amount', Number(discountAmount.value) || 0);
    add('tax_rate_id', taxRateId.value);
    add('shipping_charges', Number(shippingCharges.value) || 0);
    add('final_total', finalTotal.value.toFixed(2));
    if (overrideToken.value) add('credit_override_token', overrideToken.value);
    if (checkinToken.value) add('checkin_token', checkinToken.value);

    lines.value.forEach((l, i) => {
        // Free units ride as extra quantity; the paid value is spread over
        // all units as a percentage line discount, which is how the store
        // endpoint prices a discounted line (unit_price is before discount,
        // unit_price_inc_tax and item_tax after it).
        const e = effectsFor(l.variation_id);
        const paid = Number(l.quantity) || 0;
        const posted = paid + e.free;
        const incl = Number(l.unit_price_inc_tax) || 0;
        const perUnitInc = posted > 0 ? (paid * incl * (1 - e.off)) / posted : incl;
        const pct = incl > 0 ? (1 - perUnitInc / incl) * 100 : 0;
        const perUnitExcl = (Number(l.unit_price) || 0) * (1 - pct / 100);

        // A free-item line whose scheme no longer applies is left at zero.
        if (posted <= 0) return;

        add(`products[${i}][product_id]`, l.product_id);
        add(`products[${i}][variation_id]`, l.variation_id);
        add(`products[${i}][quantity]`, posted);
        add(`products[${i}][scheme_free_qty]`, e.free);
        add(`products[${i}][unit_price]`, l.unit_price);
        add(`products[${i}][unit_price_inc_tax]`, perUnitInc.toFixed(4));
        add(`products[${i}][item_tax]`, pct > 0.0001 ? Math.max(0, perUnitInc - perUnitExcl).toFixed(4) : l.item_tax);
        // Always sent, even when empty: the backend reads the key
        // unconditionally, and Laravel's ConvertEmptyStringsToNull turns the
        // blank into the null the tax_rates foreign key needs. Omitting it
        // throws "Undefined array key"; sending "" would insert 0 and break
        // the constraint. Both surface only as "something went wrong".
        add(`products[${i}][tax_id]`, l.tax_id);
        add(`products[${i}][enable_stock]`, l.enable_stock);
        add(`products[${i}][product_type]`, l.product_type);
        add(`products[${i}][product_unit_id]`, l.unit_id);
        add(`products[${i}][base_unit_multiplier]`, 1);
        add(`products[${i}][line_discount_type]`, pct > 0.0001 ? 'percentage' : 'fixed');
        add(`products[${i}][line_discount_amount]`, pct > 0.0001 ? pct.toFixed(4) : 0);
        add(`products[${i}][sell_line_note]`, '');
    });

    // A draft or quotation is not paid for at the point it is raised.
    if (saveAs === 'final' && Number(payAmount.value) > 0) {
        add('payment[0][amount]', Number(payAmount.value));
        add('payment[0][method]', payMethod.value);
        add('payment[0][paid_on]', toBusinessDate(transactionDate.value));
        add('payment[0][note]', payNote.value);
    }

    document.body.appendChild(form);
    form.submit();
};
</script>

<template>
    <Head title="Add sale" />

    <AppShell title="Add sale" fill>
        <div class="mx-auto flex h-full w-full max-w-[1400px] flex-col p-4 sm:px-6 sm:py-4">
            <div class="mb-3 flex shrink-0 flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-xl font-semibold text-content-primary">Add sale</h1>
                    <p class="mt-0.5 text-[13px] text-content-muted">
                        {{ lines.length }} {{ lines.length === 1 ? 'item' : 'items' }} on this invoice
                    </p>
                </div>
                <a
                    :href="links.cancel"
                    class="rounded-md border border-edge-subtle px-3 py-2 text-sm font-medium text-content-secondary hover:bg-surface-sunken hover:text-content-primary"
                >
                    Cancel
                </a>
            </div>

            <div v-if="error" class="mb-4 rounded-lg border border-danger/30 bg-danger/5 px-4 py-3 text-sm text-danger">
                {{ error }}
            </div>
            <div v-if="prefillNote" class="mb-3 flex items-center gap-2 rounded-lg border border-accent-500/40 bg-accent-500/10 px-4 py-2 text-[13px] text-content-primary">
                <Icon name="receipt" :size="15" class="shrink-0" />
                <span class="flex-1">{{ prefillNote }}</span>
                <button type="button" class="text-content-muted hover:text-content-primary" aria-label="Dismiss" @click="prefillNote = null">
                    <Icon name="close" :size="14" />
                </button>
            </div>

            <div class="grid min-h-0 flex-1 gap-4 lg:grid-cols-[1fr_340px]">
                <!-- Left: who, what -->
                <div class="flex min-h-0 flex-col gap-4">
                    <!-- Header fields -->
                    <section class="shrink-0 rounded-lg border border-edge-subtle bg-surface-raised p-4">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <label class="block">
                                <span class="mb-1 block text-xs font-medium uppercase tracking-wide text-content-muted">Location</span>
                                <select
                                    v-model.number="locationId"
                                    class="w-full rounded-md border border-edge-subtle bg-surface-page px-3 py-2 text-sm text-content-primary focus:border-accent-500 focus:outline-none"
                                >
                                    <option v-for="l in locations" :key="l.id" :value="l.id">{{ l.name }}</option>
                                </select>
                            </label>

                            <label class="block">
                                <span class="mb-1 block text-xs font-medium uppercase tracking-wide text-content-muted">Date</span>
                                <input
                                    v-model="transactionDate"
                                    type="datetime-local"
                                    class="w-full rounded-md border border-edge-subtle bg-surface-page px-3 py-2 text-sm text-content-primary focus:border-accent-500 focus:outline-none"
                                />
                            </label>
                        </div>

                        <!-- Customer -->
                        <div class="relative mt-3">
                            <span class="mb-1 block text-xs font-medium uppercase tracking-wide text-content-muted">Customer</span>

                            <button
                                type="button"
                                class="flex w-full items-center justify-between rounded-md border border-edge-subtle bg-surface-page px-3 py-2 text-left text-sm text-content-primary hover:border-accent-500"
                                @click="customerOpen = !customerOpen"
                            >
                                <span>
                                    {{ selectedCustomer?.name ?? 'Select a customer' }}
                                    <span v-if="selectedCustomer?.code" class="text-xs text-content-muted">
                                        · {{ selectedCustomer.code }}
                                    </span>
                                </span>
                                <Icon name="chevronDown" :size="15" />
                            </button>

                            <div
                                v-if="customerOpen"
                                class="absolute z-20 mt-1 w-full overflow-hidden rounded-lg border border-edge-subtle bg-surface-raised shadow-lg"
                            >
                                <input
                                    v-model="customerQuery"
                                    type="search"
                                    placeholder="Search name, code or mobile"
                                    class="w-full border-b border-edge-subtle bg-surface-raised px-3 py-2 text-sm text-content-primary placeholder:text-content-muted focus:outline-none"
                                />
                                <ul class="max-h-64 overflow-y-auto">
                                    <li v-for="c in customerMatches" :key="c.id">
                                        <button
                                            type="button"
                                            class="flex w-full flex-col px-3 py-1.5 text-left hover:bg-surface-sunken"
                                            @click="pickCustomer(c)"
                                        >
                                            <span class="text-[13px] text-content-primary">{{ c.name }}</span>
                                            <span v-if="c.mobile || c.code" class="text-xs text-content-muted">
                                                {{ [c.code, c.mobile].filter(Boolean).join(' · ') }}
                                            </span>
                                        </button>
                                    </li>
                                    <li v-if="!customerMatches.length" class="px-3 py-3 text-[13px] text-content-muted">
                                        No customer matches that.
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- What this outlet usually takes, and what it has gone quiet on. -->
                        <div v-if="pattern && (pattern.basket.length || pattern.stopped.length || pattern.rhythm.last_order_on)"
                            class="mt-3 rounded-md border border-edge-subtle bg-surface-page px-3 py-2.5">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="flex-1 text-xs text-content-muted">
                                    <template v-if="pattern.rhythm.interval_days">
                                        Orders about every {{ Math.round(pattern.rhythm.interval_days) }} days · last {{ pattern.rhythm.days_since }} days ago
                                        <span v-if="pattern.rhythm.due" class="ml-1 font-semibold text-accent-700 dark:text-accent-300">due now</span>
                                    </template>
                                    <template v-else-if="pattern.rhythm.last_order_on">Last order {{ pattern.rhythm.days_since }} days ago</template>
                                </p>
                                <button
                                    v-if="basketMissing.length"
                                    type="button"
                                    class="rounded-md border border-accent-500 bg-accent-500/10 px-2.5 py-1 text-xs font-semibold text-content-primary hover:bg-accent-500/20"
                                    @click="addBasket"
                                >
                                    Add usual order · {{ basketMissing.length }} {{ basketMissing.length === 1 ? 'item' : 'items' }}
                                </button>
                                <button v-if="contactId" type="button" class="text-xs font-medium text-brand-600 hover:underline dark:text-brand-300" @click="openOverlay('outlet', { id: contactId })">
                                    Outlet 360
                                </button>
                            </div>
                            <div v-if="pattern.stopped.length" class="mt-2 flex flex-wrap gap-1.5">
                                <button
                                    v-for="s in pattern.stopped"
                                    :key="s.variation_id"
                                    type="button"
                                    class="rounded-full border border-warning/40 bg-warning/10 px-2.5 py-0.5 text-xs text-content-primary hover:border-warning"
                                    :title="`Usually every ${Math.round(s.interval_days)} days; last ${s.days_since} days ago. Click to add.`"
                                    @click="addProduct({ variation_id: s.variation_id, name: s.name })"
                                >
                                    Ask about {{ s.name }} · {{ s.days_since }}d
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- Products -->
                    <section class="flex min-h-0 flex-1 flex-col rounded-lg border border-edge-subtle bg-surface-raised">
                        <div class="relative shrink-0 border-b border-edge-subtle p-4">
                            <span class="pointer-events-none absolute left-7 top-[calc(50%+2px)] -translate-y-1/2 text-content-muted">
                                <Icon name="search" :size="16" />
                            </span>
                            <input
                                v-model="productQuery"
                                type="search"
                                placeholder="Search a product by name or SKU to add it"
                                class="w-full rounded-md border border-edge-subtle bg-surface-page py-2 pl-9 pr-3 text-sm text-content-primary placeholder:text-content-muted focus:border-accent-500 focus:outline-none"
                                @input="search"
                            />

                            <ul
                                v-if="results.length"
                                class="absolute left-4 right-4 z-20 mt-1 max-h-72 overflow-y-auto rounded-lg border border-edge-subtle bg-surface-raised shadow-lg"
                            >
                                <li v-for="hit in results" :key="hit.variation_id">
                                    <button
                                        type="button"
                                        class="flex w-full items-center justify-between gap-3 px-3 py-2 text-left hover:bg-surface-sunken"
                                        @click="addProduct(hit)"
                                    >
                                        <span>
                                            <span class="block text-[13px] text-content-primary">{{ hit.name }}</span>
                                            <span class="block text-xs text-content-muted">{{ hit.sub_sku }}</span>
                                        </span>
                                        <span class="shrink-0 text-right">
                                            <span class="block text-[13px] text-content-primary">
                                                <Money :value="hit.selling_price" />
                                            </span>
                                            <span
                                                v-if="Number(hit.enable_stock)"
                                                class="block text-xs"
                                                :class="Number(hit.qty_available) > 0 ? 'text-content-muted' : 'text-danger'"
                                            >
                                                {{ Number(hit.qty_available) }} in stock
                                            </span>
                                        </span>
                                    </button>
                                </li>
                            </ul>

                            <p v-if="searching" class="mt-2 text-xs text-content-muted">Searching…</p>
                        </div>

                        <div class="scrollbar-slim min-h-0 flex-1 overflow-auto">
                            <table class="w-full min-w-[640px] border-collapse text-sm">
                                <thead>
                                    <tr class="sticky top-0 z-10 border-b border-edge-subtle bg-surface-sunken text-left">
                                        <th class="px-4 py-2 text-xs font-semibold uppercase tracking-wide text-content-muted">Product</th>
                                        <th class="w-28 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-content-muted">Qty</th>
                                        <th class="w-32 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-content-muted">Unit price</th>
                                        <th class="w-32 px-3 py-2 text-right text-xs font-semibold uppercase tracking-wide text-content-muted">Total</th>
                                        <th class="w-10"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(l, i) in lines" :key="l.variation_id" class="border-b border-edge-subtle last:border-0">
                                        <td class="px-4 py-2">
                                            <div class="text-content-primary">{{ l.name }}</div>
                                            <div class="text-xs text-content-muted">
                                                {{ l.sku }}
                                                <span v-if="Number(l.enable_stock)">· {{ l.qty_available }} {{ l.unit }} in stock</span>
                                            </div>
                                            <div
                                                v-if="Number(l.enable_stock) && l.quantity + effectsFor(l.variation_id).free > l.qty_available"
                                                class="mt-0.5 text-xs text-danger"
                                            >
                                                More than the stock on hand
                                            </div>
                                            <div v-if="effectsFor(l.variation_id).names.length" class="mt-1 flex flex-wrap gap-1">
                                                <span v-if="effectsFor(l.variation_id).free" class="rounded-full bg-success/10 px-2 py-0.5 text-[11px] font-semibold text-success">
                                                    +{{ effectsFor(l.variation_id).free }} free
                                                </span>
                                                <span v-for="n in effectsFor(l.variation_id).names" :key="n" class="rounded-full bg-brand-600/10 px-2 py-0.5 text-[11px] text-brand-700 dark:text-brand-300">
                                                    {{ n }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2">
                                            <input
                                                v-model.number="l.quantity"
                                                type="number"
                                                min="0"
                                                step="any"
                                                class="w-24 rounded-md border border-edge-subtle bg-surface-page px-2 py-1 text-sm text-content-primary focus:border-accent-500 focus:outline-none"
                                            />
                                        </td>
                                        <td class="px-3 py-2">
                                            <input
                                                v-model.number="l.unit_price_inc_tax"
                                                type="number"
                                                min="0"
                                                step="any"
                                                class="w-28 rounded-md border border-edge-subtle bg-surface-page px-2 py-1 text-sm text-content-primary focus:border-accent-500 focus:outline-none"
                                            />
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-2 text-right text-content-primary">
                                            <Money :value="lineTotal(l)" />
                                        </td>
                                        <td class="px-2 py-2 text-right">
                                            <button
                                                type="button"
                                                class="rounded p-1 text-content-muted hover:bg-danger/10 hover:text-danger"
                                                :aria-label="`Remove ${l.name}`"
                                                @click="removeLine(i)"
                                            >
                                                <Icon name="close" :size="15" />
                                            </button>
                                        </td>
                                    </tr>

                                    <tr v-if="!lines.length">
                                        <td colspan="5" class="px-4 py-12 text-center">
                                            <div class="text-sm font-medium text-content-primary">Nothing on this invoice yet</div>
                                            <p class="mt-1 text-[13px] text-content-muted">
                                                Search above to add the first product.
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>

                <!-- Right: money -->
                <aside class="scrollbar-slim min-h-0 space-y-4 overflow-y-auto pl-1">
                    <section class="rounded-lg border border-edge-subtle bg-surface-raised p-4">
                        <div class="mb-3 flex items-center justify-between">
                            <h2 class="text-xs font-semibold uppercase tracking-wide text-content-muted">Totals</h2>

                            <!--
                                Discount, tax and shipping are set on a minority
                                of invoices but were taking three permanent rows
                                between Subtotal and Total, burying the two
                                numbers anyone actually reads. Behind a control
                                that reports when they are in play.
                            -->
                            <Popover :width="280">
                                <template #trigger="{ open, toggle }">
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1.5 rounded-md border px-2 py-1 text-xs font-medium"
                                        :class="
                                            adjustmentCount || open
                                                ? 'border-accent-500 bg-accent-500/10 text-content-primary'
                                                : 'border-edge-subtle text-content-secondary hover:bg-surface-sunken hover:text-content-primary'
                                        "
                                        @click="toggle"
                                    >
                                        <Icon name="settings" :size="13" />
                                        Adjustments
                                        <span
                                            v-if="adjustmentCount"
                                            class="rounded bg-accent-500 px-1 text-[11px] font-semibold text-brand-950"
                                        >
                                            {{ adjustmentCount }}
                                        </span>
                                    </button>
                                </template>

                                <div class="space-y-3 p-3">
                                    <div>
                                        <span class="mb-1 block text-xs font-medium text-content-muted">Discount</span>
                                        <div class="flex items-center gap-1">
                                            <select
                                                v-model="discountType"
                                                aria-label="Discount type"
                                                class="rounded border border-edge-subtle bg-surface-page px-1.5 py-1.5 text-xs text-content-secondary focus:outline-none"
                                            >
                                                <option value="fixed">Fixed</option>
                                                <option value="percentage">%</option>
                                            </select>
                                            <input
                                                v-model.number="discountAmount"
                                                type="number"
                                                min="0"
                                                step="any"
                                                aria-label="Discount amount"
                                                class="w-full rounded border border-edge-subtle bg-surface-page px-2 py-1.5 text-right text-sm text-content-primary focus:outline-none"
                                            />
                                        </div>
                                    </div>

                                    <label v-if="taxRates.length" class="block">
                                        <span class="mb-1 block text-xs font-medium text-content-muted">Order tax</span>
                                        <select
                                            v-model="taxRateId"
                                            class="w-full rounded border border-edge-subtle bg-surface-page px-2 py-1.5 text-sm text-content-secondary focus:outline-none"
                                        >
                                            <option value="">None</option>
                                            <option v-for="t in taxRates" :key="t.id" :value="t.id">{{ t.name }}</option>
                                        </select>
                                    </label>

                                    <label class="block">
                                        <span class="mb-1 block text-xs font-medium text-content-muted">Shipping</span>
                                        <input
                                            v-model.number="shippingCharges"
                                            type="number"
                                            min="0"
                                            step="any"
                                            class="w-full rounded border border-edge-subtle bg-surface-page px-2 py-1.5 text-right text-sm text-content-primary focus:outline-none"
                                        />
                                    </label>
                                </div>
                            </Popover>
                        </div>

                        <dl class="space-y-2 text-sm">
                            <div class="flex items-center justify-between">
                                <dt class="text-content-muted">Subtotal</dt>
                                <dd class="text-content-primary"><Money :value="subtotal" /></dd>
                            </div>

                            <!-- Shown only once set, so they read as exceptions. -->
                            <div v-if="discountValue" class="flex items-center justify-between">
                                <dt class="text-content-muted">
                                    Discount
                                    <span v-if="discountType === 'percentage'" class="text-xs">
                                        ({{ discountAmount }}%)
                                    </span>
                                </dt>
                                <dd class="text-content-secondary">−<Money :value="discountValue" /></dd>
                            </div>

                            <div v-if="taxValue" class="flex items-center justify-between">
                                <dt class="text-content-muted">Order tax</dt>
                                <dd class="text-content-secondary"><Money :value="taxValue" /></dd>
                            </div>

                            <div v-if="Number(shippingCharges)" class="flex items-center justify-between">
                                <dt class="text-content-muted">Shipping</dt>
                                <dd class="text-content-secondary"><Money :value="shippingCharges" /></dd>
                            </div>

                            <div v-if="schemeSavings > 0.004" class="flex items-center justify-between">
                                <dt class="text-content-muted">Scheme savings</dt>
                                <dd class="text-success">−<Money :value="schemeSavings" /></dd>
                            </div>

                            <div class="flex items-center justify-between border-t border-edge-subtle pt-2">
                                <dt class="font-medium text-content-secondary">Total</dt>
                                <dd class="text-lg font-semibold text-content-primary"><Money :value="finalTotal" /></dd>
                            </div>
                        </dl>
                    </section>

                    <section
                        v-if="schemes.applied.length || schemes.hints.length || schemes.targets.length"
                        class="rounded-lg border border-edge-subtle bg-surface-raised p-4"
                    >
                        <h2 class="mb-2 text-xs font-semibold uppercase tracking-wide text-content-muted">Schemes</h2>
                        <ul class="space-y-1.5 text-[13px]">
                            <li v-for="a in schemes.applied" :key="`a${a.scheme_id}`" class="flex justify-between gap-2">
                                <span class="text-content-primary">{{ a.name }} <span class="text-xs text-content-muted">· {{ a.summary }}</span></span>
                                <Money v-if="a.benefit" :value="a.benefit" compact class="shrink-0 text-success" />
                            </li>
                            <li v-for="h in schemes.hints" :key="`h${h.scheme_id}`" class="text-xs font-medium text-accent-700 dark:text-accent-300">
                                {{ h.name }}: {{ h.message }}
                            </li>
                        </ul>
                        <div v-for="t in schemes.targets" :key="`t${t.id}`" class="mt-3">
                            <div class="flex justify-between text-xs text-content-muted">
                                <span>{{ t.name }}<template v-if="t.next_tier"> → {{ t.next_tier }}</template></span>
                                <span>{{ t.ends_label }}</span>
                            </div>
                            <div class="my-1 h-1.5 overflow-hidden rounded-full bg-surface-sunken">
                                <div class="h-full bg-brand-600" :style="{ width: `${t.percent}%` }"></div>
                            </div>
                            <p class="text-xs text-content-secondary numeric">
                                {{ t.achieved }} of {{ t.target }} {{ t.unit }}<template v-if="t.remaining > 0"> · {{ t.remaining }} to go</template>
                            </p>
                        </div>
                    </section>

                    <section class="rounded-lg border border-edge-subtle bg-surface-raised p-4">
                        <h2 class="mb-3 text-xs font-semibold uppercase tracking-wide text-content-muted">Payment</h2>

                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <input
                                    v-model.number="payAmount"
                                    type="number"
                                    min="0"
                                    step="any"
                                    aria-label="Amount paid"
                                    class="w-full rounded-md border border-edge-subtle bg-surface-page px-3 py-2 text-sm text-content-primary focus:border-accent-500 focus:outline-none"
                                />
                                <button
                                    type="button"
                                    class="shrink-0 rounded-md border border-edge-subtle px-2 py-2 text-xs font-medium text-content-secondary hover:bg-surface-sunken"
                                    @click="payFull"
                                >
                                    Full
                                </button>
                            </div>

                            <select
                                v-model="payMethod"
                                aria-label="Payment method"
                                class="w-full rounded-md border border-edge-subtle bg-surface-page px-3 py-2 text-sm text-content-primary focus:border-accent-500 focus:outline-none"
                            >
                                <option v-for="p in paymentTypes" :key="p.value" :value="p.value">{{ p.label }}</option>
                            </select>

                            <input
                                v-model="payNote"
                                type="text"
                                placeholder="Payment note (optional)"
                                class="w-full rounded-md border border-edge-subtle bg-surface-page px-3 py-2 text-sm text-content-primary placeholder:text-content-muted focus:border-accent-500 focus:outline-none"
                            />

                            <div class="flex justify-between border-t border-edge-subtle pt-2 text-sm">
                                <span class="text-content-muted">Balance due</span>
                                <span :class="balanceDue > 0 ? 'font-medium text-danger' : 'text-content-primary'">
                                    <Money :value="balanceDue" />
                                </span>
                            </div>
                        </div>
                    </section>

                    <div class="space-y-2">
                        <button
                            type="button"
                            class="w-full rounded-md bg-accent-500 px-3 py-2.5 text-sm font-semibold text-brand-950 hover:bg-accent-400 disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="!canSave || saving"
                            @click="submit('final')"
                        >
                            {{ saving ? 'Saving…' : 'Save sale' }}
                        </button>

                        <div class="grid grid-cols-2 gap-2">
                            <button
                                type="button"
                                class="rounded-md border border-edge-subtle px-3 py-2 text-sm font-medium text-content-secondary hover:bg-surface-sunken disabled:opacity-50"
                                :disabled="!canSave || saving"
                                @click="submit('draft')"
                            >
                                Save draft
                            </button>
                            <button
                                type="button"
                                class="rounded-md border border-edge-subtle px-3 py-2 text-sm font-medium text-content-secondary hover:bg-surface-sunken disabled:opacity-50"
                                :disabled="!canSave || saving"
                                @click="submit('quotation')"
                            >
                                Quotation
                            </button>
                        </div>

                        <p v-if="!canSave" class="text-center text-xs text-content-muted">
                            Add at least one product and pick a customer.
                        </p>
                    </div>
                </aside>
            </div>
        </div>
    </AppShell>
</template>
