<script setup>
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { ArrowRight, BadgeCheck, Banknote, FileStack, Gavel, RefreshCw, Timer } from 'lucide-vue-next';

import AppLayout from '@/components/layout/AppLayout.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
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
import { ACTION } from '@/constants/labels';
import { persen } from '@/constants/committee';

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
const miliar = (value) => `Rp ${value.toFixed(1).replace('.', ',')} M`;
const money = (value) => miliar(value * share.value);

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

/* [label, jumlah berkas, plafon dalam miliar] */
const OFFICE_BASE = [
    ['Pamanukan', 42, 6.1], ['Subang', 31, 4.4], ['Pagaden', 18, 2.6], ['Jalancagak', 14, 2.0],
    ['Kalijati', 12, 1.7], ['Sukamandi', 8, 1.1], ['Pusakajaya', 4, 0.5],
];
const PRODUCT_BASE = [
    ['KRU · Kredit Umum', 38, 7.9], ['KUP · Kredit Pegawai', 24, 3.1], ['KKO · Konstruksi', 17, 3.6],
    ['KBT · Bertahap', 13, 1.9], ['KPP · Pensiunan', 9, 1.1], ['KRISPI · Mikro', 6, 0.8],
];

const withBars = (rows) => {
    const max = Math.max(...rows.map((r) => r.plafon), 0.1);
    return rows.map((row, i) => ({
        ...row,
        percent: Math.round((row.plafon / max) * 100),
        token: `--chart-${(i % 5) + 1}`,
    }));
};

