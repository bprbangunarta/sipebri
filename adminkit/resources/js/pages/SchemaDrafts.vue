<script setup>
import { computed, ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Database, Pencil, Plus, Save, Table2, Trash2, X } from 'lucide-vue-next';

import FormActions from '@/components/composite/FormActions.vue';
import AppLayout from '@/components/layout/AppLayout.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Dialog from '@/components/ui/Dialog.vue';
import DropdownMenuItem from '@/components/ui/DropdownMenuItem.vue';
import DropdownMenuSeparator from '@/components/ui/DropdownMenuSeparator.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import Switch from '@/components/ui/Switch.vue';
import ConfirmDeleteDialog from '@/components/composite/ConfirmDeleteDialog.vue';
import DataTableCard from '@/components/composite/DataTableCard.vue';
import RowActions from '@/components/composite/RowActions.vue';
import { ACTION } from '@/constants/labels';

/** Skema Migrasi — daftar rancangan tabel (alat developer). */
const props = defineProps({
    drafts: { type: Array, default: () => [] },
});

const columns = [
    { key: 'name', label: 'Rancangan' },
    { key: 'table_name', label: 'Nama Tabel' },
    { key: 'columns_count', label: 'Kolom', align: 'right', hideBelow: 'sm' },
    { key: 'table_exists', label: 'Status', hideBelow: 'md' },
    { key: 'actions', label: '', align: 'right', width: '48px', sortable: false },
];

const editing = ref(null);
const showForm = ref(false);
const deleting = ref(null);

const form = useForm({
    name: '',
    table_name: '',
    note: '',
    with_id: true,
    with_timestamps: true,
    with_soft_deletes: false,
});

const openCreate = () => {
    editing.value = null;
    form.defaults({ name: '', table_name: '', note: '', with_id: true, with_timestamps: true, with_soft_deletes: false });
    form.reset();
    form.clearErrors();
    showForm.value = true;
};

const openEdit = (row) => {
    editing.value = row;
    form.name = row.name;
    form.table_name = row.table_name;
    form.note = row.note ?? '';
    form.with_id = row.with_id;
    form.with_timestamps = row.with_timestamps;
    form.with_soft_deletes = row.with_soft_deletes;
    form.clearErrors();
    showForm.value = true;
};

const submit = () => {
    const options = { preserveScroll: true, onSuccess: () => (showForm.value = false) };
    if (editing.value) form.put(`/schema-drafts/${editing.value.id}`, options);
    else form.post('/schema-drafts', options);
};

const removeForm = useForm({});
const confirmDelete = () =>
    removeForm.delete(`/schema-drafts/${deleting.value.id}`, {
        preserveScroll: true,
        onFinish: () => (deleting.value = null),
    });

const dialogTitle = computed(() => (editing.value ? 'Ubah Rancangan' : 'Rancangan Tabel Baru'));
</script>

