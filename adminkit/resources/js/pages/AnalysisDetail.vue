<script setup>
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { ArrowLeft, ChevronLeft, ChevronRight, PencilRuler } from 'lucide-vue-next';

import AnalysisSectionNav from '@/components/composite/AnalysisSectionNav.vue';
import BusinessList from '@/components/composite/analysis/BusinessList.vue';
import FinanceForm from '@/components/composite/analysis/FinanceForm.vue';
import OwnershipForm from '@/components/composite/analysis/OwnershipForm.vue';
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

/** Sub-bagian Analisa Usaha = tipe usaha pada tabel `analysis_businesses`. */
const businessType = computed(() => (sub.value ?? '').toUpperCase());
const typeBusinesses = computed(() => props.businesses.filter((b) => b.type === businessType.value));

const filled = computed(() => ({
    usaha: props.businesses.length > 0,
    keuangan: props.sheet.metrics.household_cost > 0 || props.sheet.obligations.length > 0,
    kepemilikan: !!props.sheet.asset_house || props.sheet.assets.length > 0,
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
                <Badge variant="default" class="font-medium" data-testid="analysis-status">
                    {{ props.record.status }}
                </Badge>
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
        </div>
    </AppLayout>
</template>
