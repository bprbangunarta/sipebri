<script setup>
import { computed, ref, watch } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Columns3,
    Copy,
    Download,
    FileCode2,
    GitCompareArrows,
    GripVertical,
    Pencil,
    Plus,
    Save,
    Trash2,
    X,
} from 'lucide-vue-next';

import FormActions from '@/components/composite/FormActions.vue';
import AppLayout from '@/components/layout/AppLayout.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
import Combobox from '@/components/ui/Combobox.vue';
import Dialog from '@/components/ui/Dialog.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import Switch from '@/components/ui/Switch.vue';
import ConfirmDeleteDialog from '@/components/composite/ConfirmDeleteDialog.vue';
import { ACTION } from '@/constants/labels';
import { notify } from '@/composables/useToast';

/** Editor rancangan satu tabel: urutan kolom bisa digeser, plus diff & pratinjau migration. */
const props = defineProps({
    draft: { type: Object, required: true },
    columns: { type: Array, default: () => [] },
    diff: { type: Array, default: () => [] },
    migration: { type: Object, required: true },
    types: { type: Array, default: () => [] },
    tableOptions: { type: Array, default: () => [] },
});

const TABS = [
    { id: 'columns', label: 'Kolom', icon: Columns3 },
    { id: 'diff', label: 'Diff', icon: GitCompareArrows },
    { id: 'migration', label: 'Migration', icon: FileCode2 },
];

const tab = ref('columns');

/* ── Urutan kolom (geser) ─────────────────────────────────────────────── */
const rows = ref([...props.columns]);
watch(() => props.columns, (list) => (rows.value = [...list]), { deep: true });

const dragIndex = ref(null);

const onDragStart = (index, event) => {
    dragIndex.value = index;
    event.dataTransfer.effectAllowed = 'move';
};

const onDragEnter = (index) => {
    if (dragIndex.value === null || dragIndex.value === index) return;
    const list = [...rows.value];
    list.splice(index, 0, list.splice(dragIndex.value, 1)[0]);
    rows.value = list;
    dragIndex.value = index;
};

const onDragEnd = () => {
    dragIndex.value = null;
    router.put(
        `/schema-drafts/${props.draft.id}/reorder`,
        { ids: rows.value.map((r) => r.id) },
        { preserveScroll: true },
    );
};

/* ── Kolom: tambah / ubah / hapus ─────────────────────────────────────── */
const showForm = ref(false);
const editing = ref(null);
const deleting = ref(null);

const blank = {
    name: '',
    type: 'string',
    length: '',
    is_nullable: false,
    default_value: '',
    is_unique: false,
    is_index: false,
    foreign_table: '',
    comment: '',
};

const form = useForm({ ...blank });

const openCreate = () => {
    editing.value = null;
    Object.assign(form, blank);
    form.clearErrors();
    showForm.value = true;
};

const openEdit = (row) => {
    editing.value = row;
    Object.assign(form, {
        name: row.name,
        type: row.type,
        length: row.length ?? '',
        is_nullable: row.is_nullable,
        default_value: row.default_value ?? '',
        is_unique: row.is_unique,
        is_index: row.is_index,
        foreign_table: row.foreign_table ?? '',
        comment: row.comment ?? '',
    });
    form.clearErrors();
    showForm.value = true;
};

const submit = () => {
    const options = { preserveScroll: true, onSuccess: () => (showForm.value = false) };
    if (editing.value) form.put(`/schema-drafts/${props.draft.id}/columns/${editing.value.id}`, options);
    else form.post(`/schema-drafts/${props.draft.id}/columns`, options);
};

const removeForm = useForm({});
const confirmDelete = () =>
    removeForm.delete(`/schema-drafts/${props.draft.id}/columns/${deleting.value.id}`, {
        preserveScroll: true,
        onFinish: () => (deleting.value = null),
    });

const importForm = useForm({});
const importColumns = () =>
    importForm.post(`/schema-drafts/${props.draft.id}/import`, { preserveScroll: true });

const typeOptions = computed(() => props.types.map((t) => ({ value: t, label: t })));
const tableChoices = computed(() => props.tableOptions.map((t) => ({ value: t, label: t })));

