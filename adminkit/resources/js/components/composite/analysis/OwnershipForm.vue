<script setup>
import { useForm } from '@inertiajs/vue3';

import ItemRows from '@/components/composite/analysis/ItemRows.vue';
import FormActions from '@/components/composite/FormActions.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardFooter from '@/components/ui/CardFooter.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
import Combobox from '@/components/ui/Combobox.vue';
import Label from '@/components/ui/Label.vue';
import { ASSET_LABELS } from '@/constants/analysisMath';

/** Analisa Kepemilikan — harta yang dimiliki pemohon. */
const props = defineProps({
    applicationId: { type: Number, required: true },
    sheet: { type: Object, required: true },
    assets: { type: Object, required: true },
});

const keys = Object.keys(ASSET_LABELS);

const form = useForm({
    ...Object.fromEntries(keys.map((key) => [key, props.sheet[key] ?? ''])),
    items: props.sheet.assets.map((a) => ({ ...a })),
});

const options = (key) => (props.assets[key] ?? []).map((value) => ({ value, label: value }));

const submit = () => form.put(`/analysis-simulation/${props.applicationId}/ownership`, { preserveScroll: true });
</script>

<template>
    <Card data-testid="ownership-form">
        <CardHeader><CardTitle>Harta Kepemilikan</CardTitle></CardHeader>
        <CardContent class="form-dense space-y-4">
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <div v-for="key in keys" :key="key" class="space-y-[var(--item-gap)]">
                    <Label :for="`ownership-${key}`">{{ ASSET_LABELS[key] }}</Label>
                    <Combobox
                        :id="`ownership-${key}`"
                        v-model="form[key]"
                        :options="options(key)"
                        placeholder="(Opsional)"
                        :data-testid="`ownership-${key}`"
                    />
                    <p v-if="form.errors[key]" class="text-xs font-medium text-destructive">{{ form.errors[key] }}</p>
                </div>
            </div>

            <div class="space-y-[var(--item-gap)]">
                <Label>Harta Lain</Label>
                <ItemRows
                    :rows="form.items"
                    :columns="[{ key: 'name', label: 'Nama Harta', type: 'text' }]"
                    :blank="{ name: '' }"
                    add-label="Tambah Harta"
                    empty-text="Belum ada harta lain yang dicatat."
                    testid="ownership-asset"
                />
            </div>
        </CardContent>
        <CardFooter>
            <FormActions
                cancel-testid="ownership-reset"
                cancel-label="Kembalikan"
                submit-testid="ownership-save"
                :processing="form.processing"
                @cancel="form.reset()"
                @submit="submit"
            />
        </CardFooter>
    </Card>
</template>
