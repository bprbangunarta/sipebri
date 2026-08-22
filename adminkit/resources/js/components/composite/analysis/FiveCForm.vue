<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

import FieldStat from '@/components/composite/analysis/FieldStat.vue';
import FormActions from '@/components/composite/FormActions.vue';
import Badge from '@/components/ui/Badge.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
import Combobox from '@/components/ui/Combobox.vue';
import Label from '@/components/ui/Label.vue';
import { rupiah } from '@/constants/committee';
import { FIVE_C, FIVE_C_KEYS } from '@/constants/analysisAssessment';

/** Analisa 5C — satu lembar penuh, kolom evaluasi dihitung sistem. */
const props = defineProps({
    applicationId: { type: Number, required: true },
    fiveC: { type: Object, required: true },
});

const form = useForm(Object.fromEntries(FIVE_C_KEYS.map((key) => [key, props.fiveC[key] ?? ''])));

/** Persentase & predikat dihitung langsung agar analis melihat dampak tiap pilihan. */
const scoreOf = (group) => {
    let score = 0;
    let max = 0;
    let filled = 0;

    group.fields.forEach((field) => {
        if (form[field.key] === '' || form[field.key] === null) return;

        filled += 1;
        score += Number(form[field.key]);
        max += Math.max(...field.options.map((o) => Number(o.value)));
    });

    const percent = max > 0 ? Math.round((score / max) * 10000) / 100 : 0;

    return { filled, score, max, percent, grade: filled ? grade(percent) : null };
};

const grade = (percent) => (percent >= 80 ? 'BAIK' : percent >= 60 ? 'CUKUP BAIK' : 'KURANG BAIK');

const scores = computed(() => Object.fromEntries(FIVE_C.map((g) => [g.key, scoreOf(g)])));
const overall = computed(() => {
    const filled = Object.values(scores.value).filter((s) => s.filled > 0);
    if (!filled.length) return { percent: 0, grade: null };

    const percent = Math.round((filled.reduce((t, s) => t + s.percent, 0) / filled.length) * 100) / 100;

    return { percent, grade: grade(percent) };
});

const submit = () => form.put(`/analysis-simulation/${props.applicationId}/five-c`, { preserveScroll: true });
</script>

<template>
    <div class="space-y-4" data-testid="five-c-form">
        <Card v-for="group in FIVE_C" :key="group.key" :data-testid="`five-c-${group.key}`">
            <CardHeader class="flex flex-row flex-wrap items-center justify-between gap-2 space-y-0">
                <CardTitle>{{ group.title }}</CardTitle>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-muted-foreground tabular-nums">
                        {{ scores[group.key].filled }}/{{ group.fields.length }} aspek ·
                        {{ scores[group.key].score }}/{{ scores[group.key].max || 0 }}
                    </span>
                    <Badge
                        :variant="scores[group.key].grade ? 'default' : 'secondary'"
                        class="font-medium"
                        :data-testid="`five-c-${group.key}-grade`"
                    >
                        {{
                            scores[group.key].grade
                                ? `${scores[group.key].grade} · ${scores[group.key].percent}%`
                                : 'Belum dinilai'
                        }}
                    </Badge>
                </div>
            </CardHeader>
            <CardContent class="form-dense grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                <div v-for="field in group.fields" :key="field.key" class="space-y-[var(--item-gap)]">
                    <Label :for="`five-c-${field.key}`">{{ field.label }}</Label>
                    <Combobox
                        :id="`five-c-${field.key}`"
                        v-model="form[field.key]"
                        :options="field.options"
                        placeholder="(Opsional)"
                        :data-testid="`five-c-${field.key}`"
                    />
                    <p v-if="form.errors[field.key]" class="text-xs font-medium text-destructive">
                        {{ form.errors[field.key] }}
                    </p>
                </div>

                <FieldStat
                    v-if="group.key === 'collateral'"
                    label="Permohonan Taksasi Agunan"
                    :value="rupiah(props.fiveC.taksasi)"
                    testid="five-c-taksasi"
                />
            </CardContent>
        </Card>

        <Card data-testid="five-c-summary">
            <CardHeader><CardTitle>Evaluasi 5C</CardTitle></CardHeader>
            <CardContent class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                <FieldStat
                    v-for="group in FIVE_C"
                    :key="group.key"
                    :label="group.title"
                    :value="scores[group.key].grade ? `${scores[group.key].grade} · ${scores[group.key].percent}%` : '—'"
                    :testid="`five-c-eval-${group.key}`"
                />
                <FieldStat
                    label="Nilai Keseluruhan"
                    :value="overall.grade ? `${overall.grade} · ${overall.percent}%` : '—'"
                    strong
                    testid="five-c-overall"
                />
                <p class="text-xs text-muted-foreground sm:col-span-2 xl:col-span-3">
                    Evaluasi dihitung dari persentase skor terhadap nilai maksimum aspek yang sudah dinilai:
                    ≥ 80% BAIK, ≥ 60% CUKUP BAIK, sisanya KURANG BAIK.
                </p>
            </CardContent>
        </Card>

        <div class="sticky bottom-3 rounded-md border bg-background/95 p-2 shadow-sm backdrop-blur">
            <FormActions
                cancel-testid="five-c-reset"
                cancel-label="Kembalikan"
                submit-testid="five-c-save"
                submit-label="Simpan Semua"
                :processing="form.processing"
                @cancel="form.reset()"
                @submit="submit"
            />
        </div>
    </div>
</template>
