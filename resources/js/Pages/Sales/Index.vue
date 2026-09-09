<script setup>
/**
 * Sales — the invoice list.
 *
 * Built around the two questions this screen is actually opened to answer:
 * "find me this invoice" and "how much is still owed". So search is the
 * primary control rather than a corner afterthought, and Due is a real
 * column instead of something you work out from Total minus a payment popup.
 *
 * Filtering goes back to the server as an Inertia visit with `only`, so a
 * keystroke re-renders the table and nothing else — no full page load, and
 * no client-side copy of the list to drift out of date.
 */
import { computed, ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppShell from '../../Layouts/AppShell.vue';
import Icon from '../../components/Icon.vue';
import Money from '../../components/Money.vue';

const props = defineProps({
    filters: { type: Object, required: true },
    summary: { type: Object, required: true },
    locations: { type: Array, default: () => [] },
    sells: { type: Object, required: true },
    perPageOptions: { type: Array, default: () => [25, 50, 100] },
    links: { type: Object, required: true },
});

const form = ref({ ...props.filters });
const busy = ref(false);

// The server is the single source of truth for filter state; when it sends a
// new set back (browser back, a reset, a page step) the controls follow it.
watch(
    () => props.filters,
    (next) => {
        form.value = { ...next };
    },
);

let debounce;
const reload = (extra = {}, { delay = 0 } = {}) => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
        router.get(
            window.location.pathname,
            { ...form.value, ...extra },
            {
                only: ['sells', 'summary', 'filters'],
                preserveState: true,
                preserveScroll: true,
                replace: true,
                onStart: () => (busy.value = true),
                onFinish: () => (busy.value = false),
            },
        );
    }, delay);
};

/** Typing should not fire a request per character. */
const search = () => reload({ page: 1 }, { delay: 300 });
const refine = () => reload({ page: 1 });
const goToPage = (page) => reload({ page });

const reset = () => {
    form.value = {
        search: null,
        date_from: null,
        date_to: null,
        status: null,
        payment_status: null,
        location_id: null,
        per_page: props.filters.per_page,
    };
    refine();
};

const hasFilters = computed(() =>
    Boolean(
        form.value.search ||
            form.value.date_from ||
            form.value.date_to ||
            form.value.status ||
            form.value.payment_status ||
            form.value.location_id,
    ),
);

const paymentTone = {
    paid: 'bg-success/10 text-success',
    partial: 'bg-warning/10 text-warning',
    due: 'bg-danger/10 text-danger',
    overdue: 'bg-danger/10 text-danger',
};

const statusTone = {
    final: 'bg-surface-sunken text-content-secondary',
    draft: 'bg-warning/10 text-warning',
    quotation: 'bg-surface-sunken text-content-muted',
};

const shortDate = (value) =>
    new Date(String(value).replace(' ', 'T')).toLocaleDateString(undefined, {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });

/**
 * A compact window of pages around the current one: enough to jump a few
 * steps without rendering hundreds of buttons on a large result set.
 */
const pages = computed(() => {
    const { current_page: current, last_page: last } = props.sells;
    const span = 2;
    const from = Math.max(1, current - span);
    const to = Math.min(last, current + span);

    return Array.from({ length: to - from + 1 }, (_, i) => from + i);
});
</script>