const byOffice = computed(() =>
    withBars(OFFICE_BASE.map(([label, count, plafon]) => ({ label, count, plafon, amount: miliar(plafon) }))),
);
const byProduct = computed(() =>
    withBars(
        PRODUCT_BASE.map(([label, count, plafon]) => ({
            label,
            count: Math.max(1, scale(count)),
            plafon: plafon * share.value,
            amount: money(plafon),
        })),
    ),
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

const target = computed(() => ({ realisasi: money(18.4), target: money(24), percent: 77 }));
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout>
        <div class="space-y-4" data-testid="dashboard-page">
            <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end sm:justify-between">
                <div class="space-y-1">
                    <h1 class="text-base font-semibold">Ringkasan Pemberian Kredit</h1>
                    <p class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-muted-foreground">
                        <span>Periode Juni 2026 · {{ isAll ? 'seluruh kantor' : `Kantor ${selected.label}` }}</span>
                        <Badge variant="secondary" class="font-medium" data-testid="dashboard-static-badge">
                            Data contoh (statis)
                        </Badge>
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Combobox
                        v-model="office"
                        :options="OFFICES"
                        placeholder="Semua Kantor"
                        class="min-w-0 flex-1 sm:w-[170px] sm:flex-none"
                        data-testid="dashboard-filter-office"
                    />
                    <Button
                        variant="outline"
                        size="sm"
                        class="shrink-0"
                        data-testid="dashboard-refresh"
                        @click="router.reload({ preserveScroll: true })"
                    >
                        <RefreshCw class="size-4" />
                        <span class="hidden sm:inline">{{ ACTION.refresh }}</span>
                    </Button>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 xl:grid-cols-5">
                <Card
                    v-for="(kpi, i) in KPIS"
                    :key="kpi.key"
                    :class="i === KPIS.length - 1 ? 'col-span-2 md:col-span-1' : ''"
                    :data-testid="`stat-${kpi.key}`"
                >
                    <CardContent class="flex items-start justify-between gap-2 px-3 py-3 sm:px-4">
                        <div class="min-w-0 space-y-0.5">
                            <p class="truncate text-[11px] uppercase tracking-wide text-muted-foreground sm:text-xs">
                                {{ kpi.label }}
                            </p>
                            <p class="text-base font-semibold tabular-nums">{{ kpi.value }}</p>
                            <p class="truncate text-[11px] text-muted-foreground sm:text-xs">{{ kpi.hint }}</p>
                        </div>
                        <span class="hidden size-8 shrink-0 items-center justify-center rounded-md border bg-muted/40 sm:flex">
                            <component :is="kpi.icon" class="size-4 text-muted-foreground" aria-hidden="true" />
                        </span>
                    </CardContent>
                </Card>
            </div>

            <div class="grid items-stretch gap-4 lg:grid-cols-3">
                <Card class="lg:col-span-2" data-testid="card-monthly">
                    <CardHeader class="pb-2"><CardTitle>Pengajuan vs Realisasi (10 bulan)</CardTitle></CardHeader>
                    <CardContent class="pb-3">
                        <div class="thin-scroll -mx-1 overflow-x-auto px-1">
                            <div class="min-w-[460px] md:min-w-0">
                                <MiniBarChart :data="monthly" :series="MONTHLY_SERIES" :height="196" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card data-testid="card-target">
                    <CardHeader class="pb-2"><CardTitle>Capaian Target Bulan Ini</CardTitle></CardHeader>
                    <CardContent class="space-y-3 pb-3">
                        <div class="space-y-1.5">
                            <div class="flex items-baseline justify-between">
                                <span class="text-lg font-semibold tabular-nums">{{ target.realisasi }}</span>
                                <span class="text-xs text-muted-foreground">dari {{ target.target }}</span>
                            </div>
                            <Progress :value="target.percent" class="h-2" />
                            <p class="text-xs text-muted-foreground">{{ persen(target.percent, 0) }} target penyaluran tercapai</p>
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
                                    <span class="tabular-nums">{{ persen(d.percent, 1) }}</span>
                                </li>
                            </ul>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="grid items-start gap-4" :class="isAll ? 'lg:grid-cols-2' : 'lg:grid-cols-1'">
                <Card v-if="isAll" data-testid="card-by-office">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle>Penyaluran per Kantor</CardTitle>
                        <span class="text-xs text-muted-foreground">berkas · plafon</span>
                    </CardHeader>
                    <CardContent class="space-y-1.5 pb-3">
                        <div v-for="row in byOffice" :key="row.label" class="space-y-1">
                            <div class="flex items-center justify-between gap-3 text-xs">
                                <span class="truncate font-medium">{{ row.label }}</span>
                                <span class="shrink-0 whitespace-nowrap text-[11px] tabular-nums text-muted-foreground sm:text-xs">
                                    {{ row.count }} berkas · <span class="text-foreground">{{ row.amount }}</span>
                                </span>
                            </div>
                            <div class="h-1.5 overflow-hidden rounded-full bg-muted">
                                <span
                                    class="block h-full rounded-full"
                                    :style="{ width: `${row.percent}%`, backgroundColor: `hsl(var(${row.token}))` }"
                                />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card data-testid="card-by-product">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle>Komposisi Produk</CardTitle>
                        <span class="text-xs text-muted-foreground">berkas · plafon</span>
                    </CardHeader>
                    <CardContent class="space-y-1.5 pb-3">
                        <div v-for="row in byProduct" :key="row.label" class="space-y-1">
                            <div class="flex items-center justify-between gap-3 text-xs">
                                <span class="truncate font-medium">{{ row.label }}</span>
                                <span class="shrink-0 whitespace-nowrap text-[11px] tabular-nums text-muted-foreground sm:text-xs">
                                    {{ row.count }} berkas · <span class="text-foreground">{{ row.amount }}</span>
                                </span>
                            </div>
                            <div class="h-1.5 overflow-hidden rounded-full bg-muted">
                                <span
                                    class="block h-full rounded-full"
                                    :style="{ width: `${row.percent}%`, backgroundColor: `hsl(var(${row.token}))` }"
                                />
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card data-testid="card-queue">
                <CardHeader class="flex flex-row flex-wrap items-center justify-between gap-2 space-y-0 pb-2">
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
                                <TableHead class="whitespace-nowrap">Nomor Berkas</TableHead>
                                <TableHead class="whitespace-nowrap">Nasabah</TableHead>
                                <TableHead v-if="isAll" class="whitespace-nowrap">Kantor</TableHead>
                                <TableHead class="whitespace-nowrap">Produk</TableHead>
                                <TableHead class="whitespace-nowrap">Plafon</TableHead>
                                <TableHead class="whitespace-nowrap">Pemutus</TableHead>
                                <TableHead class="whitespace-nowrap">Umur</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="row in queue" :key="row.code">
                                <TableCell class="whitespace-nowrap font-mono text-xs">{{ row.code }}</TableCell>
                                <TableCell class="whitespace-nowrap font-medium">{{ row.name }}</TableCell>
                                <TableCell v-if="isAll" class="whitespace-nowrap">{{ row.office }}</TableCell>
                                <TableCell class="whitespace-nowrap">{{ row.product }}</TableCell>
                                <TableCell class="whitespace-nowrap tabular-nums">{{ row.amount }}</TableCell>
                                <TableCell class="whitespace-nowrap">{{ row.tier }}</TableCell>
                                <TableCell>
                                    <Badge :variant="row.level" class="whitespace-nowrap font-medium">{{ row.age }}</Badge>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
