import { useForm } from '@inertiajs/react';
import { Combobox } from '@/components/ui/combobox';
import { Field } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { rupiah } from '@/lib/format';
import {
    MoneyField,
    Panel,
    SaveBar,
    Stat,
    TextField,
} from '@/pages/credit-analysis/parts';
import type { Memorandum } from '@/pages/credit-analysis/types';

const NEEDS = [
    ['Modal Kerja', 'working_capital'],
    ['Investasi', 'investment'],
    ['Konsumtif', 'consumption'],
    ['Pelunasan Kredit', 'loan_settlement'],
    ['Take Over', 'take_over'],
] as const;

const RATES = [
    ['Biaya Admin (%)', 'admin_rate'],
    ['Suku Bunga (%)', 'interest_rate'],
    ['Biaya Provisi (%)', 'provision_rate'],
    ['Biaya Penalti (%)', 'penalty_rate'],
] as const;

export function MemorandumSection({
    loanId,
    memorandum,
    bindings,
    canEdit,
}: {
    loanId: number;
    memorandum: Memorandum;
    bindings: string[];
    canEdit: boolean;
}) {
    const keys = [
        ...NEEDS.flatMap(([, k]) => [k, `${k}_note`]),
        ...RATES.map(([, k]) => k),
        'proposed_amount',
        'term_months',
        'before_disbursement',
        'additional_terms',
        'binding',
    ];
    const form = useForm<Record<string, string | number>>(
        Object.fromEntries(keys.map((k) => [k, memorandum[k] ?? ''])),
    );
    const totalNeed = NEEDS.reduce(
        (t, [, k]) => t + Number(form.data[k] || 0),
        0,
    );
    const set = (key: string) => (value: string) => form.setData(key, value);

    return (
        <div className="flex flex-col gap-3">
            <Panel title="Kebutuhan Dana" columns="">
                {NEEDS.map(([label, key]) => (
                    <div
                        key={key}
                        className="grid gap-3 sm:grid-cols-[16rem_minmax(0,1fr)]"
                    >
                        <MoneyField
                            label={label}
                            value={form.data[key]}
                            onChange={set(key)}
                            error={form.errors[key]}
                            disabled={!canEdit}
                        />
                        <TextField
                            label="Keterangan"
                            value={String(form.data[`${key}_note`] ?? '')}
                            onChange={set(`${key}_note`)}
                            error={form.errors[`${key}_note`]}
                            disabled={!canEdit}
                        />
                    </div>
                ))}
                <Stat
                    label="Jumlah Kebutuhan Dana"
                    value={rupiah(totalNeed)}
                    strong
                />
            </Panel>

            <Panel title="Usulan Fasilitas" columns="">
                <div className="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <Stat label="Kebutuhan Dana" value={rupiah(totalNeed)} />
                    <Stat
                        label="Plafon Diajukan"
                        value={rupiah(memorandum.requested_amount)}
                    />
                    <Stat
                        label="Taksasi Agunan"
                        value={rupiah(memorandum.appraisal_total)}
                    />
                    <Stat
                        label="Keuangan Perbulan"
                        value={rupiah(memorandum.monthly_balance)}
                    />
                </div>
                <div className="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <MoneyField
                        label="Usulan Plafon"
                        value={form.data.proposed_amount}
                        onChange={set('proposed_amount')}
                        error={form.errors.proposed_amount}
                        disabled={!canEdit}
                    />
                    <Field
                        label="Jangka Waktu (Bulan)"
                        error={form.errors.term_months}
                    >
                        <Input
                            type="number"
                            min={0}
                            disabled={!canEdit}
                            value={form.data.term_months}
                            onChange={(e) =>
                                form.setData('term_months', e.target.value)
                            }
                            className="text-right tabular-nums"
                        />
                    </Field>
                    {RATES.map(([label, key]) => (
                        <Field key={key} label={label} error={form.errors[key]}>
                            <Input
                                type="number"
                                min={0}
                                max={100}
                                step="0.01"
                                disabled={!canEdit}
                                value={form.data[key]}
                                onChange={(e) =>
                                    form.setData(key, e.target.value)
                                }
                                className="text-right tabular-nums"
                            />
                        </Field>
                    ))}
                    <Field
                        label="Pengikatan Asuransi"
                        error={form.errors.binding}
                    >
                        <Combobox
                            clearable
                            searchable={false}
                            placeholder="(Opsional)"
                            options={bindings.map((b) => ({
                                value: b,
                                label: b,
                            }))}
                            value={String(form.data.binding ?? '')}
                            onChange={(v) =>
                                canEdit && form.setData('binding', v ?? '')
                            }
                        />
                    </Field>
                    <TextField
                        className="sm:col-span-2"
                        label="Syarat Sebelum Realisasi"
                        value={String(form.data.before_disbursement ?? '')}
                        onChange={set('before_disbursement')}
                        error={form.errors.before_disbursement}
                        disabled={!canEdit}
                    />
                    <TextField
                        className="sm:col-span-2"
                        label="Syarat Tambahan"
                        value={String(form.data.additional_terms ?? '')}
                        onChange={set('additional_terms')}
                        error={form.errors.additional_terms}
                        disabled={!canEdit}
                    />
                </div>
                <p className="text-xs text-muted">
                    Usulan plafon{' '}
                    {rupiah(Number(form.data.proposed_amount || 0))} · bunga{' '}
                    {Number(form.data.interest_rate || 0)}% · provisi{' '}
                    {Number(form.data.provision_rate || 0)}%. Bandingkan dengan
                    taksasi agunan dan keuangan per bulan di atas sebelum
                    diajukan ke komite.
                </p>
            </Panel>

            {canEdit && (
                <SaveBar
                    processing={form.processing}
                    dirty={form.isDirty}
                    note={memorandum.updated_at}
                    onReset={() => form.reset()}
                    onSave={() =>
                        form.put(`/credit-analysis/${loanId}/memorandum`, {
                            preserveScroll: true,
                            onSuccess: () => form.setDefaults(),
                        })
                    }
                />
            )}
        </div>
    );
}
