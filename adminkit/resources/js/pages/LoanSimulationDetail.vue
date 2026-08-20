<script setup>
import { computed, ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Check,
    CircleCheck,
    CircleDashed,
    ClipboardCheck,
    FileSignature,
    Plus,
    Save,
    ShieldCheck,
    Trash2,
    UserSearch,
    Users,
} from 'lucide-vue-next';

import AppLayout from '@/components/layout/AppLayout.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
import Combobox from '@/components/ui/Combobox.vue';
import DatePicker from '@/components/ui/DatePicker.vue';
import DecimalInput from '@/components/ui/DecimalInput.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import NumberInput from '@/components/ui/NumberInput.vue';
import { ACTION } from '@/constants/labels';
import { rupiah } from '@/constants/committee';

/** Berkas pengajuan: tahapan Pengajuan → Jaminan → Surveyor → Konfirmasi. */
const props = defineProps({
    record: { type: Object, required: true },
    customer: { type: Object, default: null },
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

const TABS = [
    { id: 'pengajuan', label: 'Data Pengajuan', icon: FileSignature },
    { id: 'jaminan', label: 'Data Jaminan', icon: ShieldCheck },
    { id: 'surveyor', label: 'Data Surveyor', icon: Users },
    { id: 'konfirmasi', label: 'Konfirmasi', icon: ClipboardCheck },
];

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

const CHECK_LABELS = {
    nasabah: 'Informasi Nasabah (dari sistem data nasabah)',
    pengajuan: 'Informasi Pengajuan (produk, plafon, jangka waktu)',
    jaminan: 'Informasi Jaminan (minimal satu agunan)',
    surveyor: 'Informasi Surveyor (kantor & surveyor)',
};

const allChecked = computed(() => Object.values(props.record.checklist ?? {}).every(Boolean));
</script>

<template>
    <Head :title="`Berkas ${props.record.application_code}`" />
    <AppLayout>
        <div class="grid gap-4 lg:grid-cols-[280px_1fr]" data-testid="loan-detail-page">
            <!-- Info Nasabah: tampilan saja, tidak disimpan di SIPEBRI -->
            <div class="space-y-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between gap-2 space-y-0">
                        <CardTitle class="flex items-center gap-2">
                            <UserSearch class="size-4" /> Info Nasabah
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-2">
                        <div v-if="props.customer" class="space-y-1.5" data-testid="loan-detail-customer">
                            <p class="text-xs font-semibold">{{ props.customer.full_name }}</p>
                            <p class="font-mono text-[11px] text-muted-foreground">{{ props.record.nik }}</p>
                            <dl class="space-y-1 border-t pt-2">
                                <div
                                    v-for="item in [
                                        { label: 'CIF', value: props.customer.cif_number ?? 'Belum ada' },
                                        { label: 'Lahir', value: `${props.customer.birth_place ?? '—'} · ${props.customer.birth_date ?? '—'}` },
                                        { label: 'Pekerjaan', value: props.customer.occupation ?? '—' },
                                        { label: 'Tempat Kerja', value: props.customer.employer_name ?? '—' },
                                        { label: 'Telepon', value: props.customer.phone ?? '—' },
                                        { label: 'Penghasilan', value: rupiah(props.customer.monthly_income) },
                                        { label: 'Pengeluaran', value: rupiah(props.customer.monthly_expense) },
                                        {
                                            label: 'Pendamping',
                                            value: props.customer.companion
                                                ? `${props.customer.companion.name} (${props.customer.companion.relation})`
                                                : '—',
                                        },
                                        { label: 'Alamat', value: props.customer.address ?? '—' },
                                    ]"
                                    :key="item.label"
                                >
                                    <dt class="text-[11px] leading-4 text-muted-foreground">{{ item.label }}</dt>
                                    <dd class="break-words text-xs font-medium leading-5">{{ item.value }}</dd>
                                </div>
                            </dl>
                            <p class="border-t pt-2 text-[11px] text-muted-foreground">
                                Sumber: sistem pengelola nasabah (MOCK). Tidak disimpan di SIPEBRI.
                            </p>
                        </div>
                        <p v-else class="text-xs text-destructive" data-testid="loan-detail-customer-missing">
                            Nomor KTP {{ props.record.nik }} tidak ditemukan di sistem data nasabah.
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader><CardTitle>Ringkasan</CardTitle></CardHeader>
                    <CardContent class="space-y-1.5">
                        <div v-for="item in [
                            { label: 'Plafon Diajukan', value: rupiah(props.record.requested_amount) },
                            { label: 'Jangka Waktu', value: props.record.requested_tenor ? `${props.record.requested_tenor} bulan` : '—' },
                            { label: 'Produk', value: props.record.product_label ?? '—' },
                            { label: 'Total Taksasi Agunan', value: rupiah(totalAppraisal) },
                            { label: 'Dikonfirmasi', value: props.record.confirmed_at ?? 'Belum' },
                        ]" :key="item.label">
                            <dt class="text-[11px] leading-4 text-muted-foreground">{{ item.label }}</dt>
                            <dd class="text-xs font-medium leading-5">{{ item.value }}</dd>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader class="flex flex-row flex-wrap items-center justify-between gap-2 space-y-0">
                    <CardTitle class="flex min-w-0 items-center gap-2">
                        <Button
                            variant="ghost"
                            size="icon"
                            type="button"
                            data-testid="loan-detail-back"
                            @click="router.visit('/loan-simulation')"
                        >
                            <ArrowLeft class="size-4" />
                        </Button>
                        <span class="truncate font-mono text-sm">{{ props.record.application_code }}</span>
                        <span class="truncate">{{ props.record.full_name }}</span>
                    </CardTitle>
                    <Badge variant="secondary" class="font-medium">{{ props.record.status }}</Badge>
                </CardHeader>

                <CardContent class="space-y-3">
                    <div class="flex flex-wrap items-center gap-2 border-b pb-2">
                        <button
                            v-for="t in TABS"
                            :key="t.id"
                            type="button"
                            class="inline-flex h-8 items-center gap-1.5 rounded-md px-3 text-xs font-medium transition-colors"
                            :class="
                                tab === t.id
                                    ? 'bg-primary text-primary-foreground'
                                    : 'text-muted-foreground hover:bg-accent hover:text-foreground'
                            "
                            :data-testid="`loan-detail-tab-${t.id}`"
                            @click="tab = t.id"
                        >
                            <component :is="t.icon" class="size-3.5" />{{ t.label }}
                        </button>
                    </div>

                    <!-- Tahap 1: Data Pengajuan -->
                    <form
                        v-if="tab === 'pengajuan'"
                        class="form-dense space-y-3"
                        novalidate
                        @submit.prevent="saveApplication"
                    >
                        <div class="grid gap-[var(--field-gap)] sm:grid-cols-2 lg:grid-cols-4">
                            <div class="space-y-[var(--item-gap)]">
                                <Label>Tanggal Pengajuan <span class="text-destructive">*</span></Label>
                                <DatePicker v-model="form.application_date" data-testid="loan-detail-date" />
                            </div>
                            <div class="space-y-[var(--item-gap)] lg:col-span-2">
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
                                <Label>Penggunaan</Label>
                                <Combobox
                                    v-model="form.usage_type"
                                    :options="props.usageTypes"
                                    placeholder="(Opsional)"
                                    data-testid="loan-detail-usage"
                                />
                            </div>
                            <div class="space-y-[var(--item-gap)]">
                                <Label for="d-amount">Plafon <span class="text-destructive">*</span></Label>
                                <NumberInput id="d-amount" v-model="form.requested_amount" data-testid="loan-detail-amount" />
                                <p v-if="form.errors.requested_amount" class="text-xs font-medium text-destructive">
                                    {{ form.errors.requested_amount }}
                                </p>
                            </div>
                            <div class="space-y-[var(--item-gap)]">
                                <Label for="d-tenor">JK Kredit (bulan) <span class="text-destructive">*</span></Label>
                                <NumberInput id="d-tenor" v-model="form.requested_tenor" data-testid="loan-detail-tenor" />
                                <p v-if="form.errors.requested_tenor" class="text-xs font-medium text-destructive">
                                    {{ form.errors.requested_tenor }}
                                </p>
                            </div>
                            <div class="space-y-[var(--item-gap)]">
                                <Label for="d-tenor-principal">JK Pokok (bulan)</Label>
                                <NumberInput
                                    id="d-tenor-principal"
                                    v-model="form.tenor_principal"
                                    placeholder="(Opsional)"
                                    data-testid="loan-detail-tenor-principal"
                                />
                            </div>
                            <div class="space-y-[var(--item-gap)]">
                                <Label for="d-tenor-interest">JW Bunga (bulan)</Label>
                                <NumberInput
                                    id="d-tenor-interest"
                                    v-model="form.tenor_interest"
                                    placeholder="(Opsional)"
                                    data-testid="loan-detail-tenor-interest"
                                />
                            </div>
                            <div class="space-y-[var(--item-gap)]">
                                <Label>Sistem Bunga</Label>
                                <Combobox
                                    v-model="form.method_id"
                                    :options="props.methods"
                                    placeholder="-- Pilih --"
                                    data-testid="loan-detail-method"
                                />
                            </div>
                            <div class="space-y-[var(--item-gap)]">
                                <Label>Sistem Cicilan</Label>
                                <Combobox
                                    v-model="form.installment_id"
                                    :options="props.installments"
                                    placeholder="-- Pilih --"
                                    data-testid="loan-detail-installment"
                                />
                            </div>
                            <div class="space-y-[var(--item-gap)]">
                                <Label for="d-rate">Suku Bunga (%)</Label>
                                <DecimalInput id="d-rate" v-model="form.interest_rate" data-testid="loan-detail-rate" />
                            </div>
                            <div class="space-y-[var(--item-gap)]">
                                <Label for="d-provision">Provisi (%)</Label>
                                <DecimalInput id="d-provision" v-model="form.provision_rate" data-testid="loan-detail-provision" />
                            </div>
                            <div class="space-y-[var(--item-gap)]">
                                <Label for="d-admin">Biaya Admin (%)</Label>
                                <DecimalInput id="d-admin" v-model="form.admin_rate" data-testid="loan-detail-admin" />
                            </div>
                            <div class="space-y-[var(--item-gap)] lg:col-span-2">
                                <Label>Resort / Instansi</Label>
                                <Combobox
                                    v-model="form.institution_id"
                                    :options="props.institutions"
                                    placeholder="(Opsional — pengelompokan)"
                                    data-testid="loan-detail-institution"
                                />
                            </div>
                            <div class="space-y-[var(--item-gap)] lg:col-span-2">
                                <Label for="d-purpose">Tujuan Penggunaan</Label>
                                <Input
                                    id="d-purpose"
                                    v-model="form.purpose"
                                    placeholder="(Opsional)"
                                    class="uppercase"
                                    data-testid="loan-detail-purpose"
                                />
                            </div>
                            <div class="space-y-[var(--item-gap)] sm:col-span-2 lg:col-span-4">
                                <Label for="d-note">Keterangan</Label>
                                <Input
                                    id="d-note"
                                    v-model="form.note"
                                    placeholder="(Opsional)"
                                    class="uppercase"
                                    data-testid="loan-detail-note"
                                />
                            </div>
                        </div>
                        <div class="flex justify-end border-t pt-3">
                            <Button
                                v-if="props.canManage"
                                size="sm"
                                type="submit"
                                :disabled="form.processing"
                                data-testid="loan-detail-save"
                            >
                                <Save class="size-4" /> {{ form.processing ? ACTION.saving : ACTION.save }}
                            </Button>
                        </div>
                    </form>

                    <!-- Tahap 2: Data Jaminan -->
                    <div v-else-if="tab === 'jaminan'" class="space-y-3" data-testid="loan-detail-collaterals">
                        <div v-if="props.canManage" class="flex flex-wrap items-end gap-2">
                            <div class="min-w-[240px] flex-1 space-y-[var(--item-gap)]">
                                <Label>Pilih Agunan</Label>
                                <Combobox
                                    v-model="attachForm.collateral_simulation_id"
                                    :options="props.collateralOptions"
                                    placeholder="-- Pilih agunan --"
                                    data-testid="loan-detail-collateral-select"
                                />
                            </div>
                            <Button size="sm" :disabled="attachForm.processing" data-testid="loan-detail-collateral-attach" @click="attach">
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

                        <div v-if="!props.collaterals.length" class="rounded-md border border-dashed p-6 text-center text-xs text-muted-foreground">
                            Belum ada agunan yang dilekatkan pada berkas ini.
                        </div>
                        <table v-else class="w-full text-xs">
                            <thead>
                                <tr class="border-b text-[11px] uppercase tracking-wide text-muted-foreground">
                                    <th class="py-1.5 pr-3 text-left font-medium">Agunan</th>
                                    <th class="py-1.5 pr-3 text-left font-medium">Pemilik</th>
                                    <th class="hidden py-1.5 pr-3 text-left font-medium md:table-cell">Keterangan</th>
                                    <th class="py-1.5 pr-3 text-right font-medium">Taksasi</th>
                                    <th class="w-8 py-1.5" />
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="row in props.collaterals"
                                    :key="row.id"
                                    class="border-b border-border/60 last:border-0"
                                    :data-testid="`loan-detail-collateral-${row.id}`"
                                >
                                    <td class="py-1.5 pr-3 font-mono font-medium">{{ row.collateral_id ?? `#${row.id}` }}</td>
                                    <td class="py-1.5 pr-3">{{ row.owner_name ?? '—' }}</td>
                                    <td class="hidden py-1.5 pr-3 text-muted-foreground md:table-cell">{{ row.description ?? '—' }}</td>
                                    <td class="py-1.5 pr-3 text-right tabular-nums">{{ rupiah(row.appraisal_value) }}</td>
                                    <td class="py-1.5 text-right">
                                        <Button
                                            v-if="props.canManage"
                                            variant="ghost"
                                            size="icon"
                                            class="text-destructive hover:text-destructive"
                                            :data-testid="`loan-detail-collateral-detach-${row.id}`"
                                            @click="detach(row.id)"
                                        >
                                            <Trash2 class="size-3.5" />
                                        </Button>
                                    </td>
                                </tr>
                                <tr class="font-medium">
                                    <td class="py-1.5 pr-3" colspan="3">Total Taksasi</td>
                                    <td class="py-1.5 pr-3 text-right tabular-nums">{{ rupiah(totalAppraisal) }}</td>
                                    <td />
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Tahap 3: Data Surveyor -->
                    <form
                        v-else-if="tab === 'surveyor'"
                        class="form-dense space-y-3"
                        novalidate
                        @submit.prevent="saveSurvey"
                    >
                        <div class="grid gap-[var(--field-gap)] sm:grid-cols-3">
                            <div class="space-y-[var(--item-gap)]">
                                <Label>Wilayah / Kantor <span class="text-destructive">*</span></Label>
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
                                <Label>Kasi Analis</Label>
                                <Combobox
                                    v-model="surveyForm.supervisor_id"
                                    :options="props.supervisors"
                                    placeholder="-- Pilih --"
                                    data-testid="loan-detail-supervisor"
                                />
                            </div>
                            <div class="space-y-[var(--item-gap)]">
                                <Label>Surveyor</Label>
                                <Combobox
                                    v-model="surveyForm.surveyor_id"
                                    :options="props.surveyors"
                                    placeholder="-- Pilih --"
                                    data-testid="loan-detail-surveyor"
                                />
                            </div>
                        </div>
                        <div class="flex justify-end border-t pt-3">
                            <Button
                                v-if="props.canManage"
                                size="sm"
                                type="submit"
                                :disabled="surveyForm.processing"
                                data-testid="loan-detail-survey-save"
                            >
                                <Save class="size-4" /> {{ surveyForm.processing ? ACTION.saving : ACTION.save }}
                            </Button>
                        </div>
                    </form>

                    <!-- Tahap 4: Konfirmasi -->
                    <div v-else class="space-y-3" data-testid="loan-detail-confirm">
                        <p class="rounded-md border border-amber-500/40 bg-amber-500/10 p-3 text-xs text-amber-700 dark:text-amber-400">
                            Pastikan seluruh tahapan sudah benar. Setelah dikonfirmasi, berkas masuk tahap analisa
                            dan perubahan berikutnya perlu otorisasi ulang.
                        </p>
                        <ul class="space-y-1">
                            <li
                                v-for="(ok, key) in props.record.checklist"
                                :key="key"
                                class="flex items-center gap-2 rounded-md border px-3 py-2 text-xs"
                                :data-testid="`loan-detail-check-${key}`"
                            >
                                <CircleCheck v-if="ok" class="size-4 text-emerald-600" />
                                <CircleDashed v-else class="size-4 text-muted-foreground" />
                                <span :class="ok ? 'font-medium' : 'text-muted-foreground'">{{ CHECK_LABELS[key] }}</span>
                                <Badge :variant="ok ? 'secondary' : 'outline'" class="ml-auto font-medium">
                                    {{ ok ? 'Lengkap' : 'Belum' }}
                                </Badge>
                            </li>
                        </ul>
                        <div class="flex justify-end border-t pt-3">
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
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
