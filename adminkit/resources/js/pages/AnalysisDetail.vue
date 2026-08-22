<script setup>
import { computed, ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle2, ChevronLeft, ChevronRight, PencilRuler, SendHorizontal } from 'lucide-vue-next';

import ConfirmDialogBase from '@/components/ui/Dialog.vue';
import FormActions from '@/components/composite/FormActions.vue';
import AnalysisSectionNav from '@/components/composite/AnalysisSectionNav.vue';
import BusinessList from '@/components/composite/analysis/BusinessList.vue';
import FinanceForm from '@/components/composite/analysis/FinanceForm.vue';
import AdministrationForm from '@/components/composite/analysis/AdministrationForm.vue';
import CollateralAnalysisForm from '@/components/composite/analysis/CollateralAnalysisForm.vue';
import FiveCForm from '@/components/composite/analysis/FiveCForm.vue';
import MemorandumForm from '@/components/composite/analysis/MemorandumForm.vue';
import OwnershipForm from '@/components/composite/analysis/OwnershipForm.vue';
import QualitativeForm from '@/components/composite/analysis/QualitativeForm.vue';
import AppLayout from '@/components/layout/AppLayout.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
import { ANALYSIS_SECTIONS } from '@/constants/analysis';
import { rupiah } from '@/constants/committee';

const props = defineProps({
    record: { type: Object, required: true },
    businesses: { type: Array, default: () => [] },
    sheet: { type: Object, required: true },
    fiveC: { type: Object, required: true },
    qualitative: { type: Object, required: true },
    collaterals: { type: Array, default: () => [] },
    memorandum: { type: Object, required: true },
    administration: { type: Object, required: true },
    submission: { type: Object, required: true },
    options: { type: Object, required: true },
});

const activeKey = ref(ANALYSIS_SECTIONS[0].key);
const activeSub = ref({});

const section = computed(() => ANALYSIS_SECTIONS.find((s) => s.key === activeKey.value));
const index = computed(() => ANALYSIS_SECTIONS.findIndex((s) => s.key === activeKey.value));
const sub = computed(() => activeSub.value[activeKey.value] ?? section.value.subs[0]?.key ?? null);
const subLabel = computed(() => section.value.subs.find((s) => s.key === sub.value)?.label ?? null);

const select = (key) => {
    activeKey.value = key;
};
const step = (delta) => select(ANALYSIS_SECTIONS[index.value + delta].key);

/** Pengajuan ke komite hanya boleh saat seluruh bagian wajib sudah terisi. */
const showSubmit = ref(false);
const submitForm = useForm({});
const canSubmit = computed(() => props.submission.gaps.length === 0 && props.record.status !== 'KOMITE');

const submitToCommittee = () =>
    submitForm.post(`/analysis-simulation/${props.record.id}/submit`, {
        onFinish: () => (showSubmit.value = false),
    });

/** Sub-bagian Analisa Usaha = tipe usaha pada tabel `analysis_businesses`. */
const businessType = computed(() => (sub.value ?? '').toUpperCase());
const typeBusinesses = computed(() => props.businesses.filter((b) => b.type === businessType.value));

const filled = computed(() => ({
    usaha: props.businesses.length > 0,
    keuangan: props.sheet.metrics.household_cost > 0 || props.sheet.obligations.length > 0,
    kepemilikan: !!props.sheet.asset_house || props.sheet.assets.length > 0,
    'lima-c': props.fiveC.metrics.grade !== null,
    kualitatif: !!props.qualitative.bi_checking || !!props.qualitative.catatan,
    agunan: props.collaterals.some((c) => c.appraisal_value > 0 || c.lokasi),
    memorandum: !!props.memorandum.usulan_plafond,
    administrasi: props.administration.total > 0,
}));
</script>

