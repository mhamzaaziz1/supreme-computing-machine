<script setup>
/**
 * An invoice by id, for places that only know the id (the command palette,
 * an outlet's timeline). Reuses the sales list's own detail drawer.
 */
import { computed } from 'vue';
import SaleDrawer from '../Sales/SaleDrawer.vue';

const props = defineProps({ id: { type: Number, required: true } });
const emit = defineEmits(['close']);

const appBase = (document.querySelector('meta[name="app-base"]')?.content ?? window.location.origin).replace(/\/$/, '');

const sell = computed(() => ({ id: props.id, actions: { show: `${appBase}/sales/${props.id}` } }));

const print = () => window.open(`${appBase}/sells/${props.id}/print`, '_blank', 'noopener');
</script>

<template>
    <SaleDrawer :sell="sell" @close="emit('close')" @print="print" />
</template>
