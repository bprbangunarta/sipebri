<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

import FieldMoney from '@/components/composite/analysis/FieldMoney.vue';
import FieldStat from '@/components/composite/analysis/FieldStat.vue';
import ItemRows from '@/components/composite/analysis/ItemRows.vue';
import FormActions from '@/components/composite/FormActions.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardFooter from '@/components/ui/CardFooter.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
import Label from '@/components/ui/Label.vue';
import { rupiah } from '@/constants/committee';
import { financeMetrics, HOUSEHOLD_COSTS } from '@/constants/analysisMath';

/** Analisa Keuangan — kemampuan keuangan pemohon per bulan. */
const props = defineProps({
    applicationId: { type: Number, required: true },
    sheet: { type: Object, required: true },
});

const form = useForm({
    ...Object.fromEntries(HOUSEHOLD_COSTS.map((c) => [c.key, props.sheet[c.key] ?? 0])),
    items: props.sheet.obligations.map((o) => ({ ...o })),
});

const live = computed(() => financeMetrics(form, form.items, props.sheet.metrics));

const submit = () => form.put(`/analysis-simulation/${props.applicationId}/finance`, { preserveScroll: true });
</script>

<template>
    <Card data-testid="finance-form">
        <CardHeader><CardTitle>Kemampuan Keuangan</CardTitle></CardHeader>
        <CardContent class="form-dense space-y-4">
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <FieldMoney
                    v-for="cost in HOUSEHOLD_COSTS"
                    :key="cost.key"
                    v-model="form[cost.key]"
                    :label="cost.label"
                    :testid="`finance-${cost.key}`"
                    :error="form.errors[cost.key]"
                />
            </div>

            <div class="space-y-[var(--item-gap)]">
                <Label>Kewajiban Untuk</Label>
                <ItemRows
                    :rows="form.items"
                    :columns="[
                        { key: 'name', label: 'Kewajiban Untuk', type: 'text' },
                        { key: 'amount', label: 'Nominal', type: 'money', width: '200px' },
                    ]"
                    :blank="{ name: '', amount: 0 }"
                    :totals="[{ label: 'Total Kewajiban', value: rupiah(live.obligation_cost) }]"
                    add-label="Tambah Kewajiban"
                    empty-text="Tidak ada kewajiban ke pihak lain."
                    testid="finance-obligation"
                />
            </div>

            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <FieldStat label="Usaha Perdagangan" :value="rupiah(props.sheet.metrics.trade_income)" />
                <FieldStat label="Usaha Jasa" :value="rupiah(props.sheet.metrics.service_income)" />
                <FieldStat label="Usaha Pertanian" :value="rupiah(props.sheet.metrics.farm_income)" />
                <FieldStat label="Usaha Lainnya" :value="rupiah(props.sheet.metrics.other_income)" />
                <FieldStat
                    label="Pendapatan Usaha"
                    :value="rupiah(live.business_income)"
                    testid="finance-business-income"
                />
                <FieldStat label="Biaya Rumah Tangga" :value="rupiah(live.household_cost)" testid="finance-household" />
                <FieldStat label="Kewajiban Lainnya" :value="rupiah(live.obligation_cost)" />
                <FieldStat
                    label="Keuangan Perbulan"
                    :value="rupiah(live.monthly_balance)"
                    strong
                    testid="finance-balance"
                />
            </div>

            <p class="text-xs text-muted-foreground">
                Pendapatan usaha diambil otomatis dari kontribusi per bulan setiap usaha pada bagian Analisa
                Usaha — khusus pertanian dipakai pendapatan per bulan, yaitu hasil bersih panen dikurangi setoran
                pokok lalu dibagi periode sistem cicilan berkas (MUSIMAN = 6 bulan).
            </p>
        </CardContent>
        <CardFooter>
            <FormActions
                cancel-testid="finance-reset"
                cancel-label="Kembalikan"
                submit-testid="finance-save"
                :processing="form.processing"
                @cancel="form.reset()"
                @submit="submit"
            />
        </CardFooter>
    </Card>
</template>
