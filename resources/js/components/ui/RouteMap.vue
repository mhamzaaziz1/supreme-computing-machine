<script setup>
/**
 * A Leaflet map of outlets, sellers and violations.
 *
 * Circle markers rather than image pins: they need no asset paths (which
 * break when the app is served from a subdirectory) and take their colours
 * from the theme tokens, so a visited outlet is the same green here as in
 * every list.
 */
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const props = defineProps({
    /** [{ id, lat, lng, label, tone: 'visited'|'pending'|'due'|'seller'|'violation', number? }] */
    points: { type: Array, default: () => [] },
    /** Draw a line through the points in order (a route's visit sequence). */
    path: { type: Boolean, default: false },
    height: { type: String, default: '320px' },
});

const emit = defineEmits(['select']);

const el = ref(null);
let map = null;
let layer = null;

const token = (name, fallback) => {
    const v = getComputedStyle(document.documentElement).getPropertyValue(`--${name}`).trim();
    return v ? `rgb(${v})` : fallback;
};

const colours = () => ({
    visited: token('success', '#168556'),
    pending: token('brand-600', '#105474'),
    due: token('accent-500', '#f5980b'),
    seller: token('info', '#2563ad'),
    violation: token('danger', '#be2a2a'),
});

const draw = () => {
    if (!map) return;
    layer?.remove();
    layer = L.layerGroup().addTo(map);

    const c = colours();
    const pts = props.points.filter((p) => Number.isFinite(p.lat) && Number.isFinite(p.lng) && (p.lat !== 0 || p.lng !== 0));

    if (props.path && pts.length > 1) {
        L.polyline(pts.map((p) => [p.lat, p.lng]), { color: c.pending, weight: 2, opacity: 0.5, dashArray: '4 6' }).addTo(layer);
    }

    for (const p of pts) {
        const colour = c[p.tone] ?? c.pending;
        const marker = p.number
            ? L.marker([p.lat, p.lng], {
                  icon: L.divIcon({
                      className: '',
                      html: `<span style="display:grid;place-items:center;width:22px;height:22px;border-radius:999px;background:${colour};color:#fff;font:600 11px/1 system-ui;border:2px solid #fff;box-shadow:0 1px 3px rgba(0,0,0,.35)">${p.number}</span>`,
                      iconSize: [22, 22],
                      iconAnchor: [11, 11],
                  }),
              })
            : L.circleMarker([p.lat, p.lng], {
                  radius: p.tone === 'seller' ? 8 : 6,
                  color: '#fff',
                  weight: 2,
                  fillColor: colour,
                  fillOpacity: 0.95,
              });

        marker.bindTooltip(p.label ?? '', { direction: 'top', offset: [0, -8] });
        marker.on('click', () => emit('select', p));
        marker.addTo(layer);
    }

    if (pts.length === 1) map.setView([pts[0].lat, pts[0].lng], 14);
    else if (pts.length > 1) map.fitBounds(L.latLngBounds(pts.map((p) => [p.lat, p.lng])), { padding: [24, 24], maxZoom: 15 });
};

onMounted(async () => {
    await nextTick();
    map = L.map(el.value, { zoomControl: true, attributionControl: true }).setView([30.3753, 69.3451], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);
    draw();
    // Drawers animate in; recompute the size once they have settled.
    setTimeout(() => map?.invalidateSize(), 250);
});

watch(() => props.points, draw, { deep: true });

onBeforeUnmount(() => {
    map?.remove();
    map = null;
});
</script>

<template>
    <div class="relative overflow-hidden rounded-lg border border-edge-subtle" :style="{ height }">
        <div ref="el" class="h-full w-full"></div>
        <p v-if="!points.length" class="absolute inset-x-4 top-4 z-[500] rounded-md bg-surface-raised/95 px-3 py-2 text-center text-xs text-content-muted shadow-raised">
            No locations yet. Outlets are pinned automatically the first time a seller checks in there.
        </p>
    </div>
</template>
