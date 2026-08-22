<script setup>
import { computed, ref } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { Database, Loader2, Pencil, Plus, Save, SlidersHorizontal, Trash2, X } from 'lucide-vue-next';

import FormActions from '@/components/composite/FormActions.vue';
import AppLayout from '@/components/layout/AppLayout.vue';
import { menuLabelOf } from '@/composables/useMenuLabel';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Combobox from '@/components/ui/Combobox.vue';
import Switch from '@/components/ui/Switch.vue';
import Dialog from '@/components/ui/Dialog.vue';
import DropdownMenuItem from '@/components/ui/DropdownMenuItem.vue';
import DropdownMenuSeparator from '@/components/ui/DropdownMenuSeparator.vue';
import Input from '@/components/ui/Input.vue';
import NumberInput from '@/components/ui/NumberInput.vue';
import Label from '@/components/ui/Label.vue';
import ConfirmDeleteDialog from '@/components/composite/ConfirmDeleteDialog.vue';
import DataTableCard from '@/components/composite/DataTableCard.vue';
import RowActions from '@/components/composite/RowActions.vue';
import { ACTION } from '@/constants/labels';
import { all, max, required } from '@/lib/validators';
import { useLiveValidation } from '@/composables/useLiveValidation';
import { useServerTable } from '@/composables/useServerTable';

/** Halaman generik untuk seluruh modul data referensi (lihat ReferenceController). */
const props = defineProps({
    title: { type: String, required: true },
    slug: { type: String, required: true },
    module: { type: String, required: true },
    fields: { type: Array, required: true },
    records: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    hasStatus: { type: Boolean, default: false },
});

const page = usePage();
const canManage = computed(() =>
    (page.props.auth?.user?.permissions ?? []).includes(`${props.module}.manage`),
);

const pageTitle = computed(() => menuLabelOf(`/${props.slug}`, props.title));

const columns = computed(() => [
    ...props.fields.map((field) => ({ key: field.key, label: field.label, hideBelow: field.hide_below })),
    { key: 'actions', label: '', align: 'right', width: '48px', sortable: false },
]);

const isFlag = (field) => field.type === 'boolean';
const isNumber = (field) => field.type === 'number';
const textFields = computed(() => props.fields.filter((f) => !isFlag(f) && !isNumber(f)));
const numberFields = computed(() => props.fields.filter(isNumber));
const statusKey = computed(() => props.fields.find(isFlag)?.key ?? null);
const statusSlot = computed(() => `cell-${statusKey.value ?? '__none'}`);

const firstKey = computed(() => textFields.value[0].key);
const firstSlot = computed(() => `cell-${firstKey.value}`);
const lastKey = computed(() => textFields.value[textFields.value.length - 1].key);
const lastSlot = computed(() => `cell-${lastKey.value}`);

const statusOptions = [
    { value: '', label: 'Semua status' },
    { value: 'active', label: 'Aktif' },
    { value: 'inactive', label: 'Nonaktif' },
];

// Nilai kolom yang disembunyikan pada layar kecil tetap terlihat sebagai baris ringkas.
const hiddenSummary = (row) =>
    props.fields
        .filter((f) => f.hide_below)
        .map((f) => row[f.key])
        .filter(Boolean)
        .join(' · ');

const { query, loading, reload, onSearch, onSort, onPage, onPerPage, onFilter, sortState } = useServerTable({
    url: `/${props.slug}`,
    only: ['records', 'filters'],
    initial: {
        search: props.filters.search ?? '',
        sort: props.filters.sort ?? props.fields[0].key,
        dir: props.filters.dir ?? 'asc',
        status: props.filters.status ?? '',
        page: props.records.meta.page ?? 1,
        per_page: props.records.meta.per_page ?? 10,
    },
});

/* ── Formulir tambah/ubah ────────────────────────────────────────────── */
const blank = () =>
    Object.fromEntries(props.fields.map((field) => [field.key, isFlag(field) ? true : isNumber(field) ? 0 : '']));

const dialogOpen = ref(false);
const editing = ref(null);
const form = useForm(blank());

const rules = Object.fromEntries(
    textFields.value.map((field) => [
        field.key,
        all(required(field.label.toLowerCase()), max(255, field.label)),
    ]),
);
const check = useLiveValidation(form, rules);

