<script setup>
import { computed, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

import BusinessTabs from '@/components/composite/analysis/BusinessTabs.vue';
import FieldMoney from '@/components/composite/analysis/FieldMoney.vue';
import FieldStat from '@/components/composite/analysis/FieldStat.vue';
import ItemRows from '@/components/composite/analysis/ItemRows.vue';
import FormActions from '@/components/composite/FormActions.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardFooter from '@/components/ui/CardFooter.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
import Combobox from '@/components/ui/Combobox.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import Textarea from '@/components/ui/Textarea.vue';
import { persen, rupiah } from '@/constants/committee';
import { tradeMetrics, TRADE_COSTS } from '@/constants/analysisMath';

/** Usaha perdagangan: identitas, barang dagang, analisa keuangan harian. */
const props = defineProps({
    url: { type: String, required: true },
    business: { type: Object, required: true },
    options: { type: Object, required: true },
});

const tabs = [
    { key: 'identitas', label: 'Identitas' },
    { key: 'barang', label: 'Barang Dagang' },
    { key: 'keuangan', label: 'Analisa Keuangan' },
];
const tab = ref('identitas');

const identity = useForm({
    name: props.business.name,
    business_length: props.business.business_length ?? '',
    address: props.business.address ?? '',
});

const goods = useForm({
    groups: ['GOODS'],
    items: props.business.items
        .filter((i) => i.group === 'GOODS')
        .map((i) => ({ group: 'GOODS', name: i.name, price: i.price, sell_price: i.sell_price, qty: i.qty })),
});

const finance = useForm({
    daily_purchase: props.business.daily_purchase,
    cost_of_goods: props.business.cost_of_goods,
    projection_addition: props.business.projection_addition,
    ...Object.fromEntries(TRADE_COSTS.map((c) => [c.key, props.business[c.key]])),
});

const live = computed(() => tradeMetrics(finance, goods.items));

const rowProfit = (row) => Number(row.sell_price || 0) - Number(row.price || 0);
const rowMargin = (row) => (row.price ? (rowProfit(row) / Number(row.price)) * 100 : 0);

const save = (form) => form.put(props.url, { preserveScroll: true });
</script>

<template>
    <div class="space-y-3">
        <BusinessTabs :tabs="tabs" :active="tab" @select="tab = $event" />

        <Card v-if="tab === 'identitas'" data-testid="trade-identity">
            <CardHeader><CardTitle>Identitas Usaha</CardTitle></CardHeader>
            <CardContent class="form-dense grid gap-3 sm:grid-cols-2">
                <FieldStat label="Kode Usaha" :value="props.business.code" />
                <div class="space-y-[var(--item-gap)]">
                    <Label for="trade-name">Nama Usaha <span class="text-destructive">*</span></Label>
                    <Input id="trade-name" v-model="identity.name" maxlength="150" data-testid="trade-name" />
                    <p v-if="identity.errors.name" class="text-xs font-medium text-destructive">
                        {{ identity.errors.name }}
                    </p>
                </div>
                <div class="space-y-[var(--item-gap)]">
                    <Label for="trade-length">Lama Usaha</Label>
                    <Combobox
                        id="trade-length"
                        v-model="identity.business_length"
                        :options="props.options.lengths.map((v) => ({ value: v, label: v }))"
                        placeholder="(Opsional)"
                        data-testid="trade-length"
                    />
                </div>
                <div class="space-y-[var(--item-gap)] sm:col-span-2">
                    <Label for="trade-address">Alamat Usaha</Label>
                    <Textarea
                        id="trade-address"
                        v-model="identity.address"
                        rows="2"
                        maxlength="255"
                        placeholder="(Opsional)"
                        data-testid="trade-address"
                    />
                </div>
            </CardContent>
            <CardFooter>
                <FormActions
                    cancel-testid="trade-identity-reset"
                    cancel-label="Kembalikan"
                    submit-testid="trade-identity-save"
                    :processing="identity.processing"
                    :disabled="!identity.name"
                    @cancel="identity.reset()"
                    @submit="save(identity)"
                />
            </CardFooter>
        </Card>

        <Card v-else-if="tab === 'barang'" data-testid="trade-goods">
            <CardHeader><CardTitle>Barang Dagang</CardTitle></CardHeader>
            <CardContent class="form-dense space-y-4">
                <ItemRows
                    :rows="goods.items"
                    :columns="[
                        { key: 'name', label: 'Nama Barang', type: 'text' },
                        { key: 'price', label: 'Harga Beli', type: 'money', width: '160px' },
                        { key: 'sell_price', label: 'Harga Jual', type: 'money', width: '160px' },
                        { key: 'qty', label: 'Stok', type: 'money', width: '120px' },
                    ]"
                    :derived="[
                        { label: 'Laba', format: (row) => rupiah(rowProfit(row)) },
                        { label: '%', format: (row) => persen(rowMargin(row)) },
                    ]"
                    :blank="{ group: 'GOODS', name: '', price: 0, sell_price: 0, qty: 0 }"
                    add-label="Tambah Barang"
                    empty-text="Belum ada barang dagang."
                    testid="trade-goods-rows"
                />

                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                    <FieldStat label="Total Beli" :value="rupiah(live.total_buy)" />
                    <FieldStat label="Total Jual" :value="rupiah(live.total_sell)" />
                    <FieldStat label="Total Laba" :value="rupiah(live.total_profit)" />
                    <FieldStat label="Stok" :value="String(live.total_stock)" />
                    <FieldStat label="Margin" :value="persen(live.margin_percent)" strong testid="trade-margin" />
                </div>
            </CardContent>
            <CardFooter>
                <FormActions
                    cancel-testid="trade-goods-reset"
                    cancel-label="Kembalikan"
                    submit-testid="trade-goods-save"
                    :processing="goods.processing"
                    @cancel="goods.reset()"
                    @submit="save(goods)"
                />
            </CardFooter>
        </Card>

        <Card v-else data-testid="trade-finance">
            <CardHeader><CardTitle>Analisa Keuangan</CardTitle></CardHeader>
            <CardContent class="form-dense space-y-4">
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <FieldMoney
                        v-model="finance.daily_purchase"
                        label="Belanja Harian"
                        testid="trade-daily-purchase"
                        :error="finance.errors.daily_purchase"
                    />
                    <FieldMoney
                        v-model="finance.cost_of_goods"
                        label="Pokok Penjualan"
                        testid="trade-cost-of-goods"
                        :error="finance.errors.cost_of_goods"
                    />
                    <FieldMoney
                        v-for="cost in TRADE_COSTS"
                        :key="cost.key"
                        v-model="finance[cost.key]"
                        :label="cost.label"
                        :testid="`trade-${cost.key}`"
                        :error="finance.errors[cost.key]"
                    />
                    <FieldMoney
                        v-model="finance.projection_addition"
                        label="Proyeksi Penambahan"
                        testid="trade-projection"
                        :error="finance.errors.projection_addition"
                    />
                </div>

                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
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
                </div>

                <p class="text-xs text-muted-foreground">
                    Omset harian dihitung dari belanja harian dikali margin laba barang dagang
                    ({{ persen(live.margin_percent) }}).
                </p>
            </CardContent>
            <CardFooter>
                <FormActions
                    cancel-testid="trade-finance-reset"
                    cancel-label="Kembalikan"
                    submit-testid="trade-finance-save"
                    :processing="finance.processing"
                    @cancel="finance.reset()"
                    @submit="save(finance)"
                />
            </CardFooter>
        </Card>
    </div>
</template>
