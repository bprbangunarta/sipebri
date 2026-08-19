<script setup>
import { computed, ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Database, Loader2, Pencil, Plus, Save, Trash2, X } from 'lucide-vue-next';

import AppLayout from '@/components/layout/AppLayout.vue';
import { menuLabelOf } from '@/composables/useMenuLabel';
import Button from '@/components/ui/Button.vue';
import Dialog from '@/components/ui/Dialog.vue';
import DropdownMenuItem from '@/components/ui/DropdownMenuItem.vue';
import DropdownMenuSeparator from '@/components/ui/DropdownMenuSeparator.vue';
import Input from '@/components/ui/Input.vue';
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
});

const page = usePage();
const canManage = computed(() =>
    (page.props.auth?.user?.permissions ?? []).includes(`${props.module}.manage`),
);

const pageTitle = computed(() => menuLabelOf(`/${props.slug}`, props.title));

const columns = computed(() => [
    ...props.fields.map((field) => ({ key: field.key, label: field.label })),
    { key: 'actions', label: '', align: 'right', width: '48px', sortable: false },
]);

const { query, loading, reload, onSearch, onSort, onPage, onPerPage, sortState } = useServerTable({
    url: `/${props.slug}`,
    only: ['records', 'filters'],
    initial: {
        search: props.filters.search ?? '',
        sort: props.filters.sort ?? props.fields[0].key,
        dir: props.filters.dir ?? 'asc',
        page: props.records.meta.page ?? 1,
        per_page: props.records.meta.per_page ?? 10,
    },
});

/* ── Formulir tambah/ubah ────────────────────────────────────────────── */
const blank = () => Object.fromEntries(props.fields.map((field) => [field.key, '']));

const dialogOpen = ref(false);
const editing = ref(null);
const form = useForm(blank());

const rules = Object.fromEntries(
    props.fields.map((field) => [
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
    form.defaults(Object.fromEntries(props.fields.map((f) => [f.key, row[f.key] ?? ''])));
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

                <template #cell-actions="{ row }">
                    <RowActions v-if="canManage" :testid="`${props.slug}-actions-${row.id}`">
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
                    <div v-for="field in props.fields" :key="field.key" class="space-y-[var(--item-gap)]">
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
                </form>

                <template #footer>
                    <Button
                        variant="outline"
                        size="sm"
                        :data-testid="`${props.slug}-form-cancel`"
                        @click="dialogOpen = false"
                    >
                        <X class="size-4" /> {{ ACTION.cancel }}
                    </Button>
                    <Button
                        size="sm"
                        type="submit"
                        form="reference-form"
                        :disabled="form.processing"
                        :data-testid="`${props.slug}-form-save`"
                    >
                        <Loader2 v-if="form.processing" class="size-4 animate-spin" />
                        <Save v-else class="size-4" />
                        {{ form.processing ? ACTION.saving : ACTION.save }}
                    </Button>
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
