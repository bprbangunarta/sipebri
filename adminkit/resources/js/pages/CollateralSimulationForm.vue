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
import { all, max, min, required } from '@/lib/validators';
import { useLiveValidation } from '@/composables/useLiveValidation';

const props = defineProps({
    record: { type: Object, default: null },
    collateralTypes: { type: Array, default: () => [] },
    bindingTypes: { type: Array, default: () => [] },
    conditions: { type: Array, default: () => [] },
    regionOptions: { type: Array, default: () => [] },
});

const editing = computed(() => Boolean(props.record));

const form = useForm({
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
    insurance_code: props.record?.insurance_code ?? '',
    insurance_date: props.record?.insurance_date ?? '',
    guarantee_value: props.record?.guarantee_value ?? 0,
    fair_value: props.record?.fair_value ?? 0,
    njop_value: props.record?.njop_value ?? '',
    adjustment_value: props.record?.adjustment_value ?? '',
    appraisal_value: props.record?.appraisal_value ?? 0,
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

/* Validasi sisi frontend — cermin aturan controller. */
const positive = (label) => (value) =>
    value === '' || value === null || Number(value) <= 0 ? `Kolom ${label} wajib lebih dari 0.` : '';

const schema = {
    collateral_type_code: required('jenis agunan'),
    document_number: all(required('no. dokumen'), max(100, 'No. Dokumen')),
    owner_name: all(required('nama pemilik'), min(3, 'Nama Pemilik'), max(100, 'Nama Pemilik')),
    owner_address: all(required('alamat agunan'), min(5, 'Alamat Agunan'), max(255, 'Alamat Agunan')),
    region_code: required('lokasi agunan'),
    description: all(required('keterangan agunan'), min(5, 'Keterangan Agunan'), max(255, 'Keterangan Agunan')),
    ...(editing.value
        ? {
            condition_code: required('kondisi'),
            condition_date: required('tgl kondisi'),
            insurance_code: required('diasuransikan'),
            insurance_date: required('tgl asuransi'),
            guarantee_value: positive('nilai jaminan'),
            fair_value: positive('nilai pasar'),
            appraisal_value: positive('nilai taksasi'),
        }
        : {}),
};

const check = useLiveValidation(form, schema);

const back = () => router.visit('/collateral-simulation');

const submit = () =>
    check.submit(() => {
        const options = { preserveScroll: true };
        if (editing.value) form.put(`/collateral-simulation/${props.record.id}`, options);
        else form.post('/collateral-simulation', options);
    });
</script>

<template>
    <Head :title="editing ? 'Ubah Agunan' : 'Tambah Agunan'" />
    <AppLayout>
        <form class="form-dense space-y-4" novalidate @submit.prevent="submit">
            <Card>
                <CardHeader><CardTitle>Informasi Agunan</CardTitle></CardHeader>
                <CardContent class="grid gap-[var(--field-gap)] sm:grid-cols-2 lg:grid-cols-3">
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Jenis Agunan <span class="text-destructive" aria-hidden="true">*</span></Label>
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
                            placeholder="(Opsional)"
                            data-testid="collateral-form-binding"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-doc">No. Dokumen <span class="text-destructive" aria-hidden="true">*</span></Label>
                        <Input id="f-doc" v-model="form.document_number" data-testid="collateral-form-document"
                            @blur="check.validate('document_number')" />
                        <p v-if="form.errors.document_number" class="text-xs font-medium text-destructive">
                            {{ form.errors.document_number }}
                        </p>
                    </div>

                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-owner">Nama Pemilik <span class="text-destructive" aria-hidden="true">*</span></Label>
                        <Input id="f-owner" v-model="form.owner_name" data-testid="collateral-form-owner"
                            @blur="check.validate('owner_name')" />
                        <p v-if="form.errors.owner_name" class="text-xs font-medium text-destructive">
                            {{ form.errors.owner_name }}
                        </p>
                    </div>
                    <div class="space-y-[var(--item-gap)] lg:col-span-2">
                        <Label for="f-owner-address">Alamat Agunan <span class="text-destructive" aria-hidden="true">*</span></Label>
                        <Input id="f-owner-address" v-model="form.owner_address" data-testid="collateral-form-owner-address"
                            @blur="check.validate('owner_address')" />
                        <p v-if="form.errors.owner_address" class="text-xs font-medium text-destructive">
                            {{ form.errors.owner_address }}
                        </p>
                    </div>

                    <div class="space-y-[var(--item-gap)]">
                        <Label>Lokasi Agunan <span class="text-destructive" aria-hidden="true">*</span></Label>
                        <Combobox
                            :model-value="form.region_code"
                            :options="props.regionOptions"
                            placeholder="-- Pilih --"
                            data-testid="collateral-form-region"
                            @update:model-value="onRegion"
                        />
                        <p v-if="form.errors.region_code" class="text-xs font-medium text-destructive">
                            {{ form.errors.region_code }}
                        </p>
                    </div>
                    <div class="space-y-[var(--item-gap)] lg:col-span-2">
                        <Label for="f-desc">Keterangan Agunan <span class="text-destructive" aria-hidden="true">*</span></Label>
                        <Input
                            id="f-desc"
                            v-model="form.description"
                            maxlength="255"
                            data-testid="collateral-form-description"
                            @blur="check.validate('description')"
                        />
                        <p v-if="form.errors.description" class="text-xs font-medium text-destructive">
                            {{ form.errors.description }}
                        </p>
                    </div>
                </CardContent>
                <CardFooter v-if="!editing" class="justify-between">
                    <Button variant="outline" size="sm" type="button" data-testid="collateral-form-cancel" @click="back">
                        <X class="size-4" /> {{ ACTION.cancel }}
                    </Button>
                    <Button size="sm" type="submit" :disabled="form.processing" data-testid="collateral-form-save">
                        <Save class="size-4" /> {{ form.processing ? ACTION.saving : ACTION.save }}
                    </Button>
                </CardFooter>
            </Card>

            <Card v-if="editing">
                <CardHeader><CardTitle>Kondisi &amp; Asuransi</CardTitle></CardHeader>
                <CardContent class="grid gap-[var(--field-gap)] sm:grid-cols-2 lg:grid-cols-4">
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Kondisi <span class="text-destructive" aria-hidden="true">*</span></Label>
                        <Combobox
                            v-model="form.condition_code"
                            :options="props.conditions"
                            placeholder="-- Pilih --"
                            data-testid="collateral-form-condition"
                        />
                        <p v-if="form.errors.condition_code" class="text-xs font-medium text-destructive">
                            {{ form.errors.condition_code }}
                        </p>
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Tgl Kondisi <span class="text-destructive" aria-hidden="true">*</span></Label>
                        <DatePicker
                            v-model="form.condition_date"
                            placeholder="-- Pilih --"
                            data-testid="collateral-form-condition-date"
                        />
                        <p v-if="form.errors.condition_date" class="text-xs font-medium text-destructive">
                            {{ form.errors.condition_date }}
                        </p>
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Diasuransikan <span class="text-destructive" aria-hidden="true">*</span></Label>
                        <Combobox
                            v-model="form.insurance_code"
                            :options="INSURED_OPTIONS"
                            placeholder="-- Pilih --"
                            data-testid="collateral-form-insurance_code"
                        />
                        <p v-if="form.errors.insurance_code" class="text-xs font-medium text-destructive">
                            {{ form.errors.insurance_code }}
                        </p>
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Tgl Asuransi <span class="text-destructive" aria-hidden="true">*</span></Label>
                        <DatePicker
                            v-model="form.insurance_date"
                            placeholder="-- Pilih --"
                            data-testid="collateral-form-insurance-start"
                        />
                        <p v-if="form.errors.insurance_date" class="text-xs font-medium text-destructive">
                            {{ form.errors.insurance_date }}
                        </p>
                    </div>

                    <div class="grid gap-[var(--field-gap)] sm:col-span-2 sm:grid-cols-2 lg:col-span-4 lg:grid-cols-4">
                        <div class="space-y-[var(--item-gap)]">
                            <Label for="f-guarantee">Nilai Jaminan</Label>
                            <NumberInput id="f-guarantee" v-model="form.guarantee_value" data-testid="collateral-form-guarantee" />
                            <p v-if="form.errors.guarantee_value" class="text-xs font-medium text-destructive">
                                {{ form.errors.guarantee_value }}
                            </p>
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label for="f-fair">Nilai Pasar</Label>
                            <NumberInput id="f-fair" v-model="form.fair_value" data-testid="collateral-form-fair" />
                            <p v-if="form.errors.fair_value" class="text-xs font-medium text-destructive">
                                {{ form.errors.fair_value }}
                            </p>
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label for="f-njop">Nilai NJOP</Label>
                            <NumberInput
                                id="f-njop"
                                v-model="form.njop_value"
                                placeholder="(Opsional)"
                                data-testid="collateral-form-njop"
                            />
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label for="f-adjust">Adjusment</Label>
                            <NumberInput
                                id="f-adjust"
                                v-model="form.adjustment_value"
                                placeholder="(Opsional)"
                                data-testid="collateral-form-adjustment"
                            />
                        </div>
                    </div>

                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-appraisal">Nilai Taksasi</Label>
                        <NumberInput id="f-appraisal" v-model="form.appraisal_value" data-testid="collateral-form-appraisal" />
                            <p v-if="form.errors.appraisal_value" class="text-xs font-medium text-destructive">
                                {{ form.errors.appraisal_value }}
                            </p>
                    </div>
                    <div class="space-y-[var(--item-gap)] sm:col-span-2">
                        <Label>Penaksir</Label>
                        <p
                            class="flex h-8 items-center rounded-md border border-dashed px-3 text-xs text-muted-foreground"
                            data-testid="collateral-form-appraiser-note"
                        >
                            Diisi sistem saat disimpan: nama petugas analis + tanggal hari ini.
                            <span v-if="props.record?.appraiser_name" class="ml-1 font-medium text-foreground">
                                Terakhir: {{ props.record.appraiser_name }} · {{ props.record.appraised_at }}
                            </span>
                        </p>
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
