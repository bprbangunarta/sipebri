<script setup>
/** Navigasi bagian lembar analisa: daftar vertikal (desktop) / gulir horizontal (mobile). */
const props = defineProps({
    sections: { type: Array, required: true },
    active: { type: String, required: true },
});

defineEmits(['select']);
</script>

<template>
    <nav
        class="-mx-1 flex gap-1 overflow-x-auto px-1 pb-1 thin-scroll lg:mx-0 lg:flex-col lg:overflow-visible lg:px-0 lg:pb-0"
        data-testid="analysis-section-nav"
    >
        <button
            v-for="(section, index) in props.sections"
            :key="section.key"
            type="button"
            class="group flex shrink-0 items-center gap-2 rounded-md border px-2.5 py-2 text-left text-sm transition-colors lg:w-full lg:shrink"
            :class="
                props.active === section.key
                    ? 'border-primary bg-primary text-primary-foreground'
                    : 'border-transparent text-muted-foreground hover:border-border hover:bg-muted/60 hover:text-foreground'
            "
            :data-testid="`analysis-section-${section.key}`"
            @click="$emit('select', section.key)"
        >
            <span
                class="grid size-5 shrink-0 place-items-center rounded text-[11px] font-semibold tabular-nums"
                :class="props.active === section.key ? 'bg-primary-foreground/20' : 'bg-muted'"
            >
                {{ index + 1 }}
            </span>
            <component :is="section.icon" class="size-4 shrink-0" />
            <span class="whitespace-nowrap font-medium lg:whitespace-normal">{{ section.label }}</span>
        </button>
    </nav>
</template>
