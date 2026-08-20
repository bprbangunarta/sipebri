<script setup>
import { computed, ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Check,
    ClipboardCheck,
    FileSignature,
    Plus,
    Save,
    ShieldCheck,
    Trash2,
    Users,
} from 'lucide-vue-next';

import AppLayout from '@/components/layout/AppLayout.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardFooter from '@/components/ui/CardFooter.vue';
import Combobox from '@/components/ui/Combobox.vue';
import DatePicker from '@/components/ui/DatePicker.vue';
import DecimalInput from '@/components/ui/DecimalInput.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import NumberInput from '@/components/ui/NumberInput.vue';
import { rupiah } from '@/constants/committee';

/** Berkas pengajuan: tahapan Pengajuan → Jaminan → Surveyor → Konfirmasi. */
const props = defineProps({
    record: { type: Object, required: true },
    collaterals: { type: Array, default: () => [] },
    collateralOptions: { type: Array, default: () => [] },
    statuses: { type: Array, default: () => [] },
    usageTypes: { type: Array, default: () => [] },
    offices: { type: Array, default: () => [] },
    products: { type: Array, default: () => [] },
    institutions: { type: Array, default: () => [] },
    methods: { type: Array, default: () => [] },
    installments: { type: Array, default: () => [] },
    supervisors: { type: Array, default: () => [] },
    surveyors: { type: Array, default: () => [] },
    canManage: { type: Boolean, default: false },
});

const LABEL = 'text-[11px] font-medium uppercase tracking-wider text-muted-foreground';

const STATUS_TONE = {
    DIAJUKAN: 'border-blue-500/30 bg-blue-500/15 text-blue-700 dark:text-blue-400',
    ANALISA: 'border-purple-500/30 bg-purple-500/15 text-purple-700 dark:text-purple-400',
    KOMITE: 'border-amber-500/30 bg-amber-500/15 text-amber-700 dark:text-amber-400',
    DISETUJUI: 'border-emerald-500/30 bg-emerald-500/15 text-emerald-700 dark:text-emerald-400',
    DITOLAK: 'border-red-500/30 bg-red-500/15 text-red-700 dark:text-red-400',
    DIBATALKAN: 'border-slate-500/30 bg-slate-500/15 text-slate-700 dark:text-slate-400',
    REALISASI: 'border-teal-500/30 bg-teal-500/15 text-teal-700 dark:text-teal-400',
};

const STEPS = [
    { id: 'pengajuan', label: 'Data Pengajuan', icon: FileSignature, check: 'pengajuan' },
    { id: 'jaminan', label: 'Data Jaminan', icon: ShieldCheck, check: 'jaminan' },
    { id: 'surveyor', label: 'Data Surveyor', icon: Users, check: 'surveyor' },
    { id: 'konfirmasi', label: 'Konfirmasi', icon: ClipboardCheck, check: null },
];

const CHECK_LABELS = {
    nasabah: 'Nomor KTP terverifikasi di sistem data nasabah',
    pengajuan: 'Informasi Pengajuan (produk, plafon, jangka waktu)',
    jaminan: 'Informasi Jaminan (minimal satu agunan)',
    surveyor: 'Informasi Surveyor (kantor & surveyor)',
};

const tab = ref('pengajuan');

const form = useForm({
    application_date: props.record.application_date ?? '',
    office_id: props.record.office_id ?? '',
    product_id: props.record.product_id ?? '',
    institution_id: props.record.institution_id ?? '',
    requested_amount: props.record.requested_amount ?? 0,
    requested_tenor: props.record.requested_tenor ?? 0,
    tenor_principal: props.record.tenor_principal ?? '',
    tenor_interest: props.record.tenor_interest ?? '',
    usage_type: props.record.usage_type ?? '',
    method_id: props.record.method_id ?? '',
    installment_id: props.record.installment_id ?? '',
    interest_rate: props.record.interest_rate ?? '',
    provision_rate: props.record.provision_rate ?? '',
    admin_rate: props.record.admin_rate ?? '',
    purpose: props.record.purpose ?? '',
    note: props.record.note ?? '',
});

