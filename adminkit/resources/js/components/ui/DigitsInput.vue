<script setup>
import { computed, nextTick, ref } from 'vue';
import Input from './Input.vue';

/**
 * Input angka murni tanpa pemisah ribuan (nomor KTP, NPWP, telepon).
 * Karakter non-angka ditolak dan nilai DOM ikut disinkronkan agar huruf
 * tidak tertinggal di kolom.
 */
const props = defineProps({
    modelValue: { type: [String, Number], default: '' },
    maxlength: { type: [String, Number], default: null },
});

const emit = defineEmits(['update:modelValue']);
const el = ref(null);

const display = computed(() => {
    const digits = String(props.modelValue ?? '').replace(/\D/g, '');

    return props.maxlength ? digits.slice(0, Number(props.maxlength)) : digits;
});

const syncDom = () => {
    nextTick(() => {
        const node = el.value?.$el;
        if (node && node.value !== display.value) node.value = display.value;
    });
};

const onInput = (value) => {
    let digits = String(value ?? '').replace(/\D/g, '');

    if (props.maxlength) digits = digits.slice(0, Number(props.maxlength));

    emit('update:modelValue', digits);
    syncDom();
};
</script>

<template>
    <Input
        ref="el"
        :model-value="display"
        inputmode="numeric"
        :maxlength="props.maxlength ?? undefined"
        @update:model-value="onInput"
    />
</template>
