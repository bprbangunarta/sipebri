<script setup>
import { computed, ref, watch } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, FileInput, Plus, Save, Send, ShieldCheck, Trash2, X } from 'lucide-vue-next';

import AppLayout from '@/components/layout/AppLayout.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardFooter from '@/components/ui/CardFooter.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
import Combobox from '@/components/ui/Combobox.vue';
import DecimalInput from '@/components/ui/DecimalInput.vue';
import Dialog from '@/components/ui/Dialog.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import NumberInput from '@/components/ui/NumberInput.vue';
import { rupiah } from '@/constants/committee';

/** Berkas pengajuan: data pengajuan + agunan. Status awal DRAFT, lalu diajukan. */
const props = defineProps({
    record: { type: Object, required: true },
    collaterals: { type: Array, default: () => [] },
    collateralOptions: { type: Array, default: () => [] },
    usageTypes: { type: Array, default: () => [] },
    offices: { type: Array, default: () => [] },
    products: { type: Array, default: () => [] },
    institutions: { type: Array, default: () => [] },
    methods: { type: Array, default: () => [] },
    installments: { type: Array, default: () => [] },
    supervisors: { type: Array, default: () => [] },
    categoryMap: { type: Object, default: () => ({}) },
    collateralTypes: { type: Array, default: () => [] },
    bindingTypes: { type: Array, default: () => [] },
    regionOptions: { type: Array, default: () => [] },
    canManage: { type: Boolean, default: false },
});

const STATUS_TONE = {
    DRAFT: 'border-slate-500/30 bg-slate-500/15 text-slate-700 dark:text-slate-300',
    DIAJUKAN: 'border-blue-500/30 bg-blue-500/15 text-blue-700 dark:text-blue-400',
    ANALISA: 'border-purple-500/30 bg-purple-500/15 text-purple-700 dark:text-purple-400',
    KOMITE: 'border-amber-500/30 bg-amber-500/15 text-amber-700 dark:text-amber-400',
    DISETUJUI: 'border-emerald-500/30 bg-emerald-500/15 text-emerald-700 dark:text-emerald-400',
    DITOLAK: 'border-red-500/30 bg-red-500/15 text-red-700 dark:text-red-400',
    DIBATALKAN: 'border-slate-500/30 bg-slate-500/15 text-slate-700 dark:text-slate-400',
    REALISASI: 'border-teal-500/30 bg-teal-500/15 text-teal-700 dark:text-teal-400',
};

const form = useForm({
    application_date: props.record.application_date ?? '',
    product_id: props.record.product_id ?? '',
    committee_path_id: props.record.committee_path_id ?? '',
    requested_amount: props.record.requested_amount ?? 0,
    requested_tenor: props.record.requested_tenor ?? 0,
    method_id: props.record.method_id ?? '',
    installment_id: props.record.installment_id ?? '',
    interest_rate: props.record.interest_rate ?? '',
    usage_type: props.record.usage_type ?? '',
    institution_id: props.record.institution_id ?? '',
    marketing: props.record.marketing ?? '',
    office_id: props.record.office_id ?? '',
    supervisor_id: props.record.supervisor_id ?? '',
});

/** Kategori mengikuti jalur komite produk terpilih (sama seperti Simulasi Kewenangan Komite). */
const categoryOptions = computed(
    () => props.categoryMap[String(form.product_id)] ?? props.categoryMap.global ?? [],
);

watch(() => form.product_id, () => {
    if (!categoryOptions.value.some((o) => o.value === form.committee_path_id)) form.committee_path_id = '';
});

const attachForm = useForm({ collateral_simulation_id: '' });
const actionForm = useForm({});

const newCollateral = useForm({
    collateral_type_code: '',
    binding_type_code: '',
    document_number: '',
    owner_name: '',
    owner_address: '',
    region_code: '',
    region_label: '',
    description: '',
});

const showCollateralForm = ref(false);

