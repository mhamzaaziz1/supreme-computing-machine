/**
 * Toasts confirm something that already happened. They carry at most one
 * action — usually Undo, or "Send on WhatsApp" for a receipt — and leave by
 * themselves after six seconds.
 */
import { reactive } from 'vue';

export const toasts = reactive([]);

let seq = 0;

/**
 * @param {string} message
 * @param {{tone?: 'success'|'danger'|'info', action?: {label: string, run?: Function, href?: string}, timeout?: number}} opts
 */
export function toast(message, { tone = 'success', action = null, timeout = 6000 } = {}) {
    const id = ++seq;
    toasts.push({ id, message, tone, action });

    if (timeout) setTimeout(() => dismissToast(id), timeout);

    return id;
}

export function dismissToast(id) {
    const i = toasts.findIndex((t) => t.id === id);
    if (i !== -1) toasts.splice(i, 1);
}
