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
import { computed, ref, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppShell from '../../Layouts/AppShell.vue';
import Icon from '../../components/Icon.vue';
import Money from '../../components/Money.vue';

const props = defineProps({
    locations: { type: Array, default: () => [] },
    defaultLocationId: { type: [Number, null], default: null },
    customers: { type: Array, default: () => [] },
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
const contactId = ref(props.walkInCustomerId);
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
const addProduct = async (hit) => {
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
            existing.quantity += 1;
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
                quantity: 1,
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

// -----------------------------------------------------------------
// Totals
// -----------------------------------------------------------------

const lineTotal = (l) => (Number(l.quantity) || 0) * (Number(l.unit_price_inc_tax) || 0);

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

/** Paying in full is the common case, so make it one click. */
const payFull = () => (payAmount.value = Number(finalTotal.value.toFixed(2)));

// -----------------------------------------------------------------
// Submit
// -----------------------------------------------------------------

const canSave = computed(() => lines.value.length > 0 && locationId.value && contactId.value);

const submit = (saveAs) => {
    if (!canSave.value || saving.value) return;
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

    lines.value.forEach((l, i) => {
        add(`products[${i}][product_id]`, l.product_id);
        add(`products[${i}][variation_id]`, l.variation_id);
        add(`products[${i}][quantity]`, l.quantity);
        add(`products[${i}][unit_price]`, l.unit_price);
        add(`products[${i}][unit_price_inc_tax]`, l.unit_price_inc_tax);
        add(`products[${i}][item_tax]`, l.item_tax);
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
        add(`products[${i}][line_discount_type]`, 'fixed');
        add(`products[${i}][line_discount_amount]`, 0);
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

    <AppShell title="Add sale">
        <div class="mx-auto w-full max-w-[1400px] p-4 sm:p-6">
            <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
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

            <div class="grid gap-4 lg:grid-cols-[1fr_340px]">
                <!-- Left: who, what -->
                <div class="space-y-4">
                    <!-- Header fields -->
                    <section class="rounded-lg border border-edge-subtle bg-surface-raised p-4">
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
                    </section>

                    <!-- Products -->
                    <section class="rounded-lg border border-edge-subtle bg-surface-raised">
                        <div class="relative border-b border-edge-subtle p-4">
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

                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[640px] border-collapse text-sm">
                                <thead>
                                    <tr class="border-b border-edge-subtle bg-surface-sunken text-left">
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
                                                v-if="Number(l.enable_stock) && l.quantity > l.qty_available"
                                                class="mt-0.5 text-xs text-danger"
                                            >
                                                More than the stock on hand
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
                <aside class="space-y-4">
                    <section class="rounded-lg border border-edge-subtle bg-surface-raised p-4">
                        <h2 class="mb-3 text-xs font-semibold uppercase tracking-wide text-content-muted">Totals</h2>

                        <dl class="space-y-2 text-sm">
                            <div class="flex items-center justify-between">
                                <dt class="text-content-muted">Subtotal</dt>
                                <dd class="text-content-primary"><Money :value="subtotal" /></dd>
                            </div>

                            <div class="flex items-center justify-between gap-2">
                                <dt class="text-content-muted">Discount</dt>
                                <dd class="flex items-center gap-1">
                                    <select
                                        v-model="discountType"
                                        aria-label="Discount type"
                                        class="rounded border border-edge-subtle bg-surface-page px-1.5 py-1 text-xs text-content-secondary focus:outline-none"
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
                                        class="w-24 rounded border border-edge-subtle bg-surface-page px-2 py-1 text-right text-sm text-content-primary focus:outline-none"
                                    />
                                </dd>
                            </div>

                            <div v-if="taxRates.length" class="flex items-center justify-between gap-2">
                                <dt class="text-content-muted">Order tax</dt>
                                <dd>
                                    <select
                                        v-model="taxRateId"
                                        aria-label="Order tax"
                                        class="rounded border border-edge-subtle bg-surface-page px-2 py-1 text-sm text-content-secondary focus:outline-none"
                                    >
                                        <option value="">None</option>
                                        <option v-for="t in taxRates" :key="t.id" :value="t.id">{{ t.name }}</option>
                                    </select>
                                </dd>
                            </div>

                            <div class="flex items-center justify-between gap-2">
                                <dt class="text-content-muted">Shipping</dt>
                                <dd>
                                    <input
                                        v-model.number="shippingCharges"
                                        type="number"
                                        min="0"
                                        step="any"
                                        aria-label="Shipping charges"
                                        class="w-24 rounded border border-edge-subtle bg-surface-page px-2 py-1 text-right text-sm text-content-primary focus:outline-none"
                                    />
                                </dd>
                            </div>

                            <div class="flex items-center justify-between border-t border-edge-subtle pt-2">
                                <dt class="font-medium text-content-secondary">Total</dt>
                                <dd class="text-lg font-semibold text-content-primary"><Money :value="finalTotal" /></dd>
                            </div>
                        </dl>
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