<template>
    <Head title="Sales" />

    <AppShell title="Sales">
        <div class="mx-auto w-full max-w-[1600px] p-4 sm:p-6">
            <!-- Header -->
            <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-xl font-semibold text-content-primary">Sales</h1>
                    <p class="mt-0.5 text-[13px] text-content-muted">
                        {{ sells.total.toLocaleString() }}
                        {{ sells.total === 1 ? 'invoice' : 'invoices' }}
                        <span v-if="hasFilters">matching your filters</span>
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a
                        :href="links.addSale"
                        class="inline-flex items-center gap-2 rounded-md border border-edge-subtle px-3 py-2 text-sm font-medium text-content-secondary hover:bg-surface-sunken hover:text-content-primary"
                    >
                        <Icon name="sell" :size="16" />
                        Add sale
                    </a>
                    <a
                        :href="links.pos"
                        class="inline-flex items-center gap-2 rounded-md bg-accent-500 px-3 py-2 text-sm font-semibold text-brand-950 hover:bg-accent-400"
                    >
                        <Icon name="today" :size="16" />
                        Open POS
                    </a>
                </div>
            </div>

            <!-- Totals for the filtered set -->
            <div class="mb-5 grid grid-cols-2 gap-3 lg:grid-cols-4">
                <div class="rounded-lg border border-edge-subtle bg-surface-raised p-4">
                    <div class="text-xs font-medium uppercase tracking-wide text-content-muted">Invoices</div>
                    <div class="mt-1 text-2xl font-semibold text-content-primary numeric">
                        {{ summary.invoices.toLocaleString() }}
                    </div>
                </div>
                <div class="rounded-lg border border-edge-subtle bg-surface-raised p-4">
                    <div class="text-xs font-medium uppercase tracking-wide text-content-muted">Total</div>
                    <div class="mt-1 text-2xl font-semibold text-content-primary">
                        <Money :value="summary.total" compact />
                    </div>
                </div>
                <div class="rounded-lg border border-edge-subtle bg-surface-raised p-4">
                    <div class="text-xs font-medium uppercase tracking-wide text-content-muted">Collected</div>
                    <div class="mt-1 text-2xl font-semibold text-success">
                        <Money :value="summary.paid" compact />
                    </div>
                </div>
                <div class="rounded-lg border border-edge-subtle bg-surface-raised p-4">
                    <div class="text-xs font-medium uppercase tracking-wide text-content-muted">Due</div>
                    <div
                        class="mt-1 text-2xl font-semibold"
                        :class="summary.due > 0 ? 'text-danger' : 'text-content-primary'"
                    >
                        <Money :value="summary.due" compact />
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="mb-3 flex flex-wrap items-center gap-2">
                <div class="relative min-w-[240px] flex-1">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-content-muted">
                        <Icon name="search" :size="16" />
                    </span>
                    <input
                        v-model="form.search"
                        type="search"
                        placeholder="Invoice, customer, code or mobile"
                        class="w-full rounded-md border border-edge-subtle bg-surface-raised py-2 pl-9 pr-3 text-sm text-content-primary placeholder:text-content-muted focus:border-accent-500 focus:outline-none"
                        @input="search"
                    />
                </div>

                <input
                    v-model="form.date_from"
                    type="date"
                    aria-label="From date"
                    class="rounded-md border border-edge-subtle bg-surface-raised px-3 py-2 text-sm text-content-primary focus:border-accent-500 focus:outline-none"
                    @change="refine"
                />
                <input
                    v-model="form.date_to"
                    type="date"
                    aria-label="To date"
                    class="rounded-md border border-edge-subtle bg-surface-raised px-3 py-2 text-sm text-content-primary focus:border-accent-500 focus:outline-none"
                    @change="refine"
                />

                <select
                    v-model="form.payment_status"
                    aria-label="Payment status"
                    class="rounded-md border border-edge-subtle bg-surface-raised px-3 py-2 text-sm text-content-primary focus:border-accent-500 focus:outline-none"
                    @change="refine"
                >
                    <option :value="null">All payments</option>
                    <option value="paid">Paid</option>
                    <option value="partial">Partial</option>
                    <option value="due">Due</option>
                    <option value="overdue">Overdue</option>
                </select>

                <select
                    v-model="form.status"
                    aria-label="Status"
                    class="rounded-md border border-edge-subtle bg-surface-raised px-3 py-2 text-sm text-content-primary focus:border-accent-500 focus:outline-none"
                    @change="refine"
                >
                    <option :value="null">All statuses</option>
                    <option value="final">Final</option>
                    <option value="draft">Draft</option>
                    <option value="quotation">Quotation</option>
                </select>

                <select
                    v-if="locations.length > 1"
                    v-model="form.location_id"
                    aria-label="Location"
                    class="rounded-md border border-edge-subtle bg-surface-raised px-3 py-2 text-sm text-content-primary focus:border-accent-500 focus:outline-none"
                    @change="refine"
                >
                    <option :value="null">All locations</option>
                    <option v-for="l in locations" :key="l.id" :value="l.id">{{ l.name }}</option>
                </select>

                <button
                    v-if="hasFilters"
                    type="button"
                    class="rounded-md px-3 py-2 text-sm font-medium text-content-muted hover:text-content-primary"
                    @click="reset"
                >
                    Clear
                </button>
            </div>

            <!-- Table -->
            <div
                class="overflow-hidden rounded-lg border border-edge-subtle bg-surface-raised transition-opacity"
                :class="busy ? 'opacity-60' : ''"
            >
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-edge-subtle bg-surface-sunken text-left">
                                <th class="px-4 py-2.5 text-xs font-semibold uppercase tracking-wide text-content-muted">Date</th>
                                <th class="px-4 py-2.5 text-xs font-semibold uppercase tracking-wide text-content-muted">Invoice</th>
                                <th class="px-4 py-2.5 text-xs font-semibold uppercase tracking-wide text-content-muted">Customer</th>
                                <th class="px-4 py-2.5 text-xs font-semibold uppercase tracking-wide text-content-muted">Location</th>
                                <th class="px-4 py-2.5 text-xs font-semibold uppercase tracking-wide text-content-muted">Status</th>
                                <th class="px-4 py-2.5 text-right text-xs font-semibold uppercase tracking-wide text-content-muted">Total</th>
                                <th class="px-4 py-2.5 text-right text-xs font-semibold uppercase tracking-wide text-content-muted">Due</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr
                                v-for="sell in sells.data"
                                :key="sell.id"
                                class="border-b border-edge-subtle last:border-0 hover:bg-surface-page"
                            >
                                <td class="whitespace-nowrap px-4 py-2.5 text-content-secondary">
                                    {{ shortDate(sell.date) }}
                                </td>
                                <td class="px-4 py-2.5">
                                    <a
                                        :href="`${links.show}/${sell.id}`"
                                        class="font-medium text-content-primary hover:text-brand-600"
                                    >
                                        {{ sell.invoice_no }}
                                    </a>
                                </td>
                                <td class="px-4 py-2.5">
                                    <div class="text-content-primary">{{ sell.customer ?? '—' }}</div>
                                    <div v-if="sell.customer_code" class="text-xs text-content-muted">
                                        {{ sell.customer_code }}
                                    </div>
                                </td>
                                <td class="px-4 py-2.5 text-content-secondary">{{ sell.location }}</td>
                                <td class="px-4 py-2.5">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span
                                            v-if="sell.status !== 'final'"
                                            class="rounded px-1.5 py-0.5 text-xs font-medium capitalize"
                                            :class="statusTone[sell.status] ?? statusTone.final"
                                        >
                                            {{ sell.status }}
                                        </span>
                                        <span
                                            class="rounded px-1.5 py-0.5 text-xs font-medium capitalize"
                                            :class="paymentTone[sell.payment_status] ?? statusTone.final"
                                        >
                                            {{ sell.payment_status }}
                                        </span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-2.5 text-right text-content-primary">
                                    <Money :value="sell.total" />
                                </td>
                                <td class="whitespace-nowrap px-4 py-2.5 text-right">
                                    <span :class="sell.due > 0 ? 'text-danger' : 'text-content-muted'">
                                        <Money :value="sell.due" />
                                    </span>
                                </td>
                            </tr>

                            <tr v-if="!sells.data.length">
                                <td colspan="7" class="px-4 py-16 text-center">
                                    <div class="text-sm font-medium text-content-primary">No sales found</div>
                                    <p class="mx-auto mt-1 max-w-sm text-[13px] text-content-muted">
                                        <template v-if="hasFilters">
                                            Nothing matches these filters. Try widening the date range or clearing them.
                                        </template>
                                        <template v-else>
                                            Sales will appear here as soon as the first invoice is recorded.
                                        </template>
                                    </p>
                                    <button
                                        v-if="hasFilters"
                                        type="button"
                                        class="mt-3 rounded-md border border-edge-subtle px-3 py-1.5 text-sm font-medium text-content-secondary hover:bg-surface-sunken"
                                        @click="reset"
                                    >
                                        Clear filters
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    v-if="sells.total"
                    class="flex flex-wrap items-center justify-between gap-3 border-t border-edge-subtle px-4 py-3"
                >
                    <div class="text-[13px] text-content-muted">
                        Showing <span class="text-content-secondary">{{ sells.from }}–{{ sells.to }}</span>
                        of <span class="text-content-secondary">{{ sells.total.toLocaleString() }}</span>

                        <select
                            v-model.number="form.per_page"
                            aria-label="Rows per page"
                            class="ml-2 rounded border border-edge-subtle bg-surface-raised px-1.5 py-1 text-[13px] text-content-secondary focus:border-accent-500 focus:outline-none"
                            @change="refine"
                        >
                            <option v-for="n in perPageOptions" :key="n" :value="n">{{ n }} / page</option>
                        </select>
                    </div>

                    <div v-if="sells.last_page > 1" class="flex items-center gap-1">
                        <button
                            type="button"
                            class="rounded px-2 py-1 text-sm text-content-secondary disabled:opacity-40 enabled:hover:bg-surface-sunken"
                            :disabled="sells.current_page === 1"
                            @click="goToPage(sells.current_page - 1)"
                        >
                            Previous
                        </button>

                        <button
                            v-for="p in pages"
                            :key="p"
                            type="button"
                            class="min-w-[32px] rounded px-2 py-1 text-sm"
                            :class="
                                p === sells.current_page
                                    ? 'bg-accent-500 font-semibold text-brand-950'
                                    : 'text-content-secondary hover:bg-surface-sunken'
                            "
                            @click="goToPage(p)"
                        >
                            {{ p }}
                        </button>

                        <button
                            type="button"
                            class="rounded px-2 py-1 text-sm text-content-secondary disabled:opacity-40 enabled:hover:bg-surface-sunken"
                            :disabled="sells.current_page === sells.last_page"
                            @click="goToPage(sells.current_page + 1)"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppShell>
</template>
