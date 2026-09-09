<script setup>
/**
 * Today — the operational board.
 *
 * Reading order matches the order the questions get asked at end of day:
 * what did we sell, what did we collect, what is still owed, then which
 * routes are running, then what went wrong.
 */
import { Head } from '@inertiajs/vue3';
import AppShell from '../../Layouts/AppShell.vue';
import Icon from '../../components/Icon.vue';
import Money from '../../components/Money.vue';

defineProps({
    date: { type: String, required: true },
    metrics: { type: Object, required: true },
    routes: { type: Array, default: () => [] },
    exceptions: { type: Array, default: () => [] },
    recentSales: { type: Array, default: () => [] },
    links: { type: Object, required: true },
});

const paymentTone = {
    paid: 'bg-success/10 text-success',
    partial: 'bg-warning/10 text-warning',
    due: 'bg-danger/10 text-danger',
};

const longDate = (iso) =>
    new Date(iso + 'T00:00:00').toLocaleDateString(undefined, {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
    });
</script>

<template>
    <Head title="Today" />

    <AppShell title="Today">
        <div class="mx-auto max-w-[1400px] p-5">
            <p class="mb-4 text-sm text-content-muted">{{ longDate(date) }}</p>

            <!-- Headline figures. Four, not twelve: anything more and none of
                 them get read. -->
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <a
                    :href="links.sells"
                    class="group rounded-lg border border-edge-subtle bg-surface-raised p-4 transition-shadow hover:shadow-raised"
                >
                    <div class="flex items-center gap-2 text-content-muted">
                        <Icon name="sell" :size="16" />
                        <span class="text-xs font-medium uppercase tracking-wide">Sold today</span>
                    </div>
                    <p class="mt-2 text-2xl font-semibold text-content-primary">
                        <Money :value="metrics.sales_total" compact />
                    </p>
                    <p class="mt-0.5 text-xs text-content-muted">
                        {{ metrics.sales_count }}
                        {{ metrics.sales_count === 1 ? 'invoice' : 'invoices' }}
                    </p>
                </a>

                <div class="rounded-lg border border-edge-subtle bg-surface-raised p-4">
                    <div class="flex items-center gap-2 text-content-muted">
                        <Icon name="cash" :size="16" />
                        <span class="text-xs font-medium uppercase tracking-wide">Collected</span>
                    </div>
                    <p class="mt-2 text-2xl font-semibold text-success">
                        <Money :value="metrics.collected" compact />
                    </p>
                    <p class="mt-0.5 text-xs text-content-muted">payments received today</p>
                </div>

                <a
                    :href="links.contacts"
                    class="group rounded-lg border border-edge-subtle bg-surface-raised p-4 transition-shadow hover:shadow-raised"
                >
                    <div class="flex items-center gap-2 text-content-muted">
                        <Icon name="alert" :size="16" />
                        <span class="text-xs font-medium uppercase tracking-wide">Outstanding</span>
                    </div>
                    <p
                        class="mt-2 text-2xl font-semibold"
                        :class="metrics.receivable > 0 ? 'text-danger' : 'text-content-primary'"
                    >
                        <Money :value="metrics.receivable" compact />
                    </p>
                    <p class="mt-0.5 text-xs text-content-muted">unpaid across all invoices</p>
                </a>

                <a
                    :href="links.visitLogs"
                    class="group rounded-lg border border-edge-subtle bg-surface-raised p-4 transition-shadow hover:shadow-raised"
                >
                    <div class="flex items-center gap-2 text-content-muted">
                        <Icon name="route" :size="16" />
                        <span class="text-xs font-medium uppercase tracking-wide">Visits</span>
                    </div>
                    <p class="mt-2 text-2xl font-semibold text-content-primary numeric">
                        {{ metrics.visits }}
                    </p>
                    <p class="mt-0.5 text-xs text-content-muted">
                        {{ metrics.customers_visited }} customers reached
                    </p>
                </a>
            </div>

            <div class="mt-5 grid gap-4 lg:grid-cols-3">
                <!-- Routes -->
                <section
                    class="overflow-hidden rounded-lg border border-edge-subtle bg-surface-raised lg:col-span-2"
                >
                    <header
                        class="flex items-center justify-between border-b border-edge-subtle px-4 py-2.5"
                    >
                        <h2 class="text-sm font-semibold text-content-primary">Routes today</h2>
                        <a
                            :href="links.routes"
                            class="flex items-center gap-1 text-xs text-content-muted transition-colors hover:text-brand-600"
                        >
                            All routes <Icon name="chevronRight" :size="13" />
                        </a>
                    </header>

                    <table v-if="routes.length" class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-wide text-content-muted">
                                <th class="px-4 py-2 font-medium">Route</th>
                                <th class="px-4 py-2 font-medium">Salesman</th>
                                <th class="px-4 py-2 text-right font-medium">Visits</th>
                                <th class="px-4 py-2 text-right font-medium">Violations</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="r in routes"
                                :key="r.id"
                                class="border-t border-edge-subtle transition-colors hover:bg-surface-page"
                            >
                                <td class="px-4 py-2.5 font-medium text-content-primary">
                                    {{ r.name }}
                                </td>
                                <td class="px-4 py-2.5 text-content-secondary">
                                    {{ r.seller ?? '—' }}
                                </td>
                                <td class="px-4 py-2.5 text-right numeric text-content-primary">
                                    {{ r.visits }}
                                </td>
                                <td class="px-4 py-2.5 text-right numeric">
                                    <span :class="r.violations ? 'text-danger font-medium' : 'text-content-muted'">
                                        {{ r.violations }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <p v-else class="px-4 py-10 text-center text-sm text-content-muted">
                        No routes have an active salesman assignment.
                    </p>
                </section>

                <!-- Exceptions -->
                <section class="overflow-hidden rounded-lg border border-edge-subtle bg-surface-raised">
                    <header
                        class="flex items-center justify-between border-b border-edge-subtle px-4 py-2.5"
                    >
                        <h2 class="text-sm font-semibold text-content-primary">Needs attention</h2>
                        <a
                            :href="links.violations"
                            class="flex items-center gap-1 text-xs text-content-muted transition-colors hover:text-brand-600"
                        >
                            All <Icon name="chevronRight" :size="13" />
                        </a>
                    </header>

                    <ul v-if="exceptions.length" class="divide-y divide-edge-subtle">
                        <li v-for="e in exceptions" :key="e.id" class="flex gap-2.5 px-4 py-2.5">
                            <Icon name="alert" :size="15" class="mt-0.5 shrink-0 text-danger" />
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-[13px] font-medium text-content-primary">
                                    {{ e.customer ?? e.route ?? 'Unknown location' }}
                                </p>
                                <p class="truncate text-xs text-content-muted">
                                    {{ e.type }}<template v-if="e.user"> · {{ e.user }}</template>
                                    <template v-if="e.distance"> · {{ Math.round(e.distance) }}m away</template>
                                </p>
                            </div>
                            <span class="shrink-0 text-xs text-content-muted numeric">{{ e.at }}</span>
                        </li>
                    </ul>

                    <p v-else class="px-4 py-10 text-center text-sm text-content-muted">
                        Nothing flagged today.
                    </p>
                </section>
            </div>

            <!-- Recent sales -->
            <section class="mt-4 overflow-hidden rounded-lg border border-edge-subtle bg-surface-raised">
                <header
                    class="flex items-center justify-between border-b border-edge-subtle px-4 py-2.5"
                >
                    <h2 class="text-sm font-semibold text-content-primary">Latest sales</h2>
                    <a
                        :href="links.pos"
                        class="rounded-md bg-accent-500 px-2.5 py-1 text-xs font-semibold text-brand-950 transition-colors hover:bg-accent-400"
                    >
                        New sale
                    </a>
                </header>

                <div class="overflow-x-auto">
                    <table v-if="recentSales.length" class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-wide text-content-muted">
                                <th class="px-4 py-2 font-medium">Invoice</th>
                                <th class="px-4 py-2 font-medium">Customer</th>
                                <th class="px-4 py-2 font-medium">When</th>
                                <th class="px-4 py-2 font-medium">Status</th>
                                <th class="px-4 py-2 text-right font-medium">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="s in recentSales"
                                :key="s.id"
                                class="border-t border-edge-subtle transition-colors hover:bg-surface-page"
                            >
                                <td class="whitespace-nowrap px-4 py-2.5 font-medium text-content-primary numeric">
                                    {{ s.invoice_no }}
                                </td>
                                <td class="px-4 py-2.5 text-content-secondary">{{ s.customer ?? '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-2.5 text-content-muted numeric">
                                    {{ s.at }}
                                </td>
                                <td class="px-4 py-2.5">
                                    <span
                                        class="rounded-full px-2 py-0.5 text-xs font-medium capitalize"
                                        :class="paymentTone[s.payment_status] ?? 'bg-surface-sunken text-content-secondary'"
                                    >
                                        {{ s.payment_status }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-2.5 text-right font-medium text-content-primary">
                                    <Money :value="s.total" />
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <p v-else class="px-4 py-10 text-center text-sm text-content-muted">
                        No sales recorded yet.
                    </p>
                </div>
            </section>
        </div>
    </AppShell>
</template>
