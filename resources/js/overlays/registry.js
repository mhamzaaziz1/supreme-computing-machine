/**
 * Every overlay in the app, by name. Components load on first open, so the
 * shell pays nothing for overlays a page never uses.
 *
 * Drawers take a record id and are restorable from ?o=name:id.
 */
import { registerOverlay } from './store';

registerOverlay('outlet', 'drawer', () => import('../components/overlays/OutletDrawer.vue'), 'id');
registerOverlay('cheques', 'drawer', () => import('../components/overlays/ChequesDrawer.vue'));
registerOverlay('vans', 'drawer', () => import('../components/overlays/VansDrawer.vue'));

registerOverlay('creditHold', 'modal', () => import('../components/overlays/CreditHoldModal.vue'));
registerOverlay('collect', 'modal', () => import('../components/overlays/CollectModal.vue'));
registerOverlay('checkIn', 'modal', () => import('../components/overlays/CheckInModal.vue'));
registerOverlay('loadVan', 'modal', () => import('../components/overlays/LoadVanModal.vue'));
registerOverlay('settleVan', 'modal', () => import('../components/overlays/SettleVanModal.vue'));
