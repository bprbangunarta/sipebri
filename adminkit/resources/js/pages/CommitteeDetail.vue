<script setup>
import { computed, ref } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, ChevronDown, ChevronUp, Layers, Loader2, Pencil, Plus, Save, Trash2, X } from 'lucide-vue-next';

import AppLayout from '@/components/layout/AppLayout.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
import Combobox from '@/components/ui/Combobox.vue';
import Dialog from '@/components/ui/Dialog.vue';
import DropdownMenuItem from '@/components/ui/DropdownMenuItem.vue';
import DropdownMenuSeparator from '@/components/ui/DropdownMenuSeparator.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import Switch from '@/components/ui/Switch.vue';
import ConfirmDeleteDialog from '@/components/composite/ConfirmDeleteDialog.vue';
import DataTableCard from '@/components/composite/DataTableCard.vue';
import RowActions from '@/components/composite/RowActions.vue';
import { ACTION } from '@/constants/labels';
import { TIER_DECISIONS, digitsOnly, rupiah } from '@/constants/committee';

const props = defineProps({
    path: { type: Object, required: true },
    roleOptions: { type: Array, default: () => [] },
});

const page = usePage();
const canManage = computed(() =>
    (page.props.auth?.user?.permissions ?? []).includes('committees.manage'),
);

const isPlafon = computed(() => props.path.mechanism === 'plafon');

const columns = [
    { key: 'sort', label: '#', width: '48px', sortable: false },
    { key: 'label', label: 'Nama Jenjang', sortable: false },
    { key: 'role', label: 'Peranan Pemutus', sortable: false },
    { key: 'range', label: 'Batas Plafon', sortable: false },
    { key: 'decisions', label: 'Keputusan Diizinkan', sortable: false },
    { key: 'actions', label: '', align: 'right', width: '96px', sortable: false },
];

const rows = computed(() =>
    props.path.tiers.map((tier, index) => ({
        ...tier,
        position: index + 1,
        first: index === 0,
        last: index === props.path.tiers.length - 1,
    })),
);

const rangeOf = (tier) => {
    if (!isPlafon.value) return 'Tanpa batas';
    if (tier.min_amount === null && tier.max_amount === null) return 'Tanpa batas';

    return `${rupiah(tier.min_amount ?? 0)} – ${tier.max_amount === null ? 'ke atas' : rupiah(tier.max_amount)}`;
};

/* ── Formulir jenjang ────────────────────────────────────────────────── */
const dialogOpen = ref(false);
const editing = ref(null);
const blank = {
    label: '',
    role: '',
    min_amount: '',
    max_amount: '',
    can_escalate: false,
    can_approve: false,
    can_cancel: false,
    can_reject: false,
};
const form = useForm({ ...blank });

const openCreate = () => {
    editing.value = null;
    form.clearErrors();
    form.defaults({ ...blank });
    form.reset();
    dialogOpen.value = true;
};

const openEdit = (tier) => {
    editing.value = tier;
    form.clearErrors();
    form.defaults({
        label: tier.label ?? '',
        role: tier.role,
        min_amount: tier.min_amount ?? '',
        max_amount: tier.max_amount ?? '',
        can_escalate: tier.can_escalate,
        can_approve: tier.can_approve,
        can_cancel: tier.can_cancel,
        can_reject: tier.can_reject,
    });
    form.reset();
    dialogOpen.value = true;
};

const onAmountInput = (field, event) => {
    form[field] = digitsOnly(event.target.value);
};

const submit = () => {
    const options = { preserveScroll: true, onSuccess: () => (dialogOpen.value = false) };

    if (editing.value) form.put(`/committees/${props.path.id}/tiers/${editing.value.id}`, options);
    else form.post(`/committees/${props.path.id}/tiers`, options);
};

const move = (tier, direction) =>
    router.put(`/committees/${props.path.id}/tiers/${tier.id}/move/${direction}`, {}, { preserveScroll: true });

const deleting = ref(null);
const rowForm = useForm({});
const confirmDelete = () =>
    rowForm.delete(`/committees/${props.path.id}/tiers/${deleting.value.id}`, {
        preserveScroll: true,
        onFinish: () => (deleting.value = null),
    });
