<script setup>
/**
 * Currency formatting that honours the business's configured symbol,
 * separators, precision and symbol placement. Rendered with tabular figures
 * so columns of money line up.
 */
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    value: { type: [Number, String], default: 0 },
    /** Drops the decimals — for headline figures where they are noise. */
    compact: { type: Boolean, default: false },
});

const page = usePage();

const formatted = computed(() => {
    const c = page.props.currency ?? {};
    const precision = props.compact ? 0 : (c.precision ?? 2);
    const n = Number(props.value) || 0;

    const [whole, fraction] = Math.abs(n).toFixed(precision).split('.');
    const grouped = whole.replace(/\B(?=(\d{3})+(?!\d))/g, c.thousand_separator ?? ',');

    const number =
        (n < 0 ? '-' : '') + grouped + (fraction ? (c.decimal_separator ?? '.') + fraction : '');

    const symbol = c.symbol ?? '';

    return c.symbol_placement === 'after' ? `${number} ${symbol}` : `${symbol} ${number}`;
});
</script>

<template>
    <span class="numeric">{{ formatted }}</span>
</template>
