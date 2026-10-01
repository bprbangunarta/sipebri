import { useForm } from '@inertiajs/react';
import { rupiah } from '@/lib/format';
import { financeMetrics, HOUSEHOLD_COSTS } from '@/lib/analysis-math';
import {
    ItemRows,
    MoneyField,
    Panel,
    SaveBar,
    Stat,
} from '@/pages/credit-analysis/parts';
import type { Finance } from '@/pages/credit-analysis/types';

type Obligation = { name: string; amount: string | number };

export function FinanceSection({
    loanId,
    finance,
    canEdit,
}: {
    loanId: number;
    finance: Finance;
    canEdit: boolean;
}) {
    type Values = Record<string, string | number | null> & {
        items: Obligation[];
    };
    const form = useForm<Values>({
        ...Object.fromEntries(
            HOUSEHOLD_COSTS.map((c) => [c.key, finance[c.key] ?? 0]),
        ),
        items: finance.obligations,
    } as unknown as Values);
    const live = financeMetrics(form.data, form.data.items, finance.metrics);
    const shown = form.isDirty ? live : finance.metrics;

    return (
        <div className="flex flex-col gap-3">
            <Panel
                title="Kemampuan Keuangan"
                hint="Biaya rumah tangga per bulan"
            >
                {HOUSEHOLD_COSTS.map((cost) => (
                    <MoneyField
                        key={cost.key}
                        label={cost.label}
                        value={form.data[cost.key]}
                        onChange={(v) => form.setData(cost.key, v)}
                        error={form.errors[cost.key]}
                        disabled={!canEdit}
                    />
                ))}
            </Panel>
            <Panel
                title="Kewajiban Lainnya"
                hint="Cicilan atau kewajiban ke pihak lain per bulan"
                columns=""
            >
                <ItemRows
                    rows={form.data.items}
                    columns={[
                        { key: 'name', label: 'Kewajiban Untuk', type: 'text' },
                        {
                            key: 'amount',
                            label: 'Nominal per bulan',
                            type: 'money',
                        },
                    ]}
                    onChange={(items) => form.setData('items', items)}
                    addLabel="Tambah Kewajiban"
                    emptyRow={{ name: '', amount: '' }}
                    disabled={!canEdit}
                    errors={form.errors}
                />
            </Panel>
            <Panel
                title="Ringkasan Keuangan"
                hint="Pendapatan usaha dijumlahkan dari hasil tiap usaha di Analisa Usaha"
                columns="sm:grid-cols-2 lg:grid-cols-4"
            >
                <Stat
                    label="Usaha Perdagangan"
                    value={rupiah(finance.metrics.trade_income)}
                />
                <Stat
                    label="Usaha Jasa"
                    value={rupiah(finance.metrics.service_income)}
                />
                <Stat
                    label="Usaha Pertanian"
                    value={rupiah(finance.metrics.farm_income)}
                />
                <Stat
                    label="Usaha Lainnya"
                    value={rupiah(finance.metrics.other_income)}
                />
                <Stat
                    label="Pendapatan Usaha"
                    value={rupiah(shown.business_income)}
                    strong
                />
                <Stat
                    label="Biaya Rumah Tangga"
                    value={rupiah(shown.household_cost)}
                />
                <Stat
                    label="Kewajiban Lainnya"
                    value={rupiah(shown.obligation_cost)}
                />
                <Stat
                    label="Keuangan Perbulan"
                    value={rupiah(
                        'monthly_balance' in shown ? shown.monthly_balance : 0,
                    )}
                    strong
                />
            </Panel>
            {canEdit && (
                <SaveBar
                    processing={form.processing}
                    dirty={form.isDirty}
                    note={finance.updated_at}
                    onReset={() => form.reset()}
                    onSave={() =>
                        form.put(`/credit-analysis/${loanId}/finance`, {
                            preserveScroll: true,
                            onSuccess: () => form.setDefaults(),
                        })
                    }
                />
            )}
        </div>
    );
}
