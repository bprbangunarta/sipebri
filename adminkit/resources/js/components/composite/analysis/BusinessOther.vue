<script setup>
import { computed, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

import BusinessTabs from '@/components/composite/analysis/BusinessTabs.vue';
import FieldMoney from '@/components/composite/analysis/FieldMoney.vue';
import FieldStat from '@/components/composite/analysis/FieldStat.vue';
import ItemRows from '@/components/composite/analysis/ItemRows.vue';
import FormActions from '@/components/composite/FormActions.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardFooter from '@/components/ui/CardFooter.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
import Combobox from '@/components/ui/Combobox.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import Textarea from '@/components/ui/Textarea.vue';
import { rupiah } from '@/constants/committee';
import { otherMetrics } from '@/constants/analysisMath';

/** Usaha lainnya: identitas, bahan baku, rincian pendapatan & pengeluaran. */
const props = defineProps({
    url: { type: String, required: true },
    business: { type: Object, required: true },
    options: { type: Object, required: true },
    application: { type: Object, required: true },
});

const tabs = [
    { key: 'identitas', label: 'Identitas' },
    { key: 'bahan', label: 'Bahan Baku' },
    { key: 'keuangan', label: 'Keuangan' },
];
const tab = ref('identitas');

const rowsOf = (group) =>
    props.business.items
        .filter((i) => i.group === group)
        .map((i) => ({ group, name: i.name, qty: i.qty, price: i.price }));

const identity = useForm({
    name: props.business.name,
    business_kind: props.business.business_kind ?? '',
    business_length: props.business.business_length ?? '',
    address: props.business.address ?? '',
});

const materials = useForm({ groups: ['MATERIAL'], items: rowsOf('MATERIAL') });

const incomes = ref(rowsOf('INCOME'));
const expenses = ref(rowsOf('EXPENSE'));
const finance = useForm({
    groups: ['INCOME', 'EXPENSE'],
    items: [],
    projection_addition: props.business.projection_addition,
});

const live = computed(() =>
    otherMetrics(finance, materials.items, incomes.value, expenses.value),
);

const rowTotal = (row) => Number(row.qty || 0) * Number(row.price || 0);
const opts = (list) => list.map((v) => ({ value: v, label: v }));
const save = (form) => form.put(props.url, { preserveScroll: true });

const saveFinance = () => {
    finance.items = [...incomes.value, ...expenses.value];
    save(finance);
};
</script>

<template>
    <div class="space-y-3">
        <BusinessTabs :tabs="tabs" :active="tab" @select="tab = $event" />

        <Card v-if="tab === 'identitas'" data-testid="other-identity">
            <CardHeader><CardTitle>Identitas Usaha</CardTitle></CardHeader>
            <CardContent class="form-dense grid gap-3 sm:grid-cols-2">
                <FieldStat label="Kode Usaha" :value="props.business.code" />
                <div class="space-y-[var(--item-gap)]">
                    <Label for="other-name">Nama Usaha <span class="text-destructive">*</span></Label>
                    <Input id="other-name" v-model="identity.name" maxlength="150" data-testid="other-name" />
                    <p v-if="identity.errors.name" class="text-xs font-medium text-destructive">
                        {{ identity.errors.name }}
                    </p>
                </div>
                <div class="space-y-[var(--item-gap)]">
                    <Label for="other-kind">Jenis Usaha</Label>
                    <Combobox
                        id="other-kind"
                        v-model="identity.business_kind"
                        :options="opts(props.options.kinds)"
                        placeholder="(Opsional)"
                        data-testid="other-kind"
                    />
                </div>
                <div class="space-y-[var(--item-gap)]">
                    <Label for="other-length">Lama Usaha</Label>
                    <Combobox
                        id="other-length"
                        v-model="identity.business_length"
                        :options="opts(props.options.lengths)"
                        placeholder="(Opsional)"
                        data-testid="other-length"
                    />
                </div>
                <div class="space-y-[var(--item-gap)] sm:col-span-2">
                    <Label for="other-address">Alamat Usaha</Label>
                    <Textarea
                        id="other-address"
                        v-model="identity.address"
                        rows="2"
                        maxlength="255"
                        placeholder="(Opsional)"
                        data-testid="other-address"
                    />
                </div>
            </CardContent>
            <CardFooter>
                <FormActions
                    cancel-testid="other-identity-reset"
                    cancel-label="Kembalikan"
                    submit-testid="other-identity-save"
                    :processing="identity.processing"
                    :disabled="!identity.name"
                    @cancel="identity.reset()"
                    @submit="save(identity)"
                />
            </CardFooter>
        </Card>

        <Card v-else-if="tab === 'bahan'" data-testid="other-materials">
            <CardHeader><CardTitle>Bahan Baku</CardTitle></CardHeader>
            <CardContent class="form-dense space-y-4">
                <ItemRows
                    :rows="materials.items"
                    :columns="[
                        { key: 'name', label: 'Bahan Baku', type: 'text' },
                        { key: 'qty', label: 'Jumlah', type: 'money', width: '140px' },
                        { key: 'price', label: 'Harga', type: 'money', width: '180px' },
                    ]"
                    :derived="[{ label: 'Total', format: (row) => rupiah(rowTotal(row)) }]"
                    :blank="{ group: 'MATERIAL', name: '', qty: 0, price: 0 }"
                    :totals="[{ label: 'Biaya Bahan Baku', value: rupiah(live.material_cost) }]"
                    add-label="Tambah Bahan Baku"
                    empty-text="Belum ada bahan baku."
                    testid="other-material-rows"
                />
            </CardContent>
            <CardFooter>
                <FormActions
                    cancel-testid="other-materials-reset"
                    cancel-label="Kembalikan"
                    submit-testid="other-materials-save"
                    :processing="materials.processing"
                    @cancel="materials.reset()"
                    @submit="save(materials)"
                />
            </CardFooter>
        </Card>

        <Card v-else data-testid="other-finance">
            <CardHeader><CardTitle>Keuangan</CardTitle></CardHeader>
            <CardContent class="form-dense space-y-4">
                <div class="space-y-[var(--item-gap)]">
                    <Label>Rincian Pendapatan</Label>
                    <ItemRows
                        :rows="incomes"
                        :columns="[
                            { key: 'name', label: 'Nama Pendapatan', type: 'text' },
                            { key: 'price', label: 'Nominal', type: 'money', width: '200px' },
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
                        :rows="expenses"
                        :columns="[
                            { key: 'name', label: 'Pengeluaran Untuk', type: 'text' },
                            { key: 'price', label: 'Nominal', type: 'money', width: '200px' },
                        ]"
                        :blank="{ group: 'EXPENSE', name: '', qty: 0, price: 0 }"
                        :totals="[{ label: 'Biaya Operasional', value: rupiah(live.operational_cost) }]"
                        add-label="Tambah Pengeluaran"
                        empty-text="Belum ada rincian pengeluaran."
                        testid="other-expense-rows"
                    />
                </div>

                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                    <FieldMoney
                        v-model="finance.projection_addition"
                        label="Proyeksi Penambahan"
                        testid="other-projection"
                        :error="finance.errors.projection_addition"
                    />
                    <FieldStat label="Pendapatan Usaha" :value="rupiah(live.business_income)" />
                    <FieldStat label="Biaya Operasional" :value="rupiah(live.operational_cost)" />
                    <FieldStat label="Biaya Bahan Baku" :value="rupiah(live.material_cost)" />
                    <FieldStat
                        label="Hasil Usaha Bersih"
                        :value="rupiah(live.net_profit)"
                        strong
                        testid="other-net-profit"
                    />
                </div>
            </CardContent>
            <CardFooter>
                <FormActions
                    cancel-testid="other-finance-reset"
                    cancel-label="Kembalikan"
                    submit-testid="other-finance-save"
                    :processing="finance.processing"
                    @cancel="finance.reset()"
                    @submit="saveFinance"
                />
            </CardFooter>
        </Card>
    </div>
</template>
