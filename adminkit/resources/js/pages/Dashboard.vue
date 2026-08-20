<script setup>
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { ArrowRight, BadgeCheck, Banknote, FileStack, Gavel, RefreshCw, Timer } from 'lucide-vue-next';

import AppLayout from '@/components/layout/AppLayout.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardFooter from '@/components/ui/CardFooter.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
import Combobox from '@/components/ui/Combobox.vue';
import Progress from '@/components/ui/Progress.vue';
import Table from '@/components/ui/Table.vue';
import TableBody from '@/components/ui/TableBody.vue';
import TableCell from '@/components/ui/TableCell.vue';
import TableHead from '@/components/ui/TableHead.vue';
import TableHeader from '@/components/ui/TableHeader.vue';
import TableRow from '@/components/ui/TableRow.vue';
import EmptyState from '@/components/composite/EmptyState.vue';
import MiniBarChart from '@/components/composite/MiniBarChart.vue';
import HBarChart from '@/components/composite/HBarChart.vue';
import { ACTION } from '@/constants/labels';

const props = defineProps({
    activities: { type: Array, default: () => [] },
});

/* ── DATA CONTOH (statis) — belum tersambung ke basis data ─────────────────
   Angka kantor tertentu diturunkan dari porsi kantor terhadap total. */
const OFFICES = [
    { value: 'all', label: 'Semua Kantor', share: 1 },
    { value: 'Pamanukan', label: 'Pamanukan', share: 0.33 },
    { value: 'Subang', label: 'Subang', share: 0.24 },
    { value: 'Pagaden', label: 'Pagaden', share: 0.14 },
    { value: 'Jalancagak', label: 'Jalancagak', share: 0.11 },
    { value: 'Kalijati', label: 'Kalijati', share: 0.09 },
    { value: 'Sukamandi', label: 'Sukamandi', share: 0.06 },
    { value: 'Pusakajaya', label: 'Pusakajaya', share: 0.03 },
];

const office = ref('all');
const selected = computed(() => OFFICES.find((o) => o.value === office.value) ?? OFFICES[0]);
const isAll = computed(() => selected.value.value === 'all');
const share = computed(() => selected.value.share);

const scale = (value) => Math.max(0, Math.round(value * share.value));
const money = (billions) => `Rp ${(billions * share.value).toFixed(1).replace('.', ',')} M`;

const KPIS = computed(() => [
    { key: 'submitted', label: 'Pengajuan Bulan Ini', value: scale(128), hint: '+12% vs bulan lalu', icon: FileStack },
    { key: 'approved', label: 'Disetujui', value: scale(86), hint: `${money(21.6)} · 67% berkas`, icon: BadgeCheck },
    { key: 'disbursed', label: 'Plafon Dicairkan', value: money(18.4), hint: `${scale(57)} berkas akad selesai`, icon: Banknote },
    { key: 'committee', label: 'Menunggu Komite', value: scale(23), hint: `${scale(7)} berkas lewat 3 hari`, icon: Gavel },
    { key: 'sla', label: 'Rata-rata SLA', value: '4,2 hari', hint: 'target maksimal 5 hari', icon: Timer },
]);

const MONTHLY_BASE = [
    ['Sep', 96, 61], ['Okt', 104, 68], ['Nov', 112, 74], ['Des', 138, 91], ['Jan', 87, 54],
    ['Feb', 94, 59], ['Mar', 108, 70], ['Apr', 121, 82], ['Mei', 116, 78], ['Jun', 128, 57],
];
const monthly = computed(() =>
    MONTHLY_BASE.map(([label, pengajuan, realisasi]) => ({
        label,
        pengajuan: scale(pengajuan),
        realisasi: scale(realisasi),
    })),
);
const MONTHLY_SERIES = [
    { key: 'pengajuan', label: 'Pengajuan', token: '--chart-1' },
    { key: 'realisasi', label: 'Realisasi', token: '--chart-3' },
];

