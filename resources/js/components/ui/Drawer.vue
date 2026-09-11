<script setup>
/**
 * Right-hand drawer: a record's full context while the page stays visible
 * behind it. Esc or a click on the backdrop closes it, unless a modal is
 * open on top — then Esc belongs to the modal.
 */
import { onBeforeUnmount, onMounted, ref } from 'vue';
import Icon from '../Icon.vue';
import { overlays } from '../../overlays/store';

const props = defineProps({
    title: { type: String, default: '' },
    subtitle: { type: String, default: '' },
    /** 'md' ≈ 28rem, 'lg' ≈ 36rem, 'xl' ≈ 48rem */
    width: { type: String, default: 'lg' },
    loading: { type: Boolean, default: false },
    error: { type: String, default: null },
});

const emit = defineEmits(['close', 'retry']);

const panel = ref(null);

const widths = { md: 'max-w-md', lg: 'max-w-xl', xl: 'max-w-3xl' };

const onKey = (e) => {
    if (e.key === 'Escape' && !overlays.modal) emit('close');
};

onMounted(() => {
    document.addEventListener('keydown', onKey);
    panel.value?.focus();
});
onBeforeUnmount(() => document.removeEventListener('keydown', onKey));
</script>

<template>
    <Teleport to="body">
        <div class="fixed inset-0 z-40 flex justify-end">
            <div class="absolute inset-0 bg-neutral-950/40" @click="emit('close')"></div>

            <aside
                ref="panel"
                tabindex="-1"
                role="dialog"
                aria-modal="true"
                :aria-label="title"
                class="relative flex h-full w-full flex-col border-l border-edge-subtle bg-surface-page shadow-overlay focus:outline-none"
                :class="widths[width] ?? widths.lg"
            >
                <header class="flex shrink-0 items-start gap-3 border-b border-edge-subtle bg-surface-raised px-5 py-4">
                    <div class="min-w-0 flex-1">
                        <slot name="eyebrow" />
                        <h2 class="truncate text-lg font-semibold text-content-primary">
                            <slot name="title">{{ title }}</slot>
                        </h2>
                        <p v-if="subtitle || $slots.subtitle" class="mt-0.5 truncate text-[13px] text-content-muted">
                            <slot name="subtitle">{{ subtitle }}</slot>
                        </p>
                    </div>
                    <slot name="header-actions" />
                    <button
                        type="button"
                        class="grid h-8 w-8 shrink-0 place-items-center rounded-md text-content-muted hover:bg-surface-sunken hover:text-content-primary"
                        aria-label="Close"
                        @click="emit('close')"
                    >
                        <Icon name="close" :size="17" />
                    </button>
                </header>

                <div class="scrollbar-slim min-h-0 flex-1 overflow-y-auto">
                    <div v-if="loading" class="space-y-3 p-5" aria-busy="true">
                        <div v-for="n in 5" :key="n" class="h-16 animate-pulse rounded-lg bg-surface-sunken"></div>
                    </div>
                    <div v-else-if="error" class="p-5">
                        <div class="rounded-lg border border-danger/30 bg-danger/5 px-4 py-3 text-sm text-danger">
                            {{ error }}
                            <button type="button" class="ml-2 font-medium underline" @click="emit('retry')">Try again</button>
                        </div>
                    </div>
                    <slot v-else />
                </div>

                <footer v-if="$slots.footer && !loading && !error" class="shrink-0 border-t border-edge-subtle bg-surface-raised px-5 py-3">
                    <slot name="footer" />
                </footer>
            </aside>
        </div>
    </Teleport>
</template>
