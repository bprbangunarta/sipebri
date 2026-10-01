import { useForm } from '@inertiajs/react';
import { Combobox } from '@/components/ui/combobox';
import { Field } from '@/components/ui/field';
import {
    QUALITATIVE_BUSINESS,
    QUALITATIVE_CHARACTER_TEXTS,
    QUALITATIVE_CHOICE_FIELDS,
    QUALITATIVE_SCORES,
    QUALITATIVE_SWOT,
} from '@/lib/analysis-assessment';
import { Panel, SaveBar, TextField } from '@/pages/credit-analysis/parts';

type Values = Record<string, string | number | null>;

export function QualitativeSection({
    loanId,
    qualitative,
    choices,
    canEdit,
}: {
    loanId: number;
    qualitative: Values & { updated_at: string | null };
    choices: Record<string, string[]>;
    canEdit: boolean;
}) {
    const keys = [
        ...QUALITATIVE_SCORES.map((f) => f.key),
        ...QUALITATIVE_CHOICE_FIELDS.map(([, k]) => k),
        ...QUALITATIVE_CHARACTER_TEXTS.map(([, k]) => k),
        ...QUALITATIVE_BUSINESS.map(([, k]) => k),
        ...QUALITATIVE_SWOT.map(([, k]) => k),
        ...[1, 2, 3].flatMap((i) => [
            `obligation${i}_type`,
            `obligation${i}_note`,
            `obligation${i}_status`,
        ]),
        'trade_checking',
        'notes',
        'business_trade_checking',
    ];
    const form = useForm<Values>(
        Object.fromEntries(keys.map((k) => [k, qualitative[k] ?? ''])),
    );
    const options = (key: string) =>
        (choices[key] ?? []).map((v) => ({ value: v, label: v }));
    const text = (key: string) => String(form.data[key] ?? '');
    const area = (label: string, key: string) => (
        <Field
            label={label}
            error={form.errors[key]}
            className="sm:col-span-2 lg:col-span-3"
        >
            <textarea
                className="min-h-20 w-full rounded-md border border-line bg-surface px-2.5 py-1.5 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none disabled:opacity-60"
                maxLength={2000}
                disabled={!canEdit}
                value={text(key)}
                onChange={(e) => form.setData(key, e.target.value)}
            />
        </Field>
    );

    return (
        <div className="flex flex-col gap-3">
            <Panel title="Karakter">
                {QUALITATIVE_SCORES.map((field) => (
                    <Field
                        key={field.key}
                        label={field.label}
                        error={form.errors[field.key]}
                    >
                        <Combobox
                            clearable
                            searchable={false}
                            placeholder="(Opsional)"
                            options={field.options.map((o) => ({
                                value: o.value,
                                label: o.label,
                            }))}
                            value={form.data[field.key]}
                            onChange={(v) =>
                                canEdit &&
                                form.setData(
                                    field.key,
                                    v === null ? '' : Number(v),
                                )
                            }
                        />
                    </Field>
                ))}
                {QUALITATIVE_CHOICE_FIELDS.map(([label, key]) => (
                    <Field key={key} label={label} error={form.errors[key]}>
                        <Combobox
                            clearable
                            searchable={false}
                            placeholder="(Opsional)"
                            options={options(key)}
                            value={text(key)}
                            onChange={(v) =>
                                canEdit && form.setData(key, v ?? '')
                            }
                        />
                    </Field>
                ))}
                {QUALITATIVE_CHARACTER_TEXTS.map(([label, key]) => (
                    <TextField
                        key={key}
                        label={label}
                        value={text(key)}
                        onChange={(v) => form.setData(key, v)}
                        error={form.errors[key]}
                        disabled={!canEdit}
                    />
                ))}
            </Panel>

            <Panel title="Kewajiban ke Pihak Lain" columns="">
                {[1, 2, 3].map((i) => (
                    <div key={i} className="grid gap-3 sm:grid-cols-3">
                        <Field
                            label={`Pihak ${i}`}
                            error={form.errors[`obligation${i}_type`]}
                        >
                            <Combobox
                                clearable
                                searchable={false}
                                placeholder="(Opsional)"
                                options={options(`obligation${i}_type`)}
                                value={text(`obligation${i}_type`)}
                                onChange={(v) =>
                                    canEdit &&
                                    form.setData(`obligation${i}_type`, v ?? '')
                                }
                            />
                        </Field>
                        <TextField
                            label="Keterangan"
                            value={text(`obligation${i}_note`)}
                            onChange={(v) =>
                                form.setData(`obligation${i}_note`, v)
                            }
                            error={form.errors[`obligation${i}_note`]}
                            disabled={!canEdit}
                            maxLength={150}
                        />
                        <Field
                            label="Status"
                            error={form.errors[`obligation${i}_status`]}
                        >
                            <Combobox
                                clearable
                                searchable={false}
                                placeholder="(Opsional)"
                                options={options(`obligation${i}_status`)}
                                value={text(`obligation${i}_status`)}
                                onChange={(v) =>
                                    canEdit &&
                                    form.setData(
                                        `obligation${i}_status`,
                                        v ?? '',
                                    )
                                }
                            />
                        </Field>
                    </div>
                ))}
            </Panel>

            <Panel title="Usaha">
                {QUALITATIVE_BUSINESS.map(([label, key]) => (
                    <TextField
                        key={key}
                        label={label}
                        value={text(key)}
                        onChange={(v) => form.setData(key, v)}
                        error={form.errors[key]}
                        disabled={!canEdit}
                    />
                ))}
                {area('Trade Checking', 'trade_checking')}
            </Panel>

            <Panel title="SWOT" columns="sm:grid-cols-2">
                {QUALITATIVE_SWOT.map(([label, key]) => (
                    <TextField
                        key={key}
                        label={label}
                        value={text(key)}
                        onChange={(v) => form.setData(key, v)}
                        error={form.errors[key]}
                        disabled={!canEdit}
                    />
                ))}
            </Panel>

            <Panel title="Lainnya">
                {area('Catatan', 'notes')}
                {area('Trade Checking Usaha', 'business_trade_checking')}
            </Panel>

            {canEdit && (
                <SaveBar
                    processing={form.processing}
                    dirty={form.isDirty}
                    note={qualitative.updated_at}
                    onReset={() => form.reset()}
                    onSave={() =>
                        form.put(`/credit-analysis/${loanId}/qualitative`, {
                            preserveScroll: true,
                            onSuccess: () => form.setDefaults(),
                        })
                    }
                />
            )}
        </div>
    );
}
