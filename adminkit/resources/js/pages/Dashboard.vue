<script setup>
import { Head, router } from '@inertiajs/vue3';
import {
    ArrowRight, BadgeAlert, Banknote, FileStack, Gavel, RefreshCw, Timer, TrendingUp,
} from 'lucide-vue-next';

import AppLayout from '@/components/layout/AppLayout.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardFooter from '@/components/ui/CardFooter.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
import Progress from '@/components/ui/Progress.vue';
import Separator from '@/components/ui/Separator.vue';
import Table from '@/components/ui/Table.vue';
import TableBody from '@/components/ui/TableBody.vue';
import TableCell from '@/components/ui/TableCell.vue';
import TableHead from '@/components/ui/TableHead.vue';
import TableHeader from '@/components/ui/TableHeader.vue';
import TableRow from '@/components/ui/TableRow.vue';
import EmptyState from '@/components/composite/EmptyState.vue';
import MiniBarChart from '@/components/composite/MiniBarChart.vue';
import HBarChart from '@/components/composite/HBarChart.vue';
import ComponentGallery from '@/components/composite/ComponentGallery.vue';
import { ACTION } from '@/constants/labels';

const props = defineProps({
    activities: { type: Array, default: () => [] },
    storage: { type: Array, default: () => [] },
});

/* ── DATA CONTOH (statis) — belum tersambung ke basis data ─────────────── */
const KPIS = [
    { key: 'submitted', label: 'Pengajuan Bulan Ini', value: '128', hint: '+12% vs bulan lalu', icon: FileStack },
    { key: 'disbursed', label: 'Plafon Dicairkan', value: 'Rp 18,4 M', hint: '57 berkas terealisasi', icon: Banknote },
    { key: 'committee', label: 'Menunggu Komite', value: '23', hint: '7 berkas lewat 3 hari', icon: Gavel },
    { key: 'sla', label: 'Rata-rata SLA', value: '4,2 hari', hint: 'target maksimal 5 hari', icon: Timer },
    { key: 'npl', label: 'Rasio NPL', value: '2,4%', hint: 'ambang kebijakan 5%', icon: BadgeAlert },
];

const MONTHLY = [
    { label: 'Sep', pengajuan: 96, realisasi: 61 },
    { label: 'Okt', pengajuan: 104, realisasi: 68 },
    { label: 'Nov', pengajuan: 112, realisasi: 74 },
    { label: 'Des', pengajuan: 138, realisasi: 91 },
    { label: 'Jan', pengajuan: 87, realisasi: 54 },
    { label: 'Feb', pengajuan: 94, realisasi: 59 },
    { label: 'Mar', pengajuan: 108, realisasi: 70 },
    { label: 'Apr', pengajuan: 121, realisasi: 82 },
    { label: 'Mei', pengajuan: 116, realisasi: 78 },
    { label: 'Jun', pengajuan: 128, realisasi: 57 },
];

const MONTHLY_SERIES = [
    { key: 'pengajuan', label: 'Pengajuan', token: '--chart-1' },
    { key: 'realisasi', label: 'Realisasi', token: '--chart-3' },
];

const STAGES = [
    { label: '1 · Pengajuan', count: 128 },
    { label: '2 · Verifikasi Dokumen', count: 118 },
    { label: '3 · Survey & Analisa', count: 104 },
    { label: '4 · Taksasi Agunan', count: 96 },
    { label: '5 · Rekomendasi', count: 88 },
    { label: '6 · Komite Kredit', count: 74 },
    { label: '7 · Persetujuan', count: 68 },
    { label: '8 · Akad & Pengikatan', count: 61 },
    { label: '9 · Pencairan', count: 57 },
];

const BY_OFFICE = [
    { label: 'Pamanukan', count: 42 },
    { label: 'Subang', count: 31 },
    { label: 'Pagaden', count: 18 },
    { label: 'Jalancagak', count: 14 },
    { label: 'Kalijati', count: 11 },
    { label: 'Sukamandi', count: 8 },
    { label: 'Pusakajaya', count: 4 },
];

