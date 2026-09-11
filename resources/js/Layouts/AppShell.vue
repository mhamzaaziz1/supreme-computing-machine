<script setup>
/**
 * The application shell: sidebar, top chrome, command palette, flash.
 *
 * Pages render into the default slot and should not concern themselves with
 * navigation at all. `title` is the only thing the shell needs from a page.
 */
import { onMounted, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Sidebar from '../components/Nav/Sidebar.vue';
import Topbar from '../components/Nav/Topbar.vue';
import CommandPalette from '../components/CommandPalette.vue';
import OverlayHost from '../components/overlays/OverlayHost.vue';
import Icon from '../components/Icon.vue';

defineProps({
    title: { type: String, default: '' },
    /**
     * Fill the viewport instead of letting the page scroll.
     *
     * The default lets `main` scroll, which suits a document-shaped page. A
     * list or a form is better off fitting the screen with its own region
     * scrolling — the header, totals and pagination stay put instead of
     * sliding away, so the controls are where you left them. Pages that opt
     * in must lay themselves out as `h-full` columns.
     */
    fill: { type: Boolean, default: false },
});

const page = usePage();
const palette = ref(null);
const collapsed = ref(false);
const flashDismissed = ref(false);

const STORAGE_KEY = 'gj_sidebar_collapsed';

onMounted(() => {
    try {
        collapsed.value = localStorage.getItem(STORAGE_KEY) === '1';
    } catch {
        // Private browsing or blocked storage: the default is fine.
    }
});

const toggleSidebar = () => {
    collapsed.value = !collapsed.value;
    try {
        localStorage.setItem(STORAGE_KEY, collapsed.value ? '1' : '0');
    } catch {
        // Non-fatal: the preference simply will not persist.
    }
};
</script>

<template>
    <div class="flex h-screen overflow-hidden bg-surface-page">
        <Sidebar :collapsed="collapsed" class="hidden shrink-0 md:flex" />

        <div class="flex min-w-0 flex-1 flex-col">
            <Topbar
                :title="title"
                @toggle-sidebar="toggleSidebar"
                @open-search="palette?.show()"
            />

            <div
                v-if="page.props.flash?.success && !flashDismissed"
                class="flex items-center gap-2 border-b border-success/25 bg-success/10 px-4 py-2 text-sm text-success"
            >
                <span class="flex-1">{{ page.props.flash.success }}</span>
                <button type="button" aria-label="Dismiss" @click="flashDismissed = true">
                    <Icon name="close" :size="15" />
                </button>
            </div>

            <div
                v-else-if="page.props.flash?.error && !flashDismissed"
                class="flex items-center gap-2 border-b border-danger/25 bg-danger/10 px-4 py-2 text-sm text-danger"
            >
                <Icon name="alert" :size="15" class="shrink-0" />
                <span class="flex-1">{{ page.props.flash.error }}</span>
                <button type="button" aria-label="Dismiss" @click="flashDismissed = true">
                    <Icon name="close" :size="15" />
                </button>
            </div>

            <main
                class="scrollbar-slim min-h-0 flex-1"
                :class="fill ? 'overflow-hidden' : 'overflow-y-auto'"
            >
                <slot />
            </main>
        </div>

        <CommandPalette ref="palette" />
        <OverlayHost />
    </div>
</template>
