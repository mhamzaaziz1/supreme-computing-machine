<script setup>
/**
 * Outlet 360.
 *
 * Everything needed to decide what to do with one outlet — should it get
 * more stock, what does it owe and how old is it, what does it usually take
 * and what has it stopped taking — with the four things you do to an outlet
 * pinned to the bottom. Opens from any customer name in the app.
 */
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import Drawer from '../ui/Drawer.vue';
import Icon from '../Icon.vue';
import Money from '../Money.vue';
import Popover from '../Popover.vue';
import { api } from '../../overlays/api';
import { isTyping, openOverlay } from '../../overlays/store';
import { toast } from '../../overlays/toast';

const props = defineProps({
    id: { type: Number, required: true },
});

const emit = defineEmits(['close']);

const data = ref(null);
const loading = ref(true);
const error = ref(null);

const load = async () => {
    loading.value = true;
    error.value = null;
    try {
        data.value = await api(`outlets/${props.id}`);
    } catch (e) {
        error.value = e.message;
    } finally {
        loading.value = false;
    }
};

onMounted(load);

const credit = computed(() => data.value?.credit);

const standing = computed(() => {
    const c = credit.value;
    if (!c) return null;
    if (c.hold?.flag) return { label: 'On hold', tone: 'bg-danger/10 text-danger' };
    const overLimit = c.limit !== null && c.outstanding > c.limit;
    const lateTooLong = c.max_overdue_days !== null && c.oldest_overdue_days > c.max_overdue_days;
    if (overLimit || lateTooLong) return { label: 'Over terms', tone: 'bg-danger/10 text-danger' };
    if ((c.aging['61_90'] ?? 0) + (c.aging['90_plus'] ?? 0) > 0) return { label: 'Credit watch', tone: 'bg-warning/10 text-warning' };
    return { label: 'Good standing', tone: 'bg-success/10 text-success' };
});

/** Meter: current share of the limit, with the >60-day part in red. */
const meter = computed(() => {
    const c = credit.value;
    if (!c || !c.limit) return null;
    const scale = Math.max(c.limit, c.outstanding) || 1;
    const late = (c.aging['61_90'] ?? 0) + (c.aging['90_plus'] ?? 0);
    return {
        fresh: (Math.max(0, c.outstanding - late) / scale) * 100,
        late: (late / scale) * 100,
        limitAt: (c.limit / scale) * 100,
    };
});

const buckets = [
    ['0_30', '0–30'],
    ['31_60', '31–60'],
    ['61_90', '61–90'],
    ['90_plus', '90+'],
];

// -- credit popover ---------------------------------------------------------

const creditForm = ref({});
const savingCredit = ref(false);

const openCredit = () => {
    const c = credit.value;
    creditForm.value = {
        credit_limit: c.limit ?? '',
        max_overdue_days: c.max_overdue_days ?? '',
        credit_hold: !!c.hold?.flag,
        credit_hold_reason: c.hold?.reason ?? '',
    };
};

const saveCredit = async (close) => {
    savingCredit.value = true;
    try {
        const f = creditForm.value;
        const res = await api(`outlets/${props.id}/credit`, {
            method: 'PATCH',
            body: {
                credit_limit: f.credit_limit === '' ? null : Number(f.credit_limit),
                max_overdue_days: f.max_overdue_days === '' ? null : Number(f.max_overdue_days),
                credit_hold: !!f.credit_hold,
                credit_hold_reason: f.credit_hold_reason || null,
            },
        });
        data.value.credit = res.credit;
        toast(res.message);
        close();
    } catch (e) {
        toast(e.message, { tone: 'danger' });
    } finally {
        savingCredit.value = false;
    }
};

// -- actions ---------------------------------------------------------------

const sell = (withBasket = false) => {
    if (!data.value?.can.sell) return;
    const url = new URL(data.value.links.sell);
    if (withBasket) url.searchParams.set('basket', '1');
    window.location.href = url.toString();
};
const collect = () => data.value?.can.collect && openOverlay('collect', { contactId: props.id, onDone: load });
const visit = () => openOverlay('visit', { contactId: props.id, onDone: load });
const statement = () => data.value && window.open(data.value.links.statement, '_blank', 'noopener');

