<script setup>
/**
 * Page chrome: what you are looking at on the left, what you can do about
 * it on the right. The search button is the visible affordance for the
 * Ctrl-K palette — the shortcut is useless if nobody discovers it.
 */
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Icon from '../Icon.vue';

defineProps({
    title: { type: String, default: '' },
});

const emit = defineEmits(['toggle-sidebar', 'open-search']);

const page = usePage();
const user = computed(() => page.props.auth?.user ?? {});
const settingsNav = computed(() => page.props.settingsNav ?? []);

const menuOpen = ref(false);

const isMac = typeof navigator !== 'undefined' && /Mac|iPhone|iPad/.test(navigator.platform);

const toggleTheme = () => window.themeController?.toggle();
</script>

<template>
    <header
        class="flex h-14 shrink-0 items-center gap-3 border-b border-edge-subtle bg-surface-raised px-4"
    >
        <button
            type="button"
            class="grid h-8 w-8 place-items-center rounded-md text-content-secondary transition-colors hover:bg-surface-sunken hover:text-content-primary"
            aria-label="Toggle navigation"
            @click="emit('toggle-sidebar')"
        >
            <Icon name="menu" :size="18" />
        </button>

        <h1 v-if="title" class="truncate text-[15px] font-semibold text-content-primary">
            {{ title }}
        </h1>

        <!-- Search sits centre-right: reachable, but never mistaken for a page control. -->
        <button
            type="button"
            class="ml-auto flex h-8 w-56 items-center gap-2 rounded-md border border-edge-subtle bg-surface-page px-2.5 text-content-muted transition-colors hover:border-edge-strong hover:text-content-secondary"
            @click="emit('open-search')"
        >
            <Icon name="search" :size="15" class="shrink-0" />
            <span class="text-[13px]">Jump to…</span>
            <kbd class="ml-auto shrink-0 text-[10px] font-medium tracking-wide">
                {{ isMac ? '⌘' : 'Ctrl' }}K
            </kbd>
        </button>

        <button
            type="button"
            class="grid h-8 w-8 place-items-center rounded-md text-content-secondary transition-colors hover:bg-surface-sunken hover:text-content-primary"
            aria-label="Toggle theme"
            @click="toggleTheme"
        >
            <Icon name="sun" :size="17" class="block dark:hidden" />
            <Icon name="moon" :size="17" class="hidden dark:block" />
        </button>

        <div class="relative">
            <button
                type="button"
                class="flex items-center gap-2 rounded-md py-1 pl-1 pr-1.5 transition-colors hover:bg-surface-sunken"
                :aria-expanded="menuOpen"
                @click="menuOpen = !menuOpen"
            >
                <span
                    class="grid h-7 w-7 place-items-center rounded-full bg-brand-600 text-[11px] font-semibold text-white"
                >
                    {{ user.initials }}
                </span>
                <Icon name="chevronDown" :size="14" class="text-content-muted" />
            </button>

            <!-- Click-away layer: simpler and more reliable than a document listener. -->
            <div v-if="menuOpen" class="fixed inset-0 z-10" @click="menuOpen = false" />

            <div
                v-if="menuOpen"
                class="absolute right-0 z-20 mt-1.5 w-60 overflow-hidden rounded-lg bg-surface-raised py-1 shadow-overlay ring-1 ring-edge-subtle"
            >
                <div class="border-b border-edge-subtle px-3 py-2">
                    <p class="truncate text-sm font-medium text-content-primary">{{ user.name }}</p>
                    <p class="truncate text-xs text-content-muted">{{ user.email }}</p>
                </div>

                <div v-if="settingsNav.length" class="scrollbar-slim max-h-72 overflow-y-auto py-1">
                    <a
                        v-for="item in settingsNav"
                        :key="item.url"
                        :href="item.url"
                        class="block truncate px-3 py-1.5 text-[13px] text-content-secondary transition-colors hover:bg-surface-sunken hover:text-content-primary"
                    >
                        {{ item.label }}
                    </a>
                </div>

                <div class="border-t border-edge-subtle pt-1">
                    <a
                        :href="page.props.routes?.logout"
                        class="flex w-full items-center gap-2 px-3 py-1.5 text-[13px] text-content-secondary transition-colors hover:bg-surface-sunken hover:text-danger"
                    >
                        <Icon name="logout" :size="15" />
                        Sign out
                    </a>
                </div>
            </div>
        </div>
    </header>
</template>
