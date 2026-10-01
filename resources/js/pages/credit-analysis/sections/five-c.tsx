import { useForm } from '@inertiajs/react';
import { Badge } from '@/components/ui/misc';
import { Combobox } from '@/components/ui/combobox';
import { Field } from '@/components/ui/field';
import { rupiah } from '@/lib/format';
import { FIVE_C } from '@/lib/analysis-assessment';
import { Panel, SaveBar, Stat } from '@/pages/credit-analysis/parts';
import type { FiveC } from '@/pages/credit-analysis/types';

const tone = (grade: string | null) =>
    grade === 'BAIK'
        ? 'success'
        : grade === 'CUKUP BAIK'
          ? 'warning'
          : grade
            ? 'danger'
            : 'neutral';

export function FiveCSection({
    loanId,
    fiveC,
    appraisalTotal,
    canEdit,
}: {
    loanId: number;
    fiveC: FiveC;
    appraisalTotal: number;
    canEdit: boolean;
}) {
    const keys = FIVE_C.flatMap((g) => g.fields.map((f) => f.key));
    const form = useForm<Record<string, number | null>>(
        Object.fromEntries(
            keys.map((k) => [k, (fiveC[k] as number | null) ?? null]),
        ),
    );
    const { metrics } = fiveC;

    return (
        <div className="flex flex-col gap-3">
            {FIVE_C.map((group) => (
                <Panel key={group.key} title={group.title}>
                    {group.fields.map((field) => (
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
                                    label: `${o.label} (${o.value})`,
                                }))}
                                value={form.data[field.key]}
                                onChange={(v) =>
                                    canEdit &&
                                    form.setData(
                                        field.key,
                                        v === null ? null : Number(v),
                                    )
                                }
                            />
                        </Field>
                    ))}
                    {group.key === 'collateral' && (
                        <Stat
                            label="Taksasi Agunan"
                            value={rupiah(appraisalTotal)}
                        />
                    )}
                </Panel>
            ))}
            <Panel
                title="Evaluasi 5C"
                hint="Dihitung sistem dari skor yang sudah diisi; simpan untuk memperbarui"
                columns="sm:grid-cols-2 lg:grid-cols-3"
            >
                {FIVE_C.map((group) => {
                    const g = metrics.groups[group.key];

                    return (
                        <Stat
                            key={group.key}
                            label={group.title}
                            value={
                                <span className="flex items-center gap-1.5">
                                    {g.filled > 0
                                        ? `${g.percent.toFixed(2)}%`
                                        : '–'}{' '}
                                    <span className="text-xs text-muted">
                                        ({g.score}/{g.max}, {g.filled}/{g.total}{' '}
                                        aspek)
                                    </span>
                                    {g.grade && (
                                        <Badge tone={tone(g.grade)}>
                                            {g.grade}
                                        </Badge>
                                    )}
                                </span>
                            }
                        />
                    );
                })}
                <Stat
                    label="Nilai Keseluruhan"
                    strong
                    value={
                        <span className="flex items-center gap-1.5">
                            {metrics.grade
                                ? `${metrics.percent.toFixed(2)}%`
                                : '–'}{' '}
                            {metrics.grade && (
                                <Badge tone={tone(metrics.grade)}>
                                    {metrics.grade}
                                </Badge>
                            )}
                        </span>
                    }
                />
            </Panel>
            {canEdit && (
                <SaveBar
                    processing={form.processing}
                    dirty={form.isDirty}
                    note={fiveC.updated_at}
                    onReset={() => form.reset()}
                    onSave={() =>
                        form.put(`/credit-analysis/${loanId}/five-c`, {
                            preserveScroll: true,
                            onSuccess: () => form.setDefaults(),
                        })
                    }
                />
            )}
        </div>
    );
}