const openCollateralForm = () => {
    newCollateral.reset();
    newCollateral.clearErrors();
    showCollateralForm.value = true;
};

const onRegion = (value) => {
    newCollateral.region_code = value;
    newCollateral.region_label = props.regionOptions.find((o) => o.value === value)?.label ?? '';
};

const save = () => form.put(`/loan-simulation/${props.record.id}`, { preserveScroll: true });

const attach = () => {
    if (!attachForm.collateral_simulation_id) return;
    attachForm.post(`/loan-simulation/${props.record.id}/collaterals`, {
        preserveScroll: true,
        onSuccess: () => attachForm.reset(),
    });
};

const saveCollateral = () =>
    newCollateral.post(`/loan-simulation/${props.record.id}/collaterals/new`, {
        preserveScroll: true,
        onSuccess: () => (showCollateralForm.value = false),
    });

const detach = (id) =>
    actionForm.delete(`/loan-simulation/${props.record.id}/collaterals/${id}`, { preserveScroll: true });

const submitFile = () => actionForm.post(`/loan-simulation/${props.record.id}/confirm`, { preserveScroll: true });

const totalAppraisal = computed(() =>
    props.collaterals.reduce((sum, row) => sum + Number(row.appraisal_value ?? 0), 0),
);

const ready = computed(() => Object.values(props.record.checklist ?? {}).every(Boolean));
</script>