</script>

<template>
    <Head :title="`Jalur ${props.path.title}`" />
    <AppLayout>
        <div class="space-y-6" data-testid="committee-detail-view">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0">
                    <CardTitle class="flex items-center gap-2">
                        <Button
                            variant="ghost"
                            size="icon"
                            data-testid="committee-back"
                            @click="router.visit('/committees')"
                        >
                            <ArrowLeft class="size-4" />
                        </Button>
                        {{ props.path.title }}
                    </CardTitle>
                    <div class="flex items-center gap-2">
                        <Badge variant="secondary" class="font-medium">{{ props.path.mechanism_label }}</Badge>
                        <Badge :variant="props.path.is_active ? 'secondary' : 'destructive'" class="font-medium">
                            {{ props.path.status_label }}
                        </Badge>
                    </div>
                </CardHeader>
                <CardContent class="grid gap-3 text-sm sm:grid-cols-3">
                    <div>
                        <p class="text-xs uppercase tracking-wider text-muted-foreground">Produk</p>
                        <p class="font-medium" data-testid="committee-product">{{ props.path.product_label }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wider text-muted-foreground">Kondisi / Kategori</p>
                        <p class="font-medium">{{ props.path.condition_label }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wider text-muted-foreground">Catatan</p>
                        <p class="font-medium">{{ props.path.note || '—' }}</p>
                    </div>
                </CardContent>
            </Card>

            <DataTableCard
                title="Jenjang Kewenangan"
                testid="tiers"
                :columns="columns"
                :rows="rows"
                :empty-icon="Layers"
                empty-title="Belum ada jenjang"
                :empty-description="
                    isPlafon
                        ? 'Susun jenjang dari level terendah, isi batas plafon tiap level, dan izinkan Naik Komite bila plafon di atas batasnya.'
                        : 'Susun jenjang berurutan; hanya level terakhir yang boleh memutus disetujui/dibatalkan/ditolak.'
                "
                :show-refresh="false"
            >
                <template #header-action>
                    <Button v-if="canManage" size="sm" data-testid="tiers-add" @click="openCreate">
                        <Plus class="size-4" /> {{ ACTION.add }}
                    </Button>
                </template>

                <template #cell-sort="{ row }">
                    <span class="font-mono text-xs text-muted-foreground">{{ row.position }}</span>
                </template>

                <template #cell-label="{ row }">
                    <span class="font-medium">{{ row.label || '—' }}</span>
                </template>

                <template #cell-range="{ row }">
                    <span class="whitespace-nowrap text-xs">{{ rangeOf(row) }}</span>
                </template>

                <template #cell-decisions="{ row }">
                    <div class="flex flex-wrap gap-1">
                        <Badge
                            v-for="decision in TIER_DECISIONS.filter((d) => row[d.key])"
                            :key="decision.key"
                            variant="secondary"
                            class="font-medium"
                        >
                            {{ decision.label }}
                        </Badge>
                        <span v-if="!TIER_DECISIONS.some((d) => row[d.key])" class="text-xs text-muted-foreground">
                            Belum diatur
                        </span>
                    </div>
                </template>

                <template #cell-actions="{ row }">
                    <div v-if="canManage" class="flex items-center justify-end gap-1">
                        <Button
                            variant="ghost"
                            size="icon"
                            :disabled="row.first"
                            :data-testid="`tiers-up-${row.id}`"
                            @click="move(row, 'up')"
                        >
                            <ChevronUp class="size-4" />
                        </Button>
                        <Button
                            variant="ghost"
                            size="icon"
                            :disabled="row.last"
                            :data-testid="`tiers-down-${row.id}`"
                            @click="move(row, 'down')"
                        >
                            <ChevronDown class="size-4" />
                        </Button>
                        <RowActions :testid="`tiers-actions-${row.id}`">
                            <DropdownMenuItem :data-testid="`tiers-edit-${row.id}`" @select="openEdit(row)">
                                <Pencil />{{ ACTION.edit }}
                            </DropdownMenuItem>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem
                                class="text-destructive data-[highlighted]:text-destructive"
                                :data-testid="`tiers-delete-${row.id}`"
                                @click="deleting = row"
                            >
                                <Trash2 />{{ ACTION.delete }}
                            </DropdownMenuItem>
                        </RowActions>
                    </div>
                </template>
            </DataTableCard>

            <Dialog
                v-model:open="dialogOpen"
                :title="`${editing ? ACTION.edit : ACTION.add} Jenjang`"
                class="max-w-md"
            >
                <form id="tier-form" class="form-dense space-y-[var(--field-gap)]" novalidate @submit.prevent="submit">
                    <div class="grid gap-[var(--field-gap)] sm:grid-cols-2">
                        <div class="space-y-[var(--item-gap)]">
                            <Label for="tier-label">Nama Jenjang</Label>
                            <Input
                                id="tier-label"
                                v-model="form.label"
                                maxlength="50"
                                placeholder="Komite I"
                                autocomplete="off"
                                data-testid="tiers-form-label"
                            />
                            <p v-if="form.errors.label" class="text-xs font-medium text-destructive">
                                {{ form.errors.label }}
                            </p>
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label>Peranan Pemutus</Label>
                            <Combobox
                                v-model="form.role"
                                :options="props.roleOptions"
                                placeholder="Pilih Peranan"
                                data-testid="tiers-form-role"
                            />
                            <p v-if="form.errors.role" class="text-xs font-medium text-destructive" data-testid="tiers-form-role-error">
                                {{ form.errors.role }}
                            </p>
                        </div>
                    </div>

                    <div v-if="isPlafon" class="grid gap-[var(--field-gap)] sm:grid-cols-2">
                        <div class="space-y-[var(--item-gap)]">
                            <Label for="tier-min">Plafon Minimal</Label>
                            <Input
                                id="tier-min"
                                :model-value="form.min_amount"
                                inputmode="numeric"
                                placeholder="0"
                                data-testid="tiers-form-min"
                                @input="onAmountInput('min_amount', $event)"
                            />
                            <p class="text-xs text-muted-foreground">{{ rupiah(form.min_amount || null) }}</p>
                            <p v-if="form.errors.min_amount" class="text-xs font-medium text-destructive">
                                {{ form.errors.min_amount }}
                            </p>
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label for="tier-max">Plafon Maksimal</Label>
                            <Input
                                id="tier-max"
                                :model-value="form.max_amount"
                                inputmode="numeric"
                                placeholder="Kosongkan = tanpa batas"
                                data-testid="tiers-form-max"
                                @input="onAmountInput('max_amount', $event)"
                            />
                            <p class="text-xs text-muted-foreground">{{ rupiah(form.max_amount || null) }}</p>
                            <p v-if="form.errors.max_amount" class="text-xs font-medium text-destructive" data-testid="tiers-form-max-error">
                                {{ form.errors.max_amount }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-[var(--item-gap)]">
                        <Label>Keputusan Diizinkan</Label>
                        <label
                            v-for="decision in TIER_DECISIONS"
                            :key="decision.key"
                            class="flex items-center justify-between gap-3 rounded-md border px-3 py-1.5"
                        >
                            <span class="text-sm">{{ decision.label }}</span>
                            <Switch v-model="form[decision.key]" :data-testid="`tiers-form-${decision.key}`" />
                        </label>
                    </div>
                </form>

                <template #footer>
                    <Button variant="outline" size="sm" data-testid="tiers-form-cancel" @click="dialogOpen = false">
                        <X class="size-4" /> {{ ACTION.cancel }}
                    </Button>
                    <Button
                        size="sm"
                        type="submit"
                        form="tier-form"
                        :disabled="form.processing"
                        data-testid="tiers-form-save"
                    >
                        <Loader2 v-if="form.processing" class="size-4 animate-spin" />
                        <Save v-else class="size-4" />
                        {{ form.processing ? ACTION.saving : ACTION.save }}
                    </Button>
                </template>
            </Dialog>

            <ConfirmDeleteDialog
                :open="Boolean(deleting)"
                title="Hapus Jenjang?"
                description="Jenjang ini dihapus permanen dari jalur komite."
                :processing="rowForm.processing"
                @update:open="deleting = null"
                @confirm="confirmDelete"
            />
        </div>
    </AppLayout>
</template>
