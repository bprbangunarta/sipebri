<script setup>
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { ChevronRight, Plus, Trash2 } from 'lucide-vue-next';

import ConfirmDeleteDialog from '@/components/composite/ConfirmDeleteDialog.vue';
import FormActions from '@/components/composite/FormActions.vue';
import Button from '@/components/ui/Button.vue';
import Dialog from '@/components/ui/Dialog.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import { rupiah } from '@/constants/committee';

/** Daftar usaha pemohon untuk satu tipe usaha. */
const props = defineProps({
    applicationId: { type: Number, required: true },
    type: { type: String, required: true },
    typeLabel: { type: String, required: true },
    businesses: { type: Array, default: () => [] },
});

const base = `/analysis-simulation/${props.applicationId}/businesses`;

const showAdd = ref(false);
const addForm = useForm({ type: props.type, name: '' });

const submitAdd = () => {
    addForm.type = props.type;
    addForm.post(base, { onSuccess: () => (showAdd.value = false) });
};

const target = ref(null);
const deleteForm = useForm({});
const confirmDelete = () =>
    deleteForm.delete(`${base}/${target.value.id}`, { onFinish: () => (target.value = null) });
</script>

<template>
    <div class="space-y-3" data-testid="business-list">
        <div class="overflow-x-auto rounded-md border">
            <table class="w-full text-sm">
                <thead class="bg-muted/40">
                    <tr>
                        <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wider text-muted-foreground">
                            Nama Usaha
                        </th>
                        <th class="px-3 py-2 text-right text-[11px] font-medium uppercase tracking-wider text-muted-foreground">
                            Pendapatan
                        </th>
                        <th class="px-3 py-2 text-right text-[11px] font-medium uppercase tracking-wider text-muted-foreground">
                            Pengeluaran
                        </th>
                        <th class="px-3 py-2 text-right text-[11px] font-medium uppercase tracking-wider text-muted-foreground">
                            Laba Bersih
                        </th>
                        <th class="w-28 px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/60">
                    <tr
                        v-for="row in props.businesses"
                        :key="row.id"
                        class="cursor-pointer transition-colors hover:bg-muted/40"
                        :data-testid="`business-row-${row.id}`"
                        @click="router.visit(`${base}/${row.id}`)"
                    >
                        <td class="px-3 py-2">
                            <span class="block font-medium">{{ row.name }}</span>
                            <span class="block font-mono text-xs text-muted-foreground">{{ row.code }}</span>
                        </td>
                        <td class="whitespace-nowrap px-3 py-2 text-right tabular-nums">{{ rupiah(row.revenue) }}</td>
                        <td class="whitespace-nowrap px-3 py-2 text-right tabular-nums">{{ rupiah(row.expense) }}</td>
                        <td class="whitespace-nowrap px-3 py-2 text-right font-semibold tabular-nums">
                            {{ rupiah(row.net_profit) }}
                        </td>
                        <td class="px-3 py-2 text-right" @click.stop>
                            <div class="flex items-center justify-end gap-1">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="text-destructive hover:text-destructive"
                                    :data-testid="`business-delete-${row.id}`"
                                    @click="target = row"
                                >
                                    <Trash2 class="size-4" />
                                </Button>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    :data-testid="`business-open-${row.id}`"
                                    @click="router.visit(`${base}/${row.id}`)"
                                >
                                    Buka <ChevronRight class="size-4" />
                                </Button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!props.businesses.length">
                        <td colspan="5" class="px-3 py-8 text-center text-sm text-muted-foreground">
                            Belum ada {{ props.typeLabel.toLowerCase() }} yang dicatat.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Button size="sm" data-testid="business-add" @click="showAdd = true">
            <Plus class="size-4" /> Tambah {{ props.typeLabel }}
        </Button>

        <Dialog :open="showAdd" :title="`Tambah ${props.typeLabel}`" @update:open="showAdd = $event">
            <div class="form-dense space-y-[var(--item-gap)]">
                <Label for="business-name">Nama Usaha <span class="text-destructive">*</span></Label>
                <Input id="business-name" v-model="addForm.name" maxlength="150" data-testid="business-name" />
                <p v-if="addForm.errors.name" class="text-xs font-medium text-destructive">
                    {{ addForm.errors.name }}
                </p>
                <p v-else class="text-xs text-muted-foreground">
                    Kode usaha dibuat otomatis oleh sistem setelah disimpan.
                </p>
            </div>

            <template #footer>
                <FormActions
                    cancel-testid="business-add-cancel"
                    submit-testid="business-add-submit"
                    submit-label="Simpan &amp; Lanjut"
                    :processing="addForm.processing"
                    :disabled="!addForm.name"
                    @cancel="showAdd = false"
                    @submit="submitAdd"
                />
            </template>
        </Dialog>

        <ConfirmDeleteDialog
            :open="!!target"
            title="Hapus Usaha?"
            :description="`Usaha ${target?.name ?? ''} beserta rinciannya akan dihapus permanen.`"
            :processing="deleteForm.processing"
            @update:open="target = null"
            @confirm="confirmDelete"
        />
    </div>
</template>