const BY_PRODUCT = [
    { label: 'KRU · Kredit Umum', count: 38 },
    { label: 'KUP · Kredit Pegawai', count: 24 },
    { label: 'KKO · Konstruksi', count: 17 },
    { label: 'KBT · Bertahap', count: 13 },
    { label: 'KPP · Pensiunan', count: 9 },
    { label: 'KRISPI · Mikro', count: 6 },
];

const QUALITY = [
    { label: 'Lancar', percent: 91.2, token: '--chart-1' },
    { label: 'DPK', percent: 4.8, token: '--chart-2' },
    { label: 'Kurang Lancar', percent: 1.6, token: '--chart-3' },
    { label: 'Diragukan', percent: 1.1, token: '--chart-4' },
    { label: 'Macet', percent: 1.3, token: '--chart-5' },
];

const QUEUE = [
    { code: 'PK-2606-0142', name: 'Sumarni', product: 'KRU', amount: 'Rp 250.000.000', tier: 'Komite II', age: '4 hari', level: 'destructive' },
    { code: 'PK-2606-0139', name: 'Agus Salim', product: 'KUP', amount: 'Rp 75.000.000', tier: 'Kasi Analis', age: '2 hari', level: 'secondary' },
    { code: 'PK-2606-0137', name: 'CV Mitra Tani', product: 'KKO', amount: 'Rp 480.000.000', tier: 'Komite III', age: '5 hari', level: 'destructive' },
    { code: 'PK-2606-0131', name: 'Dewi Lestari', product: 'KBT', amount: 'Rp 120.000.000', tier: 'Kabag Analis', age: '1 hari', level: 'secondary' },
    { code: 'PK-2606-0128', name: 'Hendra Wijaya', product: 'KRISPI', amount: 'Rp 25.000.000', tier: 'Kasi Analis', age: '1 hari', level: 'secondary' },
];

const TARGET = { realisasi: 'Rp 18,4 M', target: 'Rp 24,0 M', percent: 77 };

