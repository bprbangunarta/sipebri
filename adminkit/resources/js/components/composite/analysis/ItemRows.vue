<script setup>
import { Plus, Trash2 } from 'lucide-vue-next';

import Button from '@/components/ui/Button.vue';
import Input from '@/components/ui/Input.vue';
import NumberInput from '@/components/ui/NumberInput.vue';

/**
 * Tabel baris rincian yang bisa diedit langsung (barang dagang, bahan baku,
 * rincian pendapatan/pengeluaran, kewajiban, harta lain).
 * `rows` diubah di tempat lalu disimpan oleh halaman pemanggil.
 */
const props = defineProps({
    rows: { type: Array, required: true },
    columns: { type: Array, required: true },
    derived: { type: Array, default: () => [] },
    totals: { type: Array, default: () => [] },
    blank: { type: Object, required: true },
    addLabel: { type: String, default: 'Tambah Baris' },
    emptyText: { type: String, default: 'Belum ada baris.' },
    testid: { type: String, required: true },
});

const add = () => props.rows.push({ ...props.blank });
const remove = (index) => props.rows.splice(index, 1);
</script>

<template>
    <div class="space-y-2" :data-testid="props.testid">
        <div class="overflow-x-auto rounded-md border">
            <table class="w-full text-sm">
                <thead class="bg-muted/40">
                    <tr>
                        <th
                            v-for="col in props.columns"
                            :key="col.key"
                            class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wider text-muted-foreground"
                            :style="col.width ? { width: col.width } : undefined"
                        >
                            {{ col.label }}
                        </th>
                        <th
                            v-for="col in props.derived"
                            :key="col.label"
                            class="whitespace-nowrap px-3 py-2 text-right text-[11px] font-medium uppercase tracking-wider text-muted-foreground"
                        >
                            {{ col.label }}
                        </th>
                        <th class="w-12 px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/60">
                    <tr v-for="(row, index) in props.rows" :key="index">
                        <td v-for="col in props.columns" :key="col.key" class="px-2 py-1.5">
                            <Input
                                v-if="col.type === 'text'"
                                v-model="row[col.key]"
                                maxlength="150"
                                :data-testid="`${props.testid}-${col.key}-${index}`"
                            />
                            <NumberInput
                                v-else
                                v-model="row[col.key]"
                                class="text-right tabular-nums"
                                :data-testid="`${props.testid}-${col.key}-${index}`"
                            />
                        </td>
                        <td
                            v-for="col in props.derived"
                            :key="col.label"
                            class="whitespace-nowrap px-3 py-1.5 text-right tabular-nums"
                        >
                            {{ col.format(row) }}
                        </td>
                        <td class="px-2 py-1.5 text-right">
                            <Button
                                variant="ghost"
                                size="icon"
                                class="text-destructive hover:text-destructive"
                                :data-testid="`${props.testid}-remove-${index}`"
                                @click="remove(index)"
                            >
                                <Trash2 class="size-4" />
                            </Button>
                        </td>
                    </tr>
                    <tr v-if="!props.rows.length">
                        <td
                            :colspan="props.columns.length + props.derived.length + 1"
                            class="px-3 py-6 text-center text-sm text-muted-foreground"
                        >
                            {{ props.emptyText }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-2">
            <Button variant="outline" size="sm" :data-testid="`${props.testid}-add`" @click="add">
                <Plus class="size-4" /> {{ props.addLabel }}
            </Button>
            <div v-if="props.totals.length" class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs">
                <span v-for="total in props.totals" :key="total.label" class="text-muted-foreground">
                    {{ total.label }}:
                    <span class="font-semibold tabular-nums text-foreground">{{ total.value }}</span>
                </span>
            </div>
        </div>
    </div>
</template>
