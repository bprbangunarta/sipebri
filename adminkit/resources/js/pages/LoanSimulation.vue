<script setup>
import { computed, ref } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { FileText, FolderOpen, Loader2, Plus, Search, Trash2 } from 'lucide-vue-next';

import AppLayout from '@/components/layout/AppLayout.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Combobox from '@/components/ui/Combobox.vue';
import Dialog from '@/components/ui/Dialog.vue';
import DigitsInput from '@/components/ui/DigitsInput.vue';
import Label from '@/components/ui/Label.vue';
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
    statuses: { type: Array, default: () => [] },
    sampleNiks: { type: Array, default: () => [] },
});

const page = usePage();
const canManage = computed(() =>
    (page.props.auth?.user?.permissions ?? []).includes('loan-simulation.manage'),
);

const columns = [
    { key: 'application_code', label: 'Kode Pengajuan' },
    { key: 'full_name', label: 'Pemohon' },
    { key: 'product_label', label: 'Produk', hideBelow: 'lg', sortable: false },
    { key: 'requested_amount', label: 'Plafon', align: 'right', hideBelow: 'md' },
    { key: 'status', label: 'Status', hideBelow: 'sm' },
    { key: 'actions', label: '', align: 'right', width: '48px', sortable: false },
];

const { query, loading, reload, onSearch, onSort, onPage, onPerPage, onFilter, sortState } = useServerTable({
    url: '/loan-simulation',
    only: ['records', 'filters'],
    initial: {
        search: props.filters.search ?? '',
        sort: props.filters.sort ?? 'application_code',
        dir: props.filters.dir ?? 'asc',
        status: props.filters.status ?? '',
        page: props.records.meta.page ?? 1,
        per_page: props.records.meta.per_page ?? 10,
    },
});

const statusOptions = computed(() => [
    { value: '', label: 'Semua status' },
    ...props.statuses.map((s) => ({ value: s, label: s })),
]);

const STATUS_TONE = {
    DIAJUKAN: 'secondary',
    ANALISA: 'secondary',
    KOMITE: 'secondary',
    DISETUJUI: 'default',
    REALISASI: 'default',
    DITOLAK: 'destructive',
    DIBATALKAN: 'outline',
};

const deleting = ref(null);
const rowForm = useForm({});
const confirmDelete = () =>
    rowForm.delete(`/loan-simulation/${deleting.value.id}`, {
        preserveScroll: true,
        onFinish: () => (deleting.value = null),
    });

/* Pengajuan baru: cukup nomor KTP, sisanya dilengkapi di halaman berkas. */
const showCreate = ref(false);
const createForm = useForm({ nik: '' });

const openCreate = () => {
    createForm.reset();
    createForm.clearErrors();
    showCreate.value = true;
};

const submitCreate = () =>
    createForm.post('/loan-simulation', {
        preserveScroll: true,
        onSuccess: () => (showCreate.value = false),
    });
</script>