const STAGE_BASE = [
    ['1 · Pengajuan', 128], ['2 · Verifikasi Dokumen', 118], ['3 · Survey & Analisa', 104],
    ['4 · Taksasi Agunan', 96], ['5 · Rekomendasi', 88], ['6 · Komite Kredit', 74],
    ['7 · Persetujuan', 68], ['8 · Akad & Pengikatan', 61], ['9 · Pencairan', 57],
];
const stages = computed(() =>
    STAGE_BASE.map(([label, count]) => ({ label, count: scale(count) })),
);
const stagePercent = (count) => Math.round((count / Math.max(1, stages.value[0].count)) * 100);

const byOffice = computed(() =>
    OFFICES.filter((o) => o.value !== 'all').map((o) => ({ label: o.label, count: Math.round(128 * o.share) })),
);

const PRODUCT_BASE = [
    ['KRU · Kredit Umum', 38], ['KUP · Kredit Pegawai', 24], ['KKO · Konstruksi', 17],
    ['KBT · Bertahap', 13], ['KPP · Pensiunan', 9], ['KRISPI · Mikro', 6],
];
const byProduct = computed(() =>
    PRODUCT_BASE.map(([label, count]) => ({ label, count: Math.max(1, scale(count)) })),
);

const DECISIONS = [
    { label: 'Disetujui', percent: 67.2, token: '--chart-1' },
    { label: 'Disetujui dengan syarat', percent: 11.7, token: '--chart-2' },
    { label: 'Ditolak', percent: 14.1, token: '--chart-3' },
    { label: 'Dibatalkan nasabah', percent: 7.0, token: '--chart-4' },
];

const QUEUE = [
    { code: 'PK-2606-0142', name: 'Sumarni', office: 'Pamanukan', product: 'KRU', amount: 'Rp 250.000.000', tier: 'Komite II', age: '4 hari', level: 'destructive' },
    { code: 'PK-2606-0139', name: 'Agus Salim', office: 'Subang', product: 'KUP', amount: 'Rp 75.000.000', tier: 'Kasi Analis', age: '2 hari', level: 'secondary' },
    { code: 'PK-2606-0137', name: 'CV Mitra Tani', office: 'Pamanukan', product: 'KKO', amount: 'Rp 480.000.000', tier: 'Komite III', age: '5 hari', level: 'destructive' },
    { code: 'PK-2606-0131', name: 'Dewi Lestari', office: 'Pagaden', product: 'KBT', amount: 'Rp 120.000.000', tier: 'Kabag Analis', age: '1 hari', level: 'secondary' },
    { code: 'PK-2606-0128', name: 'Hendra Wijaya', office: 'Kalijati', product: 'KRISPI', amount: 'Rp 25.000.000', tier: 'Kasi Analis', age: '1 hari', level: 'secondary' },
    { code: 'PK-2606-0124', name: 'Yayah Rokayah', office: 'Jalancagak', product: 'KRU', amount: 'Rp 180.000.000', tier: 'Komite I', age: '3 hari', level: 'secondary' },
    { code: 'PK-2606-0119', name: 'Toko Berkah', office: 'Sukamandi', product: 'KUP', amount: 'Rp 60.000.000', tier: 'Kasi Analis', age: '2 hari', level: 'secondary' },
    { code: 'PK-2606-0115', name: 'Rahmat Hidayat', office: 'Pusakajaya', product: 'KBT', amount: 'Rp 95.000.000', tier: 'Kabag Analis', age: '4 hari', level: 'destructive' },
];
const queue = computed(() => (isAll.value ? QUEUE : QUEUE.filter((r) => r.office === selected.value.value)));

