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
import DecimalInput from '@/components/ui/DecimalInput.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import NumberInput from '@/components/ui/NumberInput.vue';
import { ACTION } from '@/constants/labels';
import { all, max, min, required } from '@/lib/validators';
import { useLiveValidation } from '@/composables/useLiveValidation';

const props = defineProps({
    record: { type: Object, default: null },
    nextCode: { type: String, default: '' },
    statuses: { type: Array, default: () => [] },
    offices: { type: Array, default: () => [] },
    products: { type: Array, default: () => [] },
    methods: { type: Array, default: () => [] },
    installments: { type: Array, default: () => [] },
    regionOptions: { type: Array, default: () => [] },
});

const editing = computed(() => Boolean(props.record));

const form = useForm({
    application_date: props.record?.application_date ?? new Date().toISOString().slice(0, 10),
    status: props.record?.status ?? 'DIAJUKAN',
    office_id: props.record?.office_id ?? '',
    product_id: props.record?.product_id ?? '',
    purpose: props.record?.purpose ?? '',
    economic_sector: props.record?.economic_sector ?? '',
    source: props.record?.source ?? '',

    cif_number: props.record?.cif_number ?? '',
    nik: props.record?.nik ?? '',
    full_name: props.record?.full_name ?? '',
    birth_place: props.record?.birth_place ?? '',
    birth_date: props.record?.birth_date ?? '',
    gender: props.record?.gender ?? '',
    marital_status: props.record?.marital_status ?? '',
    mother_name: props.record?.mother_name ?? '',
    npwp: props.record?.npwp ?? '',
    address: props.record?.address ?? '',
    region_code: props.record?.region_code ?? '',
    region_label: props.record?.region_label ?? '',
    phone: props.record?.phone ?? '',
    email: props.record?.email ?? '',
    occupation: props.record?.occupation ?? '',
    employer_name: props.record?.employer_name ?? '',
    monthly_income: props.record?.monthly_income ?? 0,
    other_income: props.record?.other_income ?? 0,
    monthly_expense: props.record?.monthly_expense ?? 0,
    spouse_name: props.record?.spouse_name ?? '',
    spouse_nik: props.record?.spouse_nik ?? '',
    spouse_income: props.record?.spouse_income ?? 0,

    requested_amount: props.record?.requested_amount ?? 0,
    requested_tenor: props.record?.requested_tenor ?? 0,
    method_id: props.record?.method_id ?? '',
    installment_id: props.record?.installment_id ?? '',
    interest_rate: props.record?.interest_rate ?? '',
    provision_rate: props.record?.provision_rate ?? '',
    admin_rate: props.record?.admin_rate ?? '',
    collateral_note: props.record?.collateral_note ?? '',
});

const GENDERS = [
    { value: 'L', label: 'LAKI-LAKI' },
    { value: 'P', label: 'PEREMPUAN' },
];

const MARITAL = ['BELUM MENIKAH', 'MENIKAH', 'JANDA/DUDA'].map((v) => ({ value: v, label: v }));
const SOURCES = ['WALK IN', 'REFERENSI', 'KUNJUNGAN', 'ONLINE'].map((v) => ({ value: v, label: v }));
const statusOptions = computed(() => props.statuses.map((s) => ({ value: s, label: s })));

const onRegion = (value) => {
    form.region_code = value;
    form.region_label = props.regionOptions.find((o) => o.value === value)?.label ?? '';
};

/* Validasi sisi frontend — cermin aturan controller. */
const digits = (label, minLen) => (value) => {
    const raw = String(value ?? '').trim();
    if (!raw) return `Kolom ${label} wajib diisi.`;
    if (!/^\d+$/.test(raw)) return `Kolom ${label} hanya boleh angka.`;

    return raw.length < minLen ? `Kolom ${label} minimal ${minLen} digit.` : '';
};

const schema = {
    nik: digits('nomor ktp', 8),
    full_name: all(required('nama lengkap'), min(3, 'Nama Lengkap'), max(100, 'Nama Lengkap')),
};

const check = useLiveValidation(form, schema);

const back = () => router.visit('/loan-simulation');

const submit = () =>
    check.submit(() => {
        const options = { preserveScroll: true };
        if (editing.value) form.put(`/loan-simulation/${props.record.id}`, options);
        else form.post('/loan-simulation', options);
    });
</script>