const surveyForm = useForm({
    office_id: props.record.office_id ?? '',
    supervisor_id: props.record.supervisor_id ?? '',
    surveyor_id: props.record.surveyor_id ?? '',
});

const attachForm = useForm({ collateral_simulation_id: '' });
const actionForm = useForm({});

const saveApplication = () => form.put(`/loan-simulation/${props.record.id}`, { preserveScroll: true });
const saveSurvey = () => surveyForm.put(`/loan-simulation/${props.record.id}/survey`, { preserveScroll: true });

const attach = () => {
    if (!attachForm.collateral_simulation_id) return;
    attachForm.post(`/loan-simulation/${props.record.id}/collaterals`, {
        preserveScroll: true,
        onSuccess: () => attachForm.reset(),
    });
};

const detach = (id) =>
    actionForm.delete(`/loan-simulation/${props.record.id}/collaterals/${id}`, { preserveScroll: true });

const confirmFile = () =>
    actionForm.post(`/loan-simulation/${props.record.id}/confirm`, { preserveScroll: true });

const totalAppraisal = computed(() =>
    props.collaterals.reduce((sum, row) => sum + Number(row.appraisal_value ?? 0), 0),
);

const allChecked = computed(() => Object.values(props.record.checklist ?? {}).every(Boolean));

const stepDone = (step) => (step.check ? Boolean(props.record.checklist?.[step.check]) : allChecked.value);

</script>

