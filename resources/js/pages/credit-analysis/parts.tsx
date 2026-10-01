import { Plus, Trash2 } from 'lucide-react';
import type { ReactNode } from 'react';
import { Button } from '@/components/ui/button';
import { Field } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Card } from '@/components/ui/misc';
import { rupiah } from '@/lib/format';

/** A titled block of fields. */
export function Panel({
    title,
    hint,
    children,
    columns = 'sm:grid-cols-2 lg:grid-cols-3',
}: {
    title: string;
    hint?: string;
    children: ReactNode;
    columns?: string;
}) {
    return (
        <Card>
            <div className="border-b border-line px-3 py-2">
                <h3 className="text-sm font-semibold">{title}</h3>
                {hint && <p className="text-xs text-muted">{hint}</p>}
            </div>
            <div className={`grid gap-3 p-3 ${columns}`}>{children}</div>
        </Card>
    );
}

/** A read-only figure. */
export function Stat({
    label,
    value,
    strong,
}: {
    label: string;
    value: ReactNode;
    strong?: boolean;
}) {
    return (
        <div>
            <p className="text-xs text-muted">{label}</p>
            <p
                className={`tabular-nums ${strong ? 'text-base font-semibold' : 'text-sm font-medium'}`}
            >
                {value}
            </p>
        </div>
    );
}

/** An amount of rupiah, shown in words of digits underneath as the other forms of the application do. */
export function MoneyField({
    label,
    value,
    onChange,
    error,
    disabled,
    className,
}: {
    label: string;
    value: string | number | null;
    onChange: (value: string) => void;
    error?: string;
    disabled?: boolean;
    className?: string;
}) {
    return (
        <Field
            label={label}
            error={error}
            hint={
                value === '' || value === null
                    ? undefined
                    : rupiah(Number(value))
            }
            className={className}
        >
            <Input
                type="number"
                min={0}
                disabled={disabled}
                value={value ?? ''}
                onChange={(e) => onChange(e.target.value)}
                aria-invalid={!!error}
                className="text-right tabular-nums"
            />
        </Field>
    );
}

export function TextField({
    label,
    value,
    onChange,
    error,
    disabled,
    className,
    maxLength = 255,
    required,
}: {
    label: string;
    value: string | null;
    onChange: (value: string) => void;
    error?: string;
    disabled?: boolean;
    className?: string;
    maxLength?: number;
    required?: boolean;
}) {
    return (
        <Field
            label={label}
            error={error}
            className={className}
            required={required}
        >
            <Input
                disabled={disabled}
                maxLength={maxLength}
                value={value ?? ''}
                onChange={(e) => onChange(e.target.value)}
                aria-invalid={!!error}
            />
        </Field>
    );
}

export type ItemColumn = {
    key: string;
    label: string;
    type: 'text' | 'number' | 'money';
};

/** An editable list of rows (goods, obligations, ...). */
export function ItemRows<T extends Record<string, string | number>>({
    rows,
    columns,
    onChange,
    addLabel,
    emptyRow,
    disabled,
    errors,
}: {
    rows: T[];
    columns: ItemColumn[];
    onChange: (rows: T[]) => void;
    addLabel: string;
    emptyRow: T;
    disabled?: boolean;
    errors?: Record<string, string>;
}) {
    const set = (index: number, key: string, value: string) =>
        onChange(
            rows.map((row, i) =>
                i === index ? { ...row, [key]: value } : row,
            ),
        );

    return (
        <div className="flex flex-col gap-2">
            {rows.length > 0 && (
                <div className="flex flex-col gap-2">
                    {rows.map((row, index) => (
                        <div
                            key={index}
                            className="flex flex-wrap items-start gap-2 rounded-md border border-line p-2"
                        >
                            {columns.map((column) => {
                                const error =
                                    errors?.[`items.${index}.${column.key}`];

                                return (
                                    <Field
                                        key={column.key}
                                        label={column.label}
                                        error={error}
                                    >
                                        <Input
                                            type={
                                                column.type === 'text'
                                                    ? 'text'
                                                    : 'number'
                                            }
                                            min={
                                                column.type === 'text'
                                                    ? undefined
                                                    : 0
                                            }
                                            step={
                                                column.type === 'number'
                                                    ? '0.01'
                                                    : undefined
                                            }
                                            disabled={disabled}
                                            value={row[column.key] ?? ''}
                                            className={
                                                column.type === 'text'
                                                    ? undefined
                                                    : 'text-right tabular-nums'
                                            }
                                            onChange={(e) =>
                                                set(
                                                    index,
                                                    column.key,
                                                    e.target.value,
                                                )
                                            }
                                            aria-invalid={!!error}
                                        />
                                    </Field>
                                );
                            })}
                            {!disabled && (
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="icon"
                                    className="mt-5"
                                    aria-label="Hapus baris"
                                    onClick={() =>
                                        onChange(
                                            rows.filter((_, i) => i !== index),
                                        )
                                    }
                                >
                                    <Trash2 />
                                </Button>
                            )}
                        </div>
                    ))}
                </div>
            )}
            {!disabled && (
                <div>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        onClick={() => onChange([...rows, emptyRow])}
                    >
                        <Plus /> {addLabel}
                    </Button>
                </div>
            )}
        </div>
    );
}

/** The save button of a section, kept in view while the form scrolls. */
export function SaveBar({
    processing,
    dirty,
    onSave,
    onReset,
    note,
}: {
    processing: boolean;
    dirty: boolean;
    onSave: () => void;
    onReset: () => void;
    note?: string | null;
}) {
    return (
        <div className="sticky bottom-3 flex flex-wrap items-center justify-between gap-2 rounded-md border border-line bg-surface/95 p-2 shadow-sm backdrop-blur">
            <span className="text-xs text-muted">
                {note ? `Terakhir disimpan ${note}` : 'Belum disimpan'}
            </span>
            <div className="flex gap-2">
                <Button
                    type="button"
                    variant="outline"
                    disabled={!dirty || processing}
                    onClick={onReset}
                >
                    Kembalikan
                </Button>
                <Button
                    type="button"
                    loading={processing}
                    disabled={!dirty}
                    onClick={onSave}
                >
                    Simpan semua
                </Button>
            </div>
        </div>
    );
}
