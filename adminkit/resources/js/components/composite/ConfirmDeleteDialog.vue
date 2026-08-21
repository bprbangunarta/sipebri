<script setup>
import { Trash2, X } from 'lucide-vue-next';
import FormActions from '@/components/composite/FormActions.vue';
import Button from '@/components/ui/Button.vue';
import Dialog from '@/components/ui/Dialog.vue';
import { ACTION } from '@/constants/labels';

// Judul singkat di header, penjelasan di body, aksi di footer (kiri & kanan).
const props = defineProps({
    open: { type: Boolean, default: false },
    title: { type: String, default: 'Hapus Data?' },
    description: { type: String, default: 'Tindakan ini tidak dapat dibatalkan.' },
    processing: { type: Boolean, default: false },
    confirmLabel: { type: String, default: '' },
});
const emit = defineEmits(['update:open', 'confirm']);
</script>

<template>
    <Dialog
        :open="props.open"
        :title="props.title"
        class="max-w-md"
        @update:open="emit('update:open', $event)"
    >
        <p class="text-sm text-muted-foreground" data-testid="confirm-delete-description">
            {{ props.description }}
        </p>

        <template #footer>
            <FormActions
                cancel-testid="confirm-delete-cancel"
                submit-testid="confirm-delete-submit"
                submit-variant="destructive"
                :submit-icon="Trash2"
                :submit-label="props.confirmLabel || ACTION.delete"
                :processing="props.processing"
                @cancel="emit('update:open', false)"
                @submit="emit('confirm')"
            />
        </template>
    </Dialog>
</template>
