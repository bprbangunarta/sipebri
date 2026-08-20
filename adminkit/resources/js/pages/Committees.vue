<script setup>
import { computed, ref } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { Download, Gavel, Loader2, Pencil, Play, Plus, Save, Scale, Trash2, X } from 'lucide-vue-next';

import AppLayout from '@/components/layout/AppLayout.vue';
import { menuLabelOf } from '@/composables/useMenuLabel';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Combobox from '@/components/ui/Combobox.vue';
import Dialog from '@/components/ui/Dialog.vue';
import DropdownMenuItem from '@/components/ui/DropdownMenuItem.vue';
import DropdownMenuSeparator from '@/components/ui/DropdownMenuSeparator.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import Switch from '@/components/ui/Switch.vue';
import CommitteeSimulator from '@/components/composite/CommitteeSimulator.vue';
import ConfirmDeleteDialog from '@/components/composite/ConfirmDeleteDialog.vue';
import DataTableCard from '@/components/composite/DataTableCard.vue';
import RowActions from '@/components/composite/RowActions.vue';
import { ACTION } from '@/constants/labels';
import { MECHANISM_OPTIONS, conditionLabel } from '@/constants/committee';

const props = defineProps({
    paths: { type: Array, required: true },
    productOptions: { type: Array, default: () => [] },
    pathOptions: { type: Array, default: () => [] },
    conditionMap: { type: Object, default: () => ({}) },
});

const page = usePage();
const canManage = computed(() =>
    (page.props.auth?.user?.permissions ?? []).includes('committees.manage'),
);
const pageTitle = computed(() => menuLabelOf('/committees', 'Komite Kredit'));

const columns = [
    { key: 'product_label', label: 'Produk' },
    { key: 'condition_label', label: 'Kondisi / Kategori', hideBelow: 'sm' },
    { key: 'mechanism_label', label: 'Mekanisme', hideBelow: 'md' },
    { key: 'tiers_count', label: 'Jenjang', align: 'right', hideBelow: 'sm' },
    { key: 'status_label', label: 'Status', sortable: false, hideBelow: 'sm' },
    { key: 'actions', label: '', align: 'right', width: '48px', sortable: false },
];

const simulatorOpen = ref(false);
const dialogOpen = ref(false);
const editing = ref(null);
const form = useForm({
    product_id: '',
    condition: '',
    mechanism: 'plafon',
    is_active: true,
    note: '',
    copy_from: '',
});

const openCreate = () => {
    editing.value = null;
    form.clearErrors();
    form.defaults({ product_id: '', condition: '', mechanism: 'plafon', is_active: true, note: '', copy_from: '' });
    form.reset();
    dialogOpen.value = true;
};

const openEdit = (row) => {
    editing.value = row;
    form.clearErrors();
    form.defaults({
        product_id: row.product_id ? String(row.product_id) : '',
        condition: row.condition ?? '',
        mechanism: row.mechanism,
        is_active: row.is_active,
        note: row.note ?? '',
        copy_from: '',
    });
    form.reset();
    dialogOpen.value = true;
};

const submit = () => {
    const options = { preserveScroll: true, onSuccess: () => (dialogOpen.value = false) };

    if (editing.value) form.put(`/committees/${editing.value.id}`, options);
    else form.post('/committees', options);
};

const deleting = ref(null);
const rowForm = useForm({});
const confirmDelete = () =>
    rowForm.delete(`/committees/${deleting.value.id}`, {
        preserveScroll: true,
        onFinish: () => (deleting.value = null),
    });

const copyOptions = computed(() => [
    { value: '', label: 'Tidak menyalin' },
    ...props.pathOptions,
]);
</script>

