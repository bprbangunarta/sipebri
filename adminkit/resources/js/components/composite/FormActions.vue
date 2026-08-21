<script setup>
import { Loader2, Save, X } from 'lucide-vue-next';

import Button from '@/components/ui/Button.vue';
import { ACTION } from '@/constants/labels';

/**
 * FormActions — SATU-SATUNYA cara menampilkan pasangan tombol Batal/Simpan,
 * baik di `CardFooter` maupun di slot `#footer` sebuah Dialog.
 * Batal selalu di pojok kiri (outline + ikon X), aksi utama di pojok kanan.
 * Jangan menulis pasangan tombol ini manual di halaman — lihat `/app/memory/ui_rules.md`.
 */
const props = defineProps({
    // Aksi utama
    submitTestid: { type: String, required: true },
    submitLabel: { type: String, default: ACTION.save },
    submitBusyLabel: { type: String, default: ACTION.saving },
    submitIcon: { type: [Object, Function], default: () => Save },
    submitVariant: { type: String, default: 'default' },
    submitType: { type: String, default: 'button' },
    form: { type: String, default: undefined },
    processing: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    // Tombol kiri
    cancel: { type: Boolean, default: true },
    cancelTestid: { type: String, default: undefined },
    cancelLabel: { type: String, default: ACTION.cancel },
    // Sembunyikan aksi utama (mis. berkas terkunci)
    submit: { type: Boolean, default: true },
});

const emit = defineEmits(['cancel', 'submit']);

const onSubmit = () => {
    if (props.submitType === 'button') emit('submit');
};
</script>

<template>
    <div class="flex w-full flex-wrap items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <Button
                v-if="props.cancel"
                variant="outline"
                size="sm"
                type="button"
                :data-testid="props.cancelTestid"
                @click="emit('cancel')"
            >
                <X class="size-4" /> {{ props.cancelLabel }}
            </Button>
            <slot name="start" />
        </div>

        <Button
            v-if="props.submit"
            :variant="props.submitVariant"
            size="sm"
            :type="props.submitType"
            :form="props.form"
            :disabled="props.processing || props.disabled"
            :data-testid="props.submitTestid"
            @click="onSubmit"
        >
            <Loader2 v-if="props.processing" class="size-4 animate-spin" />
            <component :is="props.submitIcon" v-else class="size-4" />
            {{ props.processing ? props.submitBusyLabel : props.submitLabel }}
        </Button>
    </div>
</template>
