<script setup>
/**
 * Products — the catalogue.
 *
 * Opened to answer "do we have it, and what does it cost", so stock is a
 * first-class column with the alert threshold already applied rather than a
 * number to go and check elsewhere. Out-of-stock and low-stock counts are
 * summary tiles that double as filters, because a count you cannot act on
 * just makes you go looking.
 *
 * Same shape as the sales list on purpose: fills the viewport, only the table
 * body scrolls, filters behind one badged control, actions per row.
 */
import { computed, ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppShell from '../../Layouts/AppShell.vue';
import Icon from '../../components/Icon.vue';
import Money from '../../components/Money.vue';
import Popover from '../../components/Popover.vue';
import ProductDrawer from '../../components/Products/ProductDrawer.vue';

const props = defineProps({
    filters: { type: Object, required: true },
    summary: { type: Object, required: true },
    categories: { type: Array, default: () => [] },
    brands: { type: Array, default: () => [] },
    locations: { type: Array, default: () => [] },
    products: { type: Object, required: true },
    perPageOptions: { type: Array, default: () => [25, 50, 100] },
    links: { type: Object, required: true },
});

const form = ref({ ...props.filters });
const busy = ref(false);

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
                only: ['products', 'summary', 'filters'],
                preserveState: true,
                preserveScroll: true,
                replace: true,
                onStart: () => (busy.value = true),
                onFinish: () => (busy.value = false),
            },
        );
    }, delay);
};

const search = () => reload({ page: 1 }, { delay: 300 });
const refine = () => reload({ page: 1 });
const goToPage = (page) => reload({ page });

/** The stock tiles are filters, so clicking one toggles it rather than just reporting. */
const toggleStock = (value) => {
    form.value.stock = form.value.stock === value ? null : value;
    refine();
};

const reset = () => {
    form.value = {
        search: null,
        category_id: null,
        brand_id: null,
        location_id: null,
        type: null,
        stock: null,
        active_state: 'active',
        per_page: props.filters.per_page,
    };
    refine();
};

/** 'active' is the default view, so it is not a filter the user chose. */
const activeFilterCount = computed(
    () =>
        [
            form.value.category_id,
            form.value.brand_id,
            form.value.location_id,
            form.value.type,
            form.value.active_state === 'inactive' ? 'inactive' : null,
        ].filter(Boolean).length,
);

const hasFilters = computed(() => Boolean(activeFilterCount.value || form.value.search || form.value.stock));

const viewing = ref(null);

const qty = (n) => (n === null ? '—' : Number(n).toLocaleString(undefined, { maximumFractionDigits: 2 }));

const pages = computed(() => {
    const { current_page: current, last_page: last } = props.products;
    const from = Math.max(1, current - 2);
    const to = Math.min(last, current + 2);

    return Array.from({ length: to - from + 1 }, (_, i) => from + i);
});
</script>

