<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save, X } from 'lucide-vue-next';

import AppLayout from '@/components/layout/AppLayout.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardFooter from '@/components/ui/CardFooter.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
import Checkbox from '@/components/ui/Checkbox.vue';
import Combobox from '@/components/ui/Combobox.vue';
import DatePicker from '@/components/ui/DatePicker.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import { ACTION } from '@/constants/labels';

const props = defineProps({
    record: { type: Object, default: null },
    collateralTypes: { type: Array, default: () => [] },
    bindingTypes: { type: Array, default: () => [] },
    conditions: { type: Array, default: () => [] },
    methods: { type: Array, default: () => [] },
    ownershipStatuses: { type: Object, default: () => ({}) },
});

const editing = computed(() => Boolean(props.record));

const form = useForm({
    collateral_id: props.record?.collateral_id ?? '',
    paripasu: props.record?.paripasu ?? 0,
    file_number: props.record?.file_number ?? '',
    auto_number: props.record?.auto_number ?? false,
    collateral_type_code: props.record?.collateral_type_code ?? '',
    binding_type_code: props.record?.binding_type_code ?? '',
    ownership: props.record?.ownership ?? '',
    document_number: props.record?.document_number ?? '',
    description: props.record?.description ?? '',
    owner_name: props.record?.owner_name ?? '',
    owner_address: props.record?.owner_address ?? '',
    owner_same_as_cif: props.record?.owner_same_as_cif ?? false,
    region_code: props.record?.region_code ?? '',
    region_id: props.record?.region_id ?? null,
    region_label: props.record?.region_label ?? '',
    value_guarantee: props.record?.value_guarantee ?? 0,
    value_adjustment: props.record?.value_adjustment ?? 0,
    value_fair: props.record?.value_fair ?? 0,
    value_njop: props.record?.value_njop ?? 0,
    value_appraisal: props.record?.value_appraisal ?? 0,
    value_independent: props.record?.value_independent ?? 0,
    appraiser_name: props.record?.appraiser_name ?? '',
    appraised_at: props.record?.appraised_at ?? '',
    independent_appraiser_name: props.record?.independent_appraiser_name ?? '',
    independent_appraised_at: props.record?.independent_appraised_at ?? '',
    condition_code: props.record?.condition_code ?? '9',
    condition_date: props.record?.condition_date ?? '',
    insured: props.record?.insured ?? 'T',
    ppap_code: props.record?.ppap_code ?? '1',
    insurance_start_date: props.record?.insurance_start_date ?? '',
});

/* Label "Nilai Jaminan" berubah menjadi "Nilai Hak Tanggungan" bila diikat APHT (kode 01). */
const guaranteeLabel = computed(() =>
    form.binding_type_code === '01' ? 'Nilai Hak Tanggungan' : 'Nilai Jaminan',
);

const ownershipOptions = computed(() => props.ownershipStatuses[form.collateral_type_code] ?? []);
const INSURED_OPTIONS = [
    { value: 'T', label: 'TDK' },
    { value: 'Y', label: 'YA' },
];

/* ── Wilayah: Kabupaten → Kecamatan → Kelurahan (mengisi kode dati2) ───── */
const regencies = ref([]);
const districts = ref([]);
const villages = ref([]);
const regency = ref('');
const district = ref('');
const village = ref('');

const fetchOptions = async (params) => {
    const qs = new URLSearchParams(params).toString();
    const res = await fetch(`/regions/options?${qs}`, { headers: { Accept: 'application/json' } });
    const json = await res.json();
    return json.options ?? [];
};

const hydrating = ref(false);

onMounted(async () => {
    regencies.value = await fetchOptions({});

    // Mode ubah: pulihkan pilihan bertahap dari wilayah yang tersimpan.
    if (!props.record?.region) return;

    hydrating.value = true;
    regency.value = props.record.region.regency;
    districts.value = await fetchOptions({ regency: regency.value });
    district.value = props.record.region.district;
    villages.value = await fetchOptions({ regency: regency.value, district: district.value });
    village.value = String(props.record.region.id);
    hydrating.value = false;
});

watch(regency, async (value) => {
    if (hydrating.value) return;
    district.value = '';
    village.value = '';
    villages.value = [];
    districts.value = value ? await fetchOptions({ regency: value }) : [];
});

watch(district, async (value) => {
    if (hydrating.value) return;
    village.value = '';
    villages.value = value ? await fetchOptions({ regency: regency.value, district: value }) : [];
});

watch(village, (value) => {
    if (hydrating.value) return;
    const found = villages.value.find((v) => v.value === value);
    if (!found) return;
    form.region_code = found.code;
    form.region_id = Number(found.value);
    form.region_label = `${found.code} · ${regency.value}`;
});

