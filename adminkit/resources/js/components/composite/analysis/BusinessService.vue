<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

import FieldMoney from '@/components/composite/analysis/FieldMoney.vue';
import FieldStat from '@/components/composite/analysis/FieldStat.vue';
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
import { serviceMetrics } from '@/constants/analysisMath';

/** Usaha jasa / penghasilan tetap: identitas dan keuangan dalam satu lembar. */
const props = defineProps({
    url: { type: String, required: true },
    business: { type: Object, required: true },
    options: { type: Object, required: true },
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
</script>

<template>
    <Card data-testid="service-form">
        <CardHeader><CardTitle>Identitas &amp; Keuangan</CardTitle></CardHeader>
        <CardContent class="form-dense space-y-4">
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                <FieldStat label="Kode Usaha" :value="props.business.code" />
                <div class="space-y-[var(--item-gap)]">
                    <Label for="service-name">Nama Usaha <span class="text-destructive">*</span></Label>
                    <Input id="service-name" v-model="form.name" maxlength="150" data-testid="service-name" />
                    <p v-if="form.errors.name" class="text-xs font-medium text-destructive">{{ form.errors.name }}</p>
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
                <div class="space-y-[var(--item-gap)] sm:col-span-2 xl:col-span-3">
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
            </div>

            <div class="grid gap-3 sm:grid-cols-3">
                <FieldStat label="Total Penghasilan" :value="rupiah(live.total_income)" />
                <FieldStat label="Total Pengeluaran" :value="rupiah(live.total_expense)" />
                <FieldStat
                    label="Hasil Usaha Bersih"
                    :value="rupiah(live.net_profit)"
                    strong
                    testid="service-net-profit"
                />
            </div>
        </CardContent>
        <CardFooter>
            <FormActions
                cancel-testid="service-reset"
                cancel-label="Kembalikan"
                submit-testid="service-save"
                :processing="form.processing"
                :disabled="!form.name"
                @cancel="form.reset()"
                @submit="form.put(props.url, { preserveScroll: true })"
            />
        </CardFooter>
    </Card>
</template>
