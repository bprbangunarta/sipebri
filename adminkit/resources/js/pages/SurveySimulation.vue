<script setup>
import { Head, router } from '@inertiajs/vue3';
import { MapPinned } from 'lucide-vue-next';

import AppLayout from '@/components/layout/AppLayout.vue';
import Button from '@/components/ui/Button.vue';
import DataTableCard from '@/components/composite/DataTableCard.vue';
import { rupiah } from '@/constants/committee';
import { useServerTable } from '@/composables/useServerTable';

const props = defineProps({
    records: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    today: { type: String, default: '' },
});

const columns = [
    { key: 'application_code', label: 'Kode Pengajuan' },
    { key: 'full_name', label: 'Pemohon' },
    { key: 'product_label', label: 'Produk', hideBelow: 'lg', sortable: false },
    { key: 'survey_date', label: 'Jadwal', hideBelow: 'md', sortable: false },
    { key: 'actions', label: '', align: 'right', width: '120px', sortable: false },
];

const { query, loading, reload, onSearch, onSort, onPage, onPerPage, sortState } = useServerTable({
    url: '/survey-simulation',
    only: ['records', 'filters'],
    initial: {
        search: props.filters.search ?? '',
        sort: props.filters.sort ?? 'application_code',
        dir: props.filters.dir ?? 'asc',
        page: props.records.meta.page ?? 1,
        per_page: props.records.meta.per_page ?? 10,
    },
});
</script>

<template>
    <Head title="Survei Kredit" />
    <AppLayout>
        <div class="space-y-4" data-testid="survey-simulation-page">
            <DataTableCard
                server
                :title="`Survei Hari Ini · ${props.today}`"
                testid="survey"
                :columns="columns"
                :rows="props.records.data"
                :meta="props.records.meta"
                :search="query.search"
                :sort="sortState"
                :loading="loading"
                :empty-icon="MapPinned"
                empty-title="Tidak ada jadwal survei hari ini"
                empty-description="Daftar hanya memuat penugasan survei Anda dengan tanggal hari ini. Bila jadwal tidak bisa dikerjakan, minta penjadwalan ulang lewat tombol Batal pada lembar survei."
                row-clickable
                @update:search="onSearch"
                @update:sort="onSort"
                @update:page="onPage"
                @update:per-page="onPerPage"
                @refresh="reload()"
                @row-click="router.visit(`/survey-simulation/${$event.id}`)"
            >
                <template #cell-application_code="{ row }">
                    <span class="block whitespace-nowrap font-mono text-xs font-medium">{{ row.application_code }}</span>
                    <span class="mt-0.5 block whitespace-nowrap text-xs text-muted-foreground">
                        Kasi: {{ row.supervisor_name ?? '—' }}
                    </span>
                </template>

                <template #cell-full_name="{ row }">
                    <span class="block font-medium">{{ row.full_name }}</span>
                    <span class="block whitespace-nowrap text-xs text-muted-foreground">
                        {{ rupiah(row.requested_amount) }} · {{ row.requested_tenor ? `${row.requested_tenor} bln` : '—' }}
                    </span>
                </template>

                <template #cell-product_label="{ row }">
                    <span class="block whitespace-normal">{{ row.product_label ?? '—' }}</span>
                </template>

                <template #cell-survey_date="{ row }">
                    <span class="block whitespace-nowrap">{{ row.survey_date_label ?? '—' }}</span>
                </template>

                <template #cell-actions="{ row }">
                    <Button
                        size="sm"
                        :data-testid="`survey-open-${row.id}`"
                        @click="router.visit(`/survey-simulation/${row.id}`)"
                    >
                        <MapPinned class="size-4" /> Survei
                    </Button>
                </template>
            </DataTableCard>
        </div>
    </AppLayout>
</template>