<template>
    <Head :title="editing ? 'Ubah Pengajuan' : 'Tambah Pengajuan'" />
    <AppLayout>
        <form class="form-dense space-y-4" novalidate @submit.prevent="submit">
            <Card>
                <CardHeader><CardTitle>Berkas Pengajuan</CardTitle></CardHeader>
                <CardContent class="grid gap-[var(--field-gap)] sm:grid-cols-2 lg:grid-cols-4">
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Kode Pengajuan <span class="text-destructive" aria-hidden="true">*</span></Label>
                        <p
                            class="flex h-8 items-center rounded-md border bg-muted/40 px-3 font-mono text-xs font-medium"
                            data-testid="loan-form-code"
                        >
                            {{ props.nextCode }}
                        </p>
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Tanggal Pengajuan</Label>
                        <DatePicker v-model="form.application_date" data-testid="loan-form-date" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Status</Label>
                        <Combobox v-model="form.status" :options="statusOptions" data-testid="loan-form-status" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Kantor</Label>
                        <Combobox
                            v-model="form.office_id"
                            :options="props.offices"
                            placeholder="-- Pilih --"
                            data-testid="loan-form-office"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)] lg:col-span-2">
                        <Label>Produk Kredit</Label>
                        <Combobox
                            v-model="form.product_id"
                            :options="props.products"
                            placeholder="-- Pilih --"
                            data-testid="loan-form-product"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Asal Pengajuan</Label>
                        <Combobox
                            v-model="form.source"
                            :options="SOURCES"
                            placeholder="(Opsional)"
                            data-testid="loan-form-source"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-sector">Sektor Ekonomi</Label>
                        <Input
                            id="f-sector"
                            v-model="form.economic_sector"
                            placeholder="(Opsional)"
                            class="uppercase"
                            data-testid="loan-form-sector"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)] sm:col-span-2 lg:col-span-4">
                        <Label for="f-purpose">Tujuan Penggunaan</Label>
                        <Input
                            id="f-purpose"
                            v-model="form.purpose"
                            placeholder="(Opsional)"
                            class="uppercase"
                            data-testid="loan-form-purpose"
                        />
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Data Pemohon</CardTitle></CardHeader>
                <CardContent class="grid gap-[var(--field-gap)] sm:grid-cols-2 lg:grid-cols-4">
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-nik">Nomor KTP <span class="text-destructive" aria-hidden="true">*</span></Label>
                        <Input
                            id="f-nik"
                            v-model="form.nik"
                            inputmode="numeric"
                            class="font-mono"
                            data-testid="loan-form-nik"
                            @blur="check.validate('nik')"
                        />
                        <p v-if="form.errors.nik" class="text-xs font-medium text-destructive">{{ form.errors.nik }}</p>
                    </div>
                    <div class="space-y-[var(--item-gap)] lg:col-span-2">
                        <Label for="f-name">Nama Lengkap <span class="text-destructive" aria-hidden="true">*</span></Label>
                        <Input
                            id="f-name"
                            v-model="form.full_name"
                            class="uppercase"
                            data-testid="loan-form-name"
                            @blur="check.validate('full_name')"
                        />
                        <p v-if="form.errors.full_name" class="text-xs font-medium text-destructive">
                            {{ form.errors.full_name }}
                        </p>
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-cif">Nomor CIF</Label>
                        <Input
                            id="f-cif"
                            v-model="form.cif_number"
                            placeholder="(Dari CBS)"
                            class="font-mono"
                            data-testid="loan-form-cif"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-birthplace">Tempat Lahir</Label>
                        <Input id="f-birthplace" v-model="form.birth_place" class="uppercase" data-testid="loan-form-birth-place" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Tanggal Lahir</Label>
                        <DatePicker v-model="form.birth_date" placeholder="(Opsional)" data-testid="loan-form-birth-date" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Jenis Kelamin</Label>
                        <Combobox
                            v-model="form.gender"
                            :options="GENDERS"
                            placeholder="(Opsional)"
                            data-testid="loan-form-gender"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Status Perkawinan</Label>
                        <Combobox
                            v-model="form.marital_status"
                            :options="MARITAL"
                            placeholder="(Opsional)"
                            data-testid="loan-form-marital"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-mother">Nama Ibu</Label>
                        <Input id="f-mother" v-model="form.mother_name" class="uppercase" data-testid="loan-form-mother" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-npwp">NPWP</Label>
                        <Input id="f-npwp" v-model="form.npwp" class="font-mono" data-testid="loan-form-npwp" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-phone">Telepon</Label>
                        <Input id="f-phone" v-model="form.phone" inputmode="tel" data-testid="loan-form-phone" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-email">Email</Label>
                        <Input id="f-email" v-model="form.email" type="email" data-testid="loan-form-email" />
                        <p v-if="form.errors.email" class="text-xs font-medium text-destructive">{{ form.errors.email }}</p>
                    </div>
                    <div class="space-y-[var(--item-gap)] sm:col-span-2">
                        <Label for="f-address">Alamat</Label>
                        <Input id="f-address" v-model="form.address" class="uppercase" data-testid="loan-form-address" />
                    </div>
                    <div class="space-y-[var(--item-gap)] sm:col-span-2">
                        <Label>Wilayah</Label>
                        <Combobox
                            :model-value="form.region_code"
                            :options="props.regionOptions"
                            placeholder="-- Pilih --"
                            data-testid="loan-form-region"
                            @update:model-value="onRegion"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-occupation">Pekerjaan</Label>
                        <Input id="f-occupation" v-model="form.occupation" class="uppercase" data-testid="loan-form-occupation" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-employer">Tempat Bekerja/Usaha</Label>
                        <Input id="f-employer" v-model="form.employer_name" class="uppercase" data-testid="loan-form-employer" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-income">Penghasilan / Bulan</Label>
                        <NumberInput id="f-income" v-model="form.monthly_income" data-testid="loan-form-income" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-other-income">Penghasilan Lain</Label>
                        <NumberInput id="f-other-income" v-model="form.other_income" data-testid="loan-form-other-income" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-expense">Pengeluaran / Bulan</Label>
                        <NumberInput id="f-expense" v-model="form.monthly_expense" data-testid="loan-form-expense" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-spouse">Nama Pasangan</Label>
                        <Input id="f-spouse" v-model="form.spouse_name" class="uppercase" data-testid="loan-form-spouse" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-spouse-nik">KTP Pasangan</Label>
                        <Input
                            id="f-spouse-nik"
                            v-model="form.spouse_nik"
                            inputmode="numeric"
                            class="font-mono"
                            data-testid="loan-form-spouse-nik"
                        />
                        <p v-if="form.errors.spouse_nik" class="text-xs font-medium text-destructive">
                            {{ form.errors.spouse_nik }}
                        </p>
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-spouse-income">Penghasilan Pasangan</Label>
                        <NumberInput id="f-spouse-income" v-model="form.spouse_income" data-testid="loan-form-spouse-income" />
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Permohonan Kredit</CardTitle></CardHeader>
                <CardContent class="grid gap-[var(--field-gap)] sm:grid-cols-2 lg:grid-cols-4">
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-amount">Plafon Diajukan</Label>
                        <NumberInput id="f-amount" v-model="form.requested_amount" data-testid="loan-form-amount" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-tenor">Jangka Waktu (bulan)</Label>
                        <NumberInput id="f-tenor" v-model="form.requested_tenor" data-testid="loan-form-tenor" />
                        <p v-if="form.errors.requested_tenor" class="text-xs font-medium text-destructive">
                            {{ form.errors.requested_tenor }}
                        </p>
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Sistem Bunga</Label>
                        <Combobox
                            v-model="form.method_id"
                            :options="props.methods"
                            placeholder="-- Pilih --"
                            data-testid="loan-form-method"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Sistem Cicilan</Label>
                        <Combobox
                            v-model="form.installment_id"
                            :options="props.installments"
                            placeholder="-- Pilih --"
                            data-testid="loan-form-installment"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-rate">Suku Bunga (%)</Label>
                        <DecimalInput id="f-rate" v-model="form.interest_rate" data-testid="loan-form-rate" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-provision">Provisi (%)</Label>
                        <DecimalInput id="f-provision" v-model="form.provision_rate" data-testid="loan-form-provision" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-admin">Biaya Admin (%)</Label>
                        <DecimalInput id="f-admin" v-model="form.admin_rate" data-testid="loan-form-admin" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-collateral-note">Catatan Agunan</Label>
                        <Input
                            id="f-collateral-note"
                            v-model="form.collateral_note"
                            placeholder="(Opsional)"
                            class="uppercase"
                            data-testid="loan-form-collateral-note"
                        />
                    </div>
                </CardContent>
                <CardFooter class="justify-between">
                    <Button variant="outline" size="sm" type="button" data-testid="loan-form-cancel" @click="back">
                        <X class="size-4" /> {{ ACTION.cancel }}
                    </Button>
                    <Button size="sm" type="submit" :disabled="form.processing" data-testid="loan-form-save">
                        <Save class="size-4" /> {{ form.processing ? ACTION.saving : ACTION.save }}
                    </Button>
                </CardFooter>
            </Card>
        </form>
    </AppLayout>
</template>
