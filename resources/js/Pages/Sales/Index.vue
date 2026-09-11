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
import Popover from '../../components/Popover.vue';
import RowActions from '../../components/Sales/RowActions.vue';
import SaleDrawer from '../../components/Sales/SaleDrawer.vue';
import { openOverlay } from '../../overlays/store';

const props = defineProps({
    /** all | pos | drafts | quotations — the screens this list serves. */
    view: { type: String, default: 'all' },
    heading: { type: String, default: 'Sales' },
    noun: { type: String, default: 'invoice' },
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

/**
 * Only the filters hidden inside the popover are counted — search has its own
 * visible box, so badging it would just double-report what is on screen.
 */
const activeFilterCount = computed(
    () =>
        [
            form.value.date_from,
            form.value.date_to,
            form.value.status,
            form.value.payment_status,
            form.value.location_id,
        ].filter(Boolean).length,
);

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

/** A quotation is stored as a draft with a flag, so name it from the flag. */
const kind = (sell) => (sell.status === 'draft' && sell.is_quotation ? 'quotation' : sell.status);

const shortDate = (value) =>
    new Date(String(value).replace(' ', 'T')).toLocaleDateString(undefined, {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });

// -----------------------------------------------------------------
// Row actions
// -----------------------------------------------------------------

const viewing = ref(null);

/**
 * The legacy print action fetches {success, receipt:{html_content}} and hands
 * the markup to the browser's print dialog. Same contract here, minus the
 * jQuery: the receipt is written into an off-screen iframe, printed, and the
 * iframe removed once the dialog closes.
 */
const print = async (url) => {
    try {
        const res = await fetch(url, {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        });
        const payload = await res.json();
        const html = payload?.receipt?.html_content;

        if (!html) throw new Error('This invoice has no printable layout.');

        const frame = document.createElement('iframe');
        frame.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0;';
        document.body.appendChild(frame);

        const doc = frame.contentWindow.document;
        doc.open();
        doc.write(html);
        doc.close();

        // Give the receipt's own stylesheet a chance to apply before printing.
        frame.onload = () => {
            frame.contentWindow.focus();
            frame.contentWindow.print();
            setTimeout(() => frame.remove(), 1000);
        };
    } catch (e) {
        alert(e.message ?? 'Could not print this invoice.');
    }
};

/**
 * Deleted with a plain request rather than router.delete: the legacy endpoint
 * answers with JSON, and Inertia rejects any response that is not an Inertia
 * one. The list is refetched afterwards so totals move with the table.
 */
const destroy = async (sell) => {
    if (!window.confirm(`Delete invoice ${sell.invoice_no}? This cannot be undone.`)) return;

    busy.value = true;

    try {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        const res = await fetch(sell.actions.delete, {
            method: 'DELETE',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token ?? '',
            },
            credentials: 'same-origin',
        });

        const payload = await res.json().catch(() => ({}));

        if (!res.ok || payload.success === false || payload.success === 0) {
            throw new Error(payload.msg ?? `Could not delete invoice ${sell.invoice_no}.`);
        }

        if (viewing.value?.id === sell.id) viewing.value = null;
        reload();
    } catch (e) {
        alert(e.message ?? 'Could not delete this invoice.');
    } finally {
        busy.value = false;
    }
};

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
    <Head :title="heading" />

    <AppShell :title="heading" fill>
        <div class="mx-auto flex h-full w-full max-w-[1600px] flex-col p-4 sm:px-6 sm:py-4">
            <!-- Header -->
            <div class="mb-3 flex shrink-0 flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-xl font-semibold text-content-primary">{{ heading }}</h1>
                    <p class="mt-0.5 text-[13px] text-content-muted">
                        {{ sells.total.toLocaleString() }}
                        {{ sells.total === 1 ? noun : noun + 's' }}
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
            <div class="mb-3 grid shrink-0 grid-cols-2 gap-2 lg:grid-cols-4">
                <div class="rounded-lg border border-edge-subtle bg-surface-raised px-3 py-2">
                    <div class="text-xs font-medium uppercase tracking-wide text-content-muted">Invoices</div>
                    <div class="text-xl font-semibold text-content-primary numeric">
                        {{ summary.invoices.toLocaleString() }}
                    </div>
                </div>
                <div class="rounded-lg border border-edge-subtle bg-surface-raised px-3 py-2">
                    <div class="text-xs font-medium uppercase tracking-wide text-content-muted">Total</div>
                    <div class="text-xl font-semibold text-content-primary">
                        <Money :value="summary.total" compact />
                    </div>
                </div>
                <div class="rounded-lg border border-edge-subtle bg-surface-raised px-3 py-2">
                    <div class="text-xs font-medium uppercase tracking-wide text-content-muted">Collected</div>
                    <div class="text-xl font-semibold text-success">
                        <Money :value="summary.paid" compact />
                    </div>
                </div>
                <div class="rounded-lg border border-edge-subtle bg-surface-raised px-3 py-2">
                    <div class="text-xs font-medium uppercase tracking-wide text-content-muted">Due</div>
                    <div
                        class="text-xl font-semibold"
                        :class="summary.due > 0 ? 'text-danger' : 'text-content-primary'"
                    >
                        <Money :value="summary.due" compact />
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="mb-3 flex shrink-0 flex-wrap items-center gap-2">
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

                <!--
                    Everything but search lives behind one control. Six always-on
                    inputs cost a row of chrome to answer a question most visits
                    never ask, and search alone answers "find me this invoice".
                    The count keeps hidden filters from being forgotten.
                -->
                <Popover :width="300">
                    <template #trigger="{ open, toggle }">
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-md border px-3 py-2 text-sm font-medium"
                            :class="
                                activeFilterCount || open
                                    ? 'border-accent-500 bg-accent-500/10 text-content-primary'
                                    : 'border-edge-subtle text-content-secondary hover:bg-surface-sunken hover:text-content-primary'
                            "
                            @click="toggle"
                        >
                            <Icon name="settings" :size="15" />
                            Filters
                            <span
                                v-if="activeFilterCount"
                                class="rounded bg-accent-500 px-1.5 text-xs font-semibold text-brand-950"
                            >
                                {{ activeFilterCount }}
                            </span>
                        </button>
                    </template>

                    <div class="p-3">
                        <div class="mb-3 grid grid-cols-2 gap-2">
                            <label class="block">
                                <span class="mb-1 block text-xs font-medium text-content-muted">From</span>
                                <input
                                    v-model="form.date_from"
                                    type="date"
                                    class="w-full rounded-md border border-edge-subtle bg-surface-page px-2 py-1.5 text-sm text-content-primary focus:border-accent-500 focus:outline-none"
                                    @change="refine"
                                />
                            </label>
                            <label class="block">
                                <span class="mb-1 block text-xs font-medium text-content-muted">To</span>
                                <input
                                    v-model="form.date_to"
                                    type="date"
                                    class="w-full rounded-md border border-edge-subtle bg-surface-page px-2 py-1.5 text-sm text-content-primary focus:border-accent-500 focus:outline-none"
                                    @change="refine"
                                />
                            </label>
                        </div>

                        <label class="mb-3 block">
                            <span class="mb-1 block text-xs font-medium text-content-muted">Payment</span>
                            <select
                                v-model="form.payment_status"
                                class="w-full rounded-md border border-edge-subtle bg-surface-page px-2 py-1.5 text-sm text-content-primary focus:border-accent-500 focus:outline-none"
                                @change="refine"
                            >
                                <option :value="null">All payments</option>
                                <option value="paid">Paid</option>
                                <option value="partial">Partial</option>
                                <option value="due">Due</option>
                                <option value="overdue">Overdue</option>
                            </select>
                        </label>

                        <!-- Pointless where the screen already pins the status. -->
                        <label v-if="view === 'all'" class="mb-3 block">
                            <span class="mb-1 block text-xs font-medium text-content-muted">Status</span>
                            <select
                                v-model="form.status"
                                class="w-full rounded-md border border-edge-subtle bg-surface-page px-2 py-1.5 text-sm text-content-primary focus:border-accent-500 focus:outline-none"
                                @change="refine"
                            >
                                <option :value="null">All statuses</option>
                                <option value="final">Final</option>
                                <option value="draft">Draft</option>
                                <option value="quotation">Quotation</option>
                            </select>
                        </label>

                        <label v-if="locations.length > 1" class="mb-3 block">
                            <span class="mb-1 block text-xs font-medium text-content-muted">Location</span>
                            <select
                                v-model="form.location_id"
                                class="w-full rounded-md border border-edge-subtle bg-surface-page px-2 py-1.5 text-sm text-content-primary focus:border-accent-500 focus:outline-none"
                                @change="refine"
                            >
                                <option :value="null">All locations</option>
                                <option v-for="l in locations" :key="l.id" :value="l.id">{{ l.name }}</option>
                            </select>
                        </label>

                        <button
                            v-if="activeFilterCount"
                            type="button"
                            class="w-full rounded-md border border-edge-subtle px-3 py-1.5 text-sm font-medium text-content-secondary hover:bg-surface-sunken"
                            @click="reset"
                        >
                            Clear filters
                        </button>
                    </div>
                </Popover>

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
                class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-lg border border-edge-subtle bg-surface-raised transition-opacity"
                :class="busy ? 'opacity-60' : ''"
            >
                <div class="scrollbar-slim min-h-0 flex-1 overflow-auto">
                    <table class="w-full min-w-[900px] border-collapse text-sm">
                        <thead>
                            <tr class="sticky top-0 z-10 border-b border-edge-subtle bg-surface-sunken text-left">
                                <th class="px-4 py-2.5 text-xs font-semibold uppercase tracking-wide text-content-muted">Date</th>
                                <th class="px-4 py-2.5 text-xs font-semibold uppercase tracking-wide text-content-muted">Invoice</th>
                                <th class="px-4 py-2.5 text-xs font-semibold uppercase tracking-wide text-content-muted">Customer</th>
                                <th class="px-4 py-2.5 text-xs font-semibold uppercase tracking-wide text-content-muted">Location</th>
                                <th class="px-4 py-2.5 text-xs font-semibold uppercase tracking-wide text-content-muted">Status</th>
                                <th class="px-4 py-2.5 text-right text-xs font-semibold uppercase tracking-wide text-content-muted">Total</th>
                                <th class="px-4 py-2.5 text-right text-xs font-semibold uppercase tracking-wide text-content-muted">Due</th>
                                <th class="px-4 py-2.5 text-right text-xs font-semibold uppercase tracking-wide text-content-muted">
                                    <span class="sr-only">Actions</span>
                                </th>
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
                                    <button
                                        v-if="sell.contact_id"
                                        type="button"
                                        class="text-left text-content-primary hover:text-brand-600 hover:underline"
                                        @click="openOverlay('outlet', { id: sell.contact_id })"
                                    >
                                        {{ sell.customer ?? '—' }}
                                    </button>
                                    <div v-else class="text-content-primary">{{ sell.customer ?? '—' }}</div>
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
                                            :class="statusTone[kind(sell)] ?? statusTone.final"
                                        >
                                            {{ kind(sell) }}
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
                                <td class="whitespace-nowrap px-4 py-2.5">
                                    <RowActions
                                        :actions="sell.actions"
                                        :invoice-no="sell.invoice_no"
                                        @view="viewing = sell"
                                        @print="print"
                                        @delete="destroy(sell)"
                                    />
                                </td>
                            </tr>

                            <tr v-if="!sells.data.length">
                                <td colspan="8" class="px-4 py-16 text-center">
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
                    class="flex shrink-0 flex-wrap items-center justify-between gap-3 border-t border-edge-subtle px-4 py-2.5"
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

        <SaleDrawer :sell="viewing" @close="viewing = null" @print="print" />
    </AppShell>
</template>
