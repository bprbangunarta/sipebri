<script setup>
/** Tab segmented untuk bagian-bagian di dalam satu usaha. */
const props = defineProps({
    tabs: { type: Array, required: true },
    active: { type: String, required: true },
    testid: { type: String, default: 'business-tabs' },
});

defineEmits(['select']);
</script>

<template>
    <div class="flex flex-wrap gap-1 rounded-md border bg-muted/40 p-1" :data-testid="props.testid">
        <button
            v-for="tab in props.tabs"
            :key="tab.key"
            type="button"
            class="rounded px-3 py-1.5 text-xs font-medium transition-colors"
            :class="
                props.active === tab.key
                    ? 'bg-background text-foreground shadow-sm'
                    : 'text-muted-foreground hover:text-foreground'
            "
            :data-testid="`${props.testid}-${tab.key}`"
            @click="$emit('select', tab.key)"
        >
            {{ tab.label }}
        </button>
    </div>
</template>
