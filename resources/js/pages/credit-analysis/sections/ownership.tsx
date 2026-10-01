import { useForm } from '@inertiajs/react';
import { Combobox } from '@/components/ui/combobox';
import { Field } from '@/components/ui/field';
import { ASSET_LABELS } from '@/lib/analysis-math';
import { ItemRows, Panel, SaveBar } from '@/pages/credit-analysis/parts';
import type { Finance } from '@/pages/credit-analysis/types';

export function OwnershipSection({
    loanId,
    finance,
    assets,
    canEdit,
}: {
    loanId: number;
    finance: Finance;
    assets: Record<string, string[]>;
    canEdit: boolean;
}) {
    const keys = Object.keys(assets);
    type Values = Record<string, string | null> & { items: { name: string }[] };
    const form = useForm<Values>({
        ...Object.fromEntries(
            keys.map((k) => [k, (finance[k] as string | null) ?? '']),
        ),
        items: finance.assets,
    } as unknown as Values);

    return (
        <div className="flex flex-col gap-3">
            <Panel title="Harta Kepemilikan">
                {keys.map((key) => (
                    <Field
                        key={key}
                        label={ASSET_LABELS[key] ?? key}
                        error={form.errors[key]}
                    >
                        <Combobox
                            clearable
                            searchable={false}
                            placeholder="(Opsional)"
                            options={assets[key].map((o) => ({
                                value: o,
                                label: o,
                            }))}
                            value={form.data[key]}
                            onChange={(v) =>
                                canEdit && form.setData(key, v ?? '')
                            }
                        />
                    </Field>
                ))}
            </Panel>
            <Panel title="Harta Lain" columns="">
                <ItemRows
                    rows={form.data.items}
                    columns={[{ key: 'name', label: 'Harta', type: 'text' }]}
                    onChange={(items) => form.setData('items', items)}
                    addLabel="Tambah Harta"
                    emptyRow={{ name: '' }}
                    disabled={!canEdit}
                    errors={form.errors}
                />
            </Panel>
            {canEdit && (
                <SaveBar
                    processing={form.processing}
                    dirty={form.isDirty}
                    note={finance.updated_at}
                    onReset={() => form.reset()}
                    onSave={() =>
                        form.put(`/credit-analysis/${loanId}/ownership`, {
                            preserveScroll: true,
                            onSuccess: () => form.setDefaults(),
                        })
                    }
                />
            )}
        </div>
    );
}