<template>
    <Head title="Pengajuan Kredit" />
    <AppLayout>
        <div class="space-y-4" data-testid="loan-simulation-page">
            <DataTableCard
                server
                title="Pengajuan Kredit"
                testid="loan-simulation"
                :columns="columns"
                :rows="props.records.data"
                :meta="props.records.meta"
                :search="query.search"
                :sort="sortState"
                :loading="loading"
                :empty-icon="FileText"
                empty-title="Belum ada pengajuan"
                empty-description="Berkas pengajuan adalah pintu masuk proses pemberian kredit."
                :show-refresh="false"
                row-clickable
                @update:search="onSearch"
                @update:sort="onSort"
                @update:page="onPage"
                @update:per-page="onPerPage"
                @refresh="reload()"
                @row-click="router.visit(`/loan-simulation/${$event.id}`)"
            >
                <template #header-action>
                    <Button
                        v-if="canManage"
                        size="sm"
                        data-testid="loan-simulation-add"
                        @click="openCreate"
                    >
                        <Plus class="size-4" /> {{ ACTION.add }}
                    </Button>
                </template>

                <template #filters>
                    <Combobox
                        :model-value="query.status"
                        :options="statusOptions"
                        placeholder="Semua status"
                        class="w-full sm:w-[180px]"
                        data-testid="loan-simulation-status-filter"
                        @update:model-value="onFilter('status', $event)"
                    />
                </template>

                <template #cell-application_code="{ row }">
                    <span class="block whitespace-nowrap font-mono text-xs font-medium">{{ row.application_code }}</span>
                    <span class="mt-0.5 block whitespace-nowrap text-xs text-muted-foreground">
                        {{ row.application_date }}
                    </span>
                </template>

                <template #cell-full_name="{ row }">
                    <span class="block font-medium">{{ row.full_name }}</span>
                    <span class="block font-mono text-xs text-muted-foreground">{{ row.nik }}</span>
                </template>

                <template #cell-product_label="{ row }">
                    <span class="whitespace-normal">{{ row.product_label ?? '—' }}</span>
                </template>

                <template #cell-requested_amount="{ row }">
                    <span class="block whitespace-nowrap tabular-nums">{{ rupiah(row.requested_amount) }}</span>
                    <span class="block whitespace-nowrap text-xs text-muted-foreground">
                        {{ row.requested_tenor ? `${row.requested_tenor} bln` : '—' }}
                    </span>
                </template>

                <template #cell-status="{ row }">
                    <Badge :variant="STATUS_TONE[row.status] ?? 'secondary'" class="font-medium">
                        {{ row.status }}
                    </Badge>
                </template>

                <template #cell-actions="{ row }">
                    <RowActions :testid="`loan-simulation-actions-${row.id}`">
                        <DropdownMenuItem
                            :data-testid="`loan-simulation-open-${row.id}`"
                            @select="router.visit(`/loan-simulation/${row.id}`)"
                        >
                            <FolderOpen />Buka Berkas
                        </DropdownMenuItem>
                        <template v-if="canManage">
                            <DropdownMenuSeparator />
                            <DropdownMenuItem
                                class="text-destructive data-[highlighted]:text-destructive"
                                :data-testid="`loan-simulation-delete-${row.id}`"
                                @click="deleting = row"
                            >
                                <Trash2 />{{ ACTION.delete }}
                            </DropdownMenuItem>
                        </template>
                    </RowActions>
                </template>
            </DataTableCard>

            <Dialog :open="showCreate" title="Pengajuan Baru" @update:open="showCreate = $event">
                <div class="form-dense space-y-[var(--item-gap)]">
                    <Label for="c-nik" class="text-[11px] font-medium uppercase tracking-wider text-muted-foreground">
                        Nomor KTP <span class="text-destructive">*</span>
                    </Label>
                    <DigitsInput
                        id="c-nik"
                        v-model="createForm.nik"
                        maxlength="16"
                        placeholder="16 angka"
                        class="font-mono tracking-wider"
                        data-testid="loan-create-nik"
                        @keydown.enter.prevent="submitCreate"
                    />
                    <p v-if="createForm.errors.nik" class="text-xs font-medium text-destructive" data-testid="loan-create-error">
                        {{ createForm.errors.nik }}
                    </p>
                    <p v-else class="text-xs text-muted-foreground">
                        Nomor KTP diperiksa ke sistem data nasabah. Contoh (MOCK):
                        <span class="font-mono">{{ props.sampleNiks.join(' · ') }}</span>
                    </p>
                </div>

                <template #footer>
                    <Button variant="outline" size="sm" data-testid="loan-create-cancel" @click="showCreate = false">
                        {{ ACTION.cancel }}
                    </Button>
                    <Button
                        size="sm"
                        :disabled="createForm.processing"
                        data-testid="loan-create-submit"
                        @click="submitCreate"
                    >
                        <Loader2 v-if="createForm.processing" class="size-4 animate-spin" />
                        <Search v-else class="size-4" />
                        {{ createForm.processing ? 'Memeriksa...' : 'Cek & Lanjutkan' }}
                    </Button>
                </template>
            </Dialog>

            <ConfirmDeleteDialog
                :open="Boolean(deleting)"
                title="Hapus pengajuan?"
                :description="`Berkas ${deleting?.application_code} akan diarsipkan dan bisa dipulihkan.`"
                :processing="rowForm.processing"
                @update:open="deleting = null"
                @confirm="confirmDelete"
            />
        </div>
    </AppLayout>
</template>
