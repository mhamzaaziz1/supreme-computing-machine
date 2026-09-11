<script setup>
/**
 * The per-row actions menu for the sales list.
 *
 * The menu is teleported to <body> and positioned from the trigger's own
 * rect: the table scrolls horizontally, and a menu rendered inside that
 * overflow container would be clipped at the edge. It closes on outside
 * click, Escape, scroll and resize, since a fixed menu would otherwise drift
 * away from the row it belongs to.
 *
 * Only actions the server actually sent are rendered — permission decisions
 * stay on the server, and this never shows a control that would 403.
 */
import { computed, nextTick, onBeforeUnmount, ref } from 'vue';
import Icon from '../Icon.vue';

const props = defineProps({
    actions: { type: Object, required: true },
    invoiceNo: { type: String, default: '' },
});

const emit = defineEmits(['view', 'print', 'delete']);

const open = ref(false);
const trigger = ref(null);
const menu = ref(null);
const position = ref({ top: 0, left: 0 });

const MENU_WIDTH = 208;

const place = () => {
    const rect = trigger.value?.getBoundingClientRect();
    if (!rect) return;

    // Prefer dropping below-left of the trigger, but flip up when there is
    // not enough room, so the last rows of a long table stay usable.
    const spaceBelow = window.innerHeight - rect.bottom;
    const estimated = menu.value?.offsetHeight ?? 280;

    position.value = {
        top: spaceBelow < estimated + 16 ? Math.max(8, rect.top - estimated - 4) : rect.bottom + 4,
        left: Math.max(8, Math.min(rect.right - MENU_WIDTH, window.innerWidth - MENU_WIDTH - 8)),
    };
};

const close = () => {
    open.value = false;
    window.removeEventListener('scroll', close, true);
    window.removeEventListener('resize', close);
    document.removeEventListener('keydown', onKey);
    document.removeEventListener('mousedown', onOutside);
};

const onKey = (e) => {
    if (e.key === 'Escape') close();
};

const onOutside = (e) => {
    if (!menu.value?.contains(e.target) && !trigger.value?.contains(e.target)) close();
};

const toggle = async () => {
    if (open.value) return close();

    open.value = true;
    await nextTick();
    place();

    window.addEventListener('scroll', close, true);
    window.addEventListener('resize', close);
    document.addEventListener('keydown', onKey);
    document.addEventListener('mousedown', onOutside);
};

onBeforeUnmount(close);

const run = (event, payload) => {
    close();
    emit(event, payload);
};

/** Print variants share one endpoint and differ only by query string. */
const printUrl = (suffix = '') => props.actions.print + suffix;

const hasDownloads = computed(
    () => props.actions.pdf || props.actions.packingPdf || props.actions.document,
);
const hasMoney = computed(() => props.actions.posPayment || props.actions.sellReturn);
</script>

