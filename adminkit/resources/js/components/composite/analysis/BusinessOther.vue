<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

import FieldMoney from '@/components/composite/analysis/FieldMoney.vue';
import FieldStat from '@/components/composite/analysis/FieldStat.vue';
import ItemRows from '@/components/composite/analysis/ItemRows.vue';
import FormActions from '@/components/composite/FormActions.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
import Combobox from '@/components/ui/Combobox.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import Textarea from '@/components/ui/Textarea.vue';
import { rupiah } from '@/constants/committee';
import { otherMetrics } from '@/constants/analysisMath';

/** Usaha lainnya — satu lembar: identitas, bahan baku, rincian keuangan. */
const props = defineProps({
    url: { type: String, required: true },
    business: { type: Object, required: true },
    options: { type: Object, required: true },
    application: { type: Object, required: true },
});

const rowsOf = (group) =>
    props.business.items
        .filter((i) => i.group === group)
        .map((i) => ({ group, name: i.name, qty: i.qty, price: i.price }));

const form = useForm({
    name: props.business.name,
    business_kind: props.business.business_kind ?? '',
    business_length: props.business.business_length ?? '',
    address: props.business.address ?? '',
    projection_addition: props.business.projection_addition,
    groups: ['MATERIAL', 'INCOME', 'EXPENSE'],
    // Tiga kelompok baris disimpan di dalam form agar "Kembalikan" ikut memulihkannya.
    items_material: rowsOf('MATERIAL'),
    items_income: rowsOf('INCOME'),
    items_expense: rowsOf('EXPENSE'),
});

const live = computed(() =>
    otherMetrics(form, form.items_material, form.items_income, form.items_expense),
);

const rowTotal = (row) => Number(row.qty || 0) * Number(row.price || 0);
const opts = (list) => list.map((v) => ({ value: v, label: v }));

const submit = () =>
    form
        .transform(({ items_material: material, items_income: income, items_expense: expense, ...rest }) => ({
            ...rest,
            items: [...material, ...income, ...expense],
        }))
        .put(props.url, { preserveScroll: true });
</script>

<template>
    <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_22rem]">
        <div class="min-w-0 space-y-4">
            <Card data-testid="other-identity">
                <CardHeader><CardTitle>Identitas Usaha</CardTitle></CardHeader>
                <CardContent class="form-dense grid gap-3 sm:grid-cols-2">
                    <FieldStat label="Kode Usaha" :value="props.business.code" />
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="other-name">Nama Usaha <span class="text-destructive">*</span></Label>
                        <Input id="other-name" v-model="form.name" maxlength="150" data-testid="other-name" />
                        <p v-if="form.errors.name" class="text-xs font-medium text-destructive">
                            {{ form.errors.name }}
                        </p>
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="other-kind">Jenis Usaha</Label>
                        <Combobox
                            id="other-kind"
                            v-model="form.business_kind"
                            :options="opts(props.options.kinds)"
                            placeholder="(Opsional)"
                            data-testid="other-kind"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="other-length">Lama Usaha</Label>
                        <Combobox
                            id="other-length"
                            v-model="form.business_length"
                            :options="opts(props.options.lengths)"
                            placeholder="(Opsional)"
                            data-testid="other-length"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)] sm:col-span-2">
                        <Label for="other-address">Alamat Usaha</Label>
                        <Textarea
                            id="other-address"
                            v-model="form.address"
                            rows="2"
                            maxlength="255"
                            placeholder="(Opsional)"
                            data-testid="other-address"
                        />
                    </div>
                </CardContent>
            </Card>

            <Card data-testid="other-materials">
                <CardHeader><CardTitle>Bahan Baku</CardTitle></CardHeader>
                <CardContent class="form-dense">
                    <ItemRows
                        :rows="form.items_material"
                        :columns="[
                            { key: 'name', label: 'Bahan Baku', type: 'text' },
                            { key: 'qty', label: 'Jumlah', type: 'money', width: '130px' },
                            { key: 'price', label: 'Harga', type: 'money', width: '170px' },
                        ]"
                        :derived="[{ label: 'Total', format: (row) => rupiah(rowTotal(row)) }]"
                        :blank="{ group: 'MATERIAL', name: '', qty: 0, price: 0 }"
                        :totals="[{ label: 'Biaya Bahan Baku', value: rupiah(live.material_cost) }]"
                        add-label="Tambah Bahan Baku"
                        empty-text="Belum ada bahan baku."
                        testid="other-material-rows"
                    />
                </CardContent>
            </Card>

            <Card data-testid="other-finance">
                <CardHeader><CardTitle>Keuangan</CardTitle></CardHeader>
                <CardContent class="form-dense space-y-4">
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Rincian Pendapatan</Label>
                        <ItemRows
                            :rows="form.items_income"
                            :columns="[
                                { key: 'name', label: 'Nama Pendapatan', type: 'text' },
                                { key: 'price', label: 'Nominal', type: 'money', width: '190px' },
                            ]"
                            :blank="{ group: 'INCOME', name: '', qty: 0, price: 0 }"
                            :totals="[{ label: 'Pendapatan Usaha', value: rupiah(live.business_income) }]"
                            add-label="Tambah Pendapatan"
                            empty-text="Belum ada rincian pendapatan."
                            testid="other-income-rows"
                        />
                    </div>

                    <div class="space-y-[var(--item-gap)]">
                        <Label>Rincian Pengeluaran</Label>
                        <ItemRows
                            :rows="form.items_expense"
                            :columns="[
                                { key: 'name', label: 'Pengeluaran Untuk', type: 'text' },
                                { key: 'price', label: 'Nominal', type: 'money', width: '190px' },
                            ]"
                            :blank="{ group: 'EXPENSE', name: '', qty: 0, price: 0 }"
                            :totals="[{ label: 'Biaya Operasional', value: rupiah(live.operational_cost) }]"
                            add-label="Tambah Pengeluaran"
                            empty-text="Belum ada rincian pengeluaran."
                            testid="other-expense-rows"
                        />
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <FieldMoney
                            v-model="form.projection_addition"
                            label="Proyeksi Penambahan"
                            testid="other-projection"
                            :error="form.errors.projection_addition"
                        />
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="space-y-3 xl:sticky xl:top-20 xl:self-start">
            <Card data-testid="other-summary">
                <CardHeader><CardTitle>Ringkasan Perhitungan</CardTitle></CardHeader>
                <CardContent class="space-y-3">
                    <FieldStat label="Pendapatan Usaha" :value="rupiah(live.business_income)" />
                    <FieldStat label="Biaya Operasional" :value="rupiah(live.operational_cost)" />
                    <FieldStat label="Biaya Bahan Baku" :value="rupiah(live.material_cost)" />
                    <FieldStat
                        label="Hasil Usaha Bersih"
                        :value="rupiah(live.net_profit)"
                        strong
                        testid="other-net-profit"
                    />
                    <p class="text-xs text-muted-foreground">
                        Hasil usaha bersih = pendapatan usaha − biaya operasional − biaya bahan baku + proyeksi
                        penambahan. Angka ini dipakai bagian Analisa Keuangan.
                    </p>
                </CardContent>
            </Card>

            <div class="sticky bottom-3 rounded-md border bg-background/95 p-2 shadow-sm backdrop-blur">
                <FormActions
                    cancel-testid="other-reset"
                    cancel-label="Kembalikan"
                    submit-testid="other-save"
                    submit-label="Simpan Semua"
                    :processing="form.processing"
                    :disabled="!form.name"
                    @cancel="form.reset()"
                    @submit="submit"
                />
            </div>
        </div>
    </div>
</template>
