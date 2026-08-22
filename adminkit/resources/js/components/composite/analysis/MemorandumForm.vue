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
import DecimalInput from '@/components/ui/DecimalInput.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import NumberInput from '@/components/ui/NumberInput.vue';
import { persen, rupiah } from '@/constants/committee';

/** Memorandum (bagian 7): kebutuhan dana & usulan fasilitas. */
const props = defineProps({
    applicationId: { type: Number, required: true },
    memorandum: { type: Object, required: true },
    bindings: { type: Array, default: () => [] },
});

const NEEDS = [
    ['Modal Kerja', 'modal_kerja'],
    ['Investasi', 'investasi'],
    ['Konsumtif', 'konsumtif'],
    ['Pelunasan Kredit', 'pelunasan_kredit'],
    ['Take Over', 'take_over'],
];

const RATES = [
    ['Biaya Admin (%)', 'b_admin'],
    ['Suku Bunga (%)', 's_bunga'],
    ['Biaya Provisi (%)', 'b_provisi'],
    ['Biaya Penalti (%)', 'b_penalti'],
];

const keys = [
    ...NEEDS.map(([, k]) => k),
    ...NEEDS.map(([, k]) => `ket_${k}`),
    ...RATES.map(([, k]) => k),
    'usulan_plafond',
    'jangka_waktu',
    'sebelum_realisasi',
    'syarat_tambahan',
    'pengikatan',
];

const form = useForm(Object.fromEntries(keys.map((key) => [key, props.memorandum[key] ?? ''])));

const totalNeed = computed(() => NEEDS.reduce((t, [, key]) => t + Number(form[key] || 0), 0));

const bindingOptions = props.bindings.map((value) => ({ value, label: value }));
const submit = () => form.put(`/analysis-simulation/${props.applicationId}/memorandum`, { preserveScroll: true });
</script>

<template>
    <div class="space-y-4" data-testid="memorandum-form">
        <Card data-testid="memorandum-needs">
            <CardHeader><CardTitle>Kebutuhan Dana</CardTitle></CardHeader>
            <CardContent class="form-dense space-y-3">
                <div v-for="[label, key] in NEEDS" :key="key" class="grid gap-3 sm:grid-cols-[16rem_minmax(0,1fr)]">
                    <FieldMoney v-model="form[key]" :label="label" :testid="`memo-${key}`" :error="form.errors[key]" />
                    <div class="space-y-[var(--item-gap)]">
                        <Label :for="`memo-ket-${key}`">Keterangan</Label>
                        <Input
                            :id="`memo-ket-${key}`"
                            v-model="form[`ket_${key}`]"
                            maxlength="255"
                            :data-testid="`memo-ket_${key}`"
                        />
                    </div>
                </div>
                <FieldStat
                    label="Jumlah Kebutuhan Dana"
                    :value="rupiah(totalNeed)"
                    strong
                    testid="memo-total-need"
                />
            </CardContent>
        </Card>

        <Card data-testid="memorandum-proposal">
            <CardHeader><CardTitle>Usulan Fasilitas</CardTitle></CardHeader>
            <CardContent class="form-dense space-y-4">
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <FieldStat label="Kebutuhan Dana" :value="rupiah(totalNeed)" />
                    <FieldStat
                        label="Plafon Diajukan"
                        :value="rupiah(props.memorandum.requested_amount)"
                        testid="memo-requested"
                    />
                    <FieldStat label="Taksasi Agunan" :value="rupiah(props.memorandum.taksasi)" />
                    <FieldStat
                        label="Keuangan Perbulan"
                        :value="rupiah(props.memorandum.monthly_balance)"
                        testid="memo-capacity"
                    />
                </div>

                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <FieldMoney
                        v-model="form.usulan_plafond"
                        label="Usulan Plafon"
                        testid="memo-usulan-plafond"
                        :error="form.errors.usulan_plafond"
                    />
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="memo-tenor">Jangka Waktu (Bulan)</Label>
                        <NumberInput
                            id="memo-tenor"
                            v-model="form.jangka_waktu"
                            class="text-right tabular-nums"
                            data-testid="memo-tenor"
                        />
                    </div>
                    <div v-for="[label, key] in RATES" :key="key" class="space-y-[var(--item-gap)]">
                        <Label :for="`memo-${key}`">{{ label }}</Label>
                        <DecimalInput
                            :id="`memo-${key}`"
                            v-model="form[key]"
                            class="text-right tabular-nums"
                            :data-testid="`memo-${key}`"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="memo-pengikatan">Pengikatan Asuransi</Label>
                        <Combobox
                            id="memo-pengikatan"
                            v-model="form.pengikatan"
                            :options="bindingOptions"
                            placeholder="(Opsional)"
                            data-testid="memo-pengikatan"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)] sm:col-span-2">
                        <Label for="memo-sebelum">Syarat Sebelum Realisasi</Label>
                        <Input
                            id="memo-sebelum"
                            v-model="form.sebelum_realisasi"
                            maxlength="255"
                            data-testid="memo-sebelum-realisasi"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)] sm:col-span-2">
                        <Label for="memo-syarat">Syarat Tambahan</Label>
                        <Input
                            id="memo-syarat"
                            v-model="form.syarat_tambahan"
                            maxlength="255"
                            data-testid="memo-syarat-tambahan"
                        />
                    </div>
                </div>

                <p class="text-xs text-muted-foreground">
                    Usulan plafon {{ rupiah(form.usulan_plafond || 0) }} · bunga {{ persen(form.s_bunga || 0) }} ·
                    provisi {{ persen(form.b_provisi || 0) }}. Bandingkan dengan taksasi agunan dan keuangan
                    per bulan di atas sebelum diajukan ke komite.
                </p>
            </CardContent>
        </Card>

        <div class="sticky bottom-3 rounded-md border bg-background/95 p-2 shadow-sm backdrop-blur">
            <FormActions
                cancel-testid="memorandum-reset"
                cancel-label="Kembalikan"
                submit-testid="memorandum-save"
                submit-label="Simpan Semua"
                :processing="form.processing"
                @cancel="form.reset()"
                @submit="submit"
            />
        </div>
    </div>
</template>