<template>
    <Head title="Products" />

    <AppShell title="Products" fill>
        <div class="mx-auto flex h-full w-full max-w-[1600px] flex-col p-4 sm:px-6 sm:py-4">
            <!-- Header -->
            <div class="mb-3 flex shrink-0 flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-xl font-semibold text-content-primary">Products</h1>
                    <p class="mt-0.5 text-[13px] text-content-muted">
                        {{ products.total.toLocaleString() }}
                        {{ products.total === 1 ? 'product' : 'products' }}
                        <span v-if="hasFilters">matching your filters</span>
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a
                        :href="links.labels"
                        class="inline-flex items-center gap-2 rounded-md border border-edge-subtle px-3 py-2 text-sm font-medium text-content-secondary hover:bg-surface-sunken hover:text-content-primary"
                    >
                        <Icon name="orders" :size="16" />
                        Labels
                    </a>
                    <a
                        v-if="links.add"
                        :href="links.add"
                        class="inline-flex items-center gap-2 rounded-md bg-accent-500 px-3 py-2 text-sm font-semibold text-brand-950 hover:bg-accent-400"
                    >
                        <Icon name="stock" :size="16" />
                        Add product
                    </a>
                </div>
            </div>

            <!-- Counts that are also filters -->
            <div class="mb-3 grid shrink-0 grid-cols-3 gap-2">
                <div class="rounded-lg border border-edge-subtle bg-surface-raised px-3 py-2">
                    <div class="text-xs font-medium uppercase tracking-wide text-content-muted">Products</div>
                    <div class="text-xl font-semibold text-content-primary numeric">
                        {{ summary.products.toLocaleString() }}
                    </div>
                </div>

                <button
                    type="button"
                    class="rounded-lg border px-3 py-2 text-left transition-colors"
                    :class="
                        form.stock === 'low'
                            ? 'border-warning bg-warning/10'
                            : 'border-edge-subtle bg-surface-raised hover:bg-surface-sunken'
                    "
                    @click="toggleStock('low')"
                >
                    <div class="text-xs font-medium uppercase tracking-wide text-content-muted">Low stock</div>
                    <div class="text-xl font-semibold text-warning numeric">
                        {{ summary.low_stock.toLocaleString() }}
                    </div>
                </button>

                <button
                    type="button"
                    class="rounded-lg border px-3 py-2 text-left transition-colors"
                    :class="
                        form.stock === 'out'
                            ? 'border-danger bg-danger/10'
                            : 'border-edge-subtle bg-surface-raised hover:bg-surface-sunken'
                    "
                    @click="toggleStock('out')"
                >
                    <div class="text-xs font-medium uppercase tracking-wide text-content-muted">Out of stock</div>
                    <div class="text-xl font-semibold text-danger numeric">
                        {{ summary.out_of_stock.toLocaleString() }}
                    </div>
                </button>
            </div>

            <!-- Search + filters -->
            <div class="mb-3 flex shrink-0 flex-wrap items-center gap-2">
                <div class="relative min-w-[240px] flex-1">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-content-muted">
                        <Icon name="search" :size="16" />
                    </span>
                    <input
                        v-model="form.search"
                        type="search"
                        placeholder="Product name or SKU"
                        class="w-full rounded-md border border-edge-subtle bg-surface-raised py-2 pl-9 pr-3 text-sm text-content-primary placeholder:text-content-muted focus:border-accent-500 focus:outline-none"
                        @input="search"
                    />
                </div>

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

                    <div class="space-y-3 p-3">
                        <label class="block">
                            <span class="mb-1 block text-xs font-medium text-content-muted">Category</span>
                            <select
                                v-model="form.category_id"
                                class="w-full rounded-md border border-edge-subtle bg-surface-page px-2 py-1.5 text-sm text-content-primary focus:border-accent-500 focus:outline-none"
                                @change="refine"
                            >
                                <option :value="null">All categories</option>
                                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                        </label>

                        <label class="block">
                            <span class="mb-1 block text-xs font-medium text-content-muted">Brand</span>
                            <select
                                v-model="form.brand_id"
                                class="w-full rounded-md border border-edge-subtle bg-surface-page px-2 py-1.5 text-sm text-content-primary focus:border-accent-500 focus:outline-none"
                                @change="refine"
                            >
                                <option :value="null">All brands</option>
                                <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.name }}</option>
                            </select>
                        </label>

                        <label v-if="locations.length > 1" class="block">
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

                        <label class="block">
                            <span class="mb-1 block text-xs font-medium text-content-muted">Type</span>
                            <select
                                v-model="form.type"
                                class="w-full rounded-md border border-edge-subtle bg-surface-page px-2 py-1.5 text-sm text-content-primary focus:border-accent-500 focus:outline-none"
                                @change="refine"
                            >
                                <option :value="null">All types</option>
                                <option value="single">Single</option>
                                <option value="variable">Variable</option>
                                <option value="combo">Combo</option>
                            </select>
                        </label>

                        <label class="block">
                            <span class="mb-1 block text-xs font-medium text-content-muted">Status</span>
                            <select
                                v-model="form.active_state"
                                class="w-full rounded-md border border-edge-subtle bg-surface-page px-2 py-1.5 text-sm text-content-primary focus:border-accent-500 focus:outline-none"
                                @change="refine"
                            >
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </label>

                        <button
                            v-if="hasFilters"
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
                                <th class="px-4 py-2.5 text-xs font-semibold uppercase tracking-wide text-content-muted">Product</th>
                                <th class="px-4 py-2.5 text-xs font-semibold uppercase tracking-wide text-content-muted">Category</th>
                                <th class="px-4 py-2.5 text-xs font-semibold uppercase tracking-wide text-content-muted">Brand</th>
                                <th class="px-4 py-2.5 text-right text-xs font-semibold uppercase tracking-wide text-content-muted">Stock</th>
                                <th class="px-4 py-2.5 text-right text-xs font-semibold uppercase tracking-wide text-content-muted">Price</th>
                                <th class="px-4 py-2.5 text-right text-xs font-semibold uppercase tracking-wide text-content-muted">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr
                                v-for="p in products.data"
                                :key="p.id"
                                class="border-b border-edge-subtle last:border-0 hover:bg-surface-page"
                            >
                                <td class="px-4 py-2.5">
                                    <div class="flex items-center gap-2">
                                        <button
                                            v-if="p.actions.show"
                                            type="button"
                                            class="text-left font-medium text-content-primary hover:text-brand-600"
                                            @click="viewing = p"
                                        >
                                            {{ p.name }}
                                        </button>
                                        <span v-else class="font-medium text-content-primary">{{ p.name }}</span>

                                        <span
                                            v-if="p.type !== 'single'"
                                            class="rounded bg-surface-sunken px-1.5 py-0.5 text-xs capitalize text-content-muted"
                                        >
                                            {{ p.type }}
                                        </span>
                                        <span
                                            v-if="p.inactive"
                                            class="rounded bg-surface-sunken px-1.5 py-0.5 text-xs text-content-muted"
                                        >
                                            Inactive
                                        </span>
                                    </div>
                                    <div class="text-xs text-content-muted">{{ p.sku }}</div>
                                </td>
                                <td class="px-4 py-2.5 text-content-secondary">{{ p.category ?? '—' }}</td>
                                <td class="px-4 py-2.5 text-content-secondary">{{ p.brand ?? '—' }}</td>

                                <td class="whitespace-nowrap px-4 py-2.5 text-right">
                                    <template v-if="p.tracked">
                                        <span
                                            class="numeric"
                                            :class="p.out ? 'text-danger' : p.low ? 'text-warning' : 'text-content-primary'"
                                        >
                                            {{ qty(p.stock) }}
                                        </span>
                                        <span class="ml-1 text-xs text-content-muted">{{ p.unit }}</span>
                                    </template>
                                    <span v-else class="text-xs text-content-muted">Not tracked</span>
                                </td>

                                <td class="whitespace-nowrap px-4 py-2.5 text-right text-content-primary">
                                    <Money :value="p.min_price" />
                                    <span v-if="p.max_price > p.min_price" class="text-content-muted">
                                        – <Money :value="p.max_price" />
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-4 py-2.5 text-right">
                                    <Popover :width="208">
                                        <template #trigger="{ toggle }">
                                            <button
                                                type="button"
                                                class="inline-flex items-center gap-1 rounded-md border border-edge-subtle px-2 py-1 text-xs font-medium text-content-secondary hover:bg-surface-sunken hover:text-content-primary"
                                                :aria-label="`Actions for ${p.name}`"
                                                @click="toggle"
                                            >
                                                Actions
                                                <Icon name="chevronDown" :size="13" />
                                            </button>
                                        </template>

                                        <template #default="{ close }">
                                        <div class="py-1">
                                            <!--
                                                View opens the drawer rather than
                                                linking out: the legacy target is a
                                                Bootstrap fragment that renders
                                                unstyled without jQuery.
                                            -->
                                            <button
                                                v-if="p.actions.show"
                                                type="button"
                                                class="flex w-full items-center gap-2 px-3 py-1.5 text-left text-[13px] text-content-secondary hover:bg-surface-sunken hover:text-content-primary"
                                                @click="
                                                    close();
                                                    viewing = p;
                                                "
                                            >
                                                <Icon name="search" :size="14" /> View
                                            </button>

                                            <a
                                                v-for="item in [
                                                    { key: 'edit', label: 'Edit', icon: 'adjustment' },
                                                    { key: 'openingStock', label: 'Opening stock', icon: 'stock' },
                                                    { key: 'history', label: 'Stock history', icon: 'insight' },
                                                    { key: 'sellingPrices', label: 'Selling prices', icon: 'expense' },
                                                    { key: 'duplicate', label: 'Duplicate', icon: 'purchase' },
                                                    { key: 'activate', label: 'Reactivate', icon: 'today' },
                                                ]"
                                                v-show="p.actions[item.key]"
                                                :key="item.key"
                                                :href="p.actions[item.key]"
                                                class="flex items-center gap-2 px-3 py-1.5 text-[13px] text-content-secondary hover:bg-surface-sunken hover:text-content-primary"
                                            >
                                                <Icon :name="item.icon" :size="14" /> {{ item.label }}
                                            </a>
                                        </div>
                                        </template>
                                    </Popover>
                                </td>
                            </tr>

                            <tr v-if="!products.data.length">
                                <td colspan="6" class="px-4 py-16 text-center">
                                    <div class="text-sm font-medium text-content-primary">No products found</div>
                                    <p class="mx-auto mt-1 max-w-sm text-[13px] text-content-muted">
                                        <template v-if="hasFilters">
                                            Nothing matches these filters. Try clearing them.
                                        </template>
                                        <template v-else> Add your first product to get started. </template>
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

                <div
                    v-if="products.total"
                    class="flex shrink-0 flex-wrap items-center justify-between gap-3 border-t border-edge-subtle px-4 py-2.5"
                >
                    <div class="text-[13px] text-content-muted">
                        Showing <span class="text-content-secondary">{{ products.from }}–{{ products.to }}</span>
                        of <span class="text-content-secondary">{{ products.total.toLocaleString() }}</span>

                        <select
                            v-model.number="form.per_page"
                            aria-label="Rows per page"
                            class="ml-2 rounded border border-edge-subtle bg-surface-raised px-1.5 py-1 text-[13px] text-content-secondary focus:border-accent-500 focus:outline-none"
                            @change="refine"
                        >
                            <option v-for="n in perPageOptions" :key="n" :value="n">{{ n }} / page</option>
                        </select>
                    </div>

                    <div v-if="products.last_page > 1" class="flex items-center gap-1">
                        <button
                            type="button"
                            class="rounded px-2 py-1 text-sm text-content-secondary disabled:opacity-40 enabled:hover:bg-surface-sunken"
                            :disabled="products.current_page === 1"
                            @click="goToPage(products.current_page - 1)"
                        >
                            Previous
                        </button>
                        <button
                            v-for="p in pages"
                            :key="p"
                            type="button"
                            class="min-w-[32px] rounded px-2 py-1 text-sm"
                            :class="
                                p === products.current_page
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
                            :disabled="products.current_page === products.last_page"
                            @click="goToPage(products.current_page + 1)"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <ProductDrawer :product="viewing" @close="viewing = null" />
    </AppShell>
</template>