const stageMax = STAGES[0].count;
const stagePercent = (count) => Math.round((count / stageMax) * 100);
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout>
        <div class="space-y-6" data-testid="dashboard-page">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="space-y-1">
                    <h1 class="text-base font-semibold">Ringkasan Pemberian Kredit</h1>
                    <p class="text-xs text-muted-foreground">Periode Juni 2026 · seluruh kantor</p>
                </div>
                <div class="flex items-center gap-2">
                    <Badge variant="secondary" class="font-medium" data-testid="dashboard-static-badge">
                        Data contoh (statis)
                    </Badge>
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

            <!-- KPI -->
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
                    <CardHeader class="flex flex-row flex-wrap items-center justify-between gap-2 space-y-0">
                        <CardTitle>Pengajuan vs Realisasi (10 bulan)</CardTitle>
                        <span class="flex items-center gap-1.5 text-xs text-muted-foreground">
                            <TrendingUp class="size-3.5" aria-hidden="true" /> rasio realisasi 45–66%
                        </span>
                    </CardHeader>
                    <CardContent>
                        <MiniBarChart :data="MONTHLY" :series="MONTHLY_SERIES" :height="240" />
                    </CardContent>
                </Card>

                <Card data-testid="card-target">
                    <CardHeader><CardTitle>Capaian Target Bulan Ini</CardTitle></CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-1.5">
                            <div class="flex items-baseline justify-between">
                                <span class="text-lg font-semibold tabular-nums">{{ TARGET.realisasi }}</span>
                                <span class="text-xs text-muted-foreground">dari {{ TARGET.target }}</span>
                            </div>
                            <Progress :value="TARGET.percent" class="h-2" />
                            <p class="text-xs text-muted-foreground">{{ TARGET.percent }}% target penyaluran tercapai</p>
                        </div>
                        <Separator />
                        <div class="space-y-2">
                            <p class="text-xs uppercase tracking-wider text-muted-foreground">Kualitas Portofolio</p>
                            <div class="flex h-2.5 overflow-hidden rounded-full">
                                <span
                                    v-for="q in QUALITY"
                                    :key="q.label"
                                    :style="{ width: `${q.percent}%`, backgroundColor: `hsl(var(${q.token}))` }"
                                    :title="`${q.label}: ${q.percent}%`"
                                />
                            </div>
                            <ul class="space-y-1">
                                <li
                                    v-for="q in QUALITY"
                                    :key="q.label"
                                    class="flex items-center justify-between text-xs"
                                >
                                    <span class="flex items-center gap-1.5 text-muted-foreground">
                                        <span class="size-2 rounded-sm" :style="{ backgroundColor: `hsl(var(${q.token}))` }" />
                                        {{ q.label }}
                                    </span>
                                    <span class="tabular-nums">{{ q.percent }}%</span>
                                </li>
                            </ul>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="grid items-start gap-6 lg:grid-cols-3">
                <Card class="lg:col-span-1" data-testid="card-stages">
                    <CardHeader><CardTitle>Berkas per Tahap (9 tahap)</CardTitle></CardHeader>
                    <CardContent class="space-y-2">
                        <div v-for="stage in STAGES" :key="stage.label" class="space-y-1">
                            <div class="flex items-center justify-between text-xs">
                                <span class="truncate text-muted-foreground">{{ stage.label }}</span>
                                <span class="tabular-nums">{{ stage.count }}</span>
                            </div>
                            <Progress :value="stagePercent(stage.count)" class="h-1.5" />
                        </div>
                    </CardContent>
                </Card>

                <Card data-testid="card-by-office">
                    <CardHeader><CardTitle>Penyaluran per Kantor</CardTitle></CardHeader>
                    <CardContent>
                        <HBarChart :data="BY_OFFICE" :height="240" />
                    </CardContent>
                </Card>

                <Card data-testid="card-by-product">
                    <CardHeader><CardTitle>Komposisi Produk</CardTitle></CardHeader>
                    <CardContent>
                        <HBarChart :data="BY_PRODUCT" :height="240" />
                    </CardContent>
                </Card>
            </div>

            <Card data-testid="card-queue">
                <CardHeader class="flex flex-row flex-wrap items-center justify-between gap-2 space-y-0">
                    <CardTitle class="flex items-center gap-2">
                        Antrean Keputusan Komite
                        <Badge variant="secondary" class="font-normal tabular-nums">{{ QUEUE.length }}</Badge>
                    </CardTitle>
                    <Button as="a" href="/committees" variant="outline" size="sm" data-testid="link-committees">
                        Jalur Komite <ArrowRight class="size-4" />
                    </Button>
                </CardHeader>
                <CardContent class="p-0">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nomor Berkas</TableHead>
                                <TableHead>Nasabah</TableHead>
                                <TableHead>Produk</TableHead>
                                <TableHead>Plafon</TableHead>
                                <TableHead>Pemutus</TableHead>
                                <TableHead>Umur</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="row in QUEUE" :key="row.code">
                                <TableCell class="font-mono text-xs">{{ row.code }}</TableCell>
                                <TableCell class="font-medium">{{ row.name }}</TableCell>
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

            <div class="grid items-start gap-6 lg:grid-cols-3">
                <Card class="flex flex-col lg:col-span-2">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            Aktivitas Terakhir
                            <Badge variant="secondary" class="font-normal tabular-nums">
                                {{ props.activities.length }}
                            </Badge>
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

                <Card>
                    <CardHeader><CardTitle>Penyimpanan</CardTitle></CardHeader>
                    <CardContent class="space-y-3">
                        <div v-for="item in props.storage" :key="item.label" class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-muted-foreground">{{ item.label }}</span>
                                <span class="tabular-nums">{{ item.used }} / {{ item.total }}</span>
                            </div>
                            <Progress :value="item.percent" class="h-1.5" />
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Separator />

            <ComponentGallery />
        </div>
    </AppLayout>
</template>
