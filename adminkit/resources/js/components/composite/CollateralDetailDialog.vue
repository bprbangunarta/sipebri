<script setup>
import { computed } from 'vue';
import { X } from 'lucide-vue-next';

import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Dialog from '@/components/ui/Dialog.vue';
import { ACTION } from '@/constants/labels';
import { rupiah } from '@/constants/committee';

const props = defineProps({
    open: { type: Boolean, default: false },
    row: { type: Object, default: null },
});
defineEmits(['update:open']);

const dash = (v) => (v === null || v === undefined || v === '' ? '—' : v);
const money = (v) => (v === null || v === undefined || v === '' ? '—' : rupiah(v));
const date = (v) =>
    v ? new Date(`${v}T00:00:00`).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '—';
const bool = (v) => (v ? 'Ya' : 'Tidak');
const code = (c, label) => (c ? `${c}${label ? ` : ${label}` : ''}` : '—');

// Semua kolom tabel collateral_simulations ditampilkan supaya mudah dievaluasi.
const sections = computed(() => {
    const r = props.row;
    if (!r) return [];

    return [
        {
            title: 'Identitas',
            items: [
                { label: 'Agunan ID', value: dash(r.collateral_id), mono: true },
                { label: 'No. Berkas (no_rek)', value: dash(r.file_number), mono: true },
                { label: 'Nomor Otomatis', value: bool(r.auto_number) },
                { label: 'Paripasu', value: `${r.paripasu ?? 0}%` },
                { label: 'Jenis Agunan', value: code(r.collateral_type_code, r.type_label) },
                { label: 'Jenis Pengikatan', value: code(r.binding_type_code, r.binding_label) },
                { label: 'Kepemilikan', value: dash(r.ownership) },
                { label: 'No. Dokumen', value: dash(r.document_number), mono: true },
                { label: 'Peringkat SB', value: dash(r.securities_rank) },
                { label: 'Pemeringkat SB', value: dash(r.rating_agency) },
            ],
        },
        {
            title: 'Pemilik & Lokasi',
            items: [
                { label: 'Nama Pemilik', value: dash(r.owner_name) },
                { label: 'Sama dengan CIF', value: bool(r.owner_same_as_cif) },
                { label: 'Alamat Agunan', value: dash(r.owner_address), span: 2 },
                { label: 'Kode Lokasi', value: dash(r.region_code), mono: true },
                { label: 'Lokasi Agunan', value: dash(r.region_label), span: 2 },
                { label: 'Keterangan Agunan', value: dash(r.description), span: 2 },
            ],
        },
        {
            title: 'Nilai Agunan',
            items: [
                { label: 'Nilai Jaminan', value: money(r.value_guarantee), num: true },
                { label: 'Nilai Pasar', value: money(r.value_fair), num: true },
                { label: 'Nilai NJOP', value: money(r.value_njop), num: true },
                { label: 'Adjusment', value: money(r.value_adjustment), num: true },
                { label: 'Nilai Taksasi', value: money(r.value_appraisal), num: true },
                { label: 'Nilai Apraisal', value: money(r.value_independent), num: true },
            ],
        },
        {
            title: 'Penaksir',
            items: [
                { label: 'Penaksir Internal', value: dash(r.appraiser_name) },
                { label: 'Tgl Taksasi', value: date(r.appraised_at) },
                { label: 'Penaksir Independen', value: dash(r.independent_appraiser_name) },
                { label: 'Tgl Apraisal', value: date(r.independent_appraised_at) },
            ],
        },
        {
            title: 'Kondisi, Asuransi & PPAP',
            items: [
                { label: 'Kondisi', value: code(r.condition_code, r.condition_label) },
                { label: 'Tgl Kondisi', value: date(r.condition_date) },
                { label: 'Diasuransikan', value: r.insured === 'Y' ? 'Ya' : 'Tidak' },
                { label: 'Tgl Asuransi', value: date(r.insurance_start_date) },
                { label: 'Metode Hitung (PPAP)', value: code(r.ppap_code, r.method_label) },
            ],
        },
        {
            title: 'Rekam Jejak',
            items: [
                { label: 'Dibuat', value: dash(r.created_at) },
                { label: 'Diperbarui', value: dash(r.updated_at) },
            ],
        },
    ];
});
</script>

<!-- Detail agunan: seluruh kolom ditampilkan dalam grid ringkas per kelompok. -->
<template>
    <Dialog
        :open="props.open"
        title="Detail Agunan"
        class="max-w-3xl"
        @update:open="$emit('update:open', $event)"
    >
        <div v-if="props.row" class="space-y-3" data-testid="collateral-simulation-detail">
            <div class="flex flex-wrap items-center gap-2 rounded-md border bg-muted/40 px-3 py-2">
                <Badge variant="secondary" class="font-mono font-medium">
                    {{ props.row.collateral_id ?? `#${props.row.id}` }}
                </Badge>
                <span class="text-xs font-medium">{{ dash(props.row.owner_name) }}</span>
                <span class="ml-auto text-xs text-muted-foreground">
                    Taksasi: <span class="font-medium tabular-nums text-foreground">{{ money(props.row.value_appraisal) }}</span>
                </span>
            </div>

            <div class="thin-scroll max-h-[60vh] space-y-3 overflow-auto pr-1">
                <section v-for="section in sections" :key="section.title" class="space-y-1.5">
                    <h3 class="text-[11px] font-semibold uppercase tracking-wide text-muted-foreground">
                        {{ section.title }}
                    </h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-1.5 border-t pt-2 sm:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="item in section.items"
                            :key="item.label"
                            class="min-w-0"
                            :class="item.span === 2 ? 'sm:col-span-2' : ''"
                        >
                            <dt class="text-[11px] leading-4 text-muted-foreground">{{ item.label }}</dt>
                            <dd
                                class="break-words text-xs font-medium leading-5"
                                :class="[item.mono ? 'font-mono' : '', item.num ? 'tabular-nums' : '']"
                            >
                                {{ item.value }}
                            </dd>
                        </div>
                    </dl>
                </section>
            </div>
        </div>

        <template #footer>
            <Button
                variant="outline"
                size="sm"
                class="ml-auto"
                data-testid="collateral-simulation-detail-close"
                @click="$emit('update:open', false)"
            >
                <X class="size-4" /> {{ ACTION.close }}
            </Button>
        </template>
    </Dialog>
</template>