<template>
    <div class="text-right">
        <button
            ref="trigger"
            type="button"
            class="inline-flex items-center gap-1 rounded-md border border-edge-subtle px-2 py-1 text-xs font-medium text-content-secondary hover:bg-surface-sunken hover:text-content-primary"
            :aria-expanded="open"
            aria-haspopup="menu"
            :aria-label="`Actions for invoice ${invoiceNo}`"
            @click.stop="toggle"
        >
            Actions
            <Icon name="chevronDown" :size="13" />
        </button>

        <Teleport to="body">
            <div
                v-if="open"
                ref="menu"
                role="menu"
                class="fixed z-50 w-52 overflow-hidden rounded-lg border border-edge-subtle bg-surface-raised py-1 shadow-lg"
                :style="{ top: `${position.top}px`, left: `${position.left}px` }"
            >
                <button
                    v-if="actions.show"
                    type="button"
                    role="menuitem"
                    class="flex w-full items-center gap-2 px-3 py-1.5 text-left text-[13px] text-content-secondary hover:bg-surface-sunken hover:text-content-primary"
                    @click="run('view')"
                >
                    <Icon name="search" :size="14" /> View details
                </button>

                <a
                    v-if="actions.edit"
                    :href="actions.edit"
                    target="_blank"
                    role="menuitem"
                    class="flex items-center gap-2 px-3 py-1.5 text-[13px] text-content-secondary hover:bg-surface-sunken hover:text-content-primary"
                    @click="close"
                >
                    <Icon name="adjustment" :size="14" /> Edit
                </a>

                <div v-if="actions.print" class="my-1 border-t border-edge-subtle"></div>

                <template v-if="actions.print">
                    <button
                        type="button"
                        role="menuitem"
                        class="flex w-full items-center gap-2 px-3 py-1.5 text-left text-[13px] text-content-secondary hover:bg-surface-sunken hover:text-content-primary"
                        @click="run('print', printUrl())"
                    >
                        <Icon name="orders" :size="14" /> Print invoice
                    </button>
                    <button
                        type="button"
                        role="menuitem"
                        class="flex w-full items-center gap-2 px-3 py-1.5 text-left text-[13px] text-content-secondary hover:bg-surface-sunken hover:text-content-primary"
                        @click="run('print', printUrl('?package_slip=true'))"
                    >
                        <Icon name="orders" :size="14" /> Packing slip
                    </button>
                    <button
                        type="button"
                        role="menuitem"
                        class="flex w-full items-center gap-2 px-3 py-1.5 text-left text-[13px] text-content-secondary hover:bg-surface-sunken hover:text-content-primary"
                        @click="run('print', printUrl('?delivery_note=true'))"
                    >
                        <Icon name="truck" :size="14" /> Delivery note
                    </button>
                </template>

                <div v-if="hasDownloads" class="my-1 border-t border-edge-subtle"></div>

                <a
                    v-if="actions.pdf"
                    :href="actions.pdf"
                    target="_blank"
                    role="menuitem"
                    class="flex items-center gap-2 px-3 py-1.5 text-[13px] text-content-secondary hover:bg-surface-sunken hover:text-content-primary"
                    @click="close"
                >
                    <Icon name="purchase" :size="14" /> Download PDF
                </a>
                <a
                    v-if="actions.packingPdf"
                    :href="actions.packingPdf"
                    target="_blank"
                    role="menuitem"
                    class="flex items-center gap-2 px-3 py-1.5 text-[13px] text-content-secondary hover:bg-surface-sunken hover:text-content-primary"
                    @click="close"
                >
                    <Icon name="purchase" :size="14" /> Packing slip PDF
                </a>
                <a
                    v-if="actions.document"
                    :href="actions.document"
                    download
                    role="menuitem"
                    class="flex items-center gap-2 px-3 py-1.5 text-[13px] text-content-secondary hover:bg-surface-sunken hover:text-content-primary"
                    @click="close"
                >
                    <Icon name="purchase" :size="14" /> Attached document
                </a>

                <div v-if="hasMoney" class="my-1 border-t border-edge-subtle"></div>

                <a
                    v-if="actions.posPayment"
                    :href="actions.posPayment"
                    role="menuitem"
                    class="flex items-center gap-2 px-3 py-1.5 text-[13px] text-content-secondary hover:bg-surface-sunken hover:text-content-primary"
                    @click="close"
                >
                    <Icon name="expense" :size="14" /> Add / edit payment
                </a>
                <a
                    v-if="actions.sellReturn"
                    :href="actions.sellReturn"
                    role="menuitem"
                    class="flex items-center gap-2 px-3 py-1.5 text-[13px] text-content-secondary hover:bg-surface-sunken hover:text-content-primary"
                    @click="close"
                >
                    <Icon name="transfer" :size="14" /> Sell return
                </a>

                <template v-if="actions.delete">
                    <div class="my-1 border-t border-edge-subtle"></div>
                    <button
                        type="button"
                        role="menuitem"
                        class="flex w-full items-center gap-2 px-3 py-1.5 text-left text-[13px] text-danger hover:bg-danger/10"
                        @click="run('delete')"
                    >
                        <Icon name="close" :size="14" /> Delete
                    </button>
                </template>
            </div>
        </Teleport>
    </div>
</template>