const STATUS = {
    baru: { label: 'Baru', class: 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400' },
    berubah: { label: 'Berubah', class: 'bg-amber-500/15 text-amber-600 dark:text-amber-400' },
    dihapus: { label: 'Dihapus', class: 'bg-destructive/15 text-destructive' },
    sama: { label: 'Sama', class: 'bg-muted text-muted-foreground' },
};

const counts = computed(() =>
    props.diff.reduce((acc, row) => ({ ...acc, [row.status]: (acc[row.status] ?? 0) + 1 }), {}),
);

const flags = (row) =>
    [
        row.is_nullable ? 'nullable' : null,
        row.is_unique ? 'unique' : null,
        row.is_index && !row.is_unique ? 'index' : null,
        row.foreign_table ? `→ ${row.foreign_table}` : null,
        row.default_value ? `default ${row.default_value}` : null,
    ].filter(Boolean);

const copyCode = () => {
    navigator.clipboard?.writeText(props.migration.code);
    notify.success('Kode migration disalin.');
};
</script>

<template>
    <Head :title="`Rancangan ${props.draft.table_name}`" />
    <AppLayout>
        <Card data-testid="schema-draft-page">
            <CardHeader class="flex flex-row flex-wrap items-center justify-between gap-2 space-y-0">
                <CardTitle class="flex min-w-0 items-center gap-2">
                    <Button
                        variant="ghost"
                        size="icon"
                        type="button"
                        data-testid="schema-draft-back"
                        @click="router.visit('/schema-drafts')"
                    >
                        <ArrowLeft class="size-4" />
                    </Button>
                    <span class="truncate">{{ props.draft.name }}</span>
                    <span class="truncate font-mono text-xs text-muted-foreground">{{ props.draft.table_name }}</span>
                </CardTitle>
                <div class="flex flex-wrap items-center gap-2">
                    <Badge :variant="props.draft.table_exists ? 'secondary' : 'outline'" class="font-medium">
                        {{ props.draft.table_exists ? 'Ada di database' : 'Belum dimigrasikan' }}
                    </Badge>
                    <Button
                        v-if="props.draft.table_exists"
                        variant="outline"
                        size="sm"
                        :disabled="importForm.processing"
                        data-testid="schema-draft-import"
                        @click="importColumns"
                    >
                        <Download class="size-4" /> Impor dari tabel
                    </Button>
                    <Button size="sm" data-testid="schema-draft-column-add" @click="openCreate">
                        <Plus class="size-4" /> Kolom
                    </Button>
                </div>
            </CardHeader>

            <CardContent class="space-y-3">
                <div class="flex flex-wrap items-center gap-2 border-b pb-2">
                    <button
                        v-for="t in TABS"
                        :key="t.id"
                        type="button"
                        class="inline-flex h-8 items-center gap-1.5 rounded-md px-3 text-xs font-medium transition-colors"
                        :class="
                            tab === t.id
                                ? 'bg-primary text-primary-foreground'
                                : 'text-muted-foreground hover:bg-accent hover:text-foreground'
                        "
                        :data-testid="`schema-draft-tab-${t.id}`"
                        @click="tab = t.id"
                    >
                        <component :is="t.icon" class="size-3.5" />{{ t.label }}
                    </button>
                    <div class="ml-auto flex flex-wrap items-center gap-1.5 text-[11px]">
                        <span class="font-mono text-muted-foreground">
                            {{ rows.length }} kolom
                            <template v-if="props.draft.with_id"> + id</template>
                            <template v-if="props.draft.with_timestamps"> + timestamps</template>
                            <template v-if="props.draft.with_soft_deletes"> + softDeletes</template>
                        </span>
                    </div>
                </div>

                <!-- Kolom: geser baris untuk mengubah urutan -->
                <div v-if="tab === 'columns'" class="space-y-2" data-testid="schema-draft-columns">
                    <p class="text-xs text-muted-foreground">
                        Geser ikon untuk mengubah urutan kolom. Urutan tersimpan otomatis.
                    </p>
                    <div v-if="!rows.length" class="rounded-md border border-dashed p-6 text-center text-xs text-muted-foreground">
                        Belum ada kolom. Tambahkan kolom atau impor dari tabel yang sudah ada.
                    </div>
                    <ul v-else class="space-y-1">
                        <li
                            v-for="(row, index) in rows"
                            :key="row.id"
                            class="flex items-center gap-2 rounded-md border bg-card px-2 py-1.5 transition-colors hover:bg-muted/40"
                            :class="dragIndex === index ? 'border-primary' : ''"
                            draggable="true"
                            :data-testid="`schema-draft-column-${row.name}`"
                            @dragstart="onDragStart(index, $event)"
                            @dragenter.prevent="onDragEnter(index)"
                            @dragover.prevent
                            @dragend="onDragEnd"
                        >
                            <GripVertical class="size-4 shrink-0 cursor-grab text-muted-foreground" />
                            <span class="w-6 shrink-0 text-center text-[11px] text-muted-foreground">{{ index + 1 }}</span>
                            <span class="min-w-0 flex-1 truncate font-mono text-xs font-medium">{{ row.name }}</span>
                            <span class="hidden shrink-0 font-mono text-xs text-muted-foreground sm:inline">
                                {{ row.type }}<template v-if="row.length">({{ row.length }})</template>
                            </span>
                            <span class="hidden flex-wrap items-center gap-1 md:flex">
                                <Badge v-for="flag in flags(row)" :key="flag" variant="outline" class="text-[10px] font-normal">
                                    {{ flag }}
                                </Badge>
                            </span>
                            <div class="ml-auto flex shrink-0 items-center gap-1">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    :data-testid="`schema-draft-column-edit-${row.name}`"
                                    @click="openEdit(row)"
                                >
                                    <Pencil class="size-3.5" />
                                </Button>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="text-destructive hover:text-destructive"
                                    :data-testid="`schema-draft-column-delete-${row.name}`"
                                    @click="deleting = row"
                                >
                                    <Trash2 class="size-3.5" />
                                </Button>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Diff terhadap skema nyata -->
                <div v-else-if="tab === 'diff'" class="space-y-2" data-testid="schema-draft-diff">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span
                            v-for="(meta, key) in STATUS"
                            :key="key"
                            class="rounded-md px-2 py-0.5 text-[11px] font-medium"
                            :class="meta.class"
                        >
                            {{ meta.label }}: {{ counts[key] ?? 0 }}
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="border-b text-[11px] uppercase tracking-wide text-muted-foreground">
                                    <th class="py-1.5 pr-3 text-left font-medium">Kolom</th>
                                    <th class="py-1.5 pr-3 text-left font-medium">Status</th>
                                    <th class="hidden py-1.5 pr-3 text-left font-medium sm:table-cell">Database</th>
                                    <th class="py-1.5 pr-3 text-left font-medium">Rancangan</th>
                                    <th class="hidden py-1.5 text-left font-medium md:table-cell">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="row in props.diff"
                                    :key="row.name"
                                    class="border-b border-border/60 last:border-0"
                                    :data-testid="`schema-draft-diff-${row.name}`"
                                >
                                    <td class="py-1.5 pr-3 font-mono font-medium">{{ row.name }}</td>
                                    <td class="py-1.5 pr-3">
                                        <span
                                            class="rounded-md px-2 py-0.5 text-[11px] font-medium"
                                            :class="STATUS[row.status].class"
                                        >
                                            {{ STATUS[row.status].label }}
                                        </span>
                                    </td>
                                    <td class="hidden py-1.5 pr-3 font-mono text-muted-foreground sm:table-cell">
                                        {{ row.actual ?? '—' }}
                                    </td>
                                    <td class="py-1.5 pr-3 font-mono">{{ row.draft ?? '—' }}</td>
                                    <td class="hidden py-1.5 text-muted-foreground md:table-cell">{{ row.note || '—' }}</td>
                                </tr>
                                <tr v-if="!props.diff.length">
                                    <td colspan="5" class="py-4 text-center text-muted-foreground">
                                        Belum ada kolom untuk dibandingkan.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pratinjau migration -->
                <div v-else class="space-y-2">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="font-mono text-xs text-muted-foreground">
                            database/migrations/{{ props.migration.file }}
                        </span>
                        <Button variant="outline" size="sm" data-testid="schema-draft-copy" @click="copyCode">
                            <Copy class="size-4" /> Salin
                        </Button>
                    </div>
                    <pre
                        class="thin-scroll max-h-[60vh] overflow-auto rounded-md border bg-muted/40 p-3 text-xs leading-relaxed"
                        data-testid="schema-draft-migration"
                    >{{ props.migration.code }}</pre>
                    <p class="text-xs text-muted-foreground">
                        Pratinjau saja — modul ini tidak menulis file dan tidak menjalankan migration.
                    </p>
                </div>
            </CardContent>
        </Card>

        <Dialog :open="showForm" :title="editing ? 'Ubah Kolom' : 'Tambah Kolom'" class="max-w-xl" @update:open="showForm = $event">
            <form class="form-dense grid gap-[var(--field-gap)] sm:grid-cols-2" novalidate @submit.prevent="submit">
                <div class="space-y-[var(--item-gap)]">
                    <Label for="c-name">Nama Kolom <span class="text-destructive">*</span></Label>
                    <Input id="c-name" v-model="form.name" class="font-mono" data-testid="schema-column-name" />
                    <p v-if="form.errors.name" class="text-xs font-medium text-destructive">{{ form.errors.name }}</p>
                </div>
                <div class="space-y-[var(--item-gap)]">
                    <Label>Tipe <span class="text-destructive">*</span></Label>
                    <Combobox v-model="form.type" :options="typeOptions" data-testid="schema-column-type" />
                    <p v-if="form.errors.type" class="text-xs font-medium text-destructive">{{ form.errors.type }}</p>
                </div>
                <div class="space-y-[var(--item-gap)]">
                    <Label for="c-length">Panjang / Presisi</Label>
                    <Input id="c-length" v-model="form.length" placeholder="(Opsional)" data-testid="schema-column-length" />
                </div>
                <div class="space-y-[var(--item-gap)]">
                    <Label for="c-default">Nilai Bawaan</Label>
                    <Input id="c-default" v-model="form.default_value" placeholder="(Opsional)" data-testid="schema-column-default" />
                </div>
                <div v-if="form.type === 'foreignId'" class="space-y-[var(--item-gap)] sm:col-span-2">
                    <Label>Relasi ke Tabel</Label>
                    <Combobox
                        v-model="form.foreign_table"
                        :options="tableChoices"
                        placeholder="-- Pilih --"
                        data-testid="schema-column-foreign"
                    />
                </div>
                <div class="space-y-[var(--item-gap)] sm:col-span-2">
                    <Label for="c-comment">Komentar</Label>
                    <Input id="c-comment" v-model="form.comment" placeholder="(Opsional)" data-testid="schema-column-comment" />
                </div>
                <div class="grid gap-2 sm:col-span-2 sm:grid-cols-3">
                    <label class="flex items-center justify-between gap-2 rounded-md border px-3 py-2 text-xs">
                        <span class="font-mono">nullable</span>
                        <Switch v-model="form.is_nullable" data-testid="schema-column-nullable" />
                    </label>
                    <label class="flex items-center justify-between gap-2 rounded-md border px-3 py-2 text-xs">
                        <span class="font-mono">unique</span>
                        <Switch v-model="form.is_unique" data-testid="schema-column-unique" />
                    </label>
                    <label class="flex items-center justify-between gap-2 rounded-md border px-3 py-2 text-xs">
                        <span class="font-mono">index</span>
                        <Switch v-model="form.is_index" data-testid="schema-column-index" />
                    </label>
                </div>
            </form>

            <template #footer>
                <FormActions
                    cancel-testid="schema-column-cancel"
                    submit-testid="schema-column-save"
                    :processing="form.processing"
                    @cancel="showForm = false"
                    @submit="submit"
                />
            </template>
        </Dialog>

        <ConfirmDeleteDialog
            :open="Boolean(deleting)"
            title="Hapus kolom rancangan?"
            description="Kolom dihapus dari rancangan. Skema database tidak terpengaruh."
            :processing="removeForm.processing"
            @update:open="deleting = null"
            @confirm="confirmDelete"
        />
    </AppLayout>
</template>
