<script setup>
import { computed, ref } from 'vue';
import { CalendarCheck, CalendarClock, History, Loader2 } from 'lucide-vue-next';
import { Head, useForm } from '@inertiajs/vue3';

import AppLayout from '@/components/layout/AppLayout.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Combobox from '@/components/ui/Combobox.vue';
import Dialog from '@/components/ui/Dialog.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import DataTableCard from '@/components/composite/DataTableCard.vue';
import { ACTION } from '@/constants/labels';
import { rupiah } from '@/constants/committee';
import { useServerTable } from '@/composables/useServerTable';

const props = defineProps({
    records: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    statuses: { type: Array, default: () => [] },
    surveyorOptions: { type: Array, default: () => [] },
    maxSchedules: { type: Number, default: 3 },
});

const columns = [
    { key: 'application_code', label: 'Kode Pengajuan' },
    { key: 'full_name', label: 'Pemohon' },
    { key: 'product_label', label: 'Produk', hideBelow: 'lg', sortable: false },
    { key: 'survey_date', label: 'Jadwal Survei', hideBelow: 'md' },
    { key: 'status', label: 'Status', hideBelow: 'sm' },
    { key: 'actions', label: '', align: 'right', width: '140px', sortable: false },
];

const { query, loading, reload, onSearch, onSort, onPage, onPerPage, onFilter, sortState } = useServerTable({
    url: '/scheduling-simulation',
    only: ['records', 'filters'],
    initial: {
        search: props.filters.search ?? '',
        sort: props.filters.sort ?? 'application_date',
        dir: props.filters.dir ?? 'asc',
        status: props.filters.status ?? '',
        scope: props.filters.scope ?? 'saya',
        page: props.records.meta.page ?? 1,
        per_page: props.records.meta.per_page ?? 10,
    },
});

const statusOptions = computed(() => [
    { value: '', label: 'Semua status' },
    ...props.statuses.map((s) => ({ value: s, label: s })),
]);

const scopeOptions = [
    { value: 'saya', label: 'Berkas saya' },
    { value: 'semua', label: 'Semua Kasi Analis' },
];

const today = new Date().toISOString().slice(0, 10);

const scheduling = ref(null);
const form = useForm({ survey_date: '', surveyor_id: '', note: '' });

const openSchedule = (row) => {
    form.defaults({
        survey_date: row.survey_date ?? today,
        surveyor_id: row.surveyor_id ?? '',
        note: '',
    });
    form.reset();
    form.clearErrors();
    scheduling.value = row;
};

const submit = () =>
    form.post(`/scheduling-simulation/${scheduling.value.id}`, {
        preserveScroll: true,
        onSuccess: () => (scheduling.value = null),
    });

const history = ref(null);

const ACTION_TONE = {
    JADWAL: 'secondary',
    'JADWAL ULANG': 'default',
    BATAL: 'destructive',
};
</script>