<template>
    <Head title="Skema Migrasi" />
    <AppLayout>
        <div class="space-y-4" data-testid="schema-drafts-page">
            <DataTableCard
                title="Skema Migrasi"
                testid="schema-drafts"
                :columns="columns"
                :rows="props.drafts"
                :empty-icon="Table2"
                empty-title="Belum ada rancangan tabel"
                empty-description="Rancang struktur tabel di sini, lalu migration dibuat mengikuti rancangan ini."
                :show-refresh="false"
                row-clickable
                @row-click="router.visit(`/schema-drafts/${$event.id}`)"
            >
                <template #header-action>
                    <Button size="sm" data-testid="schema-drafts-add" @click="openCreate">
                        <Plus class="size-4" /> {{ ACTION.add }}
                    </Button>
                </template>

                <template #cell-name="{ row }">
                    <span class="block font-medium">{{ row.name }}</span>
                    <span class="block text-xs text-muted-foreground sm:hidden">{{ row.columns_count }} kolom</span>
                </template>

                <template #cell-table_name="{ row }">
                    <span class="font-mono text-xs">{{ row.table_name }}</span>
                </template>

                <template #cell-table_exists="{ row }">
                    <Badge :variant="row.table_exists ? 'secondary' : 'outline'" class="font-medium">
                        {{ row.table_exists ? 'Ada di database' : 'Belum dimigrasikan' }}
                    </Badge>
                </template>

                <template #cell-actions="{ row }">
                    <RowActions :testid="`schema-drafts-actions-${row.id}`">
                        <DropdownMenuItem
                            :data-testid="`schema-drafts-open-${row.id}`"
                            @select="router.visit(`/schema-drafts/${row.id}`)"
                        >
                            <Database />Buka Rancangan
                        </DropdownMenuItem>
                        <DropdownMenuItem :data-testid="`schema-drafts-edit-${row.id}`" @select="openEdit(row)">
                            <Pencil />{{ ACTION.edit }}
                        </DropdownMenuItem>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem
                            class="text-destructive data-[highlighted]:text-destructive"
                            :data-testid="`schema-drafts-delete-${row.id}`"
                            @click="deleting = row"
                        >
                            <Trash2 />{{ ACTION.delete }}
                        </DropdownMenuItem>
                    </RowActions>
                </template>
            </DataTableCard>

            <Dialog :open="showForm" :title="dialogTitle" @update:open="showForm = $event">
                <form class="form-dense space-y-[var(--field-gap)]" novalidate @submit.prevent="submit">
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="d-name">Nama Rancangan <span class="text-destructive">*</span></Label>
                        <Input id="d-name" v-model="form.name"  data-testid="schema-draft-name" />
                        <p v-if="form.errors.name" class="text-xs font-medium text-destructive">{{ form.errors.name }}</p>
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="d-table">Nama Tabel <span class="text-destructive">*</span></Label>
                        <Input
                            id="d-table"
                            v-model="form.table_name"
                            class="font-mono"
                            
                            data-testid="schema-draft-table"
                        />
                        <p class="text-xs text-muted-foreground">huruf kecil, angka, garis bawah — jamak, mis. `credit_applications`</p>
                        <p v-if="form.errors.table_name" class="text-xs font-medium text-destructive">{{ form.errors.table_name }}</p>
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="d-note">Catatan</Label>
                        <Input id="d-note" v-model="form.note" placeholder="(Opsional)" data-testid="schema-draft-note" />
                    </div>
                    <div class="grid gap-2 sm:grid-cols-3">
                        <label class="flex items-center justify-between gap-2 rounded-md border px-3 py-2 text-xs">
                            <span class="font-mono">id()</span>
                            <Switch v-model="form.with_id" data-testid="schema-draft-with-id" />
                        </label>
                        <label class="flex items-center justify-between gap-2 rounded-md border px-3 py-2 text-xs">
                            <span class="font-mono">timestamps()</span>
                            <Switch v-model="form.with_timestamps" data-testid="schema-draft-with-timestamps" />
                        </label>
                        <label class="flex items-center justify-between gap-2 rounded-md border px-3 py-2 text-xs">
                            <span class="font-mono">softDeletes()</span>
                            <Switch v-model="form.with_soft_deletes" data-testid="schema-draft-with-soft-deletes" />
                        </label>
                    </div>
                </form>

                <template #footer>
                    <FormActions
                        cancel-testid="schema-draft-cancel"
                        submit-testid="schema-draft-save"
                        :processing="form.processing"
                        @cancel="showForm = false"
                        @submit="submit"
                    />
                </template>
            </Dialog>

            <ConfirmDeleteDialog
                :open="Boolean(deleting)"
                title="Hapus rancangan?"
                description="Rancangan dan seluruh kolomnya dihapus. Skema database tidak terpengaruh."
                :processing="removeForm.processing"
                @update:open="deleting = null"
                @confirm="confirmDelete"
            />
        </div>
    </AppLayout>
</template>
