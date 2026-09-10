<script setup>
/**
 * Renders whatever drawer and modal are open, plus the toast stack.
 * Mounted once by the shell, so any page (or the command palette) can call
 * openOverlay() without owning the markup.
 */
import { onMounted } from 'vue';
import '../../overlays/registry';
import { closeOverlay, overlays, restoreFromUrl } from '../../overlays/store';
import ToastHost from '../ui/ToastHost.vue';

onMounted(restoreFromUrl);
</script>

<template>
    <component
        :is="overlays.drawer.component"
        v-if="overlays.drawer"
        :key="overlays.drawer.key"
        v-bind="overlays.drawer.props"
        @close="closeOverlay('drawer')"
    />
    <component
        :is="overlays.modal.component"
        v-if="overlays.modal"
        :key="overlays.modal.key"
        v-bind="overlays.modal.props"
        @close="closeOverlay('modal')"
    />
    <ToastHost />
</template>