<template>
    <Head :title="`Analisa ${props.record.application_code}`" />
    <AppLayout>
        <div class="space-y-4" data-testid="analysis-detail-page">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <Button
                        variant="ghost"
                        size="icon"
                        data-testid="analysis-back"
                        @click="router.visit('/analysis-simulation')"
                    >
                        <ArrowLeft class="size-4" />
                    </Button>
                    <div>
                        <p class="font-mono text-sm font-semibold">{{ props.record.application_code }}</p>
                        <p class="text-xs text-muted-foreground">
                            {{ props.record.full_name }} · {{ props.record.nik }}
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <Badge variant="default" class="font-medium" data-testid="analysis-status">
                        {{ props.record.status }}
                    </Badge>
                    <Button
                        v-if="props.record.status !== 'KOMITE'"
                        size="sm"
                        :disabled="!canSubmit"
                        data-testid="analysis-submit"
                        @click="showSubmit = true"
                    >
                        <SendHorizontal class="size-4" /> Ajukan ke Komite
                    </Button>
                    <span v-else class="text-xs text-muted-foreground" data-testid="analysis-submitted-info">
                        Diajukan {{ props.submission.submitted_at }} oleh {{ props.submission.submitted_by }}
                    </span>
                </div>
            </div>

            <Card>
                <CardHeader><CardTitle>Data Pengajuan</CardTitle></CardHeader>
                <CardContent class="grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <p class="text-[11px] uppercase tracking-wider text-muted-foreground">Produk</p>
                        <p>{{ props.record.product_label ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-wider text-muted-foreground">Plafon / Jangka</p>
                        <p class="tabular-nums">
                            {{ rupiah(props.record.requested_amount) }} ·
                            {{ props.record.requested_tenor ? `${props.record.requested_tenor} bln` : '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-wider text-muted-foreground">Suku Bunga</p>
                        <p class="tabular-nums">{{ props.record.interest_rate ?? '—' }}%</p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-wider text-muted-foreground">Penggunaan</p>
                        <p>{{ props.record.usage_type ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-wider text-muted-foreground">Kantor</p>
                        <p>{{ props.record.office_label ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-wider text-muted-foreground">Kasi Analis</p>
                        <p>{{ props.record.supervisor_name ?? '—' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-[11px] uppercase tracking-wider text-muted-foreground">Hasil Survei</p>
                        <p>{{ props.record.survey_note ?? 'Tanpa survei lapangan (produk KTA).' }}</p>
                        <p v-if="props.record.survey_at" class="text-xs text-muted-foreground">
                            {{ props.record.survey_by }} · {{ props.record.survey_at }}
                        </p>
                    </div>
                </CardContent>
            </Card>

            <div
                v-if="props.submission.gaps.length && props.record.status !== 'KOMITE'"
                class="rounded-md border border-amber-500/40 bg-amber-500/10 px-3 py-2 text-xs text-amber-700 dark:text-amber-400"
                data-testid="analysis-gaps"
            >
                Belum bisa diajukan ke komite — lengkapi: {{ props.submission.gaps.join(', ') }}.
            </div>

            <div class="grid gap-4 lg:grid-cols-[15rem_minmax(0,1fr)]">
                <div class="lg:sticky lg:top-20 lg:self-start">
                    <AnalysisSectionNav
                        :sections="ANALYSIS_SECTIONS"
                        :active="activeKey"
                        @select="select"
                    />
                </div>

                <div class="space-y-3">
                    <Card data-testid="analysis-section-panel">
                        <CardHeader class="flex flex-row flex-wrap items-center justify-between gap-2 space-y-0">
                            <CardTitle>{{ section.label }}</CardTitle>
                            <Badge
                                :variant="filled[activeKey] ? 'default' : 'secondary'"
                                class="font-medium"
                                data-testid="analysis-section-state"
                            >
                                {{ filled[activeKey] ? 'Sudah diisi' : 'Belum diisi' }}
                            </Badge>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <div
                                v-if="section.subs.length"
                                class="flex flex-wrap gap-1 rounded-md border bg-muted/40 p-1"
                                data-testid="analysis-subsection-tabs"
                            >
                                <button
                                    v-for="item in section.subs"
                                    :key="item.key"
                                    type="button"
                                    class="rounded px-3 py-1.5 text-xs font-medium transition-colors"
                                    :class="
                                        sub === item.key
                                            ? 'bg-background text-foreground shadow-sm'
                                            : 'text-muted-foreground hover:text-foreground'
                                    "
                                    :data-testid="`analysis-sub-${item.key}`"
                                    @click="activeSub[activeKey] = item.key"
                                >
                                    {{ item.label }}
                                </button>
                            </div>

                            <BusinessList
                                v-if="activeKey === 'usaha'"
                                :key="businessType"
                                :application-id="props.record.id"
                                :type="businessType"
                                :type-label="subLabel"
                                :businesses="typeBusinesses"
                            />

                            <FinanceForm
                                v-else-if="activeKey === 'keuangan'"
                                :application-id="props.record.id"
                                :sheet="props.sheet"
                            />

                            <OwnershipForm
                                v-else-if="activeKey === 'kepemilikan'"
                                :application-id="props.record.id"
                                :sheet="props.sheet"
                                :assets="props.options.assets"
                            />

                            <FiveCForm
                                v-else-if="activeKey === 'lima-c'"
                                :application-id="props.record.id"
                                :five-c="props.fiveC"
                            />

                            <QualitativeForm
                                v-else-if="activeKey === 'kualitatif'"
                                :application-id="props.record.id"
                                :qualitative="props.qualitative"
                                :choices="props.options.qualitativeChoices"
                            />

                            <CollateralAnalysisForm
                                v-else-if="activeKey === 'agunan'"
                                :application-id="props.record.id"
                                :collaterals="props.collaterals"
                                :kinds="props.options.collateralKinds"
                            />

                            <MemorandumForm
                                v-else-if="activeKey === 'memorandum'"
                                :application-id="props.record.id"
                                :memorandum="props.memorandum"
                                :bindings="props.options.bindings"
                            />

                            <AdministrationForm
                                v-else-if="activeKey === 'administrasi'"
                                :application-id="props.record.id"
                                :administration="props.administration"
                            />

                            <div
                                v-else
                                class="flex min-h-[260px] flex-col items-center justify-center gap-2 rounded-md border border-dashed p-6 text-center"
                                data-testid="analysis-section-empty"
                            >
                                <PencilRuler class="size-5 text-muted-foreground" />
                                <p class="text-sm font-medium">
                                    {{ subLabel ? `${section.label} — ${subLabel}` : section.label }}
                                </p>
                                <p class="max-w-md text-xs text-muted-foreground">
                                    Kolom bagian ini belum dibuat. Kerangka lembar analisa sudah siap dan isinya
                                    akan dibahas satu per satu.
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <div class="flex items-center justify-between gap-2">
                        <Button
                            variant="outline"
                            size="sm"
                            :disabled="index === 0"
                            data-testid="analysis-prev-section"
                            @click="step(-1)"
                        >
                            <ChevronLeft class="size-4" /> Sebelumnya
                        </Button>
                        <span class="text-xs text-muted-foreground tabular-nums">
                            Bagian {{ index + 1 }} dari {{ ANALYSIS_SECTIONS.length }}
                        </span>
                        <Button
                            variant="outline"
                            size="sm"
                            :disabled="index === ANALYSIS_SECTIONS.length - 1"
                            data-testid="analysis-next-section"
                            @click="step(1)"
                        >
                            Selanjutnya <ChevronRight class="size-4" />
                        </Button>
                    </div>
                </div>
            </div>

            <ConfirmDialogBase
                :open="showSubmit"
                title="Ajukan ke Komite Kredit?"
                @update:open="showSubmit = $event"
            >
                <div class="space-y-2 text-sm">
                    <p>
                        Berkas <span class="font-mono font-semibold">{{ props.record.application_code }}</span>
                        akan berpindah ke tahap komite dan lembar analisa tidak lagi muncul di daftar analisa Anda.
                    </p>
                    <div class="space-y-1 rounded-md border bg-muted/40 p-3">
                        <p v-for="section in ANALYSIS_SECTIONS" :key="section.key" class="flex items-center gap-2">
                            <CheckCircle2
                                class="size-4"
                                :class="filled[section.key] ? 'text-primary' : 'text-muted-foreground/40'"
                            />
                            <span :class="filled[section.key] ? '' : 'text-muted-foreground'">
                                {{ section.label }}
                            </span>
                        </p>
                    </div>
                </div>

                <template #footer>
                    <FormActions
                        cancel-testid="analysis-submit-cancel"
                        submit-testid="analysis-submit-confirm"
                        submit-label="Ajukan"
                        :processing="submitForm.processing"
                        @cancel="showSubmit = false"
                        @submit="submitToCommittee"
                    />
                </template>
            </ConfirmDialogBase>
        </div>
    </AppLayout>
</template>
