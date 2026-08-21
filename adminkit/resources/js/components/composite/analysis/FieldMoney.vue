<script setup>
import Label from '@/components/ui/Label.vue';
import NumberInput from '@/components/ui/NumberInput.vue';

/** Kolom nominal rupiah dengan awalan "Rp" — dipakai seluruh form analisa. */
const props = defineProps({
    label: { type: String, required: true },
    modelValue: { type: [Number, String], default: '' },
    testid: { type: String, required: true },
    error: { type: String, default: '' },
    disabled: { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue']);
</script>

<template>
    <div class="space-y-[var(--item-gap)]">
        <Label :for="props.testid">{{ props.label }}</Label>
        <div class="relative">
            <span class="pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 text-xs text-muted-foreground">
                Rp
            </span>
            <NumberInput
                :id="props.testid"
                :model-value="props.modelValue"
                :disabled="props.disabled"
                class="pl-8 text-right tabular-nums"
                :data-testid="props.testid"
                @update:model-value="emit('update:modelValue', $event)"
            />
        </div>
        <p v-if="props.error" class="text-xs font-medium text-destructive">{{ props.error }}</p>
    </div>
</template>
