<script setup>
import { ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight, IdCard, Info, Loader2, Search, ShieldAlert, UserRound } from 'lucide-vue-next';

import AppLayout from '@/components/layout/AppLayout.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import NumberInput from '@/components/ui/NumberInput.vue';
import { rupiah } from '@/constants/committee';

/**
 * Pembukaan berkas pengajuan. Data pemohon TIDAK diinput — diambil dari sistem
 * data nasabah lewat nomor KTP (MOCK sampai endpoint API siap).
 */
const props = defineProps({
    nextCode: { type: String, default: '' },
    sampleNiks: { type: Array, default: () => [] },
});

const form = useForm({ nik: '', requested_amount: 0, requested_tenor: 0 });

const customer = ref(null);
const lookupError = ref('');
const checking = ref(false);

const LABEL = 'text-[11px] font-medium uppercase tracking-wider text-muted-foreground';

const onNik = (value) => {
    form.nik = String(value ?? '').replace(/\D/g, '').slice(0, 16);
    customer.value = null;
    lookupError.value = '';
};

const checkNik = async () => {
    if (form.nik.length !== 16) {
        lookupError.value = 'Nomor KTP harus 16 angka.';

        return;
    }

    checking.value = true;
    customer.value = null;
    lookupError.value = '';

    try {
        const response = await fetch(`/loan-simulation/lookup?nik=${form.nik}`, {
            headers: { Accept: 'application/json' },
        });
        const data = await response.json();

        if (data.found) customer.value = data.customer;
        else lookupError.value = data.message;
    } catch {
        lookupError.value = 'Gagal menghubungi sistem data nasabah.';
    } finally {
        checking.value = false;
    }
};

const submit = () => {
    if (!customer.value) {
        lookupError.value = 'Periksa nomor KTP terlebih dahulu.';

        return;
    }

    form.post('/loan-simulation', { preserveScroll: true });
};
</script>

