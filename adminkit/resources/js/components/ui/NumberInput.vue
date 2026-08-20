<script setup>
import { computed, nextTick, ref } from 'vue';
import Input from './Input.vue';

/**
 * Input angka berformat Indonesia: yang TAMPIL "1.000", yang DISIMPAN 1000.
 * Karakter non-angka ditolak (nilai DOM ikut disinkronkan agar tidak tertinggal).
 */
const props = defineProps({
    modelValue: { type: [Number, String], default: '' },
});

const emit = defineEmits(['update:modelValue']);
const el = ref(null);

const display = computed(() => {
    const raw = String(props.modelValue ?? '').replace(/\D/g, '');
    return raw === '' ? '' : new Intl.NumberFormat('id-ID').format(Number(raw));
});

const syncDom = () => {
    nextTick(() => {
        const node = el.value?.$el;
        if (node && node.value !== display.value) node.value = display.value;
    });
};

const onInput = (value) => {
    const digits = String(value ?? '').replace(/\D/g, '');
    emit('update:modelValue', digits === '' ? '' : Number(digits));
    syncDom();
};
</script>

<template>
    <Input ref="el" :model-value="display" inputmode="numeric" @update:model-value="onInput" />
</template>
