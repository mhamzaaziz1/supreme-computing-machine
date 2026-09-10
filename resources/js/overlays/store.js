/**
 * The overlay stack: at most one drawer, and at most one modal on top of it.
 *
 * That limit is the whole grammar. A drawer holds a record's context; a modal
 * is a task that commits money or stock. A modal never opens another modal —
 * opening one replaces whatever modal is up — so the screen can never become
 * a pile of windows.
 *
 * The open drawer is mirrored into the query string (?o=outlet:12), so a
 * refresh, a pasted link or a WhatsApp'd URL brings the same drawer back.
 * replaceState is used rather than pushState: Inertia owns history entries,
 * and a second entry per drawer would fight its back-button handling.
 */
import { defineAsyncComponent, markRaw, reactive } from 'vue';

const registry = {};

export const overlays = reactive({ drawer: null, modal: null });

/**
 * @param {string} name
 * @param {'drawer'|'modal'} kind
 * @param {() => Promise<any>} loader
 * @param {string|null} urlParam  prop that identifies the record in ?o=name:value
 */
export function registerOverlay(name, kind, loader, urlParam = null) {
    registry[name] = { kind, urlParam, component: markRaw(defineAsyncComponent(loader)) };
}

const writeUrl = (value) => {
    try {
        const url = new URL(window.location.href);
        if (value) url.searchParams.set('o', value);
        else url.searchParams.delete('o');
        window.history.replaceState(window.history.state, '', url);
    } catch {
        // A sandboxed frame can refuse history writes; the drawer still works.
    }
};

export function openOverlay(name, props = {}) {
    const entry = registry[name];
    if (!entry) {
        console.warn(`Unknown overlay "${name}"`);
        return;
    }

    const item = { name, props, component: entry.component, key: `${name}-${Date.now()}` };

    if (entry.kind === 'drawer') {
        overlays.modal = null;
        overlays.drawer = item;
        if (!entry.urlParam) writeUrl(name);
        else writeUrl(props[entry.urlParam] != null ? `${name}:${props[entry.urlParam]}` : null);
    } else {
        overlays.modal = item;
    }
}

export function closeOverlay(kind) {
    overlays[kind] = null;
    if (kind === 'drawer') writeUrl(null);
}

/** Reopen the drawer named in ?o= after a reload or a shared link. */
export function restoreFromUrl() {
    const value = new URL(window.location.href).searchParams.get('o');
    if (!value) return;

    const [name, id] = value.split(':');
    const entry = registry[name];
    if (!entry || entry.kind !== 'drawer') return;

    openOverlay(name, entry.urlParam ? { [entry.urlParam]: /^\d+$/.test(id) ? Number(id) : id } : {});
}

/** True while typing, so single-key shortcuts don't hijack text fields. */
export const isTyping = (e) => {
    const t = e.target;
    return t instanceof HTMLElement && (t.isContentEditable || ['INPUT', 'TEXTAREA', 'SELECT'].includes(t.tagName));
};
