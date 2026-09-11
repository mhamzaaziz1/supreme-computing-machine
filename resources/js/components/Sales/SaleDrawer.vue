<script setup>
/**
 * Invoice detail, as a side drawer.
 *
 * Replaces the legacy Bootstrap "View" modal, which loaded a Blade fragment
 * and needed jQuery to work — neither of which the Inertia shell ships. This
 * fetches JSON from sales.show and renders it, so the panel styles with the
 * rest of the app instead of arriving as foreign markup.
 */
import { ref, watch } from 'vue';
import Icon from '../Icon.vue';
import Money from '../Money.vue';

const props = defineProps({
    /** The row to show, or null when the drawer is closed. */
    sell: { type: Object, default: null },
});

const emit = defineEmits(['close', 'print']);

const detail = ref(null);
const loading = ref(false);
const error = ref(null);

const load = async (url) => {
    loading.value = true;
    error.value = null;
    detail.value = null;

    try {
        const res = await fetch(url, {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        });

        if (!res.ok) throw new Error(`Server responded ${res.status}`);
        detail.value = await res.json();
    } catch (e) {
        error.value = e.message ?? 'Could not load this invoice.';
    } finally {
        loading.value = false;
    }
};

watch(
    () => props.sell,
    (row) => {
        if (row?.actions?.show) load(row.actions.show);
    },
    { immediate: true },
);

const onKey = (e) => {
    if (e.key === 'Escape') emit('close');
};

const fullDate = (value) =>
    new Date(String(value).replace(' ', 'T')).toLocaleString(undefined, {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });

const paymentTone = {
    paid: 'bg-success/10 text-success',
    partial: 'bg-warning/10 text-warning',
    due: 'bg-danger/10 text-danger',
    overdue: 'bg-danger/10 text-danger',
};
</script>

