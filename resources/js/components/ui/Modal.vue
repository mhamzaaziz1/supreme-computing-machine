<script setup>
/**
 * A task that commits money or stock. Blocks the screen, confirms
 * explicitly, and asks before throwing away typed input.
 *
 * `tone` colours the top edge so a credit hold reads as a stop before a
 * word of it is read.
 */
import { onBeforeUnmount, onMounted, ref } from 'vue';
import Icon from '../Icon.vue';

const props = defineProps({
    title: { type: String, required: true },
    eyebrow: { type: String, default: '' },
    tone: { type: String, default: 'brand' },
    /** 'sm' 26rem, 'md' 32rem, 'lg' 44rem, 'xl' 60rem */
    width: { type: String, default: 'md' },
    /** Ask before closing, because the user has typed something. */
    dirty: { type: Boolean, default: false },
    busy: { type: Boolean, default: false },
});

const emit = defineEmits(['close']);

const box = ref(null);

const widths = { sm: 'max-w-md', md: 'max-w-lg', lg: 'max-w-2xl', xl: 'max-w-4xl' };
const tones = {
    brand: 'border-t-brand-600',
    danger: 'border-t-danger',
    warning: 'border-t-warning',
    success: 'border-t-success',
};

const requestClose = () => {
    if (props.busy) return;
    if (props.dirty && !window.confirm('Discard what you have entered?')) return;
    emit('close');
};

const onKey = (e) => {
    if (e.key === 'Escape') {
        e.stopPropagation();
        requestClose();
    }
};

onMounted(() => {
    document.addEventListener('keydown', onKey);
    const first = box.value?.querySelector('[autofocus], input:not([type=hidden]), select, textarea, button:not([aria-label=Close])');
    (first ?? box.value)?.focus();
});
onBeforeUnmount(() => document.removeEventListener('keydown', onKey));

defineExpose({ requestClose });
</script>

<template>
    <Teleport to="body">
        <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-neutral-950/55 px-4 py-[8vh]" @mousedown.self="requestClose">
            <div
                ref="box"
                tabindex="-1"
                role="dialog"
                aria-modal="true"
                :aria-label="title"
                class="relative w-full overflow-hidden rounded-xl border-t-4 bg-surface-raised shadow-overlay ring-1 ring-edge-subtle focus:outline-none"
                :class="[widths[width] ?? widths.md, tones[tone] ?? tones.brand]"
            >
                <header class="flex items-start gap-3 px-5 pb-2 pt-4">
                    <div class="min-w-0 flex-1">
                        <p v-if="eyebrow" class="text-[11px] font-semibold uppercase tracking-wider text-content-muted">{{ eyebrow }}</p>
                        <h2 class="text-lg font-semibold leading-snug text-content-primary">{{ title }}</h2>
                    </div>
                    <button
                        type="button"
                        class="grid h-8 w-8 shrink-0 place-items-center rounded-md text-content-muted hover:bg-surface-sunken hover:text-content-primary disabled:opacity-40"
                        aria-label="Close"
                        :disabled="busy"
                        @click="requestClose"
                    >
                        <Icon name="close" :size="17" />
                    </button>
                </header>

                <div class="px-5 pb-4">
                    <slot />
                </div>

                <footer v-if="$slots.footer" class="flex flex-wrap items-center justify-end gap-2 border-t border-edge-subtle bg-surface-page px-5 py-3">
                    <slot name="footer" />
                </footer>
            </div>
        </div>
    </Teleport>
</template>
