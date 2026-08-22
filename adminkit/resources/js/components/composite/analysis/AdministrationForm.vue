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
import { rupiah } from '@/constants/committee';

/** Administrasi (bagian 8): rincian biaya yang ditanggung nasabah. */
const props = defineProps({
    applicationId: { type: Number, required: true },
    administration: { type: Object, required: true },
});

const GROUPS = [
    {
        title: 'Biaya Kredit',
        fields: [
            ['Administrasi', 'administrasi'],
            ['Provisi', 'provisi'],
            ['Materai', 'materai'],
            ['Transaksi Kredit', 'transaksi_kredit'],
        ],
    },
    {
        title: 'Asuransi Jiwa',
        fields: [
            ['Asr. Jiwa Menurun 1', 'asuransi_jiwa_menurun1'],
            ['Asr. Jiwa Menurun 2', 'asuransi_jiwa_menurun2'],
            ['Asr. Jiwa Menurun 3', 'asuransi_jiwa_menurun3'],
            ['Asr. Jiwa Tetap 1', 'asuransi_jiwa_tetap1'],
            ['Asr. Jiwa Tetap 2', 'asuransi_jiwa_tetap2'],
            ['Pertanggungan Asr. Jiwa', 'asuransi_jiwa'],
        ],
    },
    {
        title: 'Agunan & Pengikatan',
        fields: [
            ['Asr. Kendaraan Bermotor', 'asuransi_kendaraan_motor'],
            ['Pajak STNK', 'pajak_stnk'],
            ['Proses SHM', 'proses_shm'],
            ['Polis dan Materai', 'polis_materai'],
            ['Proses APHT', 'proses_apht'],
            ['Biaya Fiducia', 'by_fiducia'],
        ],
    },
];

const keys = GROUPS.flatMap((g) => g.fields.map(([, key]) => key));
const form = useForm(Object.fromEntries(keys.map((key) => [key, props.administration[key] ?? 0])));

const total = computed(() => keys.reduce((t, key) => t + Number(form[key] || 0), 0));

const submit = () => form.put(`/analysis-simulation/${props.applicationId}/administration`, { preserveScroll: true });
</script>

<template>
    <div class="space-y-4" data-testid="administration-form">
        <Card v-for="group in GROUPS" :key="group.title" :data-testid="`administration-${group.fields[0][1]}`">
            <CardHeader><CardTitle>{{ group.title }}</CardTitle></CardHeader>
            <CardContent class="form-dense grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                <FieldMoney
                    v-for="[label, key] in group.fields"
                    :key="key"
                    v-model="form[key]"
                    :label="label"
                    :testid="`admin-${key}`"
                    :error="form.errors[key]"
                />
            </CardContent>
        </Card>

        <Card data-testid="administration-summary">
            <CardHeader><CardTitle>Ringkasan Biaya</CardTitle></CardHeader>
            <CardContent class="grid gap-3 sm:grid-cols-2">
                <FieldStat label="Total Biaya Administrasi" :value="rupiah(total)" strong testid="admin-total" />
                <p class="self-center text-xs text-muted-foreground">
                    Seluruh biaya diisi analis sesuai ketentuan produk; total di samping dipakai sebagai
                    perkiraan biaya yang harus disiapkan nasabah saat realisasi.
                </p>
            </CardContent>
        </Card>

        <div class="sticky bottom-3 rounded-md border bg-background/95 p-2 shadow-sm backdrop-blur">
            <FormActions
                cancel-testid="administration-reset"
                cancel-label="Kembalikan"
                submit-testid="administration-save"
                submit-label="Simpan Semua"
                :processing="form.processing"
                @cancel="form.reset()"
                @submit="submit"
            />
        </div>
    </div>
</template>
