<script setup>
import { Head, router } from '@inertiajs/vue3';
import { Calculator } from 'lucide-vue-next';

import AppLayout from '@/components/layout/AppLayout.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import DataTableCard from '@/components/composite/DataTableCard.vue';
import { rupiah } from '@/constants/committee';
import { useServerTable } from '@/composables/useServerTable';

const props = defineProps({
    records: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
});

const columns = [
    { key: 'application_code', label: 'Kode Pengajuan' },
    { key: 'full_name', label: 'Pemohon' },
    { key: 'product_label', label: 'Produk', hideBelow: 'lg', sortable: false },
    { key: 'requested_amount', label: 'Plafon', align: 'right', hideBelow: 'md' },
    { key: 'survey_count', label: 'Survei', hideBelow: 'sm', sortable: false },
    { key: 'actions', label: '', align: 'right', width: '120px', sortable: false },
];

const { query, loading, reload, onSearch, onSort, onPage, onPerPage, sortState } = useServerTable({
    url: '/analysis-simulation',
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
    <Head title="Analisa Kredit" />
    <AppLayout>
        <div class="space-y-4" data-testid="analysis-page">
            <DataTableCard
                server
                title="Analisa Kredit"
                testid="analysis"
                :columns="columns"
                :rows="props.records.data"
                :meta="props.records.meta"
                :search="query.search"
                :sort="sortState"
                :loading="loading"
                :empty-icon="Calculator"
                empty-title="Belum ada berkas untuk dianalisa"
                empty-description="Berkas muncul di sini setelah hasil survei disimpan, atau untuk produk KTA setelah penjadwalan diberikan."
                row-clickable
                @update:search="onSearch"
                @update:sort="onSort"
                @update:page="onPage"
                @update:per-page="onPerPage"
                @refresh="reload()"
                @row-click="router.visit(`/analysis-simulation/${$event.id}`)"
            >
                <template #cell-application_code="{ row }">
                    <span class="block whitespace-nowrap font-mono text-xs font-medium">{{ row.application_code }}</span>
                    <span class="mt-0.5 block whitespace-nowrap text-xs text-muted-foreground">
                        {{ row.application_date }}
                    </span>
                </template>

                <template #cell-full_name="{ row }">
                    <span class="block font-medium">{{ row.full_name }}</span>
                    <span class="block whitespace-nowrap text-xs text-muted-foreground">{{ row.nik }}</span>
                </template>

                <template #cell-product_label="{ row }">
                    <span class="block whitespace-normal">{{ row.product_label ?? '—' }}</span>
                    <span class="mt-0.5 block whitespace-normal text-xs text-muted-foreground">
                        Kasi: {{ row.supervisor_name ?? '—' }}
                    </span>
                </template>

                <template #cell-requested_amount="{ row }">
                    <span class="block whitespace-nowrap tabular-nums">{{ rupiah(row.requested_amount) }}</span>
                    <span class="mt-0.5 block whitespace-nowrap text-xs text-muted-foreground">
                        {{ row.requested_tenor ? `${row.requested_tenor} bln` : '—' }}
                    </span>
                </template>

                <template #cell-survey_count="{ row }">
                    <Badge :variant="row.survey_count ? 'secondary' : 'default'" class="font-medium">
                        {{ row.survey_count ? `${row.survey_count}x survei` : 'Tanpa survei' }}
                    </Badge>
                </template>

                <template #cell-actions="{ row }">
                    <Button
                        size="sm"
                        :data-testid="`analysis-open-${row.id}`"
                        @click="router.visit(`/analysis-simulation/${row.id}`)"
                    >
                        <Calculator class="size-4" /> Analisa
                    </Button>
                </template>
            </DataTableCard>
        </div>
    </AppLayout>
</template>
