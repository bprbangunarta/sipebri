<script setup>
import { computed, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

import BusinessTabs from '@/components/composite/analysis/BusinessTabs.vue';
import FieldMoney from '@/components/composite/analysis/FieldMoney.vue';
import FieldStat from '@/components/composite/analysis/FieldStat.vue';
import FormActions from '@/components/composite/FormActions.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardFooter from '@/components/ui/CardFooter.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
import Combobox from '@/components/ui/Combobox.vue';
import DecimalInput from '@/components/ui/DecimalInput.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import NumberInput from '@/components/ui/NumberInput.vue';
import Textarea from '@/components/ui/Textarea.vue';
import { rupiah } from '@/constants/committee';
import { farmMetrics, FARM_COSTS, HARVEST_MONTHS } from '@/constants/analysisMath';

/** Usaha pertanian: informasi lahan, biaya tanam, analisa keuangan per siklus panen. */
const props = defineProps({
    url: { type: String, required: true },
    business: { type: Object, required: true },
    options: { type: Object, required: true },
});

const tabs = [
    { key: 'informasi', label: 'Informasi' },
    { key: 'biaya', label: 'Biaya Pertanian' },
    { key: 'keuangan', label: 'Analisa Keuangan' },
];
const tab = ref('informasi');

const areas = [
    { key: 'area_own', label: 'Luas Milik Sendiri (m²)' },
    { key: 'area_rent', label: 'Luas Hasil Sewa (m²)' },
    { key: 'area_pawn', label: 'Luas Hasil Gadai (m²)' },
];

const info = useForm({
    name: props.business.name,
    address: props.business.address ?? '',
    economy_sector: props.business.economy_sector ?? '',
    plant_type: props.business.plant_type ?? '',
    area_own: props.business.area_own,
    area_rent: props.business.area_rent,
    area_pawn: props.business.area_pawn,
    harvest_kw: props.business.harvest_kw,
    price_per_kw: props.business.price_per_kw,
});

const costs = useForm(Object.fromEntries(FARM_COSTS.map((c) => [c.key, props.business[c.key]])));

const finance = useForm({
    take_portion: props.business.take_portion,
    addition_result: props.business.addition_result,
    other_bank_loan: props.business.other_bank_loan,
    principal_installment: props.business.principal_installment,
});

const live = computed(() => farmMetrics({ ...info.data(), ...costs.data(), ...finance.data() }));

const opts = (list) => list.map((v) => ({ value: v, label: v }));
const save = (form) => form.put(props.url, { preserveScroll: true });
</script>

<template>
    <div class="space-y-3">
        <BusinessTabs :tabs="tabs" :active="tab" @select="tab = $event" />

        <Card v-if="tab === 'informasi'" data-testid="farm-info">
            <CardHeader><CardTitle>Informasi Usaha</CardTitle></CardHeader>
            <CardContent class="form-dense grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                <FieldStat label="Kode Usaha" :value="props.business.code" />
                <div class="space-y-[var(--item-gap)]">
                    <Label for="farm-name">Nama Usaha <span class="text-destructive">*</span></Label>
                    <Input id="farm-name" v-model="info.name" maxlength="150" data-testid="farm-name" />
                    <p v-if="info.errors.name" class="text-xs font-medium text-destructive">{{ info.errors.name }}</p>
                </div>
                <div class="space-y-[var(--item-gap)]">
                    <Label for="farm-sector">Sektor Ekonomi</Label>
                    <Combobox
                        id="farm-sector"
                        v-model="info.economy_sector"
                        :options="opts(props.options.sectors)"
                        placeholder="(Opsional)"
                        data-testid="farm-sector"
                    />
                </div>
                <div class="space-y-[var(--item-gap)] sm:col-span-2">
                    <Label for="farm-address">Alamat Usaha</Label>
                    <Textarea
                        id="farm-address"
                        v-model="info.address"
                        rows="2"
                        maxlength="255"
                        placeholder="(Opsional)"
                        data-testid="farm-address"
                    />
                </div>
                <div class="space-y-[var(--item-gap)]">
                    <Label for="farm-plant">Jenis Tanaman</Label>
                    <Combobox
                        id="farm-plant"
                        v-model="info.plant_type"
                        :options="opts(props.options.plants)"
                        placeholder="(Opsional)"
                        data-testid="farm-plant"
                    />
                </div>
                <div v-for="area in areas" :key="area.key" class="space-y-[var(--item-gap)]">
                    <Label :for="`farm-${area.key}`">{{ area.label }}</Label>
                    <NumberInput
                        :id="`farm-${area.key}`"
                        v-model="info[area.key]"
                        class="text-right tabular-nums"
                        :data-testid="`farm-${area.key}`"
                    />
                </div>
                <FieldStat
                    label="Total Luas Tanah (m²)"
                    :value="new Intl.NumberFormat('id-ID').format(live.total_area)"
                    testid="farm-total-area"
                />
                <div class="space-y-[var(--item-gap)]">
                    <Label for="farm-harvest">Hasil Panen (Kwintal)</Label>
                    <DecimalInput
                        id="farm-harvest"
                        v-model="info.harvest_kw"
                        class="text-right tabular-nums"
                        data-testid="farm-harvest"
                    />
                </div>
                <FieldMoney
                    v-model="info.price_per_kw"
                    label="Harga Per Kwintal"
                    testid="farm-price"
                    :error="info.errors.price_per_kw"
                />
            </CardContent>
            <CardFooter>
                <FormActions
                    cancel-testid="farm-info-reset"
                    cancel-label="Kembalikan"
                    submit-testid="farm-info-save"
                    :processing="info.processing"
                    :disabled="!info.name"
                    @cancel="info.reset()"
                    @submit="save(info)"
                />
            </CardFooter>
        </Card>

        <Card v-else-if="tab === 'biaya'" data-testid="farm-costs">
            <CardHeader><CardTitle>Biaya Pertanian</CardTitle></CardHeader>
            <CardContent class="form-dense space-y-4">
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <FieldMoney
                        v-for="cost in FARM_COSTS"
                        :key="cost.key"
                        v-model="costs[cost.key]"
                        :label="cost.label"
                        :testid="`farm-${cost.key}`"
                        :error="costs.errors[cost.key]"
                    />
                </div>
                <FieldStat
                    label="Pengeluaran Biaya Usaha"
                    :value="rupiah(live.total_cost)"
                    strong
                    testid="farm-total-cost"
                />
            </CardContent>
            <CardFooter>
                <FormActions
                    cancel-testid="farm-costs-reset"
                    cancel-label="Kembalikan"
                    submit-testid="farm-costs-save"
                    :processing="costs.processing"
                    @cancel="costs.reset()"
                    @submit="save(costs)"
                />
            </CardFooter>
        </Card>

        <Card v-else data-testid="farm-finance">
            <CardHeader><CardTitle>Analisa Keuangan</CardTitle></CardHeader>
            <CardContent class="form-dense space-y-4">
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <FieldMoney
                        v-model="finance.take_portion"
                        label="Ambil 70% (Catatan)"
                        testid="farm-take-portion"
                        :error="finance.errors.take_portion"
                    />
                    <FieldMoney
                        v-model="finance.addition_result"
                        label="Penambahan Hasil Usaha"
                        testid="farm-addition"
                        :error="finance.errors.addition_result"
                    />
                    <FieldMoney
                        v-model="finance.other_bank_loan"
                        label="Pinjaman Bank Lain"
                        testid="farm-other-bank"
                        :error="finance.errors.other_bank_loan"
                    />
                    <FieldMoney
                        v-model="finance.principal_installment"
                        label="Angsuran Pokok"
                        testid="farm-installment"
                        :error="finance.errors.principal_installment"
                    />
                </div>

                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <FieldStat label="Pendapatan Hasil Panen" :value="rupiah(live.harvest_income)" />
                    <FieldStat label="Pengeluaran Biaya Usaha" :value="rupiah(live.total_cost)" />
                    <FieldStat label="Hasil Bersih Usaha" :value="rupiah(live.net_profit)" testid="farm-net-profit" />
                    <FieldStat
                        label="Pendapatan Perbulan"
                        :value="rupiah(live.monthly_income)"
                        strong
                        testid="farm-monthly"
                    />
                </div>

                <p class="text-xs text-muted-foreground">
                    Pendapatan per bulan = (hasil bersih usaha + penambahan − angsuran pokok − pinjaman bank lain)
                    dibagi {{ HARVEST_MONTHS }} bulan siklus panen. Angka inilah yang dipakai bagian Analisa
                    Keuangan. Kolom "Ambil 70%" hanya catatan, tidak masuk perhitungan.
                </p>
            </CardContent>
            <CardFooter>
                <FormActions
                    cancel-testid="farm-finance-reset"
                    cancel-label="Kembalikan"
                    submit-testid="farm-finance-save"
                    :processing="finance.processing"
                    @cancel="finance.reset()"
                    @submit="save(finance)"
                />
            </CardFooter>
        </Card>
    </div>
</template>