<template>
    <Head title="Penjadwalan Survei" />
    <AppLayout>
        <div class="space-y-4" data-testid="scheduling-page">
            <DataTableCard
                server
                title="Penjadwalan Survei"
                testid="scheduling"
                :columns="columns"
                :rows="props.records.data"
                :meta="props.records.meta"
                :search="query.search"
                :sort="sortState"
                :loading="loading"
                :empty-icon="CalendarClock"
                empty-title="Belum ada berkas untuk dijadwalkan"
                empty-description="Berkas muncul di sini setelah petugas menekan Ajukan pada Pengajuan Kredit."
                @update:search="onSearch"
                @update:sort="onSort"
                @update:page="onPage"
                @update:per-page="onPerPage"
                @refresh="reload()"
            >
                <template #filters>
                    <Combobox
                        :model-value="query.scope"
                        :options="scopeOptions"
                        placeholder="Berkas saya"
                        class="w-full sm:w-[190px]"
                        data-testid="scheduling-scope-filter"
                        @update:model-value="onFilter('scope', $event)"
                    />
                    <Combobox
                        :model-value="query.status"
                        :options="statusOptions"
                        placeholder="Semua status"
                        class="w-full sm:w-[180px]"
                        data-testid="scheduling-status-filter"
                        @update:model-value="onFilter('status', $event)"
                    />
                </template>

                <template #cell-application_code="{ row }">
                    <span class="block whitespace-nowrap font-mono text-xs font-medium">{{ row.application_code }}</span>
                    <span class="mt-0.5 block whitespace-nowrap text-xs text-muted-foreground">
                        {{ row.application_date }}
                    </span>
                </template>

                <template #cell-full_name="{ row }">
                    <span class="block font-medium">{{ row.full_name }}</span>
                    <span class="block whitespace-nowrap text-xs text-muted-foreground">
                        {{ rupiah(row.requested_amount) }} · {{ row.requested_tenor ? `${row.requested_tenor} bln` : '—' }}
                    </span>
                </template>

                <template #cell-product_label="{ row }">
                    <span class="block whitespace-normal">{{ row.product_label ?? '—' }}</span>
                    <span class="mt-0.5 block whitespace-normal text-xs text-muted-foreground">
                        Kasi: {{ row.supervisor_name ?? '—' }}
                    </span>
                </template>

                <template #cell-survey_date="{ row }">
                    <span class="block whitespace-nowrap">{{ row.survey_date_label ?? '—' }}</span>
                    <span class="mt-0.5 block whitespace-normal text-xs text-muted-foreground">
                        {{ row.surveyor_name ?? 'Belum ditugaskan' }}
                    </span>
                </template>

                <template #cell-status="{ row }">
                    <Badge :variant="row.status === 'PENJADWALAN' ? 'default' : 'secondary'" class="font-medium">
                        {{ row.status }}
                    </Badge>
                    <span class="mt-0.5 block whitespace-nowrap text-xs text-muted-foreground">
                        Jadwal {{ row.schedule_count }}/{{ props.maxSchedules }}
                    </span>
                </template>

                <template #cell-actions="{ row }">
                    <div class="flex items-center justify-end gap-1">
                        <Button
                            variant="ghost"
                            size="icon"
                            title="Histori penjadwalan"
                            :data-testid="`scheduling-history-${row.id}`"
                            @click="history = row"
                        >
                            <History class="size-4" />
                        </Button>
                        <Button
                            size="sm"
                            :disabled="!row.can_schedule"
                            :data-testid="`scheduling-set-${row.id}`"
                            @click="openSchedule(row)"
                        >
                            <CalendarCheck class="size-4" />
                            {{ row.status === 'PENJADWALAN' ? 'Jadwal Ulang' : 'Jadwalkan' }}
                        </Button>
                    </div>
                </template>
            </DataTableCard>

            <Dialog
                :open="Boolean(scheduling)"
                :title="`Jadwal Survei ${scheduling?.application_code ?? ''}`"
                @update:open="scheduling = null"
            >
                <form class="form-dense space-y-3" @submit.prevent="submit">
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="s-date">Tanggal Survei <span class="text-destructive">*</span></Label>
                        <Input
                            id="s-date"
                            v-model="form.survey_date"
                            type="date"
                            :min="today"
                            data-testid="scheduling-date"
                        />
                        <p v-if="form.errors.survey_date" class="text-xs font-medium text-destructive">
                            {{ form.errors.survey_date }}
                        </p>
                    </div>

                    <div class="space-y-[var(--item-gap)]">
                        <Label>Staff Analis <span class="text-destructive">*</span></Label>
                        <Combobox
                            v-model="form.surveyor_id"
                            :options="props.surveyorOptions"
                            placeholder="Pilih staff analis"
                            data-testid="scheduling-surveyor"
                        />
                        <p v-if="form.errors.surveyor_id" class="text-xs font-medium text-destructive">
                            {{ form.errors.surveyor_id }}
                        </p>
                    </div>

                    <div class="space-y-[var(--item-gap)]">
                        <Label for="s-note">Catatan</Label>
                        <Input
                            id="s-note"
                            v-model="form.note"
                            maxlength="255"
                            placeholder="Opsional"
                            data-testid="scheduling-note"
                        />
                        <p v-if="form.errors.note" class="text-xs font-medium text-destructive">
                            {{ form.errors.note }}
                        </p>
                    </div>

                    <p class="text-xs text-muted-foreground">
                        Penjadwalan tercatat sebagai histori dan dibatasi
                        {{ props.maxSchedules }} kali per berkas.
                    </p>
                </form>

                <template #footer>
                    <Button variant="outline" size="sm" data-testid="scheduling-cancel" @click="scheduling = null">
                        {{ ACTION.cancel }}
                    </Button>
                    <Button size="sm" :disabled="form.processing" data-testid="scheduling-submit" @click="submit">
                        <Loader2 v-if="form.processing" class="size-4 animate-spin" />
                        <CalendarCheck v-else class="size-4" />
                        {{ ACTION.save }}
                    </Button>
                </template>
            </Dialog>

            <Dialog
                :open="Boolean(history)"
                :title="`Histori Penjadwalan ${history?.application_code ?? ''}`"
                @update:open="history = null"
            >
                <div v-if="history?.history?.length" class="space-y-2" data-testid="scheduling-history-list">
                    <div
                        v-for="item in history.history"
                        :key="item.id"
                        class="rounded-md border p-2.5"
                    >
                        <div class="flex items-center justify-between gap-2">
                            <Badge :variant="ACTION_TONE[item.action] ?? 'secondary'" class="font-medium">
                                {{ item.action }} #{{ item.sequence }}
                            </Badge>
                            <span class="text-xs text-muted-foreground">{{ item.created_at }}</span>
                        </div>
                        <p class="mt-1.5 text-sm">
                            {{ item.survey_date ?? '—' }} · {{ item.surveyor_name ?? '—' }}
                        </p>
                        <p v-if="item.reason" class="text-xs text-destructive">Alasan: {{ item.reason }}</p>
                        <p v-else-if="item.note" class="text-xs text-muted-foreground">Catatan: {{ item.note }}</p>
                        <p class="mt-1 text-xs text-muted-foreground">Oleh {{ item.created_by }}</p>
                    </div>
                </div>
                <p v-else class="py-6 text-center text-sm text-muted-foreground">
                    Belum ada histori penjadwalan.
                </p>

                <template #footer>
                    <Button variant="outline" size="sm" data-testid="scheduling-history-close" @click="history = null">
                        {{ ACTION.close }}
                    </Button>
                </template>
            </Dialog>
        </div>
    </AppLayout>
</template>
