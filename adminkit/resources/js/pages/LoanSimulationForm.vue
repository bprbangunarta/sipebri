<script setup>
import { ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Save, Search, UserCheck, UserX, X } from 'lucide-vue-next';

import AppLayout from '@/components/layout/AppLayout.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardFooter from '@/components/ui/CardFooter.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import NumberInput from '@/components/ui/NumberInput.vue';
import { ACTION } from '@/constants/labels';
import { rupiah } from '@/constants/committee';

/**
 * Pembukaan berkas pengajuan. Data pemohon TIDAK diinput — diambil dari sistem
 * data nasabah lewat nomor KTP (MOCK sampai endpoint API siap).
 */
const props = defineProps({
    nextCode: { type: String, default: '' },
    sampleNiks: { type: Array, default: () => [] },
});

const form = useForm({
    nik: '',
    requested_amount: 0,
    requested_tenor: 0,
});

const customer = ref(null);
const lookupError = ref('');
const checking = ref(false);

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
        <form class="form-dense space-y-4" novalidate @submit.prevent="submit">
            <Card data-testid="loan-form-page">
                <CardHeader class="flex flex-row flex-wrap items-center justify-between gap-2 space-y-0">
                    <CardTitle>Buka Berkas Pengajuan</CardTitle>
                    <Badge variant="secondary" class="font-mono font-medium" data-testid="loan-form-code">
                        {{ props.nextCode }}
                    </Badge>
                </CardHeader>
                <CardContent class="space-y-[var(--field-gap)]">
                    <div class="grid gap-[var(--field-gap)] sm:grid-cols-2 lg:grid-cols-4">
                        <div class="space-y-[var(--item-gap)] sm:col-span-2">
                            <Label for="f-nik">Nomor KTP <span class="text-destructive" aria-hidden="true">*</span></Label>
                            <div class="flex items-center gap-2">
                                <Input
                                    id="f-nik"
                                    :model-value="form.nik"
                                    inputmode="numeric"
                                    placeholder="16 angka"
                                    class="font-mono"
                                    data-testid="loan-form-nik"
                                    @update:model-value="onNik"
                                    @keydown.enter.prevent="checkNik"
                                />
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    class="shrink-0"
                                    :disabled="checking"
                                    data-testid="loan-form-check"
                                    @click="checkNik"
                                >
                                    <Search class="size-4" /> {{ checking ? 'Memeriksa...' : 'Cek KTP' }}
                                </Button>
                            </div>
                            <p v-if="form.errors.nik" class="text-xs font-medium text-destructive">{{ form.errors.nik }}</p>
                            <p v-else-if="lookupError" class="text-xs font-medium text-destructive" data-testid="loan-form-lookup-error">
                                {{ lookupError }}
                            </p>
                            <p v-else class="text-xs text-muted-foreground">
                                Contoh KTP terdaftar (data MOCK): {{ props.sampleNiks.join(', ') }}
                            </p>
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label for="f-amount">Pengajuan Plafon</Label>
                            <NumberInput id="f-amount" v-model="form.requested_amount" data-testid="loan-form-amount" />
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label for="f-tenor">Jangka Waktu (bulan)</Label>
                            <NumberInput id="f-tenor" v-model="form.requested_tenor" data-testid="loan-form-tenor" />
                            <p v-if="form.errors.requested_tenor" class="text-xs font-medium text-destructive">
                                {{ form.errors.requested_tenor }}
                            </p>
                        </div>
                    </div>

                    <!-- Identitas hanya ditampilkan; SIPEBRI tidak menyimpannya. -->
                    <div
                        v-if="customer"
                        class="rounded-md border bg-muted/40 p-3"
                        data-testid="loan-form-customer"
                    >
                        <div class="mb-2 flex items-center gap-2 text-xs font-semibold">
                            <UserCheck class="size-4 text-emerald-600" />
                            {{ customer.full_name }}
                            <Badge v-if="customer.cif_number" variant="secondary" class="font-mono font-medium">
                                {{ customer.cif_number }}
                            </Badge>
                            <Badge v-else variant="outline">Belum ada CIF</Badge>
                        </div>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-1.5 sm:grid-cols-3">
                            <div v-for="item in [
                                { label: 'Tempat / Tgl Lahir', value: `${customer.birth_place ?? '—'} · ${customer.birth_date ?? '—'}` },
                                { label: 'Jenis Kelamin', value: customer.gender ?? '—' },
                                { label: 'Status Perkawinan', value: customer.marital_status ?? '—' },
                                { label: 'Pekerjaan', value: customer.occupation ?? '—' },
                                { label: 'Tempat Bekerja', value: customer.employer_name ?? '—' },
                                { label: 'Telepon', value: customer.phone ?? '—' },
                                { label: 'Penghasilan', value: rupiah(customer.monthly_income) },
                                { label: 'Pengeluaran', value: rupiah(customer.monthly_expense) },
                                { label: 'Pendamping', value: customer.companion ? `${customer.companion.name} (${customer.companion.relation})` : '—' },
                                { label: 'Alamat', value: customer.address ?? '—', span: true },
                            ]" :key="item.label" :class="item.span ? 'sm:col-span-3' : ''">
                                <dt class="text-[11px] leading-4 text-muted-foreground">{{ item.label }}</dt>
                                <dd class="text-xs font-medium leading-5">{{ item.value }}</dd>
                            </div>
                        </dl>
                        <p class="mt-2 text-[11px] text-muted-foreground">
                            Data dari sistem pengelola nasabah — tidak disimpan di SIPEBRI (MOCK).
                        </p>
                    </div>
                    <div
                        v-else
                        class="flex items-center gap-2 rounded-md border border-dashed p-3 text-xs text-muted-foreground"
                    >
                        <UserX class="size-4" /> Periksa nomor KTP untuk menampilkan identitas nasabah.
                    </div>
                </CardContent>
                <CardFooter class="justify-between">
                    <Button
                        variant="outline"
                        size="sm"
                        type="button"
                        data-testid="loan-form-cancel"
                        @click="router.visit('/loan-simulation')"
                    >
                        <X class="size-4" /> {{ ACTION.cancel }}
                    </Button>
                    <Button
                        size="sm"
                        type="submit"
                        :disabled="form.processing || !customer"
                        data-testid="loan-form-save"
                    >
                        <Save class="size-4" /> {{ form.processing ? ACTION.saving : 'Buka Berkas' }}
                    </Button>
                </CardFooter>
            </Card>
        </form>
    </AppLayout>
</template>