const target = computed(() => ({
    realisasi: money(18.4),
    target: money(24),
    percent: 77,
}));
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout>
        <div class="space-y-6" data-testid="dashboard-page">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div class="space-y-1">
                    <h1 class="text-base font-semibold">Ringkasan Pemberian Kredit</h1>
                    <p class="text-xs text-muted-foreground">
                        Periode Juni 2026 · {{ isAll ? 'seluruh kantor' : `Kantor ${selected.label}` }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <Badge variant="secondary" class="font-medium" data-testid="dashboard-static-badge">
                        Data contoh (statis)
                    </Badge>
                    <Combobox
                        v-model="office"
                        :options="OFFICES"
                        placeholder="Semua Kantor"
                        class="w-[170px]"
                        data-testid="dashboard-filter-office"
                    />
                    <Button
                        variant="outline"
                        size="sm"
                        data-testid="dashboard-refresh"
                        @click="router.reload({ preserveScroll: true })"
                    >
                        <RefreshCw class="size-4" /> {{ ACTION.refresh }}
                    </Button>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <Card v-for="kpi in KPIS" :key="kpi.key" :data-testid="`stat-${kpi.key}`">
                    <CardContent class="flex items-start justify-between gap-3">
                        <div class="min-w-0 space-y-1">
                            <p class="text-xs uppercase tracking-wide text-muted-foreground">{{ kpi.label }}</p>
                            <p class="text-base font-semibold tabular-nums">{{ kpi.value }}</p>
                            <p class="truncate text-xs text-muted-foreground">{{ kpi.hint }}</p>
                        </div>
                        <span class="flex size-8 shrink-0 items-center justify-center rounded-md border bg-muted/40">
                            <component :is="kpi.icon" class="size-4 text-muted-foreground" aria-hidden="true" />
                        </span>
                    </CardContent>
                </Card>
            </div>

            <div class="grid items-start gap-6 lg:grid-cols-3">
                <Card class="lg:col-span-2" data-testid="card-monthly">
                    <CardHeader><CardTitle>Pengajuan vs Realisasi (10 bulan)</CardTitle></CardHeader>
                    <CardContent>
                        <MiniBarChart :data="monthly" :series="MONTHLY_SERIES" :height="240" />
                    </CardContent>
                </Card>

                <Card data-testid="card-target">
                    <CardHeader><CardTitle>Capaian Target Bulan Ini</CardTitle></CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-1.5">
                            <div class="flex items-baseline justify-between">
                                <span class="text-lg font-semibold tabular-nums">{{ target.realisasi }}</span>
                                <span class="text-xs text-muted-foreground">dari {{ target.target }}</span>
                            </div>
                            <Progress :value="target.percent" class="h-2" />
                            <p class="text-xs text-muted-foreground">{{ target.percent }}% target penyaluran tercapai</p>
                        </div>
                        <div class="space-y-2 border-t pt-3">
                            <p class="text-xs uppercase tracking-wider text-muted-foreground">Hasil Keputusan Komite</p>
                            <div class="flex h-2.5 overflow-hidden rounded-full">
                                <span
                                    v-for="d in DECISIONS"
                                    :key="d.label"
                                    :style="{ width: `${d.percent}%`, backgroundColor: `hsl(var(${d.token}))` }"
                                    :title="`${d.label}: ${d.percent}%`"
                                />
                            </div>
                            <ul class="space-y-1">
                                <li v-for="d in DECISIONS" :key="d.label" class="flex items-center justify-between text-xs">
                                    <span class="flex items-center gap-1.5 text-muted-foreground">
                                        <span class="size-2 rounded-sm" :style="{ backgroundColor: `hsl(var(${d.token}))` }" />
                                        {{ d.label }}
                                    </span>
                                    <span class="tabular-nums">{{ d.percent }}%</span>
                                </li>
                            </ul>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="grid items-start gap-6" :class="isAll ? 'lg:grid-cols-3' : 'lg:grid-cols-2'">
                <Card data-testid="card-stages">
                    <CardHeader><CardTitle>Berkas per Tahap (9 tahap)</CardTitle></CardHeader>
                    <CardContent class="space-y-2">
                        <div v-for="stage in stages" :key="stage.label" class="space-y-1">
                            <div class="flex items-center justify-between text-xs">
                                <span class="truncate text-muted-foreground">{{ stage.label }}</span>
                                <span class="tabular-nums">{{ stage.count }}</span>
                            </div>
                            <Progress :value="stagePercent(stage.count)" class="h-1.5" />
                        </div>
                    </CardContent>
                </Card>

                <Card v-if="isAll" data-testid="card-by-office">
                    <CardHeader><CardTitle>Penyaluran per Kantor</CardTitle></CardHeader>
                    <CardContent>
                        <HBarChart :data="byOffice" :height="240" />
                    </CardContent>
                </Card>

                <Card data-testid="card-by-product">
                    <CardHeader><CardTitle>Komposisi Produk</CardTitle></CardHeader>
                    <CardContent>
                        <HBarChart :data="byProduct" :height="240" />
                    </CardContent>
                </Card>
            </div>

            <Card data-testid="card-queue">
                <CardHeader class="flex flex-row flex-wrap items-center justify-between gap-2 space-y-0">
                    <CardTitle class="flex items-center gap-2">
                        Antrean Keputusan Komite
                        <Badge variant="secondary" class="font-normal tabular-nums">{{ queue.length }}</Badge>
                    </CardTitle>
                    <Button as="a" href="/committees" variant="outline" size="sm" data-testid="link-committees">
                        Jalur Komite <ArrowRight class="size-4" />
                    </Button>
                </CardHeader>
                <CardContent class="p-0">
                    <EmptyState v-if="!queue.length" variant="no-data" />
                    <Table v-else>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nomor Berkas</TableHead>
                                <TableHead>Nasabah</TableHead>
                                <TableHead v-if="isAll">Kantor</TableHead>
                                <TableHead>Produk</TableHead>
                                <TableHead>Plafon</TableHead>
                                <TableHead>Pemutus</TableHead>
                                <TableHead>Umur</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="row in queue" :key="row.code">
                                <TableCell class="font-mono text-xs">{{ row.code }}</TableCell>
                                <TableCell class="font-medium">{{ row.name }}</TableCell>
                                <TableCell v-if="isAll">{{ row.office }}</TableCell>
                                <TableCell>{{ row.product }}</TableCell>
                                <TableCell class="tabular-nums">{{ row.amount }}</TableCell>
                                <TableCell>{{ row.tier }}</TableCell>
                                <TableCell>
                                    <Badge :variant="row.level" class="whitespace-nowrap font-medium">{{ row.age }}</Badge>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        Aktivitas Terakhir
                        <Badge variant="secondary" class="font-normal tabular-nums">{{ props.activities.length }}</Badge>
                    </CardTitle>
                </CardHeader>
                <CardContent class="p-0">
                    <EmptyState v-if="!props.activities.length" variant="no-data" />
                    <div v-else class="thin-scroll max-h-72 divide-y overflow-y-auto" data-testid="recent-activities">
                        <div
                            v-for="log in props.activities"
                            :key="log.id"
                            class="flex items-start gap-3 px-6 py-2 transition-colors hover:bg-muted/40"
                        >
                            <span class="w-11 shrink-0 text-xs font-medium tabular-nums text-muted-foreground">
                                {{ log.time }}
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-[13px] font-medium">{{ log.action }}</span>
                                <span class="block truncate text-xs text-muted-foreground">
                                    {{ log.actor }} · {{ log.module }}
                                </span>
                            </span>
                        </div>
                    </div>
                </CardContent>
                <CardFooter class="justify-end">
                    <Button as="a" href="/audit-trail" variant="outline" size="sm" data-testid="link-all-activities">
                        Semua Aktivitas <ArrowRight class="size-4" />
                    </Button>
                </CardFooter>
            </Card>
        </div>
    </AppLayout>
</template>
