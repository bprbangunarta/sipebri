import { Head, router, useForm } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { Field } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Badge, PageHeader } from '@/components/ui/misc';
import {
    FARM_COSTS,
    farmMetrics,
    otherMetrics,
    serviceMetrics,
    TRADE_COSTS,
    tradeMetrics,
} from '@/lib/analysis-math';
import { rupiah } from '@/lib/format';
import {
    ItemRows,
    MoneyField,
    Panel,
    SaveBar,
    Stat,
    TextField,
} from '@/pages/credit-analysis/parts';

type Item = {
    group: string;
    name: string;
    qty: string | number;
    price: string | number;
    sell_price: string | number;
};
type Business = Record<string, string | number | null> & {
    id: number;
    type: 'trade' | 'farm' | 'service' | 'other';
    code: string;
    name: string;
    items: (Item & { id: number })[];
    metrics: Record<string, number>;
    updated_at?: string;
};

type Props = {
    application: {
        id: number;
        application_code: string;
        full_name: string;
        nik: string;
        requested_amount: number;
        requested_tenor: number;
        installment_label: string | null;
        installment_period: number;
        product_label: string | null;
    };
    business: Business;
    canEdit: boolean;
    options: {
        lengths: string[];
        sectors: string[];
        plants: string[];
        kinds: string[];
    };
};

const TYPE_LABELS = {
    trade: 'Usaha Perdagangan',
    farm: 'Usaha Pertanian',
    service: 'Usaha Jasa',
    other: 'Usaha Lainnya',
} as const;
const GROUPS = {
    trade: ['goods'],
    farm: [],
    service: [],
    other: ['material', 'income', 'expense'],
} as const;

const COMMON = ['name', 'business_length', 'address', 'projection_addition'];
const FIELDS: Record<Business['type'], string[]> = {
    trade: [
        'daily_purchase',
        'cost_of_goods',
        ...TRADE_COSTS.map((c) => c.key),
    ],
    farm: [
        'economy_sector',
        'plant_type',
        'area_own',
        'area_rent',
        'area_pawn',
        'harvest_quintals',
        'price_per_quintal',
        ...FARM_COSTS.map((c) => c.key),
        'addition_result',
    ],
    service: ['service_income', 'vehicle_tax', 'other_expense'],
    other: ['business_kind'],
};

const blank = (group: string): Item => ({
    group,
    name: '',
    qty: 0,
    price: 0,
    sell_price: 0,
});
const options = (list: string[]) => list.map((v) => ({ value: v, label: v }));