<template>
    <Head :title="`Berkas ${props.record.application_code}`" />
    <AppLayout>
        <div class="space-y-4" data-testid="loan-detail-page">
            <div class="flex flex-wrap items-center gap-3 border-b pb-3">
                <Button
                    variant="ghost"
                    size="icon"
                    type="button"
                    data-testid="loan-detail-back"
                    @click="router.visit('/loan-simulation')"
                >
                    <ArrowLeft class="size-4" />
                </Button>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-mono text-base font-semibold tracking-tight">
                            {{ props.record.application_code }}
                        </span>
                        <span
                            class="rounded-full border px-2.5 py-0.5 text-xs font-semibold"
                            :class="STATUS_TONE[props.record.status] ?? ''"
                            data-testid="loan-detail-status"
                        >
                            {{ props.record.status }}
                        </span>
                    </div>
                    <p class="truncate text-sm text-muted-foreground">
                        {{ props.record.full_name }} · {{ props.record.nik }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-muted-foreground">Plafon Diajukan</p>
                    <p class="text-sm font-semibold tabular-nums">{{ rupiah(props.record.requested_amount) }}</p>
                </div>
                <Button
                    v-if="props.canManage && props.record.status === 'DRAFT'"
                    size="sm"
                    :disabled="!ready || actionForm.processing"
                    data-testid="loan-detail-submit"
                    @click="submitFile"
                >
                    <Send class="size-4" /> Ajukan
                </Button>
            </div>

            <form class="form-dense" novalidate @submit.prevent="save">
                <Card>
                    <CardHeader><CardTitle>Data Pengajuan</CardTitle></CardHeader>
                    <CardContent class="grid gap-[var(--field-gap)] sm:grid-cols-2 lg:grid-cols-4">
                        <div class="space-y-[var(--item-gap)]">
                            <Label>Produk <span class="text-destructive">*</span></Label>
                            <Combobox
                                v-model="form.product_id"
                                :options="props.products"
                                placeholder="-- Pilih --"
                                data-testid="loan-detail-product"
                            />
                            <p v-if="form.errors.product_id" class="text-xs font-medium text-destructive">
                                {{ form.errors.product_id }}
                            </p>
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label>Kategori <span class="text-destructive">*</span></Label>
                            <Combobox
                                v-model="form.committee_path_id"
                                :options="categoryOptions"
                                :disabled="!form.product_id"
                                :placeholder="form.product_id ? '-- Pilih --' : 'Pilih produk dahulu'"
                                data-testid="loan-detail-category"
                            />
                            <p v-if="form.errors.committee_path_id" class="text-xs font-medium text-destructive">
                                {{ form.errors.committee_path_id }}
                            </p>
                            <p v-else-if="form.product_id && !categoryOptions.length" class="text-xs text-muted-foreground">
                                Produk ini belum punya jalur komite aktif.
                            </p>
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label for="d-amount">Plafon <span class="text-destructive">*</span></Label>
                            <NumberInput id="d-amount" v-model="form.requested_amount" data-testid="loan-detail-amount" />
                            <p v-if="form.errors.requested_amount" class="text-xs font-medium text-destructive">
                                {{ form.errors.requested_amount }}
                            </p>
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label for="d-tenor">JK Kredit (bln) <span class="text-destructive">*</span></Label>
                            <NumberInput id="d-tenor" v-model="form.requested_tenor" data-testid="loan-detail-tenor" />
                            <p v-if="form.errors.requested_tenor" class="text-xs font-medium text-destructive">
                                {{ form.errors.requested_tenor }}
                            </p>
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label>Sistem Bunga <span class="text-destructive">*</span></Label>
                            <Combobox
                                v-model="form.method_id"
                                :options="props.methods"
                                placeholder="-- Pilih --"
                                data-testid="loan-detail-method"
                            />
                            <p v-if="form.errors.method_id" class="text-xs font-medium text-destructive">
                                {{ form.errors.method_id }}
                            </p>
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label>Sistem Cicilan <span class="text-destructive">*</span></Label>
                            <Combobox
                                v-model="form.installment_id"
                                :options="props.installments"
                                placeholder="-- Pilih --"
                                data-testid="loan-detail-installment"
                            />
                            <p v-if="form.errors.installment_id" class="text-xs font-medium text-destructive">
                                {{ form.errors.installment_id }}
                            </p>
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label for="d-rate">Suku Bunga (%) <span class="text-destructive">*</span></Label>
                            <DecimalInput id="d-rate" v-model="form.interest_rate" data-testid="loan-detail-rate" />
                            <p v-if="form.errors.interest_rate" class="text-xs font-medium text-destructive">
                                {{ form.errors.interest_rate }}
                            </p>
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label>Penggunaan <span class="text-destructive">*</span></Label>
                            <Combobox
                                v-model="form.usage_type"
                                :options="props.usageTypes"
                                placeholder="-- Pilih --"
                                data-testid="loan-detail-usage"
                            />
                            <p v-if="form.errors.usage_type" class="text-xs font-medium text-destructive">
                                {{ form.errors.usage_type }}
                            </p>
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label>Resort / Instansi</Label>
                            <Combobox
                                v-model="form.institution_id"
                                :options="props.institutions"
                                placeholder="(Opsional)"
                                data-testid="loan-detail-institution"
                            />
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label for="d-marketing">Marketing</Label>
                            <Input
                                id="d-marketing"
                                v-model="form.marketing"
                                placeholder="(Opsional)"
                                class="uppercase placeholder:normal-case"
                                data-testid="loan-detail-marketing"
                            />
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label>Wilayah / Kantor <span class="text-destructive">*</span></Label>
                            <Combobox
                                v-model="form.office_id"
                                :options="props.offices"
                                placeholder="-- Pilih --"
                                data-testid="loan-detail-office"
                            />
                            <p v-if="form.errors.office_id" class="text-xs font-medium text-destructive">
                                {{ form.errors.office_id }}
                            </p>
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label>Kasi Analis <span class="text-destructive">*</span></Label>
                            <Combobox
                                v-model="form.supervisor_id"
                                :options="props.supervisors"
                                placeholder="-- Pilih --"
                                data-testid="loan-detail-supervisor"
                            />
                            <p v-if="form.errors.supervisor_id" class="text-xs font-medium text-destructive">
                                {{ form.errors.supervisor_id }}
                            </p>
                        </div>
                    </CardContent>
                    <CardFooter class="justify-between">
                        <Button
                            variant="outline"
                            size="sm"
                            type="button"
                            data-testid="loan-detail-cancel"
                            @click="router.visit('/loan-simulation')"
                        >
                            <X class="size-4" /> Batal
                        </Button>
                        <Button
                            v-if="props.canManage"
                            size="sm"
                            type="submit"
                            :disabled="form.processing"
                            data-testid="loan-detail-save"
                        >
                            <Save class="size-4" /> {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                        </Button>
                    </CardFooter>
                </Card>
            </form>

            <Card data-testid="loan-detail-collaterals">
                <CardHeader class="flex flex-row flex-wrap items-center justify-between gap-2 space-y-0">
                    <CardTitle>Data Agunan</CardTitle>
                    <span class="text-xs text-muted-foreground">
                        Total taksasi
                        <span class="ml-1 text-sm font-semibold tabular-nums text-foreground">
                            {{ rupiah(totalAppraisal) }}
                        </span>
                    </span>
                </CardHeader>
                <CardContent class="form-dense space-y-3">
                    <div v-if="props.canManage" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <Combobox
                            v-model="attachForm.collateral_simulation_id"
                            :options="props.collateralOptions"
                            placeholder="-- Pilih --"
                            class="flex-1"
                            data-testid="loan-detail-collateral-select"
                        />
                        <div class="flex items-center gap-2">
                            <Button
                                size="sm"
                                variant="outline"
                                :disabled="attachForm.processing"
                                data-testid="loan-detail-collateral-attach"
                                @click="attach"
                            >
                                <FileInput class="size-4" /> Lekatkan
                            </Button>
                            <Button size="sm" data-testid="loan-detail-collateral-new" @click="openCollateralForm">
                                <Plus class="size-4" /> Tambah
                            </Button>
                        </div>
                    </div>

                    <div v-if="!props.collaterals.length" class="rounded-lg border border-dashed p-8 text-center">
                        <ShieldCheck class="mx-auto size-6 text-muted-foreground" />
                        <p class="mt-2 text-sm font-medium">Belum ada agunan</p>
                        <p class="text-xs text-muted-foreground">
                            Lekatkan agunan yang dipakai pada berkas pengajuan ini.
                        </p>
                    </div>
                    <div v-else class="overflow-x-auto rounded-lg border">
                        <table class="w-full text-sm">
                            <thead class="bg-muted/40">
                                <tr class="text-left">
                                    <th class="whitespace-nowrap px-3 py-2 text-[11px] font-medium uppercase tracking-wider text-muted-foreground">Agunan</th>
                                    <th class="whitespace-nowrap px-3 py-2 text-[11px] font-medium uppercase tracking-wider text-muted-foreground">Pemilik</th>
                                    <th class="hidden whitespace-nowrap px-3 py-2 text-[11px] font-medium uppercase tracking-wider text-muted-foreground md:table-cell">Keterangan</th>
                                    <th class="whitespace-nowrap px-3 py-2 text-right text-[11px] font-medium uppercase tracking-wider text-muted-foreground">Taksasi</th>
                                    <th class="w-10 px-3 py-2" />
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border/60">
                                <tr
                                    v-for="row in props.collaterals"
                                    :key="row.id"
                                    class="transition-colors hover:bg-muted/30"
                                    :data-testid="`loan-detail-collateral-${row.id}`"
                                >
                                    <td class="whitespace-nowrap px-3 py-2 font-mono text-xs font-semibold">
                                        {{ row.collateral_id ?? `#${row.id}` }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-2">{{ row.owner_name ?? '—' }}</td>
                                    <td class="hidden px-3 py-2 text-xs text-muted-foreground md:table-cell">
                                        {{ row.description ?? '—' }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-2 text-right tabular-nums">
                                        {{ rupiah(row.appraisal_value) }}
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        <Button
                                            v-if="props.canManage"
                                            variant="ghost"
                                            size="icon"
                                            class="text-destructive transition-colors hover:text-destructive"
                                            :data-testid="`loan-detail-collateral-detach-${row.id}`"
                                            @click="detach(row.id)"
                                        >
                                            <Trash2 class="size-3.5" />
                                        </Button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            <!-- Agunan baru langsung dari berkas -->
            <Dialog
                :open="showCollateralForm"
                title="Tambah Agunan"
                class="max-w-3xl"
                @update:open="showCollateralForm = $event"
            >
                <div class="form-dense grid gap-[var(--field-gap)] sm:grid-cols-2 lg:grid-cols-3">
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Jenis Agunan <span class="text-destructive">*</span></Label>
                        <Combobox
                            v-model="newCollateral.collateral_type_code"
                            :options="props.collateralTypes"
                            placeholder="-- Pilih --"
                            data-testid="collateral-modal-type"
                        />
                        <p v-if="newCollateral.errors.collateral_type_code" class="text-xs font-medium text-destructive">
                            {{ newCollateral.errors.collateral_type_code }}
                        </p>
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Jenis Pengikatan</Label>
                        <Combobox
                            v-model="newCollateral.binding_type_code"
                            :options="props.bindingTypes"
                            placeholder="(Opsional)"
                            data-testid="collateral-modal-binding"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="m-doc">No. Dokumen <span class="text-destructive">*</span></Label>
                        <Input id="m-doc" v-model="newCollateral.document_number" class="uppercase placeholder:normal-case" data-testid="collateral-modal-document" />
                        <p v-if="newCollateral.errors.document_number" class="text-xs font-medium text-destructive">
                            {{ newCollateral.errors.document_number }}
                        </p>
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="m-owner">Nama Pemilik <span class="text-destructive">*</span></Label>
                        <Input id="m-owner" v-model="newCollateral.owner_name" class="uppercase placeholder:normal-case" data-testid="collateral-modal-owner" />
                        <p v-if="newCollateral.errors.owner_name" class="text-xs font-medium text-destructive">
                            {{ newCollateral.errors.owner_name }}
                        </p>
                    </div>
                    <div class="space-y-[var(--item-gap)] sm:col-span-2">
                        <Label for="m-address">Alamat Agunan <span class="text-destructive">*</span></Label>
                        <Input id="m-address" v-model="newCollateral.owner_address" class="uppercase placeholder:normal-case" data-testid="collateral-modal-address" />
                        <p v-if="newCollateral.errors.owner_address" class="text-xs font-medium text-destructive">
                            {{ newCollateral.errors.owner_address }}
                        </p>
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label>Lokasi Agunan <span class="text-destructive">*</span></Label>
                        <Combobox
                            :model-value="newCollateral.region_code"
                            :options="props.regionOptions"
                            placeholder="-- Pilih --"
                            data-testid="collateral-modal-region"
                            @update:model-value="onRegion"
                        />
                        <p v-if="newCollateral.errors.region_code" class="text-xs font-medium text-destructive">
                            {{ newCollateral.errors.region_code }}
                        </p>
                    </div>
                    <div class="space-y-[var(--item-gap)] sm:col-span-2">
                        <Label for="m-desc">Keterangan Agunan <span class="text-destructive">*</span></Label>
                        <Input id="m-desc" v-model="newCollateral.description" class="uppercase placeholder:normal-case" data-testid="collateral-modal-description" />
                        <p v-if="newCollateral.errors.description" class="text-xs font-medium text-destructive">
                            {{ newCollateral.errors.description }}
                        </p>
                    </div>
                </div>

                <template #footer>
                    <Button variant="outline" size="sm" data-testid="collateral-modal-cancel" @click="showCollateralForm = false">
                        <X class="size-4" /> Batal
                    </Button>
                    <Button
                        size="sm"
                        :disabled="newCollateral.processing"
                        data-testid="collateral-modal-save"
                        @click="saveCollateral"
                    >
                        <Save class="size-4" /> {{ newCollateral.processing ? 'Menyimpan...' : 'Simpan' }}
                    </Button>
                </template>
            </Dialog>
        </div>
    </AppLayout>
</template>
