<script setup>
import { computed, ref } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { Eye, Pencil, Plus, ShieldCheck, Trash2 } from 'lucide-vue-next';

import AppLayout from '@/components/layout/AppLayout.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import DropdownMenuItem from '@/components/ui/DropdownMenuItem.vue';
import DropdownMenuSeparator from '@/components/ui/DropdownMenuSeparator.vue';
import ConfirmDeleteDialog from '@/components/composite/ConfirmDeleteDialog.vue';
import DataTableCard from '@/components/composite/DataTableCard.vue';
import RowActions from '@/components/composite/RowActions.vue';
import { ACTION } from '@/constants/labels';
import { rupiah } from '@/constants/committee';
import { useServerTable } from '@/composables/useServerTable';

const props = defineProps({
    records: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
});

const page = usePage();
const canManage = computed(() =>
    (page.props.auth?.user?.permissions ?? []).includes('collateral-simulation.manage'),
);

const columns = [
    { key: 'collateral_id', label: 'Agunan ID' },
    { key: 'collateral_type_code', label: 'Jenis Agunan', hideBelow: 'md' },
    { key: 'owner_name', label: 'Pemilik' },
    { key: 'binding_label', label: 'Pengikatan', hideBelow: 'lg', sortable: false },
    { key: 'value_appraisal', label: 'Nilai Taksasi', align: 'right', hideBelow: 'md', sortable: false },
    { key: 'actions', label: '', align: 'right', width: '48px', sortable: false },
];

const { query, loading, reload, onSearch, onSort, onPage, onPerPage, sortState } = useServerTable({
    url: '/collateral-simulation',
    only: ['records', 'filters'],
    initial: {
        search: props.filters.search ?? '',
        sort: props.filters.sort ?? 'collateral_id',
        dir: props.filters.dir ?? 'asc',
        page: props.records.meta.page ?? 1,
        per_page: props.records.meta.per_page ?? 10,
    },
});

const deleting = ref(null);
const rowForm = useForm({});
const confirmDelete = () =>
    rowForm.delete(`/collateral-simulation/${deleting.value.id}`, {
        preserveScroll: true,
        onFinish: () => (deleting.value = null),
    });
</script>

<template>
    <Head title="Agunan Kredit" />
    <AppLayout>
        <div class="space-y-4" data-testid="collateral-simulation-page">
            <DataTableCard
                server
                title="Agunan Kredit"
                testid="collateral-simulation"
                :columns="columns"
                :rows="props.records.data"
                :meta="props.records.meta"
                :search="query.search"
                :sort="sortState"
                :loading="loading"
                :empty-icon="ShieldCheck"
                empty-title="Belum ada contoh agunan"
                empty-description="Tambahkan contoh per jenis agunan untuk direview bagian terkait."
                :show-refresh="false"
                @update:search="onSearch"
                @update:sort="onSort"
                @update:page="onPage"
                @update:per-page="onPerPage"
                @refresh="reload()"
            >
                <template #header-action>
                    <Button
                        v-if="canManage"
                        size="sm"
                        data-testid="collateral-simulation-add"
                        @click="router.visit('/collateral-simulation/create')"
                    >
                        <Plus class="size-4" /> {{ ACTION.add }}
                    </Button>
                </template>

                <template #cell-collateral_id="{ row }">
                    <span class="block whitespace-nowrap font-mono text-xs font-medium">{{ row.collateral_id ?? `#${row.id}` }}</span>
                    <span class="mt-0.5 block whitespace-normal text-xs text-muted-foreground md:hidden">
                        {{ row.type_label }}
                    </span>
                </template>

                <template #cell-collateral_type_code="{ row }">
                    <span class="block whitespace-normal">
                        <Badge variant="secondary" class="mr-1 font-mono font-medium">{{ row.collateral_type_code }}</Badge>
                        {{ row.type_label }}
                    </span>
                </template>

                <template #cell-owner_name="{ row }">
                    <span class="block font-medium">{{ row.owner_name ?? '—' }}</span>
                    <span class="block truncate text-xs text-muted-foreground">{{ row.region_label ?? '—' }}</span>
                </template>

                <template #cell-binding_label="{ row }">
                    <span class="whitespace-normal">{{ row.binding_label ?? 'Belum diikat' }}</span>
                </template>

                <template #cell-value_appraisal="{ row }">
                    <span class="whitespace-nowrap tabular-nums">{{ rupiah(row.value_appraisal) }}</span>
                </template>

                <template #cell-actions="{ row }">
                    <RowActions :testid="`collateral-simulation-actions-${row.id}`">
                        <DropdownMenuItem
                            v-if="canManage"
                            :data-testid="`collateral-simulation-edit-${row.id}`"
                            @select="router.visit(`/collateral-simulation/${row.id}/edit`)"
                        >
                            <Pencil />{{ ACTION.edit }}
                        </DropdownMenuItem>
                        <DropdownMenuItem
                            :data-testid="`collateral-simulation-detail-${row.id}`"
                            @select="router.visit(`/collateral-simulation/${row.id}`)"
                        >
                            <Eye />Detail
                        </DropdownMenuItem>
                        <template v-if="canManage">
                            <DropdownMenuSeparator />
                            <DropdownMenuItem
                                class="text-destructive data-[highlighted]:text-destructive"
                                :data-testid="`collateral-simulation-delete-${row.id}`"
                                @click="deleting = row"
                            >
                                <Trash2 />{{ ACTION.delete }}
                            </DropdownMenuItem>
                        </template>
                    </RowActions>
                </template>
            </DataTableCard>

            <ConfirmDeleteDialog
                :open="Boolean(deleting)"
                title="Hapus contoh agunan?"
                description="Data dihapus permanen dan tidak dapat dipulihkan."
                :processing="rowForm.processing"
                @update:open="deleting = null"
                @confirm="confirmDelete"
            />
        </div>
    </AppLayout>
</template>