const onKey = (e) => {
    if (isTyping(e) || e.ctrlKey || e.metaKey || e.altKey || !data.value) return;
    const map = { s: () => sell(), c: collect, v: visit, p: statement };
    const fn = map[e.key.toLowerCase()];
    if (fn) {
        e.preventDefault();
        fn();
    }
};

onMounted(() => document.addEventListener('keydown', onKey));
onBeforeUnmount(() => document.removeEventListener('keydown', onKey));

// -- formatting ------------------------------------------------------------

const shortDate = (v) =>
    v ? new Date(String(v).replace(' ', 'T')).toLocaleDateString(undefined, { day: 'numeric', month: 'short' }) : '';

const timelineIcon = { visit: 'geofence', sale: 'receipt', payment: 'cash', followup: 'calendar', violation: 'alert' };
const timelineTone = { violation: 'text-danger', payment: 'text-success', sale: 'text-brand-600 dark:text-brand-300' };

const serviceTone = {
    overdue: 'bg-danger/10 text-danger',
    due_soon: 'bg-warning/10 text-warning',
    ok: 'bg-surface-sunken text-content-secondary',
};
</script>

<template>
    <Drawer
        :title="data?.outlet.name ?? 'Outlet'"
        :loading="loading"
        :error="error"
        width="lg"
        @close="emit('close')"
        @retry="load"
    >
        <template v-if="standing" #eyebrow>
            <span class="mb-1 inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wide" :class="standing.tone">
                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ standing.label }}
            </span>
        </template>
        <template v-if="data" #subtitle>
            <template v-if="data.outlet.route">{{ data.outlet.route.name }}</template>
            <template v-if="data.outlet.stop"> · stop {{ data.outlet.stop.number }} of {{ data.outlet.stop.of }}</template>
            <template v-if="data.outlet.code"> · {{ data.outlet.code }}</template>
            <template v-if="data.outlet.mobile"> · {{ data.outlet.mobile }}</template>
        </template>
        <template v-if="data" #header-actions>
            <a
                :href="data.links.contact"
                class="grid h-8 w-8 shrink-0 place-items-center rounded-md text-content-muted hover:bg-surface-sunken hover:text-content-primary"
                title="Open the full contact record"
                aria-label="Open the full contact record"
            >
                <Icon name="external" :size="16" />
            </a>
        </template>

        <div v-if="data" class="divide-y divide-edge-subtle">
            <!-- Credit -->
            <section class="space-y-3 px-5 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-[11px] font-semibold uppercase tracking-wider text-content-muted">Outstanding vs limit</h3>

                    <Popover v-if="data.can.edit_credit" :width="300" @open="openCredit">
                        <template #trigger="{ toggle }">
                            <button type="button" class="rounded-md px-2 py-1 text-xs font-medium text-brand-600 hover:bg-surface-sunken dark:text-brand-300" @click="toggle">
                                Credit terms
                            </button>
                        </template>
                        <template #default="{ close }">
                            <form class="space-y-3 p-4" @submit.prevent="saveCredit(close)">
                                <label class="block">
                                    <span class="mb-1 block text-xs font-medium text-content-muted">Credit limit</span>
                                    <input v-model="creditForm.credit_limit" type="number" min="0" step="any" placeholder="No limit"
                                        class="w-full rounded-md border border-edge-subtle bg-surface-page px-2.5 py-1.5 text-sm text-content-primary focus:border-accent-500 focus:outline-none" />
                                </label>
                                <label class="block">
                                    <span class="mb-1 block text-xs font-medium text-content-muted">Stop selling when an invoice is overdue by more than</span>
                                    <div class="flex items-center gap-2">
                                        <input v-model="creditForm.max_overdue_days" type="number" min="0" step="1" placeholder="No rule"
                                            class="w-24 rounded-md border border-edge-subtle bg-surface-page px-2.5 py-1.5 text-sm text-content-primary focus:border-accent-500 focus:outline-none" />
                                        <span class="text-sm text-content-muted">days</span>
                                    </div>
                                </label>
                                <label class="flex items-center gap-2 text-sm text-content-primary">
                                    <input v-model="creditForm.credit_hold" type="checkbox" class="rounded border-edge-strong" />
                                    Put this outlet on hold
                                </label>
                                <input v-if="creditForm.credit_hold" v-model="creditForm.credit_hold_reason" type="text" placeholder="Reason (shown at the till)"
                                    class="w-full rounded-md border border-edge-subtle bg-surface-page px-2.5 py-1.5 text-sm text-content-primary focus:border-accent-500 focus:outline-none" />
                                <button type="submit" :disabled="savingCredit"
                                    class="w-full rounded-md bg-brand-600 px-3 py-2 text-sm font-semibold text-white hover:bg-brand-700 disabled:opacity-50">
                                    {{ savingCredit ? 'Saving…' : 'Save credit terms' }}
                                </button>
                            </form>
                        </template>
                    </Popover>
                </div>

                <div class="flex items-baseline justify-between">
                    <span class="text-2xl font-semibold text-content-primary"><Money :value="credit.outstanding" compact /></span>
                    <span class="text-[13px] text-content-muted">
                        <template v-if="credit.limit !== null">of <Money :value="credit.limit" compact /></template>
                        <template v-else>no limit set</template>
                    </span>
                </div>

                <div v-if="meter" class="relative h-2 overflow-hidden rounded-full bg-surface-sunken">
                    <div class="absolute inset-y-0 left-0 bg-brand-600" :style="{ width: `${meter.fresh}%` }"></div>
                    <div class="absolute inset-y-0 bg-danger" :style="{ left: `${meter.fresh}%`, width: `${meter.late}%` }"></div>
                    <div v-if="meter.limitAt < 100" class="absolute inset-y-0 w-0.5 bg-content-primary" :style="{ left: `${meter.limitAt}%` }"></div>
                </div>

                <div class="grid grid-cols-4 gap-1.5">
                    <div
                        v-for="[key, label] in buckets"
                        :key="key"
                        class="rounded-md px-2 py-1.5"
                        :class="(key === '61_90' || key === '90_plus') && credit.aging[key] > 0 ? 'bg-danger/10 text-danger' : 'bg-surface-sunken text-content-primary'"
                    >
                        <span class="block text-[10px] font-medium uppercase opacity-75">{{ label }} days</span>
                        <span class="text-[13px] font-medium"><Money :value="credit.aging[key]" compact /></span>
                    </div>
                </div>

                <p v-if="credit.hold?.flag" class="rounded-md bg-danger/10 px-3 py-2 text-[13px] text-danger">
                    On hold: {{ credit.hold.reason || 'no reason given' }}
                </p>

                <dl class="grid grid-cols-2 gap-x-4 gap-y-1 text-[13px]">
                    <div class="flex justify-between gap-2">
                        <dt class="text-content-muted">Oldest overdue</dt>
                        <dd class="numeric" :class="credit.max_overdue_days !== null && credit.oldest_overdue_days > credit.max_overdue_days ? 'font-medium text-danger' : 'text-content-primary'">
                            {{ credit.oldest_overdue_days }} days
                        </dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-content-muted">Cheques in hand</dt>
                        <dd class="text-content-primary">
                            <template v-if="credit.pdc.count">{{ credit.pdc.count }} · <Money :value="credit.pdc.amount" compact /></template>
                            <template v-else>none</template>
                        </dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-content-muted">Last payment</dt>
                        <dd class="text-content-primary">
                            <template v-if="credit.last_payment"><Money :value="credit.last_payment.amount" compact /> · {{ shortDate(credit.last_payment.date) }}</template>
                            <template v-else>never</template>
                        </dd>
                    </div>
                    <div v-if="credit.advance > 0" class="flex justify-between gap-2">
                        <dt class="text-content-muted">Advance held</dt>
                        <dd class="text-success"><Money :value="credit.advance" compact /></dd>
                    </div>
                </dl>
            </section>

            <!-- Buying rhythm -->
            <section class="px-5 py-4">
                <h3 class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-content-muted">Buying rhythm</h3>
                <p v-if="data.rhythm.interval_days" class="text-[13px] text-content-primary">
                    Orders about every <strong>{{ Math.round(data.rhythm.interval_days) }} days</strong>;
                    last order {{ data.rhythm.days_since }} days ago
                    <span v-if="data.rhythm.due" class="ml-1 rounded-full bg-accent-500/15 px-2 py-0.5 text-[11px] font-semibold text-accent-700 dark:text-accent-300">due now</span>
                </p>
                <p v-else-if="data.rhythm.last_order_on" class="text-[13px] text-content-secondary">
                    One order so far, {{ data.rhythm.days_since }} days ago. A rhythm shows after the second.
                </p>
                <p v-else class="text-[13px] text-content-secondary">No orders in the last year.</p>
                <p v-if="data.rhythm.avg_order_value" class="mt-0.5 text-xs text-content-muted">
                    Typical order <Money :value="data.rhythm.avg_order_value" compact /> · {{ data.rhythm.orders_90d }} orders in 90 days
                </p>
            </section>

            <!-- Stopped buying -->
            <section v-if="data.stopped.length" class="px-5 py-4">
                <h3 class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-content-muted">Stopped buying</h3>
                <ul class="space-y-1.5">
                    <li v-for="s in data.stopped" :key="s.variation_id" class="flex items-center gap-3 rounded-md border border-edge-subtle px-3 py-2">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[13px] text-content-primary">{{ s.name }}</p>
                            <p class="text-xs text-content-muted">usually every {{ Math.round(s.interval_days) }} days · last {{ s.days_since }} days ago</p>
                        </div>
                        <span class="shrink-0 text-xs font-semibold text-accent-700 dark:text-accent-300">Ask</span>
                    </li>
                </ul>
            </section>

            <!-- Usual order -->
            <section v-if="data.basket.length" class="px-5 py-4">
                <div class="mb-2 flex items-center justify-between">
                    <h3 class="text-[11px] font-semibold uppercase tracking-wider text-content-muted">Usual order</h3>
                    <button v-if="data.can.sell" type="button" class="text-xs font-medium text-brand-600 hover:underline dark:text-brand-300" @click="sell(true)">
                        Start an order with this
                    </button>
                </div>
                <ul class="text-[13px]">
                    <li v-for="b in data.basket" :key="b.variation_id" class="flex justify-between gap-3 py-0.5">
                        <span class="truncate text-content-primary">{{ b.name }}</span>
                        <span class="shrink-0 text-content-muted numeric">× {{ b.quantity }} <span class="text-[11px]">({{ b.frequency }})</span></span>
                    </li>
                </ul>
            </section>

            <!-- Schemes -->
            <section v-if="data.schemes.length" class="space-y-3 px-5 py-4">
                <h3 class="text-[11px] font-semibold uppercase tracking-wider text-content-muted">Scheme progress</h3>
                <div v-for="s in data.schemes" :key="s.id">
                    <div class="flex justify-between text-[13px]">
                        <span class="text-content-primary">{{ s.name }}<span v-if="s.next_tier"> — {{ s.next_tier }}</span></span>
                        <span class="text-content-muted">{{ s.ends_label }}</span>
                    </div>
                    <div class="my-1.5 h-2 overflow-hidden rounded-full bg-surface-sunken">
                        <div class="h-full bg-brand-600" :style="{ width: `${Math.min(100, s.percent)}%` }"></div>
                    </div>
                    <p class="text-xs text-content-muted numeric">
                        {{ s.achieved }} of {{ s.target }} {{ s.unit }}<template v-if="s.remaining > 0"> · {{ s.remaining }} to go</template><template v-else> · reached</template>
                    </p>
                </div>
            </section>

            <!-- Follow-ups -->
            <section v-if="data.followups.length" class="px-5 py-4">
                <h3 class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-content-muted">Follow-ups</h3>
                <ul class="space-y-1 text-[13px]">
                    <li v-for="f in data.followups" :key="f.id" class="flex gap-2">
                        <span class="w-14 shrink-0 text-xs numeric" :class="f.overdue ? 'text-danger' : 'text-content-muted'">{{ shortDate(f.date) }}</span>
                        <span class="text-content-primary">{{ f.notes || 'No notes' }}</span>
                    </li>
                </ul>
            </section>

            <!-- Vehicles (oil change bay customers) -->
            <section v-if="data.vehicles.length" class="px-5 py-4">
                <h3 class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-content-muted">Vehicles</h3>
                <ul class="space-y-1.5">
                    <li v-for="v in data.vehicles" :key="v.id" class="flex items-center gap-3 rounded-md border border-edge-subtle px-3 py-2">
                        <Icon name="truck" :size="16" class="shrink-0 text-content-muted" />
                        <div class="min-w-0 flex-1">
                            <p class="text-[13px] font-medium text-content-primary">{{ v.plate || 'No plate' }} <span class="font-normal text-content-muted">{{ v.name }}</span></p>
                            <p v-if="v.service" class="text-xs text-content-muted">
                                Next at {{ v.service.next_mileage.toLocaleString() }} km · ≈ {{ shortDate(v.service.due_on) }}
                            </p>
                        </div>
                        <button
                            v-if="v.service"
                            type="button"
                            class="shrink-0 rounded-full px-2 py-0.5 text-[11px] font-semibold"
                            :class="serviceTone[v.service.status]"
                            @click="openOverlay('vehicle', { id: v.id })"
                        >
                            {{ v.service.status === 'overdue' ? `${-v.service.days_until}d overdue` : v.service.status === 'due_soon' ? `due in ${v.service.days_until}d` : 'History' }}
                        </button>
                    </li>
                </ul>
            </section>

            <!-- Timeline -->
            <section class="px-5 py-4">
                <h3 class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-content-muted">Timeline</h3>
                <ol v-if="data.timeline.length" class="space-y-2">
                    <li v-for="(t, i) in data.timeline" :key="i" class="flex gap-3 text-[13px]">
                        <span class="w-12 shrink-0 pt-0.5 text-xs text-content-muted numeric">{{ shortDate(t.at) }}</span>
                        <Icon :name="timelineIcon[t.kind] ?? 'clock'" :size="15" class="mt-0.5 shrink-0" :class="timelineTone[t.kind] ?? 'text-content-muted'" />
                        <span class="min-w-0 flex-1 text-content-primary">
                            {{ t.text }}
                            <span v-if="t.amount" class="text-content-muted"> · <Money :value="t.amount" compact /></span>
                        </span>
                    </li>
                </ol>
                <p v-else class="text-[13px] text-content-muted">Nothing recorded yet.</p>
            </section>
        </div>

        <template #footer>
            <div class="grid grid-cols-4 gap-2">
                <button type="button" :disabled="!data?.can.sell"
                    class="rounded-md bg-accent-500 px-2 py-2 text-sm font-semibold text-brand-950 hover:bg-accent-400 disabled:opacity-40" @click="sell()">
                    Sell <kbd class="ml-1 text-[10px] opacity-70">S</kbd>
                </button>
                <button type="button" :disabled="!data?.can.collect"
                    class="rounded-md border border-edge-strong bg-surface-raised px-2 py-2 text-sm font-medium text-content-primary hover:bg-surface-sunken disabled:opacity-40" @click="collect">
                    Collect <kbd class="ml-1 text-[10px] opacity-60">C</kbd>
                </button>
                <button type="button"
                    class="rounded-md border border-edge-strong bg-surface-raised px-2 py-2 text-sm font-medium text-content-primary hover:bg-surface-sunken" @click="visit">
                    Visit <kbd class="ml-1 text-[10px] opacity-60">V</kbd>
                </button>
                <button type="button"
                    class="rounded-md border border-edge-strong bg-surface-raised px-2 py-2 text-sm font-medium text-content-primary hover:bg-surface-sunken" @click="statement">
                    Statement <kbd class="ml-1 text-[10px] opacity-60">P</kbd>
                </button>
            </div>
        </template>
    </Drawer>
</template>
