<script setup>
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { ArrowLeft, Gavel, Info } from 'lucide-vue-next';

import ApprovalDecisionDialog from '@/components/composite/ApprovalDecisionDialog.vue';
import AppLayout from '@/components/layout/AppLayout.vue';
import Alert from '@/components/ui/Alert.vue';
import AlertDescription from '@/components/ui/AlertDescription.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
import { persen, rupiah } from '@/constants/committee';

/** Keputusan komite atas satu berkas: ringkasan, jenjang pemutus, catatan, dan dialog keputusan. */
const props = defineProps({
    record: { type: Object, required: true },
    tiers: { type: Array, default: () => [] },
    analyst: { type: Object, required: true },
    basis: { type: Object, required: true },
    flow: { type: Object, required: true },
    methodOptions: { type: Array, default: () => [] },
});

const dialogOpen = ref(false);

const DECISION_TONE = {
    TERUSKAN: 'secondary',
    DISETUJUI: 'default',
    DITOLAK: 'destructive',
    DIBATALKAN: 'destructive',
};

const facts = computed(() => [
    ['Produk', props.record.product_label ?? '—'],
    ['Jalur Komite', props.record.committee_path ?? '—'],
    ['Kantor', props.record.office_label ?? '—'],
    ['Penggunaan', props.record.usage_type ?? '—'],
    ['Plafon Diajukan', rupiah(props.record.requested_amount)],
    ['Jangka Diajukan', props.record.requested_tenor ? `${props.record.requested_tenor} bulan` : '—'],
    ['Usulan Analis', rupiah(props.basis.amount)],
    ['Jangka Usulan', props.basis.tenor ? `${props.basis.tenor} bulan` : '—'],
    ['Keuangan / Bulan', rupiah(props.basis.capacity)],
    ['Ambang RC Produk', persen(props.basis.rc_threshold)],
    ['Max Plafon', rupiah(props.basis.max_amount)],
    ['RC Usulan', persen(props.basis.rc_ratio)],
    ['Taksasi Agunan', rupiah(props.basis.taksasi)],
    ['Metode RPS', props.basis.method_label ?? '—'],
    ['Kasi Analis', props.record.supervisor_name ?? '—'],
    ['Diajukan', `${props.record.submitted_by ?? '—'} · ${props.record.submitted_at ?? '—'}`],
]);

const analystSummary = computed(() => [
    `Usulan: ${rupiah(props.analyst.amount)} · ${props.analyst.tenor || 0} bulan · Metode: ${props.analyst.method_label ?? '—'}`,
    `B. Provisi: ${persen(props.analyst.provision_rate)} · B. Admin: ${persen(props.analyst.admin_rate)} · Bunga: ${persen(props.analyst.interest_rate)} · RC: ${persen(props.analyst.rc_ratio)}`,
    `Catatan: ${props.analyst.note || 'TIDAK ADA CATATAN'}`,
]);

const tierRange = (tier) =>
    tier.max_amount === null
        ? `> ${rupiah(tier.min_amount)}`
        : `${rupiah(tier.min_amount)} – ${rupiah(tier.max_amount)}`;
</script>

