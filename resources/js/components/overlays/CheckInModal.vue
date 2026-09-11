<script setup>
/**
 * Check-in failed. Not a hard wall: the seller can retry the GPS fix or say
 * why they are somewhere else, and the reason is logged with the violation
 * for the manager to judge later.
 */
import { ref } from 'vue';
import Modal from '../ui/Modal.vue';
import { api, currentPosition } from '../../overlays/api';

const props = defineProps({
    contactId: { type: Number, required: true },
    contactName: { type: String, default: 'this outlet' },
    action: { type: String, default: 'place_order' },
    /** The server's answer to the failed attempt. */
    result: { type: Object, default: null },
    position: { type: Object, default: null },
    onResolve: { type: Function, required: true },
});

const emit = defineEmits(['close']);

const reasons = ['The outlet has moved', 'GPS is weak here', 'Order taken by phone', 'Delivering for another seller'];

const current = ref(props.result);
const pos = ref(props.position);
const picked = ref('');
const other = ref('');
const busy = ref(false);
const error = ref(null);

const send = async (reason = null) => {
    busy.value = true;
    error.value = null;
    try {
        const r = await api('checkin', {
            method: 'POST',
            body: { contact_id: props.contactId, action: props.action, lat: pos.value?.lat ?? null, lng: pos.value?.lng ?? null, accuracy: pos.value?.accuracy ?? null, reason },
        });
        if (r.allowed) {
            props.onResolve(r.token, r);
            emit('close');
        } else {
            current.value = r;
        }
    } catch (e) {
        error.value = e.message;
    } finally {
        busy.value = false;
    }
};

const retry = async () => {
    busy.value = true;
    try {
        pos.value = await currentPosition();
    } catch (e) {
        error.value = e.message;
        busy.value = false;
        return;
    }
    await send();
};

const submitReason = () => {
    const reason = picked.value === 'other' ? other.value.trim() : picked.value;
    if (reason) send(reason);
};
</script>

<template>
    <Modal :title="`Check-in failed at ${contactName}`" eyebrow="Location check" tone="warning" width="sm" :busy="busy" @close="emit('close')">
        <p class="text-[13.5px] text-content-primary">
            <template v-if="current?.distance">You are about <strong>{{ current.distance.toLocaleString() }} m</strong> from the outlet.</template>
            {{ current?.message ?? 'Your location could not be confirmed.' }}
        </p>
        <p v-if="pos?.accuracy" class="mt-1 text-xs text-content-muted">GPS accuracy ±{{ pos.accuracy }} m</p>

        <form class="mt-4 space-y-1.5" @submit.prevent="submitReason">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-content-muted">Carry on anyway — why?</p>
            <label v-for="r in reasons" :key="r" class="flex items-center gap-2 rounded-md px-2 py-1.5 text-sm text-content-primary hover:bg-surface-sunken">
                <input v-model="picked" type="radio" :value="r" name="checkin-reason" /> {{ r }}
            </label>
            <label class="flex items-center gap-2 rounded-md px-2 py-1.5 text-sm text-content-primary hover:bg-surface-sunken">
                <input v-model="picked" type="radio" value="other" name="checkin-reason" /> Something else
            </label>
            <input v-if="picked === 'other'" v-model="other" type="text" maxlength="190" placeholder="Say what happened"
                class="w-full rounded-md border border-edge-subtle bg-surface-page px-2.5 py-1.5 text-sm text-content-primary focus:outline-none" />
            <p class="pt-1 text-xs text-content-muted">Your reason is recorded with this visit and shown to your manager.</p>
        </form>

        <p v-if="error" class="mt-3 rounded-md bg-danger/10 px-3 py-2 text-[13px] text-danger">{{ error }}</p>

        <template #footer>
            <button type="button" class="rounded-md px-3 py-2 text-sm font-medium text-content-secondary hover:bg-surface-sunken" :disabled="busy" @click="retry">Try GPS again</button>
            <button type="button" :disabled="busy || !picked || (picked === 'other' && !other.trim())"
                class="rounded-md bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700 disabled:opacity-50" @click="submitReason">
                Continue with reason
            </button>
        </template>
    </Modal>
</template>
