<script setup>
import { useForm } from '@inertiajs/vue3';

import FormActions from '@/components/composite/FormActions.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
import Combobox from '@/components/ui/Combobox.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import Textarea from '@/components/ui/Textarea.vue';
import {
    QUALITATIVE_BUSINESS,
    QUALITATIVE_CHARACTER_TEXTS,
    QUALITATIVE_CHOICE_FIELDS,
    QUALITATIVE_KEYS,
    QUALITATIVE_SCORES,
    QUALITATIVE_SWOT,
} from '@/constants/analysisAssessment';

/** Analisa Kualitatif — satu lembar penuh: karakter, usaha, SWOT, catatan. */
const props = defineProps({
    applicationId: { type: Number, required: true },
    qualitative: { type: Object, required: true },
    choices: { type: Object, required: true },
});

const form = useForm(Object.fromEntries(QUALITATIVE_KEYS.map((key) => [key, props.qualitative[key] ?? ''])));

const options = (key) => (props.choices[key] ?? []).map((value) => ({ value, label: value }));
const rows = [1, 2, 3];

const submit = () => form.put(`/analysis-simulation/${props.applicationId}/qualitative`, { preserveScroll: true });
</script>

<template>
    <div class="space-y-4" data-testid="qualitative-form">
        <Card data-testid="qualitative-character">
            <CardHeader><CardTitle>Karakter</CardTitle></CardHeader>
            <CardContent class="form-dense space-y-4">
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <div v-for="field in QUALITATIVE_SCORES" :key="field.key" class="space-y-[var(--item-gap)]">
                        <Label :for="`qual-${field.key}`">{{ field.label }}</Label>
                        <Combobox
                            :id="`qual-${field.key}`"
                            v-model="form[field.key]"
                            :options="field.options"
                            placeholder="(Opsional)"
                            :data-testid="`qual-${field.key}`"
                        />
                    </div>
                    <div v-for="[label, key] in QUALITATIVE_CHOICE_FIELDS" :key="key" class="space-y-[var(--item-gap)]">
                        <Label :for="`qual-${key}`">{{ label }}</Label>
                        <Combobox
                            :id="`qual-${key}`"
                            v-model="form[key]"
                            :options="options(key)"
                            placeholder="(Opsional)"
                            :data-testid="`qual-${key}`"
                        />
                    </div>
                    <div v-for="[label, key] in QUALITATIVE_CHARACTER_TEXTS" :key="key" class="space-y-[var(--item-gap)]">
                        <Label :for="`qual-${key}`">{{ label }}</Label>
                        <Input
                            :id="`qual-${key}`"
                            v-model="form[key]"
                            maxlength="255"
                            :data-testid="`qual-${key}`"
                        />
                    </div>
                </div>

                <div class="space-y-2">
                    <Label>Kewajiban ke Pihak Lain</Label>
                    <div
                        v-for="i in rows"
                        :key="i"
                        class="grid gap-3 rounded-md border bg-muted/30 p-3 sm:grid-cols-3"
                    >
                        <div class="space-y-[var(--item-gap)]">
                            <Label :for="`qual-kewajiban${i}`" class="text-xs">Pihak {{ i }}</Label>
                            <Combobox
                                :id="`qual-kewajiban${i}`"
                                v-model="form[`kewajiban${i}`]"
                                :options="options(`kewajiban${i}`)"
                                placeholder="(Opsional)"
                                :data-testid="`qual-kewajiban${i}`"
                            />
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label :for="`qual-ket-kewajiban${i}`" class="text-xs">Keterangan</Label>
                            <Input
                                :id="`qual-ket-kewajiban${i}`"
                                v-model="form[`ket_kewajiban${i}`]"
                                maxlength="255"
                                :data-testid="`qual-ket_kewajiban${i}`"
                            />
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label :for="`qual-status${i}`" class="text-xs">Status</Label>
                            <Combobox
                                :id="`qual-status${i}`"
                                v-model="form[`status${i}`]"
                                :options="options(`status${i}`)"
                                placeholder="(Opsional)"
                                :data-testid="`qual-status${i}`"
                            />
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>

        <Card data-testid="qualitative-business">
            <CardHeader><CardTitle>Usaha</CardTitle></CardHeader>
            <CardContent class="form-dense space-y-3">
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <div v-for="[label, key] in QUALITATIVE_BUSINESS" :key="key" class="space-y-[var(--item-gap)]">
                        <Label :for="`qual-${key}`">{{ label }}</Label>
                        <Input :id="`qual-${key}`" v-model="form[key]" maxlength="255" :data-testid="`qual-${key}`" />
                    </div>
                </div>
                <div class="space-y-[var(--item-gap)]">
                    <Label for="qual-trade-checking">Trade Checking</Label>
                    <Textarea
                        id="qual-trade-checking"
                        v-model="form.trade_checking"
                        rows="3"
                        maxlength="2000"
                        placeholder="(Opsional)"
                        data-testid="qual-trade_checking"
                    />
                </div>
            </CardContent>
        </Card>

        <Card data-testid="qualitative-swot">
            <CardHeader><CardTitle>SWOT</CardTitle></CardHeader>
            <CardContent class="form-dense grid gap-3 sm:grid-cols-2">
                <div v-for="[label, key] in QUALITATIVE_SWOT" :key="key" class="space-y-[var(--item-gap)]">
                    <Label :for="`qual-${key}`">{{ label }}</Label>
                    <Input :id="`qual-${key}`" v-model="form[key]" maxlength="255" :data-testid="`qual-${key}`" />
                </div>
            </CardContent>
        </Card>

        <Card data-testid="qualitative-notes">
            <CardHeader><CardTitle>Lainnya</CardTitle></CardHeader>
            <CardContent class="form-dense grid gap-3 sm:grid-cols-2">
                <div class="space-y-[var(--item-gap)]">
                    <Label for="qual-catatan">Catatan</Label>
                    <Textarea
                        id="qual-catatan"
                        v-model="form.catatan"
                        rows="4"
                        maxlength="2000"
                        placeholder="(Opsional)"
                        data-testid="qual-catatan"
                    />
                </div>
                <div class="space-y-[var(--item-gap)]">
                    <Label for="qual-trade-usaha">Trade Checking Usaha</Label>
                    <Textarea
                        id="qual-trade-usaha"
                        v-model="form.trade_checking_usaha"
                        rows="4"
                        maxlength="2000"
                        placeholder="(Opsional)"
                        data-testid="qual-trade_checking_usaha"
                    />
                </div>
            </CardContent>
        </Card>

        <div class="sticky bottom-3 rounded-md border bg-background/95 p-2 shadow-sm backdrop-blur">
            <FormActions
                cancel-testid="qualitative-reset"
                cancel-label="Kembalikan"
                submit-testid="qualitative-save"
                submit-label="Simpan Semua"
                :processing="form.processing"
                @cancel="form.reset()"
                @submit="submit"
            />
        </div>
    </div>
</template>
