<script setup>
import { ref, watch } from 'vue';
import Input from './Input.vue';

/**
 * Input desimal gaya Indonesia: yang TAMPIL "12,75", yang DISIMPAN "12.75".
 * Dipakai untuk suku bunga, provisi, biaya admin, ambang RC, dan persentase lain.
 */
const props = defineProps({
    modelValue: { type: [Number, String], default: '' },
    decimals: { type: Number, default: 2 },
});

const emit = defineEmits(['update:modelValue']);

const toDisplay = (value) =>
    value === null || value === undefined || value === '' ? '' : String(value).replace('.', ',');

const display = ref(toDisplay(props.modelValue));

watch(
    () => props.modelValue,
    (value) => {
        // Perbarui tampilan hanya bila nilai luar berbeda dari yang sedang diketik.
        if (String(value ?? '') !== display.value.replace(',', '.')) {
            display.value = toDisplay(value);
        }
    },
);

const onInput = (value) => {
    // Terima koma maupun titik, buang karakter lain, sisakan satu pemisah desimal.
    let raw = String(value ?? '').replace(/[^\d.,]/g, '').replace(/\./g, ',');
    const [head, ...rest] = raw.split(',');
    raw = rest.length ? `${head},${rest.join('').slice(0, props.decimals)}` : head;

    display.value = raw;
    emit('update:modelValue', raw === '' ? '' : raw.replace(',', '.'));
};
</script>

<template>
    <Input :model-value="display" inputmode="decimal" @update:model-value="onInput" />
</template>