const openCreate = () => {
    editing.value = null;
    form.clearErrors();
    form.defaults(blank());
    form.reset();
    dialogOpen.value = true;
};

const openEdit = (row) => {
    editing.value = row;
    form.clearErrors();
    form.defaults(
        Object.fromEntries(
            props.fields.map((f) => [
                f.key,
                isFlag(f) ? Boolean(row[f.key]) : isNumber(f) ? (row[f.key] ?? 0) : (row[f.key] ?? ''),
            ]),
        ),
    );
    form.reset();
    dialogOpen.value = true;
};

const submit = () => {
    if (!check.validateAll()) return;

    const options = { preserveScroll: true, onSuccess: () => (dialogOpen.value = false) };

    if (editing.value) form.put(`/${props.slug}/${editing.value.id}`, options);
    else form.post(`/${props.slug}`, options);
};

/* ── Hapus (permanen) per baris & massal ─────────────────────────────── */
const deleting = ref(null);
const rowForm = useForm({});
const selected = ref([]);
const bulkForm = useForm({ ids: [] });
const bulkConfirm = ref(false);

const confirmDelete = () =>
    rowForm.delete(`/${props.slug}/${deleting.value.id}`, {
        preserveScroll: true,
        onFinish: () => (deleting.value = null),
    });

const runBulkDelete = () => {
    bulkForm.ids = [...selected.value];
    bulkForm.post(`/${props.slug}/bulk`, {
        preserveScroll: true,
        onSuccess: () => {
            selected.value = [];
            bulkConfirm.value = false;
        },
    });
};
</script>

