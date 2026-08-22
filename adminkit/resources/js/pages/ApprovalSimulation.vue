<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ChevronRight, Gavel } from 'lucide-vue-next';

import DataTableCard from '@/components/composite/DataTableCard.vue';
import AppLayout from '@/components/layout/AppLayout.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import { useServerTable } from '@/composables/useServerTable';
import { rupiah } from '@/constants/committee';

const props = defineProps({
    records: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
});

const columns = [
    { key: 'application_code', label: 'Kode Pengajuan' },
    { key: 'full_name', label: 'Pemohon' },
    { key: 'product_label', label: 'Produk & Jalur', hideBelow: 'lg', sortable: false },
    { key: 'requested_amount', label: 'Plafon', align: 'right', hideBelow: 'md' },
    { key: 'proposed_amount', label: 'Usulan Analis', align: 'right', hideBelow: 'lg', sortable: false },
    { key: 'submitted_at', label: 'Diajukan', hideBelow: 'sm', sortable: false },
    { key: 'actions', label: '', align: 'right', width: '120px', sortable: false },
];

const { query, loading, reload, onSearch, onSort, onPage, onPerPage, sortState } = useServerTable({
    url: '/approval-simulation',
    only: ['records', 'filters'],
    initial: {
        search: props.filters.search ?? '',
        sort: props.filters.sort ?? 'application_code',
        dir: props.filters.dir ?? 'asc',
        page: props.records.meta?.page ?? 1,
        per_page: props.records.meta?.per_page ?? 10,
    },
});
</script>

<template>
    <Head title="Persetujuan Komite" />
    <AppLayout>
        <div class="space-y-4" data-testid="approval-simulation-page">
            <DataTableCard
                server
                title="Persetujuan Komite"
                testid="approval"
                :columns="columns"
                :rows="props.records.data"
                :meta="props.records.meta"
                :search="query.search"
                :sort="sortState"
                :loading="loading"
                :empty-icon="Gavel"
                empty-title="Belum ada berkas yang menunggu keputusan"
                empty-description="Berkas muncul di sini setelah Staff Analis menekan Ajukan ke Komite pada lembar analisa."
                row-clickable
                @update:search="onSearch"
                @update:sort="onSort"
                @update:page="onPage"
                @update:per-page="onPerPage"
                @refresh="reload()"
                @row-click="router.visit(`/approval-simulation/${$event.id}`)"
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
                    <span class="block">{{ row.product_label ?? '—' }}</span>
                    <span class="block text-xs text-muted-foreground">
                        {{ row.committee_path ?? 'Jalur komite belum diatur' }}
                    </span>
                    <span v-if="row.pending_position" class="block text-xs text-muted-foreground">
                        Menunggu: {{ row.pending_position }}
                    </span>
                </template>

                <template #cell-requested_amount="{ row }">
                    <span class="block whitespace-nowrap tabular-nums">{{ rupiah(row.requested_amount) }}</span>
                    <span class="block whitespace-nowrap text-xs text-muted-foreground">
                        {{ row.requested_tenor ? `${row.requested_tenor} bln` : '—' }}
                    </span>
                </template>

                <template #cell-proposed_amount="{ row }">
                    <Badge :variant="row.proposed_amount ? 'default' : 'secondary'" class="font-medium">
                        {{ row.proposed_amount ? rupiah(row.proposed_amount) : 'Tanpa usulan' }}
                    </Badge>
                    <span v-if="row.proposed_tenor" class="mt-0.5 block whitespace-nowrap text-xs text-muted-foreground">
                        {{ row.proposed_tenor }} bln
                    </span>
                </template>

                <template #cell-submitted_at="{ row }">
                    <span class="block whitespace-nowrap text-xs">{{ row.submitted_by ?? '—' }}</span>
                    <span class="block whitespace-nowrap text-xs text-muted-foreground">
                        {{ row.submitted_at ?? '—' }}
                    </span>
                </template>

                <template #cell-actions="{ row }">
                    <Button
                        variant="outline"
                        size="sm"
                        :data-testid="`approval-open-${row.id}`"
                        @click.stop="router.visit(`/approval-simulation/${row.id}`)"
                    >
                        Buka <ChevronRight class="size-4" />
                    </Button>
                </template>
            </DataTableCard>
        </div>
    </AppLayout>
</template>
