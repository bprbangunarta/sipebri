<script setup>
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

import FieldStat from '@/components/composite/analysis/FieldStat.vue';
import FormActions from '@/components/composite/FormActions.vue';
import Combobox from '@/components/ui/Combobox.vue';
import DecimalInput from '@/components/ui/DecimalInput.vue';
import Label from '@/components/ui/Label.vue';
import NumberInput from '@/components/ui/NumberInput.vue';
import Textarea from '@/components/ui/Textarea.vue';
import Dialog from '@/components/ui/Dialog.vue';
import { maxPlafon, persen, rcRatio, rupiah } from '@/constants/committee';

/** Dialog keputusan satu jenjang komite (cermin dialog "Persetujuan Komite" sistem lama). */
const props = defineProps({
    open: { type: Boolean, default: false },
    applicationId: { type: Number, required: true },
    basis: { type: Object, required: true },
    methodOptions: { type: Array, default: () => [] },
    allowed: { type: Array, default: () => [] },
    tierLabel: { type: String, default: '' },
    requestedAmount: { type: Number, default: 0 },
});

const emit = defineEmits(['update:open']);

const DECISION_LABEL = {
    TERUSKAN: 'Teruskan (Naik Komite)',
    DISETUJUI: 'Disetujui',
    DITOLAK: 'Ditolak',
    DIBATALKAN: 'Dibatalkan',
};

const decisionOptions = computed(() =>
    props.allowed.map((value) => ({ value, label: DECISION_LABEL[value] ?? value })),
);

const initial = () => ({
    decision: '',
    method_id: props.basis.method_id ?? '',
    amount: props.basis.amount ?? '',
    tenor: props.basis.tenor ?? '',
    interest_rate: props.basis.interest_rate ?? '',
    provision_rate: props.basis.provision_rate ?? '',
    admin_rate: props.basis.admin_rate ?? '',
    note: '',
});

const form = useForm(initial());

watch(
    () => props.open,
    (open) => {
        if (open) form.defaults(initial()).reset().clearErrors();
    },
);

const methodLabel = computed(
    () => props.methodOptions.find((o) => String(o.value) === String(form.method_id))?.label ?? '',
);

const ceiling = computed(() =>
    maxPlafon({
        capacity: props.basis.capacity,
        rcThreshold: props.basis.rc_threshold,
        rate: form.interest_rate,
        tenor: form.tenor,
        method: methodLabel.value,
    }),
);

const rc = computed(() => rcRatio(form.amount || 0, ceiling.value));
const overCapacity = computed(() => rc.value > 100);

const submit = () =>
    form.post(`/approval-simulation/${props.applicationId}/decision`, {
        preserveScroll: true,
        onSuccess: () => emit('update:open', false),
    });
</script>