<template>
    <Head :title="pageTitle" />
    <AppLayout>
        <div class="space-y-6" data-testid="committees-page-view">
            <DataTableCard
                :title="pageTitle"
                testid="committees"
                :columns="columns"
                :rows="props.paths"
                :empty-icon="Gavel"
                empty-title="Belum ada jalur komite"
                empty-description="Buat jalur per produk (atau lintas produk untuk kondisi seperti RELOAN), lalu susun jenjang kewenangannya."
                :show-refresh="false"
                row-clickable
                @row-click="router.visit(`/committees/${$event.id}`)"
            >
                <template #header-action>
                    <Button variant="outline" size="sm" data-testid="committees-simulate" @click="simulatorOpen = true">
                        <Play class="size-4" /> Simulasi
                    </Button>
                    <Button variant="outline" size="sm" as="a" href="/committees/export" data-testid="committees-export">
                        <Download class="size-4" /> {{ ACTION.export }}
                    </Button>
                    <Button v-if="canManage" size="sm" data-testid="committees-add" @click="openCreate">
                        <Plus class="size-4" /> {{ ACTION.add }}
                    </Button>
                </template>

                <template #cell-product_label="{ row }">
                    <span class="block truncate font-medium">{{ row.product_label }}</span>
                    <span class="mt-0.5 block whitespace-normal text-xs text-muted-foreground md:hidden">
                        {{ conditionLabel(row.condition_label) }} · {{ row.mechanism_label }} ·
                        {{ row.tiers_count }} jenjang ·
                        {{ row.status_label }}
                    </span>
                </template>

                <template #cell-condition_label="{ row }">
                    <Badge :variant="row.condition ? 'default' : 'secondary'" class="font-medium">
                        {{ conditionLabel(row.condition_label) }}
                    </Badge>
                </template>

                <template #cell-mechanism_label="{ row }">
                    <span class="inline-flex items-center gap-1.5">
                        <Scale class="size-3.5 text-muted-foreground" />{{ row.mechanism_label }}
                    </span>
                </template>

                <template #cell-status_label="{ row }">
                    <Badge :variant="row.is_active ? 'secondary' : 'destructive'" class="font-medium">
                        {{ row.status_label }}
                    </Badge>
                </template>

                <template #cell-actions="{ row }">
                    <RowActions v-if="canManage" :testid="`committees-actions-${row.id}`">
                        <DropdownMenuItem
                            :data-testid="`committees-detail-${row.id}`"
                            @select="router.visit(`/committees/${row.id}`)"
                        >
                            <Gavel />Kelola Jenjang
                        </DropdownMenuItem>
                        <DropdownMenuItem :data-testid="`committees-edit-${row.id}`" @select="openEdit(row)">
                            <Pencil />{{ ACTION.edit }}
                        </DropdownMenuItem>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem
                            class="text-destructive data-[highlighted]:text-destructive"
                            :data-testid="`committees-delete-${row.id}`"
                            @click="deleting = row"
                        >
                            <Trash2 />{{ ACTION.delete }}
                        </DropdownMenuItem>
                    </RowActions>
                </template>
            </DataTableCard>

            <Dialog
                v-model:open="dialogOpen"
                :title="`${editing ? ACTION.edit : ACTION.add} Jalur Komite`"
                class="max-w-md"
            >
                <form id="path-form" class="form-dense space-y-[var(--field-gap)]" novalidate @submit.prevent="submit">
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Produk</Label>
                        <Combobox
                            v-model="form.product_id"
                            :options="props.productOptions"
                            placeholder="Semua Produk"
                            data-testid="committees-form-product"
                        />
                        <p v-if="form.errors.product_id" class="text-xs font-medium text-destructive">
                            {{ form.errors.product_id }}
                        </p>
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="path-condition">Kondisi / Kategori</Label>
                        <Input
                            id="path-condition"
                            v-model="form.condition"
                            class="uppercase placeholder:normal-case"
                            placeholder="Kosongkan untuk Normal"
                            maxlength="30"
                            autocomplete="off"
                            data-testid="committees-form-condition"
                        />
                        <p v-if="form.errors.condition" class="text-xs font-medium text-destructive" data-testid="committees-form-condition-error">
                            {{ form.errors.condition }}
                        </p>
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Mekanisme</Label>
                        <Combobox
                            v-model="form.mechanism"
                            :options="MECHANISM_OPTIONS"
                            data-testid="committees-form-mechanism"
                        />
                        <p v-if="form.errors.mechanism" class="text-xs font-medium text-destructive">
                            {{ form.errors.mechanism }}
                        </p>
                    </div>
                    <div v-if="!editing" class="space-y-[var(--item-gap)]">
                        <Label>Salin Jenjang Dari</Label>
                        <Combobox
                            v-model="form.copy_from"
                            :options="copyOptions"
                            placeholder="Tidak menyalin"
                            data-testid="committees-form-copy"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="path-note">Catatan</Label>
                        <Input
                            id="path-note"
                            v-model="form.note"
                            maxlength="255"
                            autocomplete="off"
                            data-testid="committees-form-note"
                        />
                    </div>
                    <label class="flex items-center justify-between gap-3 pt-1">
                        <span class="text-sm">Aktif</span>
                        <Switch v-model="form.is_active" data-testid="committees-form-active" />
                    </label>
                </form>

                <template #footer>
                    <Button variant="outline" size="sm" data-testid="committees-form-cancel" @click="dialogOpen = false">
                        <X class="size-4" /> {{ ACTION.cancel }}
                    </Button>
                    <Button
                        size="sm"
                        type="submit"
                        form="path-form"
                        :disabled="form.processing"
                        data-testid="committees-form-save"
                    >
                        <Loader2 v-if="form.processing" class="size-4 animate-spin" />
                        <Save v-else class="size-4" />
                        {{ form.processing ? ACTION.saving : ACTION.save }}
                    </Button>
                </template>
            </Dialog>

            <CommitteeSimulator
                v-model:open="simulatorOpen"
                :product-options="props.productOptions"
                :condition-map="props.conditionMap"
            />

            <ConfirmDeleteDialog
                :open="Boolean(deleting)"
                title="Hapus Jalur Komite?"
                description="Seluruh jenjang pada jalur ini ikut terhapus dan tidak dapat dipulihkan."
                :processing="rowForm.processing"
                @update:open="deleting = null"
                @confirm="confirmDelete"
            />
        </div>
    </AppLayout>
</template>
