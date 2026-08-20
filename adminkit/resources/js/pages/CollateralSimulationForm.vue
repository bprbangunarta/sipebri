<script setup>
import { computed } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Save, X } from 'lucide-vue-next';

import AppLayout from '@/components/layout/AppLayout.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardFooter from '@/components/ui/CardFooter.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
import Combobox from '@/components/ui/Combobox.vue';
import DatePicker from '@/components/ui/DatePicker.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import NumberInput from '@/components/ui/NumberInput.vue';
import { ACTION } from '@/constants/labels';

const props = defineProps({
    record: { type: Object, default: null },
    collateralTypes: { type: Array, default: () => [] },
    bindingTypes: { type: Array, default: () => [] },
    conditions: { type: Array, default: () => [] },
    regionOptions: { type: Array, default: () => [] },
});

const editing = computed(() => Boolean(props.record));

const form = useForm({
    collateral_id: props.record?.collateral_id ?? '',
    collateral_type_code: props.record?.collateral_type_code ?? '',
    binding_type_code: props.record?.binding_type_code ?? '',
    document_number: props.record?.document_number ?? '',
    owner_name: props.record?.owner_name ?? '',
    owner_address: props.record?.owner_address ?? '',
    region_code: props.record?.region_code ?? '',
    region_label: props.record?.region_label ?? '',
    description: props.record?.description ?? '',
    condition_code: props.record?.condition_code ?? '',
    condition_date: props.record?.condition_date ?? '',
    insured: props.record?.insured ?? '',
    insurance_start_date: props.record?.insurance_start_date ?? '',
    value_guarantee: props.record?.value_guarantee ?? 0,
    value_fair: props.record?.value_fair ?? 0,
    value_appraisal: props.record?.value_appraisal ?? 0,
    appraised_at: props.record?.appraised_at ?? '',
    value_independent: props.record?.value_independent ?? '',
    independent_appraised_at: props.record?.independent_appraised_at ?? '',
    ppap_code: props.record?.ppap_code ?? '1',
});

const INSURED_OPTIONS = [
    { value: 'T', label: 'TDK' },
    { value: 'Y', label: 'YA' },
];

const onRegion = (value) => {
    form.region_code = value;
    form.region_label = props.regionOptions.find((o) => o.value === value)?.label ?? '';
};

const back = () => router.visit('/collateral-simulation');

const submit = () => {
    const options = { preserveScroll: true };
    if (editing.value) form.put(`/collateral-simulation/${props.record.id}`, options);
    else form.post('/collateral-simulation', options);
};
</script>

<template>
    <Head :title="editing ? 'Ubah Agunan' : 'Tambah Agunan'" />
    <AppLayout>
        <form class="form-dense space-y-4" novalidate @submit.prevent="submit">
            <Card>
                <CardHeader><CardTitle>Informasi Agunan</CardTitle></CardHeader>
                <CardContent class="grid gap-[var(--field-gap)] sm:grid-cols-2 lg:grid-cols-3">
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Jenis Agunan</Label>
                        <Combobox
                            v-model="form.collateral_type_code"
                            :options="props.collateralTypes"
                            placeholder="-- Pilih --"
                            data-testid="collateral-form-type"
                        />
                        <p v-if="form.errors.collateral_type_code" class="text-xs font-medium text-destructive">
                            {{ form.errors.collateral_type_code }}
                        </p>
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Jenis Pengikatan</Label>
                        <Combobox
                            v-model="form.binding_type_code"
                            :options="props.bindingTypes"
                            placeholder="-- Pilih --"
                            data-testid="collateral-form-binding"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-doc">No. Dokumen</Label>
                        <Input
                            id="f-doc"
                            v-model="form.document_number"
                            placeholder="(Opsional)"
                            data-testid="collateral-form-document"
                        />
                    </div>

                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-owner">Nama Pemilik</Label>
                        <Input id="f-owner" v-model="form.owner_name" data-testid="collateral-form-owner" />
                    </div>
                    <div class="space-y-[var(--item-gap)] lg:col-span-2">
                        <Label for="f-owner-address">Alamat Agunan</Label>
                        <Input id="f-owner-address" v-model="form.owner_address" data-testid="collateral-form-owner-address" />
                    </div>

                    <div class="space-y-[var(--item-gap)]">
                        <Label>Lokasi Agunan</Label>
                        <Combobox
                            :model-value="form.region_code"
                            :options="props.regionOptions"
                            placeholder="-- Pilih --"
                            data-testid="collateral-form-region"
                            @update:model-value="onRegion"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)] lg:col-span-2">
                        <Label for="f-desc">Keterangan Agunan</Label>
                        <Input id="f-desc" v-model="form.description" maxlength="255" data-testid="collateral-form-description" />
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Kondisi &amp; Asuransi</CardTitle></CardHeader>
                <CardContent class="grid gap-[var(--field-gap)] sm:grid-cols-2 lg:grid-cols-4">
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Kondisi</Label>
                        <Combobox
                            v-model="form.condition_code"
                            :options="props.conditions"
                            placeholder="-- Pilih --"
                            data-testid="collateral-form-condition"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Tgl Kondisi</Label>
                        <DatePicker
                            v-model="form.condition_date"
                            placeholder="-- Pilih --"
                            data-testid="collateral-form-condition-date"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Diasuransikan</Label>
                        <Combobox
                            v-model="form.insured"
                            :options="INSURED_OPTIONS"
                            placeholder="-- Pilih --"
                            data-testid="collateral-form-insured"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Tgl Asuransi</Label>
                        <DatePicker
                            v-model="form.insurance_start_date"
                            placeholder="-- Pilih --"
                            data-testid="collateral-form-insurance-start"
                        />
                    </div>

                    <div class="space-y-[var(--item-gap)] sm:col-span-2">
                        <Label for="f-guarantee">Nilai Jaminan</Label>
                        <NumberInput id="f-guarantee" v-model="form.value_guarantee" data-testid="collateral-form-guarantee" />
                    </div>
                    <div class="space-y-[var(--item-gap)] sm:col-span-2">
                        <Label for="f-fair">Nilai Pasar</Label>
                        <NumberInput id="f-fair" v-model="form.value_fair" data-testid="collateral-form-fair" />
                    </div>

                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-appraisal">Nilai Taksasi</Label>
                        <NumberInput id="f-appraisal" v-model="form.value_appraisal" data-testid="collateral-form-appraisal" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Tgl Taksasi</Label>
                        <DatePicker
                            v-model="form.appraised_at"
                            placeholder="-- Pilih --"
                            data-testid="collateral-form-appraised-at"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-independent">Nilai Apraisal</Label>
                        <NumberInput
                            id="f-independent"
                            v-model="form.value_independent"
                            placeholder="(Opsional)"
                            data-testid="collateral-form-independent"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Tgl Apraisal</Label>
                        <DatePicker
                            v-model="form.independent_appraised_at"
                            placeholder="(Opsional)"
                            data-testid="collateral-form-independent-appraised-at"
                        />
                    </div>
                </CardContent>
                <CardFooter class="justify-between">
                    <Button variant="outline" size="sm" type="button" data-testid="collateral-form-cancel" @click="back">
                        <X class="size-4" /> {{ ACTION.cancel }}
                    </Button>
                    <Button size="sm" type="submit" :disabled="form.processing" data-testid="collateral-form-save">
                        <Save class="size-4" /> {{ form.processing ? ACTION.saving : ACTION.save }}
                    </Button>
                </CardFooter>
            </Card>
        </form>
    </AppLayout>
</template>
