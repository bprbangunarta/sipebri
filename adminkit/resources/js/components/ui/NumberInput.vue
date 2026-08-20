<script setup>
import { computed } from 'vue';
import Input from './Input.vue';

/**
 * Input angka berformat Indonesia: yang TAMPIL "1.000", yang DISIMPAN 1000.
 * Hanya untuk bilangan bulat (rupiah, plafon, tenor, persen bulat).
 */
const props = defineProps({
    modelValue: { type: [Number, String], default: '' },
});

const emit = defineEmits(['update:modelValue']);

const display = computed(() => {
    const raw = String(props.modelValue ?? '').replace(/\D/g, '');
    return raw === '' ? '' : new Intl.NumberFormat('id-ID').format(Number(raw));
});

const onInput = (value) => {
    const digits = String(value ?? '').replace(/\D/g, '');
    emit('update:modelValue', digits === '' ? '' : Number(digits));
};
</script>

<template>
    <Input :model-value="display" inputmode="numeric" @update:model-value="onInput" />
</template>