<template>
    <Head :title="pageTitle" />
    <AppLayout>
        <div class="space-y-6" :data-testid="`${props.slug}-page-view`">
            <DataTableCard
                server
                :title="pageTitle"
                :testid="props.slug"
                :columns="columns"
                :rows="props.records.data"
                :meta="props.records.meta"
                :search="query.search"
                :sort="sortState"
                :loading="loading"
                :empty-icon="Database"
                :empty-title="`Belum ada ${pageTitle.toLowerCase()}`"
                empty-description="Tambahkan data pertama untuk mulai memakainya di modul lain."
                :show-refresh="false"
                selectable
                :selected="selected"
                @update:selected="selected = $event"
                @update:search="onSearch"
                @update:sort="onSort"
                @update:page="onPage"
                @update:per-page="onPerPage"
                @refresh="reload()"
            >
                <template v-if="props.hasStatus" #filters>
                    <Combobox
                        :model-value="query.status"
                        :options="statusOptions"
                        placeholder="Semua status"
                        class="w-full sm:w-[160px]"
                        :data-testid="`${props.slug}-status-filter`"
                        @update:model-value="onFilter('status', $event)"
                    />
                </template>

                <template #bulk-actions>
                    <Button
                        variant="destructive"
                        size="sm"
                        :disabled="bulkForm.processing"
                        :data-testid="`${props.slug}-bulk-delete`"
                        @click="bulkConfirm = true"
                    >
                        <Trash2 class="size-4" /> {{ ACTION.delete }}
                    </Button>
                </template>

                <template #header-action>
                    <Button
                        v-if="canManage"
                        size="sm"
                        :data-testid="`${props.slug}-add`"
                        @click="openCreate"
                    >
                        <Plus class="size-4" /> {{ ACTION.add }}
                    </Button>
                </template>

                <template #[firstSlot]="{ row }">
                    <span class="block font-medium">{{ row[firstKey] }}</span>
                    <span
                        v-if="hiddenSummary(row)"
                        class="mt-0.5 block whitespace-normal text-xs text-muted-foreground sm:hidden"
                    >
                        {{ hiddenSummary(row) }}
                    </span>
                </template>

                <template #[lastSlot]="{ row }">
                    <span class="block max-w-[45vw] truncate sm:max-w-none">{{ row[lastKey] }}</span>
                </template>

                <template v-if="statusKey" #[statusSlot]="{ row }">
                    <Badge
                        :variant="row[statusKey] ? 'secondary' : 'destructive'"
                        class="font-medium"
                        :data-testid="`${props.slug}-status-${row.id}`"
                    >
                        {{ row[statusKey] ? 'Aktif' : 'Nonaktif' }}
                    </Badge>
                </template>

                <template #cell-actions="{ row }">
                    <RowActions v-if="canManage" :testid="`${props.slug}-actions-${row.id}`">
                        <DropdownMenuItem
                            v-if="props.slug === 'products'"
                            :data-testid="`${props.slug}-parameter-${row.id}`"
                            @select="router.visit(`/products/${row.id}`)"
                        >
                            <SlidersHorizontal />Atur Parameter
                        </DropdownMenuItem>
                        <DropdownMenuItem :data-testid="`${props.slug}-edit-${row.id}`" @select="openEdit(row)">
                            <Pencil />{{ ACTION.edit }}
                        </DropdownMenuItem>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem
                            class="text-destructive data-[highlighted]:text-destructive"
                            :data-testid="`${props.slug}-delete-${row.id}`"
                            @click="deleting = row"
                        >
                            <Trash2 />{{ ACTION.delete }}
                        </DropdownMenuItem>
                    </RowActions>
                </template>
            </DataTableCard>

            <Dialog
                v-model:open="dialogOpen"
                :title="`${editing ? ACTION.edit : ACTION.add} ${pageTitle}`"
                class="max-w-md"
            >
                <form
                    id="reference-form"
                    class="form-dense space-y-[var(--field-gap)]"
                    novalidate
                    @submit.prevent="submit"
                >
                    <div v-for="field in textFields" :key="field.key" class="space-y-[var(--item-gap)]">
                        <Label :for="`ref-${field.key}`">{{ field.label }}</Label>
                        <Input
                            :id="`ref-${field.key}`"
                            v-model="form[field.key]"
                            :class="field.uppercase ? 'uppercase' : ''"
                            autocomplete="off"
                            maxlength="255"
                            :data-testid="`${props.slug}-form-${field.key}`"
                            @blur="check.validate(field.key)"
                        />
                        <p
                            v-if="form.errors[field.key]"
                            class="text-xs font-medium text-destructive"
                            :data-testid="`${props.slug}-form-${field.key}-error`"
                        >
                            {{ form.errors[field.key] }}
                        </p>
                    </div>

                    <div v-for="field in numberFields" :key="field.key" class="space-y-[var(--item-gap)]">
                        <Label :for="`ref-${field.key}`">{{ field.label }}</Label>
                        <NumberInput
                            :id="`ref-${field.key}`"
                            v-model="form[field.key]"
                            class="text-right tabular-nums"
                            :data-testid="`${props.slug}-form-${field.key}`"
                        />
                        <p
                            v-if="form.errors[field.key]"
                            class="text-xs font-medium text-destructive"
                            :data-testid="`${props.slug}-form-${field.key}-error`"
                        >
                            {{ form.errors[field.key] }}
                        </p>
                        <p v-else-if="field.hint" class="text-xs text-muted-foreground">{{ field.hint }}</p>
                    </div>

                    <label v-if="statusKey" class="flex items-center justify-between gap-3 pt-1">
                        <span class="text-sm">Aktif</span>
                        <Switch
                            v-model="form[statusKey]"
                            :data-testid="`${props.slug}-form-${statusKey}`"
                        />
                    </label>
                </form>

                <template #footer>
                    <FormActions
                        :cancel-testid="`${props.slug}-form-cancel`"
                        :submit-testid="`${props.slug}-form-save`"
                        submit-type="submit"
                        form="reference-form"
                        :processing="form.processing"
                        @cancel="dialogOpen = false"
                    />
                </template>
            </Dialog>

            <ConfirmDeleteDialog
                :open="Boolean(deleting)"
                :title="`Hapus ${pageTitle}?`"
                description="Data dihapus permanen dan tidak dapat dipulihkan."
                :processing="rowForm.processing"
                @update:open="deleting = null"
                @confirm="confirmDelete"
            />

            <ConfirmDeleteDialog
                :open="bulkConfirm"
                :title="`Hapus ${selected.length} Data Terpilih?`"
                description="Data dihapus permanen dan tidak dapat dipulihkan."
                :processing="bulkForm.processing"
                @update:open="bulkConfirm = false"
                @confirm="runBulkDelete"
            />
        </div>
    </AppLayout>
</template>
