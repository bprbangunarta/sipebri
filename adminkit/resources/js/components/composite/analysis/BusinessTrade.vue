<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

import FieldMoney from '@/components/composite/analysis/FieldMoney.vue';
import FieldStat from '@/components/composite/analysis/FieldStat.vue';
import ItemRows from '@/components/composite/analysis/ItemRows.vue';
import FormActions from '@/components/composite/FormActions.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
import Combobox from '@/components/ui/Combobox.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import Textarea from '@/components/ui/Textarea.vue';
import { persen, rupiah } from '@/constants/committee';
import { tradeMetrics, TRADE_COSTS } from '@/constants/analysisMath';

/** Usaha perdagangan — satu lembar: identitas, barang dagang, keuangan harian. */
const props = defineProps({
    url: { type: String, required: true },
    business: { type: Object, required: true },
    options: { type: Object, required: true },
    application: { type: Object, required: true },
});

const form = useForm({
    name: props.business.name,
    business_length: props.business.business_length ?? '',
    address: props.business.address ?? '',
    daily_purchase: props.business.daily_purchase,
    cost_of_goods: props.business.cost_of_goods,
    projection_addition: props.business.projection_addition,
    ...Object.fromEntries(TRADE_COSTS.map((c) => [c.key, props.business[c.key]])),
    groups: ['GOODS'],
    items: props.business.items
        .filter((i) => i.group === 'GOODS')
        .map((i) => ({ group: 'GOODS', name: i.name, price: i.price, sell_price: i.sell_price, qty: i.qty })),
});

const live = computed(() => tradeMetrics(form, form.items));

const rowProfit = (row) => Number(row.sell_price || 0) - Number(row.price || 0);
const rowMargin = (row) => (row.price ? (rowProfit(row) / Number(row.price)) * 100 : 0);

const submit = () => form.put(props.url, { preserveScroll: true });
</script>

<template>
    <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_22rem]">
        <div class="min-w-0 space-y-4">
            <Card data-testid="trade-identity">
                <CardHeader><CardTitle>Identitas Usaha</CardTitle></CardHeader>
                <CardContent class="form-dense grid gap-3 sm:grid-cols-2">
                    <FieldStat label="Kode Usaha" :value="props.business.code" />
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="trade-name">Nama Usaha <span class="text-destructive">*</span></Label>
                        <Input id="trade-name" v-model="form.name" maxlength="150" data-testid="trade-name" />
                        <p v-if="form.errors.name" class="text-xs font-medium text-destructive">
                            {{ form.errors.name }}
                        </p>
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="trade-length">Lama Usaha</Label>
                        <Combobox
                            id="trade-length"
                            v-model="form.business_length"
                            :options="props.options.lengths.map((v) => ({ value: v, label: v }))"
                            placeholder="(Opsional)"
                            data-testid="trade-length"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="trade-address">Alamat Usaha</Label>
                        <Textarea
                            id="trade-address"
                            v-model="form.address"
                            rows="2"
                            maxlength="255"
                            placeholder="(Opsional)"
                            data-testid="trade-address"
                        />
                    </div>
                </CardContent>
            </Card>

            <Card data-testid="trade-goods">
                <CardHeader><CardTitle>Barang Dagang</CardTitle></CardHeader>
                <CardContent class="form-dense">
                    <ItemRows
                        :rows="form.items"
                        :columns="[
                            { key: 'name', label: 'Nama Barang', type: 'text' },
                            { key: 'price', label: 'Harga Beli', type: 'money', width: '150px' },
                            { key: 'sell_price', label: 'Harga Jual', type: 'money', width: '150px' },
                            { key: 'qty', label: 'Stok', type: 'money', width: '110px' },
                        ]"
                        :derived="[
                            { label: 'Laba', format: (row) => rupiah(rowProfit(row)) },
                            { label: '%', format: (row) => persen(rowMargin(row)) },
                        ]"
                        :blank="{ group: 'GOODS', name: '', price: 0, sell_price: 0, qty: 0 }"
                        :totals="[
                            { label: 'Total Beli', value: rupiah(live.total_buy) },
                            { label: 'Total Jual', value: rupiah(live.total_sell) },
                            { label: 'Total Laba', value: rupiah(live.total_profit) },
                            { label: 'Margin', value: persen(live.margin_percent) },
                        ]"
                        add-label="Tambah Barang"
                        empty-text="Belum ada barang dagang."
                        testid="trade-goods-rows"
                    />
                </CardContent>
            </Card>

            <Card data-testid="trade-finance">
                <CardHeader><CardTitle>Analisa Keuangan</CardTitle></CardHeader>
                <CardContent class="form-dense grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <FieldMoney
                        v-model="form.daily_purchase"
                        label="Belanja Harian"
                        testid="trade-daily-purchase"
                        :error="form.errors.daily_purchase"
                    />
                    <FieldMoney
                        v-model="form.cost_of_goods"
                        label="Pokok Penjualan"
                        testid="trade-cost-of-goods"
                        :error="form.errors.cost_of_goods"
                    />
                    <FieldMoney
                        v-for="cost in TRADE_COSTS"
                        :key="cost.key"
                        v-model="form[cost.key]"
                        :label="cost.label"
                        :testid="`trade-${cost.key}`"
                        :error="form.errors[cost.key]"
                    />
                    <FieldMoney
                        v-model="form.projection_addition"
                        label="Proyeksi Penambahan"
                        testid="trade-projection"
                        :error="form.errors.projection_addition"
                    />
                </CardContent>
            </Card>
        </div>

        <div class="space-y-3 xl:sticky xl:top-20 xl:self-start">
            <Card data-testid="trade-summary">
                <CardHeader><CardTitle>Ringkasan Perhitungan</CardTitle></CardHeader>
                <CardContent class="space-y-3">
                    <FieldStat label="Margin Barang Dagang" :value="persen(live.margin_percent)" testid="trade-margin" />
                    <FieldStat label="Omset Harian" :value="rupiah(live.daily_revenue)" testid="trade-daily-revenue" />
                    <FieldStat label="Laba Bersih Harian" :value="rupiah(live.daily_profit)" />
                    <FieldStat label="Biaya Harian" :value="rupiah(live.daily_cost)" />
                    <FieldStat label="Laba Bulanan" :value="rupiah(live.monthly_profit)" />
                    <FieldStat label="Biaya Bulanan" :value="rupiah(live.monthly_cost)" />
                    <FieldStat
                        label="Hasil Bersih Usaha"
                        :value="rupiah(live.net_profit)"
                        strong
                        testid="trade-net-profit"
                    />
                    <p class="text-xs text-muted-foreground">
                        Omset harian = belanja harian × (1 + margin barang dagang). Laba dan biaya bulanan dihitung
                        30 hari. Hasil bersih usaha inilah yang dipakai bagian Analisa Keuangan.
                    </p>
                </CardContent>
            </Card>

            <div class="sticky bottom-3 rounded-md border bg-background/95 p-2 shadow-sm backdrop-blur">
                <FormActions
                    cancel-testid="trade-reset"
                    cancel-label="Kembalikan"
                    submit-testid="trade-save"
                    submit-label="Simpan Semua"
                    :processing="form.processing"
                    :disabled="!form.name"
                    @cancel="form.reset()"
                    @submit="submit"
                />
            </div>
        </div>
    </div>
</template>
