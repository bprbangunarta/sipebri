<script setup>
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { ArrowLeft, Copy, Database, FileJson, Pencil, Send } from 'lucide-vue-next';

import AppLayout from '@/components/layout/AppLayout.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
import Input from '@/components/ui/Input.vue';
import { ACTION } from '@/constants/labels';
import { notify } from '@/composables/useToast';

/** Halaman inspeksi untuk developer: kolom mentah tabel + payload CBS. */
const props = defineProps({
    record: { type: Object, required: true },
    columns: { type: Array, default: () => [] },
    payload: { type: Object, default: () => ({}) },
    canManage: { type: Boolean, default: false },
});

const TABS = [
    { id: 'database', label: 'Database', icon: Database },
    { id: 'payload', label: 'Payload CBS', icon: FileJson },
];

const tab = ref('database');
const search = ref('');

const isEmpty = (v) => v === null || v === undefined || v === '';

const rows = computed(() => {
    const q = search.value.trim().toLowerCase();
    return props.columns.filter(
        (c) => !q || c.name.includes(q) || String(c.value ?? '').toLowerCase().includes(q),
    );
});

const filled = computed(() => props.columns.filter((c) => !isEmpty(c.value)).length);

const payloadText = computed(() => JSON.stringify(props.payload, null, 2));

const copy = (text, label) => {
    navigator.clipboard?.writeText(text);
    notify.success(`${label} disalin.`);
};

// Pengiriman ke core banking belum diaktifkan.
const postPayload = () => notify.info('Posting ke core banking belum diaktifkan.');
</script>

<template>
    <Head :title="`Detail ${props.record.collateral_id ?? props.record.id}`" />
    <AppLayout>
        <Card data-testid="collateral-detail-page">
            <CardHeader class="flex flex-row flex-wrap items-center justify-between gap-2 space-y-0">
                <CardTitle class="flex min-w-0 items-center gap-2">
                    <Button
                        variant="ghost"
                        size="icon"
                        type="button"
                        data-testid="collateral-detail-back"
                        @click="router.visit('/collateral-simulation')"
                    >
                        <ArrowLeft class="size-4" />
                    </Button>
                    <span class="truncate font-mono text-sm">
                        {{ props.record.table }}#{{ props.record.id }}
                    </span>
                </CardTitle>
                <div class="flex flex-wrap items-center gap-2">
                    <Badge variant="secondary" class="font-mono font-medium">
                        {{ filled }}/{{ props.columns.length }} kolom terisi
                    </Badge>
                    <Button
                        v-if="props.canManage"
                        size="sm"
                        variant="outline"
                        data-testid="collateral-detail-edit"
                        @click="router.visit(`/collateral-simulation/${props.record.id}/edit`)"
                    >
                        <Pencil class="size-4" /> {{ ACTION.edit }}
                    </Button>
                </div>
            </CardHeader>

            <CardContent class="space-y-3">
                <div class="flex flex-wrap items-center gap-2 border-b pb-2">
                    <button
                        v-for="t in TABS"
                        :key="t.id"
                        type="button"
                        class="inline-flex h-8 items-center gap-1.5 rounded-md px-3 text-xs font-medium transition-colors"
                        :class="
                            tab === t.id
                                ? 'bg-primary text-primary-foreground'
                                : 'text-muted-foreground hover:bg-accent hover:text-foreground'
                        "
                        :data-testid="`collateral-detail-tab-${t.id}`"
                        @click="tab = t.id"
                    >
                        <component :is="t.icon" class="size-3.5" />{{ t.label }}
                    </button>

                    <div class="ml-auto flex items-center gap-2">
                        <Input
                            v-if="tab === 'database'"
                            v-model="search"
                            placeholder="Cari kolom/nilai..."
                            class="h-8 w-[200px]"
                            data-testid="collateral-detail-search"
                        />
                        <Button
                            v-if="tab === 'payload'"
                            variant="outline"
                            size="sm"
                            data-testid="collateral-detail-payload-copy"
                            @click="copy(payloadText, 'Payload')"
                        >
                            <Copy class="size-4" /> Salin
                        </Button>
                        <Button
                            v-if="tab === 'payload'"
                            size="sm"
                            data-testid="collateral-detail-payload-post"
                            @click="postPayload"
                        >
                            <Send class="size-4" /> Posting
                        </Button>
                    </div>
                </div>

                <!-- Tab Database: nama kolom apa adanya supaya mudah dicocokkan dengan migration. -->
                <div v-if="tab === 'database'" class="overflow-x-auto" data-testid="collateral-detail-database">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="border-b text-[11px] uppercase tracking-wide text-muted-foreground">
                                <th class="py-1.5 pr-3 text-left font-medium">Kolom</th>
                                <th class="py-1.5 pr-3 text-left font-medium">Tipe</th>
                                <th class="hidden py-1.5 pr-3 text-left font-medium sm:table-cell">Null</th>
                                <th class="hidden py-1.5 pr-3 text-left font-medium md:table-cell">Default</th>
                                <th class="py-1.5 text-left font-medium">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="col in rows"
                                :key="col.name"
                                class="border-b border-border/60 last:border-0 hover:bg-muted/40"
                            >
                                <td class="py-1.5 pr-3 font-mono font-medium">{{ col.name }}</td>
                                <td class="py-1.5 pr-3 font-mono text-muted-foreground">{{ col.type }}</td>
                                <td class="hidden py-1.5 pr-3 text-muted-foreground sm:table-cell">
                                    {{ col.nullable ? 'YES' : 'NO' }}
                                </td>
                                <td class="hidden py-1.5 pr-3 font-mono text-muted-foreground md:table-cell">
                                    {{ col.default ?? '—' }}
                                </td>
                                <td class="py-1.5 break-all font-mono">
                                    <span v-if="isEmpty(col.value)" class="text-muted-foreground">NULL</span>
                                    <span v-else>{{ col.value }}</span>
                                </td>
                            </tr>
                            <tr v-if="!rows.length">
                                <td colspan="5" class="py-4 text-center text-muted-foreground">
                                    Kolom tidak ditemukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Tab Payload: bentuk JSON yang dikirim ke core banking. -->
                <div v-else class="space-y-2">
                    <p class="text-xs text-muted-foreground">
                        Bentuk data yang dikirim ke core banking. SIPEBRI tidak menghitung apa pun.
                    </p>
                    <pre
                        class="thin-scroll max-h-[60vh] overflow-auto rounded-md border bg-muted/40 p-3 text-xs leading-relaxed"
                        data-testid="collateral-detail-payload"
                    >{{ payloadText }}</pre>
                </div>
            </CardContent>
        </Card>
    </AppLayout>
</template>