<template>
    <Dialog
        :open="props.open"
        title="Persetujuan Komite"
        class="max-h-[90vh] max-w-2xl overflow-y-auto"
        @update:open="emit('update:open', $event)"
    >
        <form
            id="approval-decision-form"
            class="form-dense space-y-[var(--field-gap)]"
            novalidate
            @submit.prevent="submit"
        >
            <p class="text-xs text-muted-foreground" data-testid="approval-decision-tier">
                Jenjang pemutus: <span class="font-medium text-foreground">{{ props.tierLabel }}</span>
            </p>

            <div class="grid gap-3 sm:grid-cols-2">
                <FieldStat
                    label="Max Plafon"
                    :value="rupiah(ceiling)"
                    strong
                    testid="approval-max-plafon"
                />
                <FieldStat
                    label="RC"
                    :value="persen(rc)"
                    :strong="overCapacity"
                    testid="approval-rc"
                />
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div class="space-y-[var(--item-gap)]">
                    <Label for="approval-method">Metode RPS</Label>
                    <Combobox
                        id="approval-method"
                        v-model="form.method_id"
                        :options="props.methodOptions"
                        placeholder="-- Pilih --"
                        data-testid="approval-form-method"
                    />
                    <p v-if="form.errors.method_id" class="text-xs font-medium text-destructive">
                        {{ form.errors.method_id }}
                    </p>
                </div>
                <div class="space-y-[var(--item-gap)]">
                    <Label for="approval-decision">Keputusan Komite</Label>
                    <Combobox
                        id="approval-decision"
                        v-model="form.decision"
                        :options="decisionOptions"
                        placeholder="-- Pilih --"
                        data-testid="approval-form-decision"
                    />
                    <p v-if="form.errors.decision" class="text-xs font-medium text-destructive" data-testid="approval-form-decision-error">
                        {{ form.errors.decision }}
                    </p>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="space-y-[var(--item-gap)]">
                    <Label for="approval-provision">Biaya Provisi (%)</Label>
                    <DecimalInput
                        id="approval-provision"
                        v-model="form.provision_rate"
                        class="text-right tabular-nums"
                        data-testid="approval-form-provision"
                    />
                </div>
                <div class="space-y-[var(--item-gap)]">
                    <Label for="approval-admin">Biaya Admin (%)</Label>
                    <DecimalInput
                        id="approval-admin"
                        v-model="form.admin_rate"
                        class="text-right tabular-nums"
                        data-testid="approval-form-admin"
                    />
                </div>
                <div class="space-y-[var(--item-gap)]">
                    <Label for="approval-interest">Suku Bunga (%)</Label>
                    <DecimalInput
                        id="approval-interest"
                        v-model="form.interest_rate"
                        class="text-right tabular-nums"
                        data-testid="approval-form-interest"
                    />
                    <p v-if="form.errors.interest_rate" class="text-xs font-medium text-destructive">
                        {{ form.errors.interest_rate }}
                    </p>
                </div>
                <div class="space-y-[var(--item-gap)]">
                    <Label for="approval-tenor">Jangka (Bulan)</Label>
                    <NumberInput
                        id="approval-tenor"
                        v-model="form.tenor"
                        class="text-right tabular-nums"
                        data-testid="approval-form-tenor"
                    />
                    <p v-if="form.errors.tenor" class="text-xs font-medium text-destructive">
                        {{ form.errors.tenor }}
                    </p>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div class="space-y-[var(--item-gap)]">
                    <Label for="approval-amount">Usulan Plafon</Label>
                    <NumberInput
                        id="approval-amount"
                        v-model="form.amount"
                        class="text-right tabular-nums"
                        data-testid="approval-form-amount"
                    />
                    <p v-if="form.errors.amount" class="text-xs font-medium text-destructive">
                        {{ form.errors.amount }}
                    </p>
                </div>
                <FieldStat
                    label="Plafon Diajukan Pemohon"
                    :value="rupiah(props.requestedAmount)"
                    testid="approval-requested"
                />
            </div>

            <div class="space-y-[var(--item-gap)]">
                <Label for="approval-note">Catatan Komite</Label>
                <Textarea
                    id="approval-note"
                    v-model="form.note"
                    :rows="3"
                    maxlength="255"
                    placeholder="(Opsional)"
                    data-testid="approval-form-note"
                />
                <p v-if="form.errors.note" class="text-xs font-medium text-destructive">{{ form.errors.note }}</p>
            </div>

            <p class="text-xs font-medium text-destructive" data-testid="approval-form-hint">
                Untuk keputusan TOLAK / BATAL, isi Usulan Plafon sesuai nominal permohonan.
            </p>
            <p v-if="overCapacity" class="text-xs text-amber-600 dark:text-amber-500" data-testid="approval-rc-warning">
                RC {{ persen(rc) }} melewati 100% — usulan plafon di atas kemampuan angsuran
                ({{ rupiah(ceiling) }}).
            </p>
        </form>

        <template #footer>
            <FormActions
                cancel-testid="approval-form-cancel"
                submit-testid="approval-form-save"
                submit-type="submit"
                form="approval-decision-form"
                :processing="form.processing"
                @cancel="emit('update:open', false)"
            />
        </template>
    </Dialog>
</template>
