<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

import FieldMoney from '@/components/composite/analysis/FieldMoney.vue';
import FieldStat from '@/components/composite/analysis/FieldStat.vue';
import FormActions from '@/components/composite/FormActions.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
import Combobox from '@/components/ui/Combobox.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import NumberInput from '@/components/ui/NumberInput.vue';
import Textarea from '@/components/ui/Textarea.vue';
import { rupiah } from '@/constants/committee';

/**
 * Analisa Agunan (berita acara pemeriksaan). Data yang dikirim ke CBS tetap
 * agunan pada berkas pengajuan; di sini analis melengkapi hasil pemeriksaan.
 */
const props = defineProps({
    applicationId: { type: Number, required: true },
    collaterals: { type: Array, default: () => [] },
    kinds: { type: Array, default: () => [] },
});

const VEHICLE = [
    ['Merek Kendaraan', 'merek'],
    ['Tipe Kendaraan', 'tipe_kendaraan'],
    ['Tahun Kendaraan', 'tahun'],
    ['Nomor Rangka', 'no_rangka'],
    ['Nomor Mesin', 'no_mesin'],
    ['Nomor Polisi', 'no_polisi'],
    ['Warna Kendaraan', 'warna'],
];

const form = useForm({ rows: props.collaterals.map((row) => ({ ...row })) });

const totalAppraisal = computed(() => form.rows.reduce((t, r) => t + Number(r.appraisal_value || 0), 0));
const totalMarket = computed(() => form.rows.reduce((t, r) => t + Number(r.market_value || 0), 0));

const kindOptions = props.kinds.map((value) => ({ value, label: value }));
const submit = () => form.put(`/analysis-simulation/${props.applicationId}/collaterals`, { preserveScroll: true });
</script>

<template>
    <div class="space-y-4" data-testid="collateral-analysis-form">
        <div
            v-if="!form.rows.length"
            class="flex min-h-[220px] flex-col items-center justify-center gap-2 rounded-md border border-dashed p-6 text-center"
            data-testid="collateral-analysis-empty"
        >
            <p class="text-sm font-medium">Berkas ini belum punya agunan</p>
            <p class="max-w-md text-xs text-muted-foreground">
                Tambahkan agunan pada berkas pengajuan terlebih dahulu; pemeriksaan agunan mengikuti daftar
                agunan berkas.
            </p>
        </div>

        <Card v-for="(row, index) in form.rows" :key="row.collateral_simulation_id" :data-testid="`collateral-card-${index}`">
            <CardHeader>
                <CardTitle>{{ row.owner_name }} · {{ row.document_number }}</CardTitle>
                <p class="text-xs text-muted-foreground">{{ row.label }}</p>
            </CardHeader>
            <CardContent class="form-dense grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                <div class="space-y-[var(--item-gap)]">
                    <Label :for="`collateral-kind-${index}`">Jenis Pemeriksaan</Label>
                    <Combobox
                        :id="`collateral-kind-${index}`"
                        v-model="row.kind"
                        :options="kindOptions"
                        placeholder="-- Pilih --"
                        :data-testid="`collateral-kind-${index}`"
                    />
                </div>
                <FieldStat label="Taksasi CBS" :value="rupiah(row.cbs_appraisal)" />

                <template v-if="row.kind === 'KENDARAAN'">
                    <div v-for="[label, key] in VEHICLE" :key="key" class="space-y-[var(--item-gap)]">
                        <Label :for="`collateral-${key}-${index}`">{{ label }}</Label>
                        <Input
                            :id="`collateral-${key}-${index}`"
                            v-model="row[key]"
                            maxlength="100"
                            :data-testid="`collateral-${key}-${index}`"
                        />
                    </div>
                </template>

                <div v-if="row.kind === 'TANAH'" class="space-y-[var(--item-gap)]">
                    <Label :for="`collateral-luas-${index}`">Luas Tanah (m²)</Label>
                    <NumberInput
                        :id="`collateral-luas-${index}`"
                        v-model="row.luas"
                        class="text-right tabular-nums"
                        :data-testid="`collateral-luas-${index}`"
                    />
                </div>

                <div class="space-y-[var(--item-gap)] sm:col-span-2">
                    <Label :for="`collateral-lokasi-${index}`">Lokasi Agunan</Label>
                    <Input
                        :id="`collateral-lokasi-${index}`"
                        v-model="row.lokasi"
                        maxlength="255"
                        :data-testid="`collateral-lokasi-${index}`"
                    />
                </div>

                <FieldMoney
                    v-model="row.market_value"
                    label="Nilai Pasar"
                    :testid="`collateral-market-${index}`"
                />
                <FieldMoney
                    v-model="row.appraisal_value"
                    label="Nilai Taksasi"
                    :testid="`collateral-appraisal-${index}`"
                />

                <div class="space-y-[var(--item-gap)] sm:col-span-2 xl:col-span-3">
                    <Label :for="`collateral-catatan-${index}`">Catatan Pemeriksaan</Label>
                    <Textarea
                        :id="`collateral-catatan-${index}`"
                        v-model="row.catatan"
                        rows="2"
                        maxlength="1000"
                        placeholder="(Opsional)"
                        :data-testid="`collateral-catatan-${index}`"
                    />
                </div>
            </CardContent>
        </Card>

        <Card v-if="form.rows.length" data-testid="collateral-analysis-summary">
            <CardHeader><CardTitle>Ringkasan Agunan</CardTitle></CardHeader>
            <CardContent class="grid gap-3 sm:grid-cols-3">
                <FieldStat label="Jumlah Agunan" :value="String(form.rows.length)" />
                <FieldStat label="Total Nilai Pasar" :value="rupiah(totalMarket)" />
                <FieldStat
                    label="Total Nilai Taksasi"
                    :value="rupiah(totalAppraisal)"
                    strong
                    testid="collateral-total-appraisal"
                />
            </CardContent>
        </Card>

        <div
            v-if="form.rows.length"
            class="sticky bottom-3 rounded-md border bg-background/95 p-2 shadow-sm backdrop-blur"
        >
            <FormActions
                cancel-testid="collateral-analysis-reset"
                cancel-label="Kembalikan"
                submit-testid="collateral-analysis-save"
                submit-label="Simpan Semua"
                :processing="form.processing"
                @cancel="form.reset()"
                @submit="submit"
            />
        </div>
    </div>
</template>
