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
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import Textarea from '@/components/ui/Textarea.vue';
import { rupiah } from '@/constants/committee';
import { serviceMetrics } from '@/constants/analysisMath';

/** Usaha jasa / penghasilan tetap — satu lembar. */
const props = defineProps({
    url: { type: String, required: true },
    business: { type: Object, required: true },
    options: { type: Object, required: true },
    application: { type: Object, required: true },
});

const form = useForm({
    name: props.business.name,
    business_length: props.business.business_length ?? '',
    address: props.business.address ?? '',
    service_income: props.business.service_income,
    vehicle_tax: props.business.vehicle_tax,
    other_expense: props.business.other_expense,
});

const live = computed(() => serviceMetrics(form));
const submit = () => form.put(props.url, { preserveScroll: true });
</script>

<template>
    <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_22rem]">
        <div class="min-w-0 space-y-4">
            <Card data-testid="service-identity">
                <CardHeader><CardTitle>Identitas Usaha</CardTitle></CardHeader>
                <CardContent class="form-dense grid gap-3 sm:grid-cols-2">
                    <FieldStat label="Kode Usaha" :value="props.business.code" />
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="service-name">Nama Usaha <span class="text-destructive">*</span></Label>
                        <Input id="service-name" v-model="form.name" maxlength="150" data-testid="service-name" />
                        <p v-if="form.errors.name" class="text-xs font-medium text-destructive">
                            {{ form.errors.name }}
                        </p>
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="service-length">Lama Usaha</Label>
                        <Combobox
                            id="service-length"
                            v-model="form.business_length"
                            :options="props.options.lengths.map((v) => ({ value: v, label: v }))"
                            placeholder="(Opsional)"
                            data-testid="service-length"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="service-address">Alamat Usaha</Label>
                        <Textarea
                            id="service-address"
                            v-model="form.address"
                            rows="2"
                            maxlength="255"
                            placeholder="(Opsional)"
                            data-testid="service-address"
                        />
                    </div>
                </CardContent>
            </Card>

            <Card data-testid="service-finance">
                <CardHeader><CardTitle>Analisa Keuangan</CardTitle></CardHeader>
                <CardContent class="form-dense grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <FieldMoney
                        v-model="form.service_income"
                        label="Pendapatan Usaha"
                        testid="service-income"
                        :error="form.errors.service_income"
                    />
                    <FieldMoney
                        v-model="form.vehicle_tax"
                        label="Pajak Kendaraan"
                        testid="service-vehicle-tax"
                        :error="form.errors.vehicle_tax"
                    />
                    <FieldMoney
                        v-model="form.other_expense"
                        label="Pengeluaran Lainnya"
                        testid="service-other-expense"
                        :error="form.errors.other_expense"
                    />
                </CardContent>
            </Card>
        </div>

        <div class="space-y-3 xl:sticky xl:top-20 xl:self-start">
            <Card data-testid="service-summary">
                <CardHeader><CardTitle>Ringkasan Perhitungan</CardTitle></CardHeader>
                <CardContent class="space-y-3">
                    <FieldStat label="Total Penghasilan" :value="rupiah(live.total_income)" />
                    <FieldStat label="Total Pengeluaran" :value="rupiah(live.total_expense)" />
                    <FieldStat
                        label="Hasil Usaha Bersih"
                        :value="rupiah(live.net_profit)"
                        strong
                        testid="service-net-profit"
                    />
                    <p class="text-xs text-muted-foreground">
                        Hasil usaha bersih = pendapatan usaha − (pajak kendaraan + pengeluaran lainnya). Angka ini
                        dipakai bagian Analisa Keuangan.
                    </p>
                </CardContent>
            </Card>

            <div class="sticky bottom-3 rounded-md border bg-background/95 p-2 shadow-sm backdrop-blur">
                <FormActions
                    cancel-testid="service-reset"
                    cancel-label="Kembalikan"
                    submit-testid="service-save"
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
