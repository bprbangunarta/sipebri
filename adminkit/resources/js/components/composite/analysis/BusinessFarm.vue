<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

import FieldMoney from '@/components/composite/analysis/FieldMoney.vue';
import FieldStat from '@/components/composite/analysis/FieldStat.vue';
import FormActions from '@/components/composite/FormActions.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
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

/** Usaha pertanian — satu lembar: informasi lahan, biaya tanam, ringkasan keuangan. */
const props = defineProps({
    url: { type: String, required: true },
    business: { type: Object, required: true },
    options: { type: Object, required: true },
    application: { type: Object, required: true },
});

const areas = [
    { key: 'area_own', label: 'Luas Milik Sendiri (m²)' },
    { key: 'area_rent', label: 'Luas Hasil Sewa (m²)' },
    { key: 'area_pawn', label: 'Luas Hasil Gadai (m²)' },
];

const form = useForm({
    name: props.business.name,
    address: props.business.address ?? '',
    economy_sector: props.business.economy_sector ?? '',
    plant_type: props.business.plant_type ?? '',
    area_own: props.business.area_own,
    area_rent: props.business.area_rent,
    area_pawn: props.business.area_pawn,
    harvest_kw: props.business.harvest_kw,
    price_per_kw: props.business.price_per_kw,
    addition_result: props.business.addition_result,
    ...Object.fromEntries(FARM_COSTS.map((c) => [c.key, props.business[c.key]])),
});

const live = computed(() => farmMetrics(form, props.application));
const angka = (value) => new Intl.NumberFormat('id-ID').format(value ?? 0);

const opts = (list) => list.map((v) => ({ value: v, label: v }));
const submit = () => form.put(props.url, { preserveScroll: true });
</script>

<template>
    <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_22rem]">
        <div class="space-y-4">
            <Card data-testid="farm-info">
                <CardHeader><CardTitle>Informasi Usaha</CardTitle></CardHeader>
                <CardContent class="form-dense grid gap-3 sm:grid-cols-2">
                    <FieldStat label="Kode Usaha" :value="props.business.code" />
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="farm-name">Nama Usaha <span class="text-destructive">*</span></Label>
                        <Input id="farm-name" v-model="form.name" maxlength="150" data-testid="farm-name" />
                        <p v-if="form.errors.name" class="text-xs font-medium text-destructive">
                            {{ form.errors.name }}
                        </p>
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="farm-sector">Sektor Ekonomi</Label>
                        <Combobox
                            id="farm-sector"
                            v-model="form.economy_sector"
                            :options="opts(props.options.sectors)"
                            placeholder="(Opsional)"
                            data-testid="farm-sector"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="farm-plant">Jenis Tanaman</Label>
                        <Combobox
                            id="farm-plant"
                            v-model="form.plant_type"
                            :options="opts(props.options.plants)"
                            placeholder="(Opsional)"
                            data-testid="farm-plant"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)] sm:col-span-2">
                        <Label for="farm-address">Alamat Usaha</Label>
                        <Textarea
                            id="farm-address"
                            v-model="form.address"
                            rows="2"
                            maxlength="255"
                            placeholder="(Opsional)"
                            data-testid="farm-address"
                        />
                    </div>

                    <div v-for="area in areas" :key="area.key" class="space-y-[var(--item-gap)]">
                        <Label :for="`farm-${area.key}`">{{ area.label }}</Label>
                        <NumberInput
                            :id="`farm-${area.key}`"
                            v-model="form[area.key]"
                            class="text-right tabular-nums"
                            :data-testid="`farm-${area.key}`"
                        />
                    </div>
                    <FieldStat
                        label="Total Luas Tanah (m²)"
                        :value="angka(live.total_area)"
                        testid="farm-total-area"
                    />

                    <div class="space-y-[var(--item-gap)]">
                        <Label for="farm-harvest">Hasil Panen (Kwintal)</Label>
                        <DecimalInput
                            id="farm-harvest"
                            v-model="form.harvest_kw"
                            class="text-right tabular-nums"
                            data-testid="farm-harvest"
                        />
                    </div>
                    <FieldMoney
                        v-model="form.price_per_kw"
                        label="Harga Per Kwintal"
                        testid="farm-price"
                        :error="form.errors.price_per_kw"
                    />
                </CardContent>
            </Card>

            <Card data-testid="farm-costs">
                <CardHeader><CardTitle>Biaya Pertanian</CardTitle></CardHeader>
                <CardContent class="form-dense grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <FieldMoney
                        v-for="cost in FARM_COSTS"
                        :key="cost.key"
                        v-model="form[cost.key]"
                        :label="cost.label"
                        :disabled="cost.key === 'cost_amortization'"
                        :testid="`farm-${cost.key}`"
                        :error="form.errors[cost.key]"
                    />
                </CardContent>
            </Card>

            <Card data-testid="farm-finance">
                <CardHeader><CardTitle>Analisa Keuangan</CardTitle></CardHeader>
                <CardContent class="form-dense grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <FieldMoney
                        v-model="form.addition_result"
                        label="Penambahan Hasil Usaha"
                        testid="farm-addition"
                        :error="form.errors.addition_result"
                    />
                    <FieldStat label="Ambil 70%" :value="rupiah(props.business.take_portion)" />
                    <FieldStat
                        label="Pinjaman Bank Lain"
                        :value="rupiah(live.other_bank_loan)"
                        testid="farm-other-bank"
                    />
                    <FieldStat
                        label="Angsuran Pokok"
                        :value="rupiah(live.principal_installment)"
                        testid="farm-installment"
                    />
                </CardContent>
            </Card>
        </div>

        <div class="space-y-3 xl:sticky xl:top-20 xl:self-start">
            <Card data-testid="farm-summary">
                <CardHeader><CardTitle>Ringkasan Perhitungan</CardTitle></CardHeader>
                <CardContent class="space-y-3">
                    <FieldStat label="Pendapatan Hasil Panen" :value="rupiah(live.harvest_income)" />
                    <FieldStat
                        label="Pengeluaran Biaya Usaha"
                        :value="rupiah(live.total_cost)"
                        testid="farm-total-cost"
                    />
                    <FieldStat label="Hasil Bersih Usaha" :value="rupiah(live.net_profit)" testid="farm-net-profit" />
                    <FieldStat
                        label="Pendapatan Perbulan"
                        :value="rupiah(live.monthly_income)"
                        strong
                        testid="farm-monthly"
                    />
                    <p class="text-xs text-muted-foreground">
                        Angsuran pokok = plafon {{ rupiah(props.application.requested_amount) }} ÷
                        {{ props.application.requested_tenor }} bulan × {{ HARVEST_MONTHS }} bulan musiman.
                        Pendapatan per bulan = (hasil bersih + penambahan − angsuran pokok) ÷
                        {{ HARVEST_MONTHS }}. Pinjaman bank lain sudah masuk pos biaya, jadi tidak dikurangi lagi.
                    </p>
                </CardContent>
            </Card>

            <div class="sticky bottom-3 rounded-md border bg-background/95 p-2 shadow-sm backdrop-blur">
                <FormActions
                    cancel-testid="farm-reset"
                    cancel-label="Kembalikan"
                    submit-testid="farm-save"
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
