<script setup>
/**
 * Primary navigation.
 *
 * Six job-shaped groups instead of seventeen table-shaped ones. Only one
 * group is expanded at a time, and the group containing the current page
 * opens automatically, so the list never grows past roughly a screen.
 *
 * Destinations are a mix of Inertia pages and not-yet-migrated Blade pages;
 * `item.spa` decides whether a click is a client-side visit or a full load.
 */
import { computed, ref, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Icon from '../Icon.vue';

const props = defineProps({
    collapsed: { type: Boolean, default: false },
});

const page = usePage();
const nav = computed(() => page.props.nav ?? []);
const business = computed(() => page.props.business ?? {});

/** Longest matching URL wins, so /sells/create beats /sells. */
const activeUrl = computed(() => {
    const here = page.url.split('?')[0];
    let best = null;

    for (const group of nav.value) {
        for (const item of group.items) {
            const path = new URL(item.url, window.location.origin).pathname;
            if ((here === path || here.startsWith(path + '/')) && (!best || path.length > best.length)) {
                best = path;
            }
        }
    }

    return best;
});

const isActive = (item) =>
    activeUrl.value !== null &&
    new URL(item.url, window.location.origin).pathname === activeUrl.value;

const groupContainsActive = (group) => group.items.some(isActive);

const openGroup = ref(null);

// Follow the page: after any navigation, reveal the group you landed in.
watch(
    () => page.url,
    () => {
        const found = nav.value.findIndex(groupContainsActive);
        if (found !== -1) openGroup.value = found;
    },
    { immediate: true },
);

const toggle = (index) => {
    openGroup.value = openGroup.value === index ? null : index;
};
</script>

<template>
    <nav
        class="flex h-full flex-col bg-nav-bg text-nav-fg transition-[width] duration-200"
        :class="collapsed ? 'w-[68px]' : 'w-64'"
        aria-label="Main"
    >
        <!-- Brand -->
        <a
            :href="page.props.routes?.home"
            class="flex h-14 shrink-0 items-center gap-2.5 border-b border-nav-border px-4"
        >
            <img
                v-if="business.logo"
                :src="business.logo"
                alt=""
                class="h-8 w-8 shrink-0 rounded object-contain"
            />
            <span
                v-else
                class="grid h-8 w-8 shrink-0 place-items-center rounded bg-accent-500 text-sm font-bold text-brand-950"
            >
                {{ (business.name ?? '?').charAt(0).toUpperCase() }}
            </span>
            <span
                v-if="!collapsed"
                class="truncate text-sm font-semibold text-nav-fg-active"
            >
                {{ business.name }}
            </span>
        </a>

        <div class="scrollbar-slim flex-1 overflow-y-auto overflow-x-hidden py-2">
            <ul class="space-y-0.5 px-2">
                <li v-for="(group, i) in nav" :key="group.label">
                    <!-- A group with its own URL is a plain link (Today). -->
                    <a
                        v-if="group.url"
                        :href="group.url"
                        class="group flex items-center gap-3 rounded-md px-2.5 py-2 text-sm font-medium transition-colors hover:bg-white/5 hover:text-nav-fg-active"
                        :title="collapsed ? group.label : null"
                    >
                        <Icon :name="group.icon" :size="19" class="shrink-0" />
                        <span v-if="!collapsed" class="truncate">{{ group.label }}</span>
                    </a>

                    <template v-else>
                        <button
                            type="button"
                            class="flex w-full items-center gap-3 rounded-md px-2.5 py-2 text-sm font-medium transition-colors hover:bg-white/5 hover:text-nav-fg-active"
                            :class="groupContainsActive(group) && 'text-nav-fg-active'"
                            :aria-expanded="openGroup === i"
                            :title="collapsed ? group.label : null"
                            @click="toggle(i)"
                        >
                            <Icon :name="group.icon" :size="19" class="shrink-0" />
                            <template v-if="!collapsed">
                                <span class="truncate">{{ group.label }}</span>
                                <Icon
                                    name="chevronDown"
                                    :size="15"
                                    class="ml-auto shrink-0 transition-transform duration-150"
                                    :class="openGroup === i && 'rotate-180'"
                                />
                            </template>
                        </button>

                        <ul
                            v-if="openGroup === i && !collapsed"
                            class="mb-1 mt-0.5 space-y-px border-l border-nav-border pl-3 ml-4"
                        >
                            <li v-for="item in group.items" :key="item.url">
                                <component
                                    :is="item.spa ? Link : 'a'"
                                    :href="item.url"
                                    class="block truncate rounded-md px-2.5 py-1.5 text-[13px] transition-colors hover:bg-white/5 hover:text-nav-fg-active"
                                    :class="
                                        isActive(item)
                                            ? 'bg-nav-active-bg font-medium text-nav-fg-active'
                                            : 'text-nav-fg'
                                    "
                                    :aria-current="isActive(item) ? 'page' : null"
                                >
                                    {{ item.label }}
                                </component>
                            </li>
                        </ul>
                    </template>
                </li>
            </ul>
        </div>
    </nav>
</template>
