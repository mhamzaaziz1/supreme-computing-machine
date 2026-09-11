<script setup>
/**
 * Product detail, as a side drawer.
 *
 * Replaces the legacy "View" action, which linked to product.view-modal — a
 * Bootstrap fragment that needs jQuery to be dropped into a modal. The
 * Inertia shell ships neither, so following that link produced a page of raw
 * unstyled markup. This fetches JSON from catalog.show and renders it.
 *
 * Ordered by what the screen gets opened for: where the stock is, then what
 * it sells for, then the classification.
 */
import { ref, watch } from 'vue';
import Icon from '../Icon.vue';
import Money from '../Money.vue';
import { api } from '../../overlays/api';
import { toast } from '../../overlays/toast';

const props = defineProps({
    /** The row to show, or null when closed. */
    product: { type: Object, default: null },
});

const emit = defineEmits(['close']);

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
        error.value = e.message ?? 'Could not load this product.';
    } finally {
        loading.value = false;
    }
};

watch(
    () => props.product,
    (row) => {
        if (row?.actions?.show) load(row.actions.show);
    },
    { immediate: true },
);

const qty = (n) => Number(n ?? 0).toLocaleString(undefined, { maximumFractionDigits: 2 });

/**
 * Pack size in litres: what one selling unit holds (4 for a 4 L can, 208
 * for a drum, 12 for a carton of 12 × 1 L). Principal targets and margin
 * per litre are computed from it.
 */
const pack = ref('');
const savingPack = ref(false);
watch(detail, (d) => (pack.value = d?.product.pack_litres ?? ''));

const savePack = async () => {
    savingPack.value = true;
    try {
        const r = await api(`products/${detail.value.product.id}/pack`, {
            method: 'PATCH',
            body: { pack_litres: pack.value === '' ? null : Number(pack.value) },
        });
        detail.value.product.pack_litres = pack.value === '' ? null : Number(pack.value);
        toast(r.message);
    } catch (e) {
        toast(e.message, { tone: 'danger' });
    } finally {
        savingPack.value = false;
    }
};
</script>

