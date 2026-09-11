<script setup>
import Icon from '../Icon.vue';
import { dismissToast, toasts } from '../../overlays/toast';

const tones = {
    success: 'border-success/30 text-success',
    danger: 'border-danger/30 text-danger',
    info: 'border-info/30 text-info',
};

const icons = { success: 'check', danger: 'alert', info: 'bell' };

const run = (t) => {
    if (t.action?.run) t.action.run();
    if (t.action?.href) window.open(t.action.href, '_blank', 'noopener');
    dismissToast(t.id);
};
</script>

<template>
    <Teleport to="body">
        <div class="pointer-events-none fixed bottom-4 right-4 z-[60] flex w-[min(24rem,calc(100vw-2rem))] flex-col gap-2" aria-live="polite">
            <div
                v-for="t in toasts"
                :key="t.id"
                class="pointer-events-auto flex items-center gap-3 rounded-lg border bg-surface-raised px-3.5 py-2.5 shadow-overlay"
                :class="tones[t.tone] ?? tones.success"
            >
                <Icon :name="icons[t.tone] ?? 'check'" :size="16" class="shrink-0" />
                <p class="min-w-0 flex-1 text-[13px] text-content-primary">{{ t.message }}</p>
                <button
                    v-if="t.action"
                    type="button"
                    class="shrink-0 rounded-md px-2 py-1 text-xs font-semibold text-brand-600 hover:bg-surface-sunken dark:text-brand-300"
                    @click="run(t)"
                >
                    {{ t.action.label }}
                </button>
                <button type="button" class="shrink-0 text-content-muted hover:text-content-primary" aria-label="Dismiss" @click="dismissToast(t.id)">
                    <Icon name="close" :size="14" />
                </button>
            </div>
        </div>
    </Teleport>
</template>