<template>
    <Teleport to="body">
        <div v-if="sell" class="fixed inset-0 z-40 flex justify-end" @keydown="onKey">
            <div class="absolute inset-0 bg-black/40" @click="emit('close')"></div>

            <aside
                class="relative flex h-full w-full max-w-xl flex-col border-l border-edge-subtle bg-surface-page shadow-2xl"
                role="dialog"
                aria-modal="true"
                :aria-label="`Invoice ${sell.invoice_no}`"
            >
                <!-- Header -->
                <header class="flex shrink-0 items-start justify-between gap-3 border-b border-edge-subtle px-5 py-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-semibold text-content-primary">{{ sell.invoice_no }}</h2>
                            <span
                                class="rounded px-1.5 py-0.5 text-xs font-medium capitalize"
                                :class="paymentTone[sell.payment_status] ?? 'bg-surface-sunken text-content-secondary'"
                            >
                                {{ sell.payment_status }}
                            </span>
                        </div>
                        <p class="mt-0.5 text-[13px] text-content-muted">
                            {{ fullDate(sell.date) }} · {{ sell.location }}
                        </p>
                    </div>

                    <button
                        type="button"
                        class="rounded p-1 text-content-muted hover:bg-surface-sunken hover:text-content-primary"
                        aria-label="Close"
                        @click="emit('close')"
                    >
                        <Icon name="close" :size="18" />
                    </button>
                </header>

                <!-- Body -->
                <div class="min-h-0 flex-1 overflow-y-auto px-5 py-4">
                    <p v-if="loading" class="py-12 text-center text-sm text-content-muted">Loading invoice…</p>

                    <div
                        v-else-if="error"
                        class="rounded-lg border border-danger/30 bg-danger/5 px-4 py-3 text-sm text-danger"
                    >
                        {{ error }}
                    </div>

                    <template v-else-if="detail">
                        <!-- Customer -->
                        <section class="mb-5 rounded-lg border border-edge-subtle bg-surface-raised p-4">
                            <div class="text-xs font-medium uppercase tracking-wide text-content-muted">Customer</div>
                            <div class="mt-1 text-sm font-medium text-content-primary">
                                {{ sell.customer ?? '—' }}
                            </div>
                            <div v-if="sell.customer_code" class="text-xs text-content-muted">
                                {{ sell.customer_code }}
                            </div>
                            <div v-if="sell.created_by" class="mt-2 text-xs text-content-muted">
                                Sold by {{ sell.created_by }}
                            </div>
                        </section>

                        <!-- Lines -->
                        <section class="mb-5">
                            <h3 class="mb-2 text-xs font-semibold uppercase tracking-wide text-content-muted">
                                Items ({{ detail.lines.length }})
                            </h3>

                            <div class="overflow-hidden rounded-lg border border-edge-subtle bg-surface-raised">
                                <table class="w-full border-collapse text-sm">
                                    <tbody>
                                        <tr
                                            v-for="line in detail.lines"
                                            :key="line.id"
                                            class="border-b border-edge-subtle last:border-0"
                                        >
                                            <td class="px-3 py-2">
                                                <div class="text-content-primary">{{ line.product }}</div>
                                                <div class="text-xs text-content-muted">
                                                    {{ line.quantity }}{{ line.unit ? ' ' + line.unit : '' }}
                                                    ×
                                                    <Money :value="line.unit_price" />
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-2 text-right text-content-primary">
                                                <Money :value="line.line_total" />
                                            </td>
                                        </tr>
                                        <tr v-if="!detail.lines.length">
                                            <td colspan="2" class="px-3 py-6 text-center text-[13px] text-content-muted">
                                                No line items recorded.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>

                        <!-- Payments -->
                        <section class="mb-5">
                            <h3 class="mb-2 text-xs font-semibold uppercase tracking-wide text-content-muted">
                                Payments ({{ detail.payments.length }})
                            </h3>

                            <div class="overflow-hidden rounded-lg border border-edge-subtle bg-surface-raised">
                                <table class="w-full border-collapse text-sm">
                                    <tbody>
                                        <tr
                                            v-for="p in detail.payments"
                                            :key="p.id"
                                            class="border-b border-edge-subtle last:border-0"
                                        >
                                            <td class="px-3 py-2">
                                                <div class="capitalize text-content-primary">
                                                    {{ (p.method ?? '').replace('_', ' ') || '—' }}
                                                </div>
                                                <div class="text-xs text-content-muted">
                                                    {{ p.paid_on ? fullDate(p.paid_on) : '' }}
                                                    <span v-if="p.ref">· {{ p.ref }}</span>
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-2 text-right text-success">
                                                <Money :value="p.amount" />
                                            </td>
                                        </tr>
                                        <tr v-if="!detail.payments.length">
                                            <td colspan="2" class="px-3 py-6 text-center text-[13px] text-content-muted">
                                                Nothing collected against this invoice yet.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </template>
                </div>

                <!-- Totals + actions -->
                <footer class="shrink-0 border-t border-edge-subtle bg-surface-raised px-5 py-4">
                    <dl class="mb-3 space-y-1 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-content-muted">Total</dt>
                            <dd class="font-medium text-content-primary"><Money :value="sell.total" /></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-content-muted">Paid</dt>
                            <dd class="text-success"><Money :value="sell.paid" /></dd>
                        </div>
                        <div class="flex justify-between border-t border-edge-subtle pt-1">
                            <dt class="font-medium text-content-secondary">Due</dt>
                            <dd
                                class="font-semibold"
                                :class="sell.due > 0 ? 'text-danger' : 'text-content-primary'"
                            >
                                <Money :value="sell.due" />
                            </dd>
                        </div>
                    </dl>

                    <div class="flex flex-wrap gap-2">
                        <button
                            v-if="sell.actions.print"
                            type="button"
                            class="inline-flex items-center gap-2 rounded-md border border-edge-subtle px-3 py-1.5 text-sm font-medium text-content-secondary hover:bg-surface-sunken hover:text-content-primary"
                            @click="emit('print', sell.actions.print)"
                        >
                            <Icon name="orders" :size="15" /> Print
                        </button>
                        <a
                            v-if="sell.actions.edit"
                            :href="sell.actions.edit"
                            target="_blank"
                            class="inline-flex items-center gap-2 rounded-md border border-edge-subtle px-3 py-1.5 text-sm font-medium text-content-secondary hover:bg-surface-sunken hover:text-content-primary"
                        >
                            <Icon name="adjustment" :size="15" /> Edit
                        </a>
                        <a
                            v-if="sell.actions.posPayment"
                            :href="sell.actions.posPayment"
                            class="inline-flex items-center gap-2 rounded-md bg-accent-500 px-3 py-1.5 text-sm font-semibold text-brand-950 hover:bg-accent-400"
                        >
                            <Icon name="expense" :size="15" /> Add payment
                        </a>
                    </div>
                </footer>
            </aside>
        </div>
    </Teleport>
</template>