<template>
    <Head title="Tambah Pengajuan" />
    <AppLayout>
        <form class="form-dense mx-auto w-full max-w-3xl space-y-4" novalidate @submit.prevent="submit">
            <!-- Kepala halaman -->
            <div class="flex flex-wrap items-center gap-3">
                <Button
                    variant="ghost"
                    size="icon"
                    type="button"
                    data-testid="loan-form-back"
                    @click="router.visit('/loan-simulation')"
                >
                    <ArrowLeft class="size-4" />
                </Button>
                <div class="min-w-0 flex-1">
                    <h1 class="text-lg font-semibold tracking-tight">Buka Berkas Pengajuan</h1>
                    <p class="text-xs text-muted-foreground">
                        Nomor berkas dibuat sistem. Identitas pemohon diambil dari sistem data nasabah.
                    </p>
                </div>
                <span
                    class="rounded-full border bg-muted/40 px-2.5 py-0.5 font-mono text-xs font-semibold"
                    data-testid="loan-form-code"
                >
                    {{ props.nextCode }}
                </span>
            </div>

            <!-- Langkah 1: verifikasi KTP -->
            <Card data-testid="loan-form-page">
                <CardContent class="space-y-3 p-4">
                    <div class="flex items-center gap-2 border-b pb-2">
                        <IdCard class="size-4 text-muted-foreground" />
                        <h2 class="text-sm font-semibold">1 · Verifikasi Nomor KTP</h2>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end">
                        <div class="flex-1 space-y-[var(--item-gap)]">
                            <Label for="f-nik" :class="LABEL">Nomor KTP <span class="text-destructive">*</span></Label>
                            <Input
                                id="f-nik"
                                :model-value="form.nik"
                                inputmode="numeric"
                                placeholder="16 angka"
                                class="font-mono tracking-wider"
                                data-testid="loan-form-nik"
                                @update:model-value="onNik"
                                @keydown.enter.prevent="checkNik"
                            />
                        </div>
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            class="shrink-0 transition-colors"
                            :disabled="checking"
                            data-testid="loan-form-check"
                            @click="checkNik"
                        >
                            <Loader2 v-if="checking" class="size-4 animate-spin" />
                            <Search v-else class="size-4" />
                            {{ checking ? 'Memeriksa' : 'Cek KTP' }}
                        </Button>
                    </div>

                    <p v-if="form.errors.nik" class="text-xs font-medium text-destructive">{{ form.errors.nik }}</p>
                    <p
                        v-else-if="lookupError"
                        class="flex items-start gap-2 rounded-md border border-destructive/30 bg-destructive/10 px-3 py-2 text-xs font-medium text-destructive"
                        data-testid="loan-form-lookup-error"
                    >
                        <ShieldAlert class="mt-0.5 size-4 shrink-0" /> {{ lookupError }}
                    </p>
                    <p v-else class="text-xs text-muted-foreground">
                        Contoh KTP terdaftar (data MOCK):
                        <span class="font-mono">{{ props.sampleNiks.join(' · ') }}</span>
                    </p>

                    <!-- Identitas hanya ditampilkan; SIPEBRI tidak menyimpannya. -->
                    <div
                        v-if="customer"
                        class="space-y-3 rounded-lg border bg-muted/30 p-4"
                        data-testid="loan-form-customer"
                    >
                        <div class="flex flex-wrap items-center gap-2 border-b border-border/60 pb-2">
                            <UserRound class="size-4 text-emerald-600 dark:text-emerald-400" />
                            <span class="text-sm font-semibold">{{ customer.full_name }}</span>
                            <span
                                class="rounded-full border px-2 py-0.5 font-mono text-[11px] font-semibold"
                                :class="customer.cif_number
                                    ? 'border-emerald-500/30 bg-emerald-500/15 text-emerald-700 dark:text-emerald-400'
                                    : 'text-muted-foreground'"
                            >
                                {{ customer.cif_number ?? 'BELUM ADA CIF' }}
                            </span>
                        </div>
                        <dl class="grid grid-cols-1 gap-x-6 gap-y-3 sm:grid-cols-2">
                            <div
                                v-for="item in [
                                    { label: 'Tempat / Tgl Lahir', value: `${customer.birth_place ?? '—'} · ${customer.birth_date ?? '—'}` },
                                    { label: 'Jenis Kelamin', value: customer.gender ?? '—' },
                                    { label: 'Status Perkawinan', value: customer.marital_status ?? '—' },
                                    { label: 'Telepon', value: customer.phone ?? '—' },
                                    { label: 'Pekerjaan', value: customer.occupation ?? '—' },
                                    { label: 'Tempat Bekerja', value: customer.employer_name ?? '—' },
                                    { label: 'Penghasilan / Bulan', value: rupiah(customer.monthly_income) },
                                    { label: 'Pengeluaran / Bulan', value: rupiah(customer.monthly_expense) },
                                    {
                                        label: 'Pendamping',
                                        value: customer.companion
                                            ? `${customer.companion.name} (${customer.companion.relation})`
                                            : '—',
                                        span: true,
                                    },
                                    { label: 'Alamat', value: customer.address ?? '—', span: true },
                                ]"
                                :key="item.label"
                                :class="item.span ? 'sm:col-span-2' : ''"
                            >
                                <dt :class="LABEL">{{ item.label }}</dt>
                                <dd class="text-sm font-semibold leading-5">{{ item.value }}</dd>
                            </div>
                        </dl>
                        <p class="flex items-start gap-2 rounded-md border border-border/60 bg-card px-3 py-2 text-xs text-muted-foreground">
                            <Info class="mt-0.5 size-3.5 shrink-0" />
                            Data berasal dari sistem pengelola nasabah (MOCK) dan tidak disimpan di SIPEBRI.
                        </p>
                    </div>
                </CardContent>
            </Card>

            <!-- Langkah 2: permohonan awal -->
            <Card>
                <CardContent class="space-y-3 p-4">
                    <div class="flex items-center gap-2 border-b pb-2">
                        <ArrowRight class="size-4 text-muted-foreground" />
                        <h2 class="text-sm font-semibold">2 · Permohonan Awal</h2>
                    </div>
                    <div class="grid gap-[var(--field-gap)] sm:grid-cols-2">
                        <div class="space-y-[var(--item-gap)]">
                            <Label for="f-amount" :class="LABEL">Pengajuan Plafon</Label>
                            <NumberInput id="f-amount" v-model="form.requested_amount" data-testid="loan-form-amount" />
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label for="f-tenor" :class="LABEL">Jangka Waktu (bulan)</Label>
                            <NumberInput id="f-tenor" v-model="form.requested_tenor" data-testid="loan-form-tenor" />
                            <p v-if="form.errors.requested_tenor" class="text-xs font-medium text-destructive">
                                {{ form.errors.requested_tenor }}
                            </p>
                        </div>
                    </div>
                    <p class="text-xs text-muted-foreground">
                        Produk, parameter kredit, agunan, dan surveyor dilengkapi setelah berkas dibuka.
                    </p>
                </CardContent>
            </Card>

            <div class="flex flex-wrap items-center justify-between gap-2">
                <Button
                    variant="outline"
                    size="sm"
                    type="button"
                    data-testid="loan-form-cancel"
                    @click="router.visit('/loan-simulation')"
                >
                    Batal
                </Button>
                <Button size="sm" type="submit" :disabled="form.processing || !customer" data-testid="loan-form-save">
                    {{ form.processing ? 'Menyimpan...' : 'Buka Berkas' }}
                    <ArrowRight class="size-4" />
                </Button>
            </div>
        </form>
    </AppLayout>
</template>