export default function CreditAnalysisBusiness({
    application,
    business,
    canEdit,
    options: opts,
}: Props) {
    const type = business.type;
    const keys = [...COMMON, ...FIELDS[type]];
    type Values = Record<string, string | number | null> & {
        groups: string[];
        items: Item[];
    };
    const form = useForm<Values>({
        ...Object.fromEntries(keys.map((k) => [k, business[k] ?? ''])),
        groups: [...GROUPS[type]],
        items: business.items.map(
            ({ group, name, qty, price, sell_price }) => ({
                group,
                name,
                qty,
                price,
                sell_price,
            }),
        ),
    } as unknown as Values);
    const set = (key: string) => (value: string) => form.setData(key, value);
    const itemsOf = (group: string) =>
        form.data.items.filter((i) => i.group === group);
    const setGroup = (group: string) => (rows: Item[]) =>
        form.setData('items', [
            ...form.data.items.filter((i) => i.group !== group),
            ...rows,
        ]);

    const live =
        type === 'trade'
            ? tradeMetrics(form.data, itemsOf('goods'))
            : type === 'farm'
              ? farmMetrics(form.data, application)
              : type === 'service'
                ? serviceMetrics(form.data)
                : otherMetrics(
                      form.data,
                      itemsOf('material'),
                      itemsOf('income'),
                      itemsOf('expense'),
                  );
    const m: Record<string, number> = form.isDirty ? live : business.metrics;
    const money = (key: string) => (
        <MoneyField
            key={key}
            label={
                (
                    [...TRADE_COSTS, ...FARM_COSTS] as {
                        key: string;
                        label: string;
                    }[]
                ).find((c) => c.key === key)?.label ?? key
            }
            value={form.data[key]}
            onChange={set(key)}
            error={form.errors[key]}
            disabled={!canEdit}
        />
    );

    const save = () =>
        form.put(
            `/credit-analysis/${application.id}/businesses/${business.id}`,
            { preserveScroll: true, onSuccess: () => form.setDefaults() },
        );

    return (
        <>
            <Head title={`${business.code} ${business.name}`} />
            <PageHeader
                title={business.name}
                description={`${TYPE_LABELS[type]} · ${application.application_code} · ${application.full_name}`}
                actions={
                    <>
                        <Badge tone="neutral">{business.code}</Badge>
                        <Button
                            variant="outline"
                            onClick={() =>
                                router.visit(
                                    `/credit-analysis/${application.id}?section=business&type=${type}`,
                                )
                            }
                        >
                            <ArrowLeft /> Kembali
                        </Button>
                    </>
                }
            />

            <div className="flex flex-col gap-3">
                <Panel
                    title={
                        type === 'farm' ? 'Informasi Usaha' : 'Identitas Usaha'
                    }
                >
                    <Stat label="Kode Usaha" value={business.code} />
                    <TextField
                        label="Nama Usaha"
                        required
                        value={String(form.data.name ?? '')}
                        onChange={set('name')}
                        error={form.errors.name}
                        disabled={!canEdit}
                        maxLength={150}
                    />
                    {type === 'other' && (
                        <Field
                            label="Jenis Usaha"
                            error={form.errors.business_kind}
                        >
                            <Combobox
                                clearable
                                searchable={false}
                                placeholder="(Opsional)"
                                options={options(opts.kinds)}
                                value={String(form.data.business_kind ?? '')}
                                onChange={(v) =>
                                    canEdit &&
                                    form.setData('business_kind', v ?? '')
                                }
                            />
                        </Field>
                    )}
                    {type !== 'farm' && (
                        <Field
                            label="Lama Usaha"
                            error={form.errors.business_length}
                        >
                            <Combobox
                                clearable
                                searchable={false}
                                placeholder="(Opsional)"
                                options={options(opts.lengths)}
                                value={String(form.data.business_length ?? '')}
                                onChange={(v) =>
                                    canEdit &&
                                    form.setData('business_length', v ?? '')
                                }
                            />
                        </Field>
                    )}
                    {type === 'farm' && (
                        <>
                            <Field
                                label="Sektor Ekonomi"
                                error={form.errors.economy_sector}
                            >
                                <Combobox
                                    clearable
                                    searchable={false}
                                    placeholder="(Opsional)"
                                    options={options(opts.sectors)}
                                    value={String(
                                        form.data.economy_sector ?? '',
                                    )}
                                    onChange={(v) =>
                                        canEdit &&
                                        form.setData('economy_sector', v ?? '')
                                    }
                                />
                            </Field>
                            <Field
                                label="Jenis Tanaman"
                                error={form.errors.plant_type}
                            >
                                <Combobox
                                    clearable
                                    placeholder="(Opsional)"
                                    options={options(opts.plants)}
                                    value={String(form.data.plant_type ?? '')}
                                    onChange={(v) =>
                                        canEdit &&
                                        form.setData('plant_type', v ?? '')
                                    }
                                />
                            </Field>
                        </>
                    )}
                    <TextField
                        label="Alamat Usaha"
                        value={String(form.data.address ?? '')}
                        onChange={set('address')}
                        error={form.errors.address}
                        disabled={!canEdit}
                        className="sm:col-span-2"
                    />
                </Panel>

                {type === 'trade' && (
                    <>
                        <Panel title="Barang Dagang" columns="">
                            <ItemRows
                                rows={
                                    itemsOf('goods') as unknown as Record<
                                        string,
                                        string | number
                                    >[]
                                }
                                columns={[
                                    {
                                        key: 'name',
                                        label: 'Nama Barang',
                                        type: 'text',
                                    },
                                    {
                                        key: 'price',
                                        label: 'Harga Beli',
                                        type: 'money',
                                    },
                                    {
                                        key: 'sell_price',
                                        label: 'Harga Jual',
                                        type: 'money',
                                    },
                                    {
                                        key: 'qty',
                                        label: 'Stok',
                                        type: 'number',
                                    },
                                ]}
                                onChange={(rows) =>
                                    setGroup('goods')(rows as unknown as Item[])
                                }
                                addLabel="Tambah Barang"
                                emptyRow={
                                    blank('goods') as unknown as Record<
                                        string,
                                        string | number
                                    >
                                }
                                disabled={!canEdit}
                                errors={form.errors}
                            />
                            <div className="grid gap-3 sm:grid-cols-4">
                                <Stat
                                    label="Total Beli"
                                    value={rupiah(m.total_buy)}
                                />
                                <Stat
                                    label="Total Jual"
                                    value={rupiah(m.total_sell)}
                                />
                                <Stat
                                    label="Total Laba"
                                    value={rupiah(m.total_profit)}
                                />
                                <Stat
                                    label="Margin"
                                    value={`${Number(m.margin_percent).toFixed(2)}%`}
                                />
                            </div>
                        </Panel>
                        <Panel title="Analisa Keuangan">
                            <MoneyField
                                label="Belanja Harian"
                                value={form.data.daily_purchase}
                                onChange={set('daily_purchase')}
                                error={form.errors.daily_purchase}
                                disabled={!canEdit}
                            />
                            <MoneyField
                                label="Pokok Penjualan"
                                value={form.data.cost_of_goods}
                                onChange={set('cost_of_goods')}
                                error={form.errors.cost_of_goods}
                                disabled={!canEdit}
                            />
                            {TRADE_COSTS.map((c) => money(c.key))}
                            <MoneyField
                                label="Proyeksi Penambahan"
                                value={form.data.projection_addition}
                                onChange={set('projection_addition')}
                                error={form.errors.projection_addition}
                                disabled={!canEdit}
                            />
                        </Panel>
                        <Panel
                            title="Ringkasan Perhitungan"
                            columns="sm:grid-cols-2 lg:grid-cols-4"
                        >
                            <Stat
                                label="Margin Barang Dagang"
                                value={`${Number(m.margin_percent).toFixed(2)}%`}
                            />
                            <Stat
                                label="Omset Harian"
                                value={rupiah(m.daily_revenue)}
                            />
                            <Stat
                                label="Laba Bersih Harian"
                                value={rupiah(m.daily_profit)}
                            />
                            <Stat
                                label="Biaya Harian"
                                value={rupiah(m.daily_cost)}
                            />
                            <Stat
                                label="Laba Bulanan"
                                value={rupiah(m.monthly_profit)}
                            />
                            <Stat
                                label="Biaya Bulanan"
                                value={rupiah(m.monthly_cost)}
                            />
                            <Stat
                                label="Hasil Bersih Usaha"
                                value={rupiah(m.net_profit)}
                                strong
                            />
                        </Panel>
                    </>
                )}

                {type === 'farm' && (
                    <>
                        <Panel title="Lahan dan Hasil Panen">
                            <MoneyField
                                label="Luas Milik Sendiri (m²)"
                                value={form.data.area_own}
                                onChange={set('area_own')}
                                error={form.errors.area_own}
                                disabled={!canEdit}
                            />
                            <MoneyField
                                label="Luas Hasil Sewa (m²)"
                                value={form.data.area_rent}
                                onChange={set('area_rent')}
                                error={form.errors.area_rent}
                                disabled={!canEdit}
                            />
                            <MoneyField
                                label="Luas Hasil Gadai (m²)"
                                value={form.data.area_pawn}
                                onChange={set('area_pawn')}
                                error={form.errors.area_pawn}
                                disabled={!canEdit}
                            />
                            <Stat
                                label="Total Luas Tanah (m²)"
                                value={String(m.total_area ?? 0)}
                            />
                            <Field
                                label="Hasil Panen (Kwintal)"
                                error={form.errors.harvest_quintals}
                            >
                                <Input
                                    type="number"
                                    min={0}
                                    step="0.01"
                                    disabled={!canEdit}
                                    value={form.data.harvest_quintals ?? ''}
                                    onChange={(e) =>
                                        form.setData(
                                            'harvest_quintals',
                                            e.target.value,
                                        )
                                    }
                                    className="text-right tabular-nums"
                                />
                            </Field>
                            <MoneyField
                                label="Harga Per Kwintal"
                                value={form.data.price_per_quintal}
                                onChange={set('price_per_quintal')}
                                error={form.errors.price_per_quintal}
                                disabled={!canEdit}
                            />
                        </Panel>
                        <Panel title="Biaya Pertanian">
                            {FARM_COSTS.map((c) => money(c.key))}
                        </Panel>
                        <Panel title="Analisa Keuangan">
                            <MoneyField
                                label="Penambahan Hasil Usaha"
                                value={form.data.addition_result}
                                onChange={set('addition_result')}
                                error={form.errors.addition_result}
                                disabled={!canEdit}
                            />
                            <Stat
                                label="Pinjaman Bank Lain"
                                value={rupiah(m.other_bank_loan)}
                            />
                            <Stat
                                label="Setoran Pokok"
                                value={`${rupiah(m.principal_installment)} (tiap ${m.installment_period} bulan)`}
                            />
                        </Panel>
                        <Panel
                            title="Ringkasan Perhitungan"
                            columns="sm:grid-cols-2 lg:grid-cols-3"
                        >
                            <Stat
                                label="Pendapatan Hasil Panen"
                                value={rupiah(m.harvest_income)}
                            />
                            <Stat
                                label="Pengeluaran Biaya Usaha"
                                value={rupiah(m.total_cost)}
                            />
                            <Stat
                                label="Hasil Bersih Usaha"
                                value={rupiah(m.net_profit)}
                            />
                            <Stat
                                label="Pendapatan Setelah Pokok"
                                value={rupiah(m.after_principal)}
                            />
                            <Stat
                                label="Pendapatan Perbulan"
                                value={rupiah(m.monthly_income)}
                                strong
                            />
                        </Panel>
                    </>
                )}

                {type === 'service' && (
                    <>
                        <Panel title="Analisa Keuangan">
                            <MoneyField
                                label="Pendapatan Usaha"
                                value={form.data.service_income}
                                onChange={set('service_income')}
                                error={form.errors.service_income}
                                disabled={!canEdit}
                            />
                            <MoneyField
                                label="Pajak Kendaraan"
                                value={form.data.vehicle_tax}
                                onChange={set('vehicle_tax')}
                                error={form.errors.vehicle_tax}
                                disabled={!canEdit}
                            />
                            <MoneyField
                                label="Pengeluaran Lainnya"
                                value={form.data.other_expense}
                                onChange={set('other_expense')}
                                error={form.errors.other_expense}
                                disabled={!canEdit}
                            />
                        </Panel>
                        <Panel title="Ringkasan Perhitungan">
                            <Stat
                                label="Total Penghasilan"
                                value={rupiah(m.total_income)}
                            />
                            <Stat
                                label="Total Pengeluaran"
                                value={rupiah(m.total_expense)}
                            />
                            <Stat
                                label="Hasil Usaha Bersih"
                                value={rupiah(m.net_profit)}
                                strong
                            />
                        </Panel>
                    </>
                )}

                {type === 'other' && (
                    <>
                        <Panel title="Bahan Baku" columns="">
                            <ItemRows
                                rows={
                                    itemsOf('material') as unknown as Record<
                                        string,
                                        string | number
                                    >[]
                                }
                                columns={[
                                    {
                                        key: 'name',
                                        label: 'Bahan Baku',
                                        type: 'text',
                                    },
                                    {
                                        key: 'qty',
                                        label: 'Jumlah',
                                        type: 'number',
                                    },
                                    {
                                        key: 'price',
                                        label: 'Harga',
                                        type: 'money',
                                    },
                                ]}
                                onChange={(rows) =>
                                    setGroup('material')(
                                        rows as unknown as Item[],
                                    )
                                }
                                addLabel="Tambah Bahan Baku"
                                emptyRow={
                                    blank('material') as unknown as Record<
                                        string,
                                        string | number
                                    >
                                }
                                disabled={!canEdit}
                                errors={form.errors}
                            />
                            <Stat
                                label="Biaya Bahan Baku"
                                value={rupiah(m.material_cost)}
                            />
                        </Panel>
                        <Panel title="Keuangan" columns="">
                            <p className="text-xs font-medium">
                                Rincian Pendapatan
                            </p>
                            <ItemRows
                                rows={
                                    itemsOf('income') as unknown as Record<
                                        string,
                                        string | number
                                    >[]
                                }
                                columns={[
                                    {
                                        key: 'name',
                                        label: 'Pendapatan',
                                        type: 'text',
                                    },
                                    {
                                        key: 'price',
                                        label: 'Nominal',
                                        type: 'money',
                                    },
                                ]}
                                onChange={(rows) =>
                                    setGroup('income')(
                                        rows as unknown as Item[],
                                    )
                                }
                                addLabel="Tambah Pendapatan"
                                emptyRow={
                                    blank('income') as unknown as Record<
                                        string,
                                        string | number
                                    >
                                }
                                disabled={!canEdit}
                                errors={form.errors}
                            />
                            <p className="text-xs font-medium">
                                Rincian Pengeluaran
                            </p>
                            <ItemRows
                                rows={
                                    itemsOf('expense') as unknown as Record<
                                        string,
                                        string | number
                                    >[]
                                }
                                columns={[
                                    {
                                        key: 'name',
                                        label: 'Pengeluaran',
                                        type: 'text',
                                    },
                                    {
                                        key: 'price',
                                        label: 'Nominal',
                                        type: 'money',
                                    },
                                ]}
                                onChange={(rows) =>
                                    setGroup('expense')(
                                        rows as unknown as Item[],
                                    )
                                }
                                addLabel="Tambah Pengeluaran"
                                emptyRow={
                                    blank('expense') as unknown as Record<
                                        string,
                                        string | number
                                    >
                                }
                                disabled={!canEdit}
                                errors={form.errors}
                            />
                            <div className="max-w-xs">
                                <MoneyField
                                    label="Proyeksi Penambahan"
                                    value={form.data.projection_addition}
                                    onChange={set('projection_addition')}
                                    error={form.errors.projection_addition}
                                    disabled={!canEdit}
                                />
                            </div>
                        </Panel>
                        <Panel title="Ringkasan Perhitungan">
                            <Stat
                                label="Pendapatan Usaha"
                                value={rupiah(m.business_income)}
                            />
                            <Stat
                                label="Biaya Operasional"
                                value={rupiah(m.operational_cost)}
                            />
                            <Stat
                                label="Biaya Bahan Baku"
                                value={rupiah(m.material_cost)}
                            />
                            <Stat
                                label="Hasil Usaha Bersih"
                                value={rupiah(m.net_profit)}
                                strong
                            />
                        </Panel>
                    </>
                )}

                {canEdit && (
                    <SaveBar
                        processing={form.processing}
                        dirty={form.isDirty}
                        note={business.updated_at ?? null}
                        onReset={() => form.reset()}
                        onSave={save}
                    />
                )}
            </div>
        </>
    );
}
