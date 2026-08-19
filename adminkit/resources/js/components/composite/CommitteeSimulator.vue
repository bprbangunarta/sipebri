<script setup>
import { computed, ref } from 'vue';
import { AlertTriangle, Gavel, Loader2, Play, Scale, X } from 'lucide-vue-next';

import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Combobox from '@/components/ui/Combobox.vue';
import Dialog from '@/components/ui/Dialog.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import { digitsOnly, rupiah } from '@/constants/committee';

/** Simulasi kewenangan komite — hanya membaca aturan, tidak menyimpan apa pun. */
const props = defineProps({
    open: { type: Boolean, default: false },
    productOptions: { type: Array, default: () => [] },
    conditionOptions: { type: Array, default: () => [] },
});
const emit = defineEmits(['update:open']);

const productId = ref('');
const condition = ref('');
const amount = ref('');
const loading = ref(false);
const result = ref(null);
const error = ref('');

const statusVariant = (status) =>
    ({ decider: 'default', escalate: 'secondary', blocked: 'destructive', not_needed: 'outline' })[status] ?? 'secondary';

const rangeOf = (row) => {
    if (row.min_amount === null && row.max_amount === null) return 'Tanpa batas';

    return `${rupiah(row.min_amount ?? 0)} – ${row.max_amount === null ? 'ke atas' : rupiah(row.max_amount)}`;
};

const run = async () => {
    loading.value = true;
    error.value = '';
    result.value = null;

    const params = new URLSearchParams({ amount: amount.value || '0' });
    if (productId.value) params.set('product_id', productId.value);
    if (condition.value) params.set('condition', condition.value);

    try {
        const response = await fetch(`/committees/simulate?${params.toString()}`, {
            headers: { Accept: 'application/json' },
        });

        if (!response.ok) throw new Error('Simulasi gagal dijalankan.');
        result.value = await response.json();
    } catch (e) {
        error.value = e.message;
    } finally {
        loading.value = false;
    }
};

const close = () => emit('update:open', false);

// Produk wajib dipilih: simulasi untuk "Semua Produk" tidak bermakna.
const products = computed(() => props.productOptions.filter((o) => o.value !== ''));
const canRun = computed(() => Boolean(productId.value) && amount.value !== '' && !loading.value);
</script>

<template>
    <Dialog
        :open="props.open"
        title="Simulasi Kewenangan Komite"
        class="max-w-2xl"
        @update:open="emit('update:open', $event)"
    >
        <div class="space-y-4">
            <form
                id="simulate-form"
                class="form-dense grid gap-[var(--field-gap)] sm:grid-cols-3"
                novalidate
                @submit.prevent="run"
            >
                <div class="space-y-[var(--item-gap)]">
                    <Label>Produk</Label>
                    <Combobox
                        v-model="productId"
                        :options="products"
                        placeholder="Pilih Produk"
                        data-testid="simulate-product"
                    />
                </div>
                <div class="space-y-[var(--item-gap)]">
                    <Label>Kondisi / Kategori</Label>
                    <Combobox
                        v-model="condition"
                        :options="props.conditionOptions"
                        placeholder="Normal"
                        data-testid="simulate-condition"
                    />
                </div>
                <div class="space-y-[var(--item-gap)]">
                    <Label for="simulate-amount">Plafon</Label>
                    <Input
                        id="simulate-amount"
                        :model-value="amount"
                        inputmode="numeric"
                        placeholder="150000000"
                        data-testid="simulate-amount"
                        @input="amount = digitsOnly($event.target.value)"
                    />
                    <p class="text-xs text-muted-foreground">{{ rupiah(amount || null) }}</p>
                </div>
            </form>

            <p v-if="error" class="text-sm font-medium text-destructive" data-testid="simulate-error">{{ error }}</p>

            <div v-if="result && !result.found" class="rounded-md border border-dashed p-4 text-sm" data-testid="simulate-empty">
                {{ result.message }}
            </div>

            <div v-if="result?.found" class="space-y-3" data-testid="simulate-result">
                <div class="flex flex-wrap items-center gap-2 rounded-md border bg-muted/30 p-3 text-sm">
                    <Scale class="size-4 text-muted-foreground" />
                    <span class="font-medium">{{ result.path.product_label }}</span>
                    <Badge variant="secondary" class="font-medium">{{ result.path.condition_label }}</Badge>
                    <Badge class="font-medium">{{ result.path.mechanism_label }}</Badge>
                    <span
                        v-if="result.path.matched_globally"
                        class="text-xs text-muted-foreground"
                        data-testid="simulate-global-note"
                    >
                        (memakai jalur lintas produk)
                    </span>
                </div>

                <div class="space-y-2">
                    <div
                        v-for="row in result.chain"
                        :key="row.id"
                        class="flex flex-wrap items-center justify-between gap-2 rounded-md border p-3"
                        :class="row.status === 'decider' ? 'border-foreground/40 bg-muted/40' : ''"
                        :data-testid="`simulate-row-${row.role.replace(/\s+/g, '-').toLowerCase()}`"
                    >
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium">
                                {{ row.label ? `${row.label} · ` : '' }}{{ row.role }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ rangeOf(row) }} · {{ row.user_count }} pengguna
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <Badge v-if="row.status === 'decider'" class="gap-1 font-medium">
                                <Gavel class="size-3" />{{ row.status_label }}
                            </Badge>
                            <Badge v-else :variant="statusVariant(row.status)" class="font-medium">
                                {{ row.status_label }}
                            </Badge>
                            <span v-if="row.status === 'decider'" class="text-xs text-muted-foreground">
                                {{ row.decisions.filter((d) => d !== 'Naik Komite').join(' / ') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div
                    v-for="warning in result.warnings"
                    :key="warning"
                    class="flex items-start gap-2 rounded-md border border-destructive/40 bg-destructive/5 p-3 text-xs text-destructive"
                    data-testid="simulate-warning"
                >
                    <AlertTriangle class="mt-0.5 size-3.5 shrink-0" />
                    <span>{{ warning }}</span>
                </div>
            </div>
        </div>

        <template #footer>
            <Button variant="outline" size="sm" data-testid="simulate-close" @click="close">
                <X class="size-4" /> Tutup
            </Button>
            <Button size="sm" type="submit" form="simulate-form" :disabled="!canRun" data-testid="simulate-run">
                <Loader2 v-if="loading" class="size-4 animate-spin" />
                <Play v-else class="size-4" />
                {{ loading ? 'Menghitung…' : 'Jalankan Simulasi' }}
            </Button>
        </template>
    </Dialog>
</template>