const back = () => router.visit('/collateral-simulation');

const submit = () => {
    const options = { preserveScroll: true };
    if (editing.value) form.put(`/collateral-simulation/${props.record.id}`, options);
    else form.post('/collateral-simulation', options);
};
</script>

<template>
    <Head :title="editing ? 'Ubah Contoh Agunan' : 'Tambah Contoh Agunan'" />
    <AppLayout>
        <form class="form-dense space-y-4" novalidate @submit.prevent="submit">
            <Card>
                <CardHeader class="flex flex-row flex-wrap items-center justify-between gap-2 space-y-0">
                    <CardTitle class="flex min-w-0 items-center gap-2">
                        <Button variant="ghost" size="icon" type="button" data-testid="collateral-form-back" @click="back">
                            <ArrowLeft class="size-4" />
                        </Button>
                        <span class="truncate">{{ editing ? 'Ubah' : 'Tambah' }} Contoh Agunan</span>
                    </CardTitle>
                </CardHeader>
                <CardContent class="space-y-3">
                    <p class="text-sm font-medium">Identitas Agunan</p>
                    <div class="grid gap-[var(--field-gap)] sm:grid-cols-2 lg:grid-cols-4">
                        <div class="space-y-[var(--item-gap)]">
                            <Label for="f-collateral-id">Agunan ID</Label>
                            <Input id="f-collateral-id" v-model="form.collateral_id" data-testid="collateral-form-id" />
                            <p v-if="form.errors.collateral_id" class="text-xs font-medium text-destructive">
                                {{ form.errors.collateral_id }}
                            </p>
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label for="f-paripasu">Paripasu (%)</Label>
                            <Input id="f-paripasu" v-model="form.paripasu" type="number" min="0" max="100" data-testid="collateral-form-paripasu" />
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label for="f-file-number">Nomor Berkas</Label>
                            <Input
                                id="f-file-number"
                                v-model="form.file_number"
                                :disabled="form.auto_number"
                                data-testid="collateral-form-file-number"
                            />
                        </div>
                        <label class="flex h-8 items-center gap-2 self-end rounded-md border px-3 text-sm normal-case tracking-normal">
                            <Checkbox v-model="form.auto_number" data-testid="collateral-form-auto-number" />
                            <span>Nomor otomatis</span>
                        </label>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Jenis, Pengikatan &amp; Dokumen</CardTitle></CardHeader>
                <CardContent class="grid gap-[var(--field-gap)] sm:grid-cols-2 lg:grid-cols-3">
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Jenis Agunan</Label>
                        <Combobox
                            v-model="form.collateral_type_code"
                            :options="props.collateralTypes"
                            placeholder="Pilih jenis agunan"
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
                            placeholder="Belum diikat"
                            data-testid="collateral-form-binding"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-doc">No. Dokumen (SHM/BPKB/dll)</Label>
                        <Input id="f-doc" v-model="form.document_number" data-testid="collateral-form-document" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Status / Bukti Kepemilikan</Label>
                        <Combobox
                            v-if="ownershipOptions.length"
                            v-model="form.ownership"
                            :options="ownershipOptions"
                            placeholder="Pilih status kepemilikan"
                            data-testid="collateral-form-ownership"
                        />
                        <Input
                            v-else
                            v-model="form.ownership"
                            placeholder="Belum ada referensi untuk jenis ini"
                            data-testid="collateral-form-ownership"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-rank">Peringkat Surat Berharga</Label>
                        <Input id="f-rank" disabled placeholder="Belum dipakai" data-testid="collateral-form-rank" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-agency">Pemeringkat</Label>
                        <Input id="f-agency" disabled placeholder="Belum dipakai" data-testid="collateral-form-agency" />
                    </div>
                    <div class="space-y-[var(--item-gap)] sm:col-span-2 lg:col-span-3">
                        <Label for="f-desc">Keterangan Agunan</Label>
                        <Input id="f-desc" v-model="form.description" maxlength="255" data-testid="collateral-form-description" />
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Pemilik &amp; Lokasi</CardTitle></CardHeader>
                <CardContent class="grid gap-[var(--field-gap)] sm:grid-cols-2 lg:grid-cols-3">
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-owner">Nama Pemilik</Label>
                        <Input id="f-owner" v-model="form.owner_name" data-testid="collateral-form-owner" />
                    </div>
                    <div class="space-y-[var(--item-gap)] sm:col-span-1 lg:col-span-2">
                        <Label for="f-owner-address">Alamat Agunan</Label>
                        <Input id="f-owner-address" v-model="form.owner_address" data-testid="collateral-form-owner-address" />
                    </div>
                    <label class="flex h-8 items-center gap-2 rounded-md border px-3 text-sm normal-case tracking-normal sm:col-span-2 lg:col-span-3">
                        <Checkbox v-model="form.owner_same_as_cif" data-testid="collateral-form-same-cif" />
                        <span>Nama dan alamat sesuai CIF</span>
                    </label>

                    <div class="space-y-[var(--item-gap)]">
                        <Label>Kabupaten/Kota</Label>
                        <Combobox v-model="regency" :options="regencies" placeholder="Pilih kabupaten" data-testid="collateral-form-regency" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Kecamatan</Label>
                        <Combobox v-model="district" :options="districts" placeholder="Pilih kecamatan" data-testid="collateral-form-district" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Kelurahan/Desa</Label>
                        <Combobox v-model="village" :options="villages" placeholder="Pilih kelurahan" data-testid="collateral-form-village" />
                    </div>
                    <div class="space-y-[var(--item-gap)] sm:col-span-2 lg:col-span-3">
                        <Label for="f-region">Lokasi Agunan (kode dikirim ke CBS)</Label>
                        <Input
                            id="f-region"
                            :model-value="village ? `${form.region_code} · ${regency} — ${district}, ${villages.find((v) => v.value === village)?.village ?? ''}` : (form.region_label || form.region_code || '—')"
                            readonly
                            disabled
                            data-testid="collateral-form-region"
                        />
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row flex-wrap items-center justify-between gap-2 space-y-0">
                    <CardTitle>Nilai Agunan</CardTitle>
                    <span class="text-xs text-muted-foreground">diisi Staff Analis setelah survey · tanpa perhitungan</span>
                </CardHeader>
                <CardContent class="grid gap-[var(--field-gap)] sm:grid-cols-2 lg:grid-cols-3">
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-guarantee">{{ guaranteeLabel }}</Label>
                        <Input id="f-guarantee" v-model="form.value_guarantee" type="number" min="0" data-testid="collateral-form-guarantee" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-adjust">Adjusment</Label>
                        <Input id="f-adjust" v-model="form.value_adjustment" type="number" min="0" data-testid="collateral-form-adjustment" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-fair">Nilai Wajar/Pasar</Label>
                        <Input id="f-fair" v-model="form.value_fair" type="number" min="0" data-testid="collateral-form-fair" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-njop">NJOP</Label>
                        <Input id="f-njop" v-model="form.value_njop" type="number" min="0" data-testid="collateral-form-njop" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-appraisal">Nilai Taksasi (Internal)</Label>
                        <Input id="f-appraisal" v-model="form.value_appraisal" type="number" min="0" data-testid="collateral-form-appraisal" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-independent">Nilai Apraisal Independen</Label>
                        <Input id="f-independent" v-model="form.value_independent" type="number" min="0" data-testid="collateral-form-independent" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-appraiser">Nama Penilai (Internal)</Label>
                        <Input id="f-appraiser" v-model="form.appraiser_name" data-testid="collateral-form-appraiser" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Tgl Penilaian (Internal)</Label>
                        <DatePicker v-model="form.appraised_at" data-testid="collateral-form-appraised-at" />
                    </div>
                    <div class="space-y-[var(--item-gap)]" />
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="f-ind-appraiser">Nama Penilai (Independen)</Label>
                        <Input id="f-ind-appraiser" v-model="form.independent_appraiser_name" data-testid="collateral-form-independent-appraiser" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Tgl Penilaian (Independen)</Label>
                        <DatePicker v-model="form.independent_appraised_at" data-testid="collateral-form-independent-appraised-at" />
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Kondisi &amp; Asuransi</CardTitle></CardHeader>
                <CardContent class="grid gap-[var(--field-gap)] sm:grid-cols-2 lg:grid-cols-3">
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Kondisi Agunan</Label>
                        <Combobox
                            v-model="form.condition_code"
                            :options="props.conditions"
                            placeholder="Pilih kondisi"
                            data-testid="collateral-form-condition"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Tgl Kondisi</Label>
                        <DatePicker v-model="form.condition_date" data-testid="collateral-form-condition-date" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Diasuransikan</Label>
                        <Combobox v-model="form.insured" :options="INSURED_OPTIONS" data-testid="collateral-form-insured" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Metode Hitung PPAP</Label>
                        <Combobox
                            v-model="form.ppap_code"
                            :options="props.methods"
                            placeholder="Pilih metode"
                            data-testid="collateral-form-ppap"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Tgl Mulai Asuransi</Label>
                        <DatePicker v-model="form.insurance_start_date" data-testid="collateral-form-insurance-start" />
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