<template>
    <Head :title="`Berkas ${props.record.application_code}`" />
    <AppLayout>
        <div class="space-y-4" data-testid="loan-detail-page">
            <!-- Kepala berkas -->
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
                    <p :class="LABEL">Plafon Diajukan</p>
                    <p class="text-sm font-semibold tabular-nums">{{ rupiah(props.record.requested_amount) }}</p>
                </div>
            </div>

            <!-- Tahapan berkas -->
            <div class="-mx-1 flex items-stretch gap-2 overflow-x-auto px-1 pb-1">
                <button
                    v-for="(step, index) in STEPS"
                    :key="step.id"
                    type="button"
                    class="group flex min-w-[168px] flex-1 items-center gap-2.5 rounded-lg border px-3 py-2 text-left transition-colors"
                    :class="
                        tab === step.id
                            ? 'border-primary bg-primary/5'
                            : 'border-border/60 bg-card hover:border-border hover:bg-muted/40'
                    "
                    :data-testid="`loan-detail-tab-${step.id}`"
                    @click="tab = step.id"
                >
                    <span
                        class="flex size-7 shrink-0 items-center justify-center rounded-full border text-xs font-semibold transition-colors"
                        :class="
                            stepDone(step)
                                ? 'border-emerald-500/40 bg-emerald-500/15 text-emerald-700 dark:text-emerald-400'
                                : tab === step.id
                                    ? 'border-primary bg-primary text-primary-foreground'
                                    : 'border-border text-muted-foreground'
                        "
                    >
                        <Check v-if="stepDone(step)" class="size-3.5" />
                        <template v-else>{{ index + 1 }}</template>
                    </span>
                    <span class="min-w-0">
                        <span class="block truncate text-xs font-semibold">{{ step.label }}</span>
                        <span class="block text-[11px] text-muted-foreground">
                            {{ stepDone(step) ? 'Lengkap' : 'Belum lengkap' }}
                        </span>
                    </span>
                </button>
            </div>

            <!-- Tahap 1: Data Pengajuan -->
            <form
                v-if="tab === 'pengajuan'"
                class="form-dense space-y-4"
                novalidate
                @submit.prevent="saveApplication"
            >
                <Card>
                    <CardHeader><CardTitle>Informasi Dasar</CardTitle></CardHeader>
                    <CardContent class="grid gap-[var(--field-gap)] sm:grid-cols-2 lg:grid-cols-3">
                        <div class="space-y-[var(--item-gap)]">
                            <Label :class="LABEL">Tanggal Pengajuan <span class="text-destructive">*</span></Label>
                            <DatePicker v-model="form.application_date" data-testid="loan-detail-date" />
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label :class="LABEL">Produk <span class="text-destructive">*</span></Label>
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
                            <Label :class="LABEL">Penggunaan</Label>
                            <Combobox
                                v-model="form.usage_type"
                                :options="props.usageTypes"
                                placeholder="(Opsional)"
                                data-testid="loan-detail-usage"
                            />
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label :class="LABEL">Resort / Instansi</Label>
                            <Combobox
                                v-model="form.institution_id"
                                :options="props.institutions"
                                placeholder="(Opsional)"
                                data-testid="loan-detail-institution"
                            />
                        </div>
                        <div class="space-y-[var(--item-gap)] lg:col-span-2">
                            <Label for="d-purpose" :class="LABEL">Tujuan Penggunaan</Label>
                            <Input
                                id="d-purpose"
                                v-model="form.purpose"
                                placeholder="(Opsional)"
                                class="uppercase"
                                data-testid="loan-detail-purpose"
                            />
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader><CardTitle>Parameter Kredit</CardTitle></CardHeader>
                    <CardContent class="grid gap-[var(--field-gap)] sm:grid-cols-2 lg:grid-cols-4">
                        <div class="space-y-[var(--item-gap)]">
                            <Label for="d-amount" :class="LABEL">Plafon <span class="text-destructive">*</span></Label>
                            <NumberInput id="d-amount" v-model="form.requested_amount" data-testid="loan-detail-amount" />
                            <p v-if="form.errors.requested_amount" class="text-xs font-medium text-destructive">
                                {{ form.errors.requested_amount }}
                            </p>
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label for="d-tenor" :class="LABEL">JK Kredit (bln) <span class="text-destructive">*</span></Label>
                            <NumberInput id="d-tenor" v-model="form.requested_tenor" data-testid="loan-detail-tenor" />
                            <p v-if="form.errors.requested_tenor" class="text-xs font-medium text-destructive">
                                {{ form.errors.requested_tenor }}
                            </p>
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label for="d-tenor-principal" :class="LABEL">JK Pokok (bln)</Label>
                            <NumberInput
                                id="d-tenor-principal"
                                v-model="form.tenor_principal"
                                placeholder="(Opsional)"
                                data-testid="loan-detail-tenor-principal"
                            />
                            <p v-if="form.errors.tenor_principal" class="text-xs font-medium text-destructive">
                                {{ form.errors.tenor_principal }}
                            </p>
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label for="d-tenor-interest" :class="LABEL">JW Bunga (bln)</Label>
                            <NumberInput
                                id="d-tenor-interest"
                                v-model="form.tenor_interest"
                                placeholder="(Opsional)"
                                data-testid="loan-detail-tenor-interest"
                            />
                            <p v-if="form.errors.tenor_interest" class="text-xs font-medium text-destructive">
                                {{ form.errors.tenor_interest }}
                            </p>
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label :class="LABEL">Sistem Bunga</Label>
                            <Combobox
                                v-model="form.method_id"
                                :options="props.methods"
                                placeholder="(Opsional)"
                                data-testid="loan-detail-method"
                            />
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label :class="LABEL">Sistem Cicilan</Label>
                            <Combobox
                                v-model="form.installment_id"
                                :options="props.installments"
                                placeholder="(Opsional)"
                                data-testid="loan-detail-installment"
                            />
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label for="d-rate" :class="LABEL">Suku Bunga (%)</Label>
                            <DecimalInput id="d-rate" v-model="form.interest_rate" data-testid="loan-detail-rate" />
                        </div>
                        <div class="grid grid-cols-2 gap-[var(--field-gap)]">
                            <div class="space-y-[var(--item-gap)]">
                                <Label for="d-provision" :class="LABEL">Provisi (%)</Label>
                                <DecimalInput id="d-provision" v-model="form.provision_rate" data-testid="loan-detail-provision" />
                            </div>
                            <div class="space-y-[var(--item-gap)]">
                                <Label for="d-admin" :class="LABEL">Admin (%)</Label>
                                <DecimalInput id="d-admin" v-model="form.admin_rate" data-testid="loan-detail-admin" />
                            </div>
                        </div>
                        <div class="space-y-[var(--item-gap)] sm:col-span-2 lg:col-span-4">
                            <Label for="d-note" :class="LABEL">Keterangan</Label>
                            <Input
                                id="d-note"
                                v-model="form.note"
                                placeholder="(Opsional)"
                                class="uppercase"
                                data-testid="loan-detail-note"
                            />
                        </div>
                    </CardContent>
                    <CardFooter class="justify-end">
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

            <!-- Tahap 2: Data Jaminan -->
            <Card v-else-if="tab === 'jaminan'" data-testid="loan-detail-collaterals">
                <CardHeader class="flex flex-row flex-wrap items-center justify-between gap-2 space-y-0">
                    <CardTitle>Agunan pada Berkas</CardTitle>
                    <span class="text-xs text-muted-foreground">
                        Total taksasi
                        <span class="ml-1 text-sm font-semibold tabular-nums text-foreground">
                            {{ rupiah(totalAppraisal) }}
                        </span>
                    </span>
                </CardHeader>
                <CardContent class="form-dense space-y-3">
                    <div v-if="props.canManage" class="flex flex-col gap-2 sm:flex-row sm:items-end">
                        <div class="flex-1 space-y-[var(--item-gap)]">
                            <Label :class="LABEL">Pilih Agunan</Label>
                            <Combobox
                                v-model="attachForm.collateral_simulation_id"
                                :options="props.collateralOptions"
                                placeholder="-- Pilih agunan --"
                                data-testid="loan-detail-collateral-select"
                            />
                        </div>
                        <div class="flex items-center gap-2">
                            <Button
                                size="sm"
                                :disabled="attachForm.processing"
                                data-testid="loan-detail-collateral-attach"
                                @click="attach"
                            >
                                <Plus class="size-4" /> Lekatkan
                            </Button>
                            <Button
                                size="sm"
                                variant="outline"
                                data-testid="loan-detail-collateral-new"
                                @click="router.visit('/collateral-simulation/create')"
                            >
                                Agunan Baru
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
                            <tfoot class="border-t bg-muted/40">
                                <tr>
                                    <td class="px-3 py-2 text-xs font-semibold uppercase tracking-wider" colspan="3">
                                        Total Taksasi
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-2 text-right text-sm font-semibold tabular-nums">
                                        {{ rupiah(totalAppraisal) }}
                                    </td>
                                    <td />
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </CardContent>
            </Card>

            <!-- Tahap 3: Data Surveyor -->
            <form v-else-if="tab === 'surveyor'" class="form-dense" novalidate @submit.prevent="saveSurvey">
                <Card>
                    <CardHeader><CardTitle>Penugasan Survei</CardTitle></CardHeader>
                    <CardContent class="grid gap-[var(--field-gap)] sm:grid-cols-2 lg:grid-cols-3">
                        <div class="space-y-[var(--item-gap)]">
                            <Label :class="LABEL">Wilayah / Kantor <span class="text-destructive">*</span></Label>
                            <Combobox
                                v-model="surveyForm.office_id"
                                :options="props.offices"
                                placeholder="-- Pilih --"
                                data-testid="loan-detail-office"
                            />
                            <p v-if="surveyForm.errors.office_id" class="text-xs font-medium text-destructive">
                                {{ surveyForm.errors.office_id }}
                            </p>
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label :class="LABEL">Kasi Analis</Label>
                            <Combobox
                                v-model="surveyForm.supervisor_id"
                                :options="props.supervisors"
                                placeholder="(Opsional)"
                                data-testid="loan-detail-supervisor"
                            />
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label :class="LABEL">Surveyor</Label>
                            <Combobox
                                v-model="surveyForm.surveyor_id"
                                :options="props.surveyors"
                                placeholder="(Opsional)"
                                data-testid="loan-detail-surveyor"
                            />
                        </div>
                        <p class="text-xs text-muted-foreground sm:col-span-2 lg:col-span-3">
                            Daftar petugas mengikuti peranan pengguna SIPEBRI (Kasi Analis, Staff Analis, AO Kredit).
                        </p>
                    </CardContent>
                    <CardFooter class="justify-end">
                        <Button
                            v-if="props.canManage"
                            size="sm"
                            type="submit"
                            :disabled="surveyForm.processing"
                            data-testid="loan-detail-survey-save"
                        >
                            <Save class="size-4" /> {{ surveyForm.processing ? 'Menyimpan...' : 'Simpan' }}
                        </Button>
                    </CardFooter>
                </Card>
            </form>

            <!-- Tahap 4: Konfirmasi -->
            <Card v-else data-testid="loan-detail-confirm">
                <CardHeader><CardTitle>Konfirmasi Kelengkapan</CardTitle></CardHeader>
                <CardContent class="space-y-3">
                    <p class="rounded-md border border-amber-500/30 bg-amber-500/10 px-3 py-2 text-xs text-amber-700 dark:text-amber-400">
                        Pastikan seluruh tahapan sudah benar. Setelah dikonfirmasi, berkas masuk tahap analisa
                        dan perubahan berikutnya perlu otorisasi ulang.
                    </p>
                    <ul class="divide-y divide-border/60 overflow-hidden rounded-lg border">
                        <li
                            v-for="(ok, key) in props.record.checklist"
                            :key="key"
                            class="flex items-center gap-3 px-3 py-2.5"
                            :data-testid="`loan-detail-check-${key}`"
                        >
                            <span
                                class="flex size-6 shrink-0 items-center justify-center rounded-full border text-xs font-semibold"
                                :class="ok
                                    ? 'border-emerald-500/40 bg-emerald-500/15 text-emerald-700 dark:text-emerald-400'
                                    : 'border-border text-muted-foreground'"
                            >
                                <Check v-if="ok" class="size-3.5" />
                                <template v-else>!</template>
                            </span>
                            <span class="min-w-0 flex-1 text-sm" :class="ok ? 'font-medium' : 'text-muted-foreground'">
                                {{ CHECK_LABELS[key] }}
                            </span>
                            <span
                                class="shrink-0 rounded-full border px-2 py-0.5 text-[11px] font-semibold"
                                :class="ok
                                    ? 'border-emerald-500/30 bg-emerald-500/15 text-emerald-700 dark:text-emerald-400'
                                    : 'text-muted-foreground'"
                            >
                                {{ ok ? 'Lengkap' : 'Belum' }}
                            </span>
                        </li>
                    </ul>
                </CardContent>
                <CardFooter class="justify-between">
                    <p class="text-xs text-muted-foreground">
                        {{ props.record.confirmed_at
                            ? `Dikonfirmasi ${props.record.confirmed_at}`
                            : 'Berkas belum dikonfirmasi.' }}
                    </p>
                    <Button
                        v-if="props.canManage"
                        size="sm"
                        :disabled="!allChecked || actionForm.processing || Boolean(props.record.confirmed_at)"
                        data-testid="loan-detail-confirm-button"
                        @click="confirmFile"
                    >
                        <Check class="size-4" />
                        {{ props.record.confirmed_at ? 'Sudah dikonfirmasi' : 'Konfirmasi Berkas' }}
                    </Button>
                </CardFooter>
            </Card>
        </div>
    </AppLayout>
</template>
