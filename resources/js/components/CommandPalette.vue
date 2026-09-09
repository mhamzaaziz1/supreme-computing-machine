<script setup>
/**
 * Ctrl/Cmd-K palette.
 *
 * The real cost of the old navigation was traversal: five clicks through
 * three menus to reach a screen you visit twenty times a day. This makes
 * every destination one keystroke and a few characters away, which matters
 * more than how the menu itself is grouped.
 *
 * Currently searches navigation destinations. Record search (customers,
 * invoices, products) plugs into the same list via an async source.
 */
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Icon from './Icon.vue';

const page = usePage();
const open = ref(false);
const query = ref('');
const cursor = ref(0);
const input = ref(null);
const listEl = ref(null);

/** Flatten the nav tree into one searchable list, keeping the group name. */
const destinations = computed(() => {
    const out = [];

    for (const group of page.props.nav ?? []) {
        if (group.url) {
            out.push({ label: group.label, group: null, url: group.url, spa: group.spa, icon: group.icon });
        }
        for (const item of group.items) {
            out.push({ label: item.label, group: group.label, url: item.url, spa: item.spa, icon: group.icon });
        }
    }

    for (const item of page.props.settingsNav ?? []) {
        out.push({ label: item.label, group: 'Settings', url: item.url, spa: item.spa, icon: 'settings' });
    }

    return out;
});

/**
 * Subsequence match, so "prodl" finds "Product List" and "vexp" finds
 * "Vehicle Expenses". Scores earlier and more contiguous matches higher.
 */
const score = (text, q) => {
    const haystack = text.toLowerCase();
    let i = 0;
    let points = 0;
    let streak = 0;

    for (const ch of q) {
        const at = haystack.indexOf(ch, i);
        if (at === -1) return -1;
        streak = at === i ? streak + 1 : 0;
        points += streak * 2 + (at === 0 ? 5 : 0) - Math.min(at - i, 4);
        i = at + 1;
    }

    // Shorter labels win ties: "Brands" should beat "Brands Import Template".
    return points - haystack.length * 0.05;
};

const results = computed(() => {
    const q = query.value.trim().toLowerCase();
    if (!q) return destinations.value.slice(0, 8);

    return destinations.value
        .map((d) => ({ d, s: Math.max(score(d.label, q), score(`${d.group ?? ''} ${d.label}`, q) - 2) }))
        .filter((r) => r.s > -1)
        .sort((a, b) => b.s - a.s)
        .slice(0, 12)
        .map((r) => r.d);
});

watch(query, () => (cursor.value = 0));
watch(cursor, async () => {
    await nextTick();
    listEl.value?.querySelector('[data-active]')?.scrollIntoView({ block: 'nearest' });
});

const show = async () => {
    open.value = true;
    query.value = '';
    cursor.value = 0;
    await nextTick();
    input.value?.focus();
};

const hide = () => (open.value = false);

const go = (dest) => {
    if (!dest) return;
    hide();
    if (dest.spa) {
        router.visit(dest.url);
    } else {
        window.location.href = dest.url;
    }
};

const onKeydown = (e) => {
    const combo = (e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k';

    if (combo) {
        e.preventDefault();
        open.value ? hide() : show();
        return;
    }

    if (!open.value) return;

    if (e.key === 'Escape') {
        e.preventDefault();
        hide();
    } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        cursor.value = (cursor.value + 1) % Math.max(results.value.length, 1);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        cursor.value = (cursor.value - 1 + results.value.length) % Math.max(results.value.length, 1);
    } else if (e.key === 'Enter') {
        e.preventDefault();
        go(results.value[cursor.value]);
    }
};

onMounted(() => window.addEventListener('keydown', onKeydown));
onUnmounted(() => window.removeEventListener('keydown', onKeydown));

defineExpose({ show });
</script>

<template>
    <div>
        <!-- Global, so the shortcut works from any page including Blade-rendered ones. -->
        <teleport to="body">
            <div
                v-if="open"
                class="fixed inset-0 z-50 flex items-start justify-center bg-neutral-950/50 px-4 pt-[12vh] backdrop-blur-[2px]"
                @click.self="hide"
            >
                <div
                    class="w-full max-w-xl overflow-hidden rounded-xl bg-surface-raised shadow-overlay ring-1 ring-edge-subtle"
                    role="dialog"
                    aria-modal="true"
                    aria-label="Search"
                >
                    <div class="flex items-center gap-3 border-b border-edge-subtle px-4">
                        <Icon name="search" :size="18" class="shrink-0 text-content-muted" />
                        <input
                            ref="input"
                            v-model="query"
                            type="text"
                            placeholder="Jump to…"
                            class="w-full bg-transparent py-3.5 text-[15px] text-content-primary placeholder:text-content-muted focus:outline-none"
                            autocomplete="off"
                            spellcheck="false"
                        />
                        <kbd
                            class="shrink-0 rounded border border-edge-subtle px-1.5 py-0.5 text-[10px] font-medium text-content-muted"
                        >
                            ESC
                        </kbd>
                    </div>

                    <ul
                        v-if="results.length"
                        ref="listEl"
                        class="scrollbar-slim max-h-80 overflow-y-auto p-1.5"
                    >
                        <li v-for="(r, i) in results" :key="r.url + r.label">
                            <button
                                type="button"
                                :data-active="i === cursor ? '' : null"
                                class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left transition-colors"
                                :class="
                                    i === cursor
                                        ? 'bg-brand-600 text-white'
                                        : 'text-content-primary hover:bg-surface-sunken'
                                "
                                @click="go(r)"
                                @mousemove="cursor = i"
                            >
                                <Icon
                                    :name="r.icon"
                                    :size="17"
                                    class="shrink-0"
                                    :class="i === cursor ? 'text-white/80' : 'text-content-muted'"
                                />
                                <span class="truncate text-sm">{{ r.label }}</span>
                                <span
                                    v-if="r.group"
                                    class="ml-auto shrink-0 truncate text-xs"
                                    :class="i === cursor ? 'text-white/70' : 'text-content-muted'"
                                >
                                    {{ r.group }}
                                </span>
                            </button>
                        </li>
                    </ul>

                    <p v-else class="px-4 py-8 text-center text-sm text-content-muted">
                        Nothing matches “{{ query }}”.
                    </p>
                </div>
            </div>
        </teleport>
    </div>
</template>