<template>
    <Head :title="`Keputusan ${props.record.application_code}`" />
    <AppLayout>
        <div class="space-y-4" data-testid="approval-detail-page">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <Button
                        variant="ghost"
                        size="icon"
                        data-testid="approval-back"
                        @click="router.visit('/approval-simulation')"
                    >
                        <ArrowLeft class="size-4" />
                    </Button>
                    <div>
                        <p class="font-mono text-sm font-semibold">{{ props.record.application_code }}</p>
                        <p class="text-xs text-muted-foreground">
                            {{ props.record.full_name }} · {{ props.record.nik }}
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <Badge variant="secondary" class="font-medium" data-testid="approval-pending-tier">
                        Menunggu: {{ props.flow.pending_position ?? '—' }}
                    </Badge>
                    <Badge variant="default" class="font-medium" data-testid="approval-status">
                        {{ props.record.status }}
                    </Badge>
                    <Button
                        v-if="props.flow.my_turn && props.flow.allowed.length"
                        size="sm"
                        data-testid="approval-decide-open"
                        @click="dialogOpen = true"
                    >
                        <Gavel class="size-4" /> <span class="hidden sm:inline">Beri Keputusan</span>
                    </Button>
                </div>
            </div>

            <Alert v-if="props.flow.blocked_reason" data-testid="approval-blocked">
                <Info class="size-4" />
                <AlertDescription>{{ props.flow.blocked_reason }}</AlertDescription>
            </Alert>

            <Card>
                <CardHeader><CardTitle>Ringkasan Berkas</CardTitle></CardHeader>
                <CardContent class="grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-4">
                    <div v-for="[label, value] in facts" :key="label">
                        <p class="text-[11px] uppercase tracking-wider text-muted-foreground">{{ label }}</p>
                        <p class="tabular-nums">{{ value }}</p>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Jenjang Pemutus</CardTitle></CardHeader>
                <CardContent>
                    <div class="overflow-x-auto rounded-md border">
                        <table class="w-full text-sm">
                            <thead class="bg-muted/40">
                                <tr>
                                    <th class="w-16 whitespace-nowrap px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wider text-muted-foreground">
                                        Level
                                    </th>
                                    <th class="whitespace-nowrap px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wider text-muted-foreground">
                                        Pemutus
                                    </th>
                                    <th class="whitespace-nowrap px-3 py-2 text-right text-[11px] font-medium uppercase tracking-wider text-muted-foreground">
                                        Batas Plafon
                                    </th>
                                    <th class="whitespace-nowrap px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wider text-muted-foreground">
                                        Keputusan
                                    </th>
                                    <th class="whitespace-nowrap px-3 py-2 text-right text-[11px] font-medium uppercase tracking-wider text-muted-foreground">
                                        Usulan · RC
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border/60">
                                <tr v-for="tier in props.tiers" :key="tier.level" :data-testid="`approval-tier-${tier.level}`">
                                    <td class="px-3 py-2 tabular-nums">{{ tier.level }}</td>
                                    <td class="px-3 py-2">
                                        <span class="block whitespace-nowrap font-medium">{{ tier.position }}</span>
                                        <span class="block whitespace-nowrap text-xs text-muted-foreground">
                                            {{ tier.role ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-2 text-right tabular-nums">
                                        {{ tierRange(tier) }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-2">
                                        <Badge
                                            v-if="tier.decision"
                                            :variant="DECISION_TONE[tier.decision] ?? 'secondary'"
                                            class="font-medium"
                                        >
                                            {{ tier.decision }}
                                        </Badge>
                                        <Badge
                                            v-else-if="tier.level === props.flow.pending_level"
                                            variant="outline"
                                            class="font-medium"
                                        >
                                            Menunggu
                                        </Badge>
                                        <span v-else class="text-xs text-muted-foreground">—</span>
                                        <span v-if="tier.decided_by" class="mt-0.5 block text-xs text-muted-foreground">
                                            {{ tier.decided_by }} · {{ tier.decided_at }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-2 text-right tabular-nums">
                                        <span v-if="tier.decision">
                                            {{ rupiah(tier.amount) }} · {{ persen(tier.rc_ratio) }}
                                        </span>
                                        <span v-else class="text-xs text-muted-foreground">—</span>
                                    </td>
                                </tr>
                                <tr v-if="!props.tiers.length">
                                    <td colspan="5" class="px-3 py-6 text-center text-sm text-muted-foreground">
                                        Jalur komite berkas ini belum punya jenjang pemutus.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Catatan Komite</CardTitle></CardHeader>
                <CardContent class="space-y-3">
                    <div data-testid="approval-note-analyst">
                        <p class="text-[11px] font-medium uppercase tracking-wider text-muted-foreground">
                            Staff Analis{{ props.analyst.name ? ` · ${props.analyst.name}` : '' }}
                        </p>
                        <div class="mt-1 space-y-0.5 rounded-md border bg-muted/40 px-3 py-2 text-sm">
                            <p v-for="line in analystSummary" :key="line" class="tabular-nums">{{ line }}</p>
                        </div>
                    </div>

                    <div v-for="tier in props.tiers" :key="`note-${tier.level}`" :data-testid="`approval-note-${tier.level}`">
                        <p class="text-[11px] font-medium uppercase tracking-wider text-muted-foreground">
                            {{ tier.position }} · {{ tier.role }}{{ tier.decided_by ? ` · ${tier.decided_by}` : '' }}
                        </p>
                        <div class="mt-1 rounded-md border bg-muted/40 px-3 py-2 text-sm">
                            <p v-if="tier.decision" class="tabular-nums">
                                {{ tier.decision }} · {{ rupiah(tier.amount) }} · {{ tier.tenor }} bulan ·
                                {{ tier.method_label ?? '—' }} · RC {{ persen(tier.rc_ratio) }}
                            </p>
                            <p :class="tier.note ? '' : 'text-muted-foreground'">
                                {{ tier.note || 'TIDAK ADA CATATAN' }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <ApprovalDecisionDialog
                v-model:open="dialogOpen"
                :application-id="props.record.id"
                :basis="props.basis"
                :method-options="props.methodOptions"
                :allowed="props.flow.allowed"
                :tier-label="props.flow.pending_position ?? ''"
                :requested-amount="props.record.requested_amount"
            />
        </div>
    </AppLayout>
</template>
