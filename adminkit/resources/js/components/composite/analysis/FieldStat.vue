<script setup>
/** Angka hasil hitung (read-only). Tidak pernah diinput analis. */
const props = defineProps({
    label: { type: String, required: true },
    value: { type: String, required: true },
    strong: { type: Boolean, default: false },
    testid: { type: String, default: undefined },
});
</script>

<template>
    <div
        class="rounded-md border px-3 py-2"
        :class="props.strong ? 'border-primary/40 bg-primary/5' : 'bg-muted/40'"
        :data-testid="props.testid"
    >
        <p class="text-[11px] uppercase tracking-wider text-muted-foreground">{{ props.label }}</p>
        <p class="tabular-nums" :class="props.strong ? 'text-sm font-semibold' : 'text-sm'">{{ props.value }}</p>
    </div>
</template>
