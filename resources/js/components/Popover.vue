<script setup>
/**
 * An overlay anchored to its trigger.
 *
 * Teleported to <body> and positioned from the trigger's rect, because the
 * panels that need one sit inside horizontally scrolling tables and overflow
 * containers that would otherwise clip it. Being fixed, it closes on scroll
 * and resize rather than drifting away from the control it belongs to, and
 * flips above the trigger when there is no room below.
 *
 * Slots: `trigger` (receives { open, toggle }) and default (the panel body).
 */
import { nextTick, onBeforeUnmount, ref } from 'vue';

const props = defineProps({
    /** Panel width in px; also the width used to keep it on screen. */
    width: { type: Number, default: 280 },
    /** 'left' pins the panel's left edge to the trigger, 'right' its right. */
    align: { type: String, default: 'right' },
});

const emit = defineEmits(['open', 'close']);

const open = ref(false);
const trigger = ref(null);
const panel = ref(null);
const position = ref({ top: 0, left: 0 });

const place = () => {
    const rect = trigger.value?.getBoundingClientRect();
    if (!rect) return;

    const height = panel.value?.offsetHeight ?? 320;
    const spaceBelow = window.innerHeight - rect.bottom;
    const left = props.align === 'left' ? rect.left : rect.right - props.width;

    position.value = {
        top: spaceBelow < height + 16 ? Math.max(8, rect.top - height - 6) : rect.bottom + 6,
        left: Math.max(8, Math.min(left, window.innerWidth - props.width - 8)),
    };
};

const close = () => {
    if (!open.value) return;

    open.value = false;
    window.removeEventListener('scroll', close, true);
    window.removeEventListener('resize', close);
    document.removeEventListener('keydown', onKey);
    document.removeEventListener('mousedown', onOutside);
    emit('close');
};

const onKey = (e) => {
    if (e.key === 'Escape') close();
};

const onOutside = (e) => {
    if (!panel.value?.contains(e.target) && !trigger.value?.contains(e.target)) close();
};

const show = async () => {
    open.value = true;
    emit('open');

    await nextTick();
    place();

    window.addEventListener('scroll', close, true);
    window.addEventListener('resize', close);
    document.addEventListener('keydown', onKey);
    document.addEventListener('mousedown', onOutside);
};

const toggle = () => (open.value ? close() : show());

onBeforeUnmount(close);

defineExpose({ close });
</script>

<template>
    <div ref="trigger" class="inline-flex">
        <slot name="trigger" :open="open" :toggle="toggle" />
    </div>

    <Teleport to="body">
        <div
            v-if="open"
            ref="panel"
            class="fixed z-50 overflow-hidden rounded-lg border border-edge-subtle bg-surface-raised shadow-lg"
            :style="{ top: `${position.top}px`, left: `${position.left}px`, width: `${width}px` }"
        >
            <slot :close="close" />
        </div>
    </Teleport>
</template>