<template>
    <Teleport to="body">
        <div v-if="product" class="fixed inset-0 z-40 flex justify-end">
            <div class="absolute inset-0 bg-black/40" @click="emit('close')"></div>

            <aside
                class="relative flex h-full w-full max-w-lg flex-col border-l border-edge-subtle bg-surface-page shadow-2xl"
                role="dialog"
                aria-modal="true"
                :aria-label="product.name"
            >
                <header class="flex shrink-0 items-start justify-between gap-3 border-b border-edge-subtle px-5 py-4">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="truncate text-lg font-semibold text-content-primary">{{ product.name }}</h2>
                            <span
                                v-if="product.type !== 'single'"
                                class="rounded bg-surface-sunken px-1.5 py-0.5 text-xs capitalize text-content-muted"
                            >
                                {{ product.type }}
                            </span>
                            <span
                                v-if="product.inactive"
                                class="rounded bg-surface-sunken px-1.5 py-0.5 text-xs text-content-muted"
                            >
                                Inactive
                            </span>
                        </div>
                        <p class="mt-0.5 text-[13px] text-content-muted">{{ product.sku }}</p>
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

                <div class="min-h-0 flex-1 overflow-y-auto px-5 py-4">
                    <p v-if="loading" class="py-12 text-center text-sm text-content-muted">Loading product…</p>

                    <div
                        v-else-if="error"
                        class="rounded-lg border border-danger/30 bg-danger/5 px-4 py-3 text-sm text-danger"
                    >
                        {{ error }}
                    </div>

                    <template v-else-if="detail">
                        <!-- Stock first: it is why the screen is open. -->
                        <section v-if="detail.product.tracked" class="mb-5">
                            <div class="mb-2 flex items-baseline justify-between">
                                <h3 class="text-xs font-semibold uppercase tracking-wide text-content-muted">
                                    Stock on hand
                                </h3>
                                <span
                                    class="numeric text-lg font-semibold"
                                    :class="
                                        detail.total_stock <= 0
                                            ? 'text-danger'
                                            : product.low
                                              ? 'text-warning'
                                              : 'text-content-primary'
                                    "
                                >
                                    {{ qty(detail.total_stock) }}
                                    <span class="text-xs font-normal text-content-muted">
                                        {{ detail.product.unit }}
                                    </span>
                                </span>
                            </div>

                            <div class="overflow-hidden rounded-lg border border-edge-subtle bg-surface-raised">
                                <table class="w-full border-collapse text-sm">
                                    <tbody>
                                        <tr
                                            v-for="s in detail.stock"
                                            :key="s.location"
                                            class="border-b border-edge-subtle last:border-0"
                                        >
                                            <td class="px-3 py-2 text-content-secondary">{{ s.location }}</td>
                                            <td
                                                class="numeric px-3 py-2 text-right"
                                                :class="s.qty <= 0 ? 'text-danger' : 'text-content-primary'"
                                            >
                                                {{ qty(s.qty) }}
                                            </td>
                                        </tr>
                                        <tr v-if="!detail.stock.length">
                                            <td colspan="2" class="px-3 py-6 text-center text-[13px] text-content-muted">
                                                No stock recorded at any location you can see.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <p
                                v-if="detail.product.alert_quantity"
                                class="mt-1.5 text-xs text-content-muted"
                            >
                                Alerts below {{ qty(detail.product.alert_quantity) }} {{ detail.product.unit }}.
                            </p>
                        </section>

                        <p v-else class="mb-5 text-[13px] text-content-muted">Stock is not tracked for this product.</p>

                        <!-- Prices -->
                        <section class="mb-5">
                            <h3 class="mb-2 text-xs font-semibold uppercase tracking-wide text-content-muted">
                                {{ detail.variations.length > 1 ? `Variations (${detail.variations.length})` : 'Price' }}
                            </h3>

                            <div class="overflow-hidden rounded-lg border border-edge-subtle bg-surface-raised">
                                <table class="w-full border-collapse text-sm">
                                    <thead>
                                        <tr class="border-b border-edge-subtle bg-surface-sunken text-left">
                                            <th class="px-3 py-2 text-xs font-semibold uppercase tracking-wide text-content-muted">
                                                {{ detail.variations.length > 1 ? 'Variation' : 'SKU' }}
                                            </th>
                                            <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wide text-content-muted">
                                                Cost
                                            </th>
                                            <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wide text-content-muted">
                                                Sells for
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="v in detail.variations"
                                            :key="v.id"
                                            class="border-b border-edge-subtle last:border-0"
                                        >
                                            <td class="px-3 py-2">
                                                <div class="text-content-primary">{{ v.name ?? v.sku }}</div>
                                                <div v-if="v.name" class="text-xs text-content-muted">{{ v.sku }}</div>
                                            </td>
                                            <td class="numeric px-3 py-2 text-right text-content-secondary">
                                                <Money :value="v.purchase_price" />
                                            </td>
                                            <td class="numeric px-3 py-2 text-right text-content-primary">
                                                <Money :value="v.sell_price" />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>

                        <!-- Classification -->
                        <section>
                            <h3 class="mb-2 text-xs font-semibold uppercase tracking-wide text-content-muted">
                                Details
                            </h3>

                            <dl class="overflow-hidden rounded-lg border border-edge-subtle bg-surface-raised text-sm">
                                <div
                                    v-for="row in [
                                        { label: 'Category', value: detail.product.category },
                                        { label: 'Sub category', value: detail.product.sub_category },
                                        { label: 'Brand', value: detail.product.brand },
                                        { label: 'Unit', value: detail.product.unit },
                                        { label: 'Tax', value: detail.product.tax },
                                        { label: 'Weight', value: detail.product.weight },
                                    ]"
                                    :key="row.label"
                                    class="flex justify-between gap-3 border-b border-edge-subtle px-3 py-2 last:border-0"
                                >
                                    <dt class="text-content-muted">{{ row.label }}</dt>
                                    <dd class="text-right text-content-primary">{{ row.value || '—' }}</dd>
                                </div>
                                <form
                                    class="flex items-center justify-between gap-3 px-3 py-2"
                                    @submit.prevent="savePack"
                                >
                                    <label for="pack-litres" class="text-content-muted">Pack size</label>
                                    <span v-if="!detail.product.can_edit_pack" class="text-right text-content-primary">
                                        {{ detail.product.pack_litres ? `${detail.product.pack_litres} L` : '—' }}
                                    </span>
                                    <span v-else class="flex items-center gap-1.5">
                                        <input
                                            id="pack-litres"
                                            v-model="pack"
                                            type="number"
                                            min="0"
                                            step="any"
                                            placeholder="litres"
                                            class="w-24 rounded border border-edge-subtle bg-surface-page px-2 py-1 text-right text-sm text-content-primary focus:border-accent-500 focus:outline-none"
                                        />
                                        <span class="text-content-muted">L</span>
                                        <button
                                            v-if="String(pack) !== String(detail.product.pack_litres ?? '')"
                                            type="submit"
                                            :disabled="savingPack"
                                            class="rounded bg-brand-600 px-2 py-1 text-xs font-semibold text-white hover:bg-brand-700 disabled:opacity-50"
                                        >
                                            Save
                                        </button>
                                    </span>
                                </form>
                            </dl>

                            <p v-if="detail.product.description" class="mt-3 text-[13px] text-content-secondary">
                                {{ detail.product.description.replace(/<[^>]*>/g, '') }}
                            </p>
                        </section>
                    </template>
                </div>

                <footer class="flex shrink-0 flex-wrap gap-2 border-t border-edge-subtle bg-surface-raised px-5 py-3">
                    <a
                        v-if="product.actions.edit"
                        :href="product.actions.edit"
                        class="inline-flex items-center gap-2 rounded-md bg-accent-500 px-3 py-1.5 text-sm font-semibold text-brand-950 hover:bg-accent-400"
                    >
                        <Icon name="adjustment" :size="15" /> Edit
                    </a>
                    <a
                        v-if="product.actions.history"
                        :href="product.actions.history"
                        class="inline-flex items-center gap-2 rounded-md border border-edge-subtle px-3 py-1.5 text-sm font-medium text-content-secondary hover:bg-surface-sunken hover:text-content-primary"
                    >
                        <Icon name="insight" :size="15" /> Stock history
                    </a>
                    <a
                        v-if="product.actions.openingStock"
                        :href="product.actions.openingStock"
                        class="inline-flex items-center gap-2 rounded-md border border-edge-subtle px-3 py-1.5 text-sm font-medium text-content-secondary hover:bg-surface-sunken hover:text-content-primary"
                    >
                        <Icon name="stock" :size="15" /> Opening stock
                    </a>
                </footer>
            </aside>
        </div>
    </Teleport>
</template>
