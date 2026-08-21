<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

/**
 * Peta titik survei (Leaflet + OpenStreetMap, tanpa API key).
 * Setiap titik = satu foto lokasi beserta koordinat saat foto diambil.
 */
const props = defineProps({
    points: { type: Array, default: () => [] },
    testid: { type: String, default: 'survey-map' },
});

const container = ref(null);
const tilesFailed = ref(false);

/** Sumber peta bisa diarahkan ke server tile sendiri lewat VITE_MAP_TILE_URL. */
const TILE_URL = import.meta.env.VITE_MAP_TILE_URL || 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
let map = null;
let layer = null;

const pin = (index) =>
    L.divIcon({
        className: '',
        html: `<div class="flex size-6 items-center justify-center rounded-full border-2 border-white bg-primary text-[11px] font-semibold text-primary-foreground shadow-md">${index}</div>`,
        iconSize: [24, 24],
        iconAnchor: [12, 12],
    });

const draw = () => {
    if (!map) return;

    layer?.remove();
    layer = L.layerGroup().addTo(map);

    const coords = [];

    props.points.forEach((point, index) => {
        const latLng = [Number(point.latitude), Number(point.longitude)];
        coords.push(latLng);

        L.marker(latLng, { icon: pin(index + 1) })
            .addTo(layer)
            .bindPopup(
                `<div style="text-align:center">${point.url ? `<img src="${point.url}" alt="Foto ${index + 1}" style="width:160px;border-radius:6px;margin-bottom:4px" />` : ''}` +
                    `<div style="font-size:11px">${latLng[0].toFixed(6)}, ${latLng[1].toFixed(6)}</div></div>`,
            );
    });

    if (coords.length === 1) map.setView(coords[0], 17);
    else if (coords.length > 1) map.fitBounds(L.latLngBounds(coords).pad(0.35));
};

onMounted(() => {
    map = L.map(container.value, { scrollWheelZoom: false }).setView([-6.5712, 107.7601], 13);

    L.tileLayer(TILE_URL, {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap',
    })
        .on('tileerror', () => (tilesFailed.value = true))
        .addTo(map);

    draw();
});

onBeforeUnmount(() => {
    map?.remove();
    map = null;
});

watch(() => props.points, draw, { deep: true });
</script>

<template>
    <div class="space-y-2">
        <div class="relative">
            <div
                ref="container"
                class="h-[280px] w-full overflow-hidden rounded-md border"
                :data-testid="props.testid"
            />
            <div
                v-if="tilesFailed"
                class="pointer-events-none absolute inset-x-0 top-0 m-2 rounded-md border border-amber-500/40 bg-amber-500/10 p-2 text-xs font-medium text-amber-700 dark:text-amber-400"
                data-testid="survey-map-offline"
            >
                Gambar peta gagal dimuat (jaringan ke server peta diblokir). Penanda &amp; koordinat
                tetap akurat — arahkan VITE_MAP_TILE_URL ke server peta internal bila perlu.
            </div>
        </div>
        <p class="text-xs text-muted-foreground">
            Titik diambil dari koordinat setiap foto survei. Klik penanda untuk melihat fotonya.
        </p>
    </div>
</template>

<style>
.leaflet-container {
    background: hsl(var(--muted));
    font-family: inherit;
}
</style>
