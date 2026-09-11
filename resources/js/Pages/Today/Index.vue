<script setup>
/**
 * Today — the operational board.
 *
 * Reading order matches the order the questions get asked at end of day:
 * what did we sell, what did we collect, what is still owed; then what is
 * waiting on someone (cheques to bank, vans to settle, owners to remind);
 * then which routes are running and where; then what went wrong.
 *
 * Nothing here is a page of its own. Every row opens the drawer or modal
 * where you act on it, so a manager can run the day from this one screen.
 */
import { computed, onMounted, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppShell from '../../Layouts/AppShell.vue';
import Icon from '../../components/Icon.vue';
import Money from '../../components/Money.vue';
import RouteMap from '../../components/ui/RouteMap.vue';
import { api } from '../../overlays/api';
import { openOverlay } from '../../overlays/store';

const props = defineProps({
    date: { type: String, required: true },
    metrics: { type: Object, required: true },
    routes: { type: Array, default: () => [] },
    exceptions: { type: Array, default: () => [] },
    recentSales: { type: Array, default: () => [] },
    /** The field-operations queue (named "board" so it cannot shadow the shared "ops" prop). */
    board: { type: Object, default: () => ({ can: {}, at_risk: [], targets: [] }) },
    links: { type: Object, required: true },
});

const ops = computed(() => props.board);

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

/** Refresh the board's own figures after an overlay changes something. */
const refresh = () => router.reload({ only: ['metrics', 'routes', 'exceptions', 'recentSales', 'board'] });

// -- "Waiting on someone" queue ------------------------------------------------

const queue = computed(() => {
    const o = ops.value;
    const items = [];
    if (o.cheques) {
        items.push({
            key: 'cheques',
            icon: 'cheque',
            title: o.cheques.count ? `${o.cheques.count} ${o.cheques.count === 1 ? 'cheque' : 'cheques'} to deposit` : 'No cheques due',
            amount: o.cheques.count ? o.cheques.amount : null,
            hint: 'dated in the next two days',
            hot: o.cheques.count > 0,
            run: () => openOverlay('cheques'),
        });
    }
    if (o.vans) {
        const v = o.vans;
        items.push({
            key: 'vans',
            icon: 'truck',
            title: v.total ? `${v.loaded} of ${v.total} vans out · ${v.settled} settled` : 'No vans set up',
            amount: v.stock_value || null,
            hint: v.differences ? `${v.differences} with differences to approve` : 'stock on vans at cost',
            hot: v.differences > 0 || (v.loaded > v.settled && new Date().getHours() >= 17),
            run: () => openOverlay('vans'),
        });
    }
    if (o.service_due !== null && o.service_due !== undefined) {
        items.push({
            key: 'oil',
            icon: 'oil',
            title: o.service_due ? `${o.service_due} oil ${o.service_due === 1 ? 'change' : 'changes'} due` : 'No oil changes due',
            hint: 'in the next 7 days · remind owners',
            hot: o.service_due > 0,
            run: () => openOverlay('serviceDue'),
        });
    }
    if (o.can?.schemes) {
        items.push({
            key: 'schemes',
            icon: 'target',
            title: `${o.schemes_running} ${o.schemes_running === 1 ? 'scheme' : 'schemes'} running`,
            hint: 'applied automatically when selling',
            hot: false,
            run: () => openOverlay('schemes'),
        });
    }
    return items;
});

// -- Map ------------------------------------------------------------------------

const mapData = ref(null);
onMounted(async () => {
    if (!ops.value.can?.map) return;
    try {
        mapData.value = await api('routes/map');
    } catch {
        mapData.value = { outlets: [], sellers: [], violations: [] };
    }
});

const mapPoints = computed(() => {
    if (!mapData.value) return [];
    return [
        ...mapData.value.outlets.map((o) => ({ id: o.id, kind: 'outlet', lat: o.lat, lng: o.lng, label: `${o.name} · ${o.route}`, tone: o.visited ? 'visited' : 'pending' })),
        ...mapData.value.violations.map((v) => ({ id: v.contact_id, kind: 'violation', lat: v.lat, lng: v.lng, label: `${v.type.replace('_', ' ')} · ${v.at}`, tone: 'violation' })),
        ...mapData.value.sellers.map((s) => ({ id: s.id, kind: 'seller', lat: s.lat, lng: s.lng, label: `${s.name} · last seen ${s.at}`, tone: 'seller' })),
    ];
});

const onMapSelect = (p) => {
    if (p.kind !== 'seller' && p.id) openOverlay('outlet', { id: p.id });
};

const targetUnit = { qty: 'units', litres: 'L' };
</script>

<template>
    <Head title="Today" />

    <AppShell title="Today">
        <div class="mx-auto max-w-[1400px] p-5">
            <div class="mb-4 flex flex-wrap items-center gap-2">
                <p class="mr-auto text-sm text-content-muted">{{ longDate(date) }}</p>
                <a :href="links.field" class="inline-flex items-center gap-1.5 rounded-md border border-edge-subtle bg-surface-raised px-2.5 py-1.5 text-xs font-medium text-content-secondary hover:border-edge-strong hover:text-content-primary">
                    <Icon name="route" :size="14" /> Field app
                </a>
                <button v-if="ops.can?.economics" type="button" class="inline-flex items-center gap-1.5 rounded-md border border-edge-subtle bg-surface-raised px-2.5 py-1.5 text-xs font-medium text-content-secondary hover:border-edge-strong hover:text-content-primary" @click="openOverlay('routeEconomics')">
                    <Icon name="insight" :size="14" /> Cost to serve
                </button>
                <button v-if="ops.can?.principal" type="button" class="inline-flex items-center gap-1.5 rounded-md border border-edge-subtle bg-surface-raised px-2.5 py-1.5 text-xs font-medium text-content-secondary hover:border-edge-strong hover:text-content-primary" @click="openOverlay('principal')">
                    <Icon name="download" :size="14" /> Principal file
                </button>
            </div>

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

            <!-- Waiting on someone -->
            <div v-if="queue.length" class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <button
                    v-for="q in queue"
                    :key="q.key"
                    type="button"
                    class="flex items-start gap-3 rounded-lg border bg-surface-raised p-3.5 text-left transition-shadow hover:shadow-raised"
                    :class="q.hot ? 'border-accent-500/60' : 'border-edge-subtle'"
                    @click="q.run"
                >
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-md" :class="q.hot ? 'bg-accent-500/15 text-accent-700 dark:text-accent-300' : 'bg-surface-sunken text-content-muted'">
                        <Icon :name="q.icon" :size="17" />
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-medium text-content-primary">{{ q.title }}</span>
                        <span class="block text-xs text-content-muted">
                            <template v-if="q.amount"><Money :value="q.amount" compact /> · </template>{{ q.hint }}
                        </span>
                    </span>
                    <Icon name="chevronRight" :size="15" class="mt-1 shrink-0 text-content-muted" />
                </button>
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
                                class="cursor-pointer border-t border-edge-subtle transition-colors hover:bg-surface-page"
                                @click="openOverlay('route', { id: r.id })"
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
                        <li v-for="e in exceptions" :key="e.id">
                            <button
                                type="button"
                                class="flex w-full gap-2.5 px-4 py-2.5 text-left transition-colors hover:bg-surface-page disabled:cursor-default"
                                :disabled="!e.contact_id"
                                @click="e.contact_id && openOverlay('outlet', { id: e.contact_id })"
                            >
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
                            </button>
                        </li>
                    </ul>

                    <p v-else class="px-4 py-10 text-center text-sm text-content-muted">
                        Nothing flagged today.
                    </p>
                </section>
            </div>

            <!-- Where everyone is -->
            <section v-if="ops.can?.map" class="mt-4 overflow-hidden rounded-lg border border-edge-subtle bg-surface-raised">
                <header class="flex flex-wrap items-center gap-x-4 gap-y-1 border-b border-edge-subtle px-4 py-2.5">
                    <h2 class="mr-auto text-sm font-semibold text-content-primary">On the road</h2>
                    <span class="flex items-center gap-1.5 text-xs text-content-muted"><span class="h-2.5 w-2.5 rounded-full bg-success"></span>visited</span>
                    <span class="flex items-center gap-1.5 text-xs text-content-muted"><span class="h-2.5 w-2.5 rounded-full bg-brand-600"></span>not yet</span>
                    <span class="flex items-center gap-1.5 text-xs text-content-muted"><span class="h-2.5 w-2.5 rounded-full bg-info"></span>seller</span>
                    <span class="flex items-center gap-1.5 text-xs text-content-muted"><span class="h-2.5 w-2.5 rounded-full bg-danger"></span>violation</span>
                </header>
                <div class="p-3">
                    <RouteMap v-if="mapData" :points="mapPoints" height="300px" @select="onMapSelect" />
                    <div v-else class="h-[300px] animate-pulse rounded-lg bg-surface-sunken"></div>
                </div>
            </section>

            <div v-if="ops.at_risk.length || ops.targets.length" class="mt-4 grid gap-4 lg:grid-cols-2">
                <!-- Slipping outlets -->
                <section v-if="ops.at_risk.length" class="overflow-hidden rounded-lg border border-edge-subtle bg-surface-raised">
                    <header class="border-b border-edge-subtle px-4 py-2.5">
                        <h2 class="text-sm font-semibold text-content-primary">Outlets slipping</h2>
                        <p class="text-xs text-content-muted">Last 30 days more than 30% below their own usual month</p>
                    </header>
                    <ul class="divide-y divide-edge-subtle">
                        <li v-for="o in ops.at_risk" :key="o.id">
                            <button type="button" class="flex w-full items-center gap-3 px-4 py-2.5 text-left hover:bg-surface-page" @click="openOverlay('outlet', { id: o.id })">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-[13px] font-medium text-content-primary">{{ o.name }}</p>
                                    <p class="text-xs text-content-muted">{{ o.route ?? 'No route' }} · usually <Money :value="o.usual" compact /> a month</p>
                                </div>
                                <span class="shrink-0 text-right text-xs">
                                    <span class="block font-semibold text-danger">−{{ o.drop_pct }}%</span>
                                    <Money :value="o.recent" compact class="text-content-muted" />
                                </span>
                            </button>
                        </li>
                    </ul>
                </section>

                <!-- Principal targets -->
                <section v-if="ops.targets.length" class="overflow-hidden rounded-lg border border-edge-subtle bg-surface-raised">
                    <header class="flex items-center justify-between border-b border-edge-subtle px-4 py-2.5">
                        <h2 class="text-sm font-semibold text-content-primary">Principal targets</h2>
                        <button type="button" class="text-xs text-content-muted hover:text-brand-600" @click="openOverlay('principal', { tab: 'targets' })">All targets</button>
                    </header>
                    <div class="space-y-3 px-4 py-3">
                        <div v-for="t in ops.targets" :key="t.id">
                            <div class="flex justify-between text-[13px]">
                                <span class="font-medium text-content-primary">{{ t.name }}</span>
                                <span class="text-content-muted">{{ t.days_left }} days left</span>
                            </div>
                            <div class="relative my-1.5 h-2 overflow-hidden rounded-full bg-surface-sunken">
                                <div class="h-full rounded-full" :class="t.percent >= 100 ? 'bg-success' : 'bg-brand-600'" :style="{ width: `${Math.min(100, t.percent)}%` }"></div>
                                <div class="absolute inset-y-0 w-0.5 bg-content-primary/60" :style="{ left: `${t.elapsed_percent}%` }"></div>
                            </div>
                            <p class="text-xs text-content-muted numeric">
                                <template v-if="t.measure === 'value'"><Money :value="t.achieved" compact /> of <Money :value="t.target" compact /></template>
                                <template v-else>{{ t.achieved.toLocaleString() }} of {{ t.target.toLocaleString() }} {{ targetUnit[t.measure] }}</template>
                                · {{ t.percent }}%
                                <span v-if="t.projected_percent !== null" :class="t.projected_percent >= 100 ? 'text-success' : 'text-warning'">· on pace for {{ Math.round(t.projected_percent) }}%</span>
                            </p>
                        </div>
                    </div>
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
                                <td class="whitespace-nowrap px-4 py-2.5 font-medium numeric">
                                    <button type="button" class="text-content-primary hover:text-brand-600 hover:underline" @click="openOverlay('invoice', { id: s.id })">
                                        {{ s.invoice_no }}
                                    </button>
                                </td>
                                <td class="px-4 py-2.5 text-content-secondary">
                                    <button v-if="s.contact_id" type="button" class="text-left hover:text-brand-600 hover:underline" @click="openOverlay('outlet', { id: s.contact_id })">
                                        {{ s.customer ?? '—' }}
                                    </button>
                                    <template v-else>{{ s.customer ?? '—' }}</template>
                                </td>
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
