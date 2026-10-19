import { Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';
import type { ReactNode } from 'react';
import { Button } from '@/components/ui/button';
import { CurrencyInput } from '@/components/ui/currency-input';
import { Field } from '@/components/ui/field';
import { FilteredInput } from '@/components/ui/filtered-input';
import { Input } from '@/components/ui/input';
import { MaskedInput } from '@/components/ui/masked-input';
import { Card, CardBody, CardFooter, CardHeader } from '@/components/ui/misc';
import { Tip } from '@/components/ui/tooltip';
import { formatDateTime } from '@/lib/format';
import { cn } from '@/lib/utils';

/** Digits and a dot: the decimals the server accepts as `numeric` (rates, quantities). */
const DECIMAL_CHARACTER = /^[0-9.]$/;

/** A titled card of fields; `footer` holds the card's submit button. */
export function Panel({
    title,
    hint,
    actions,
    footer,
    children,
    columns = 'sm:grid-cols-2 lg:grid-cols-3',
}: {
    title: string;
    hint?: string;
    actions?: ReactNode;
    footer?: ReactNode;
    children: ReactNode;
    columns?: string;
}) {
    return (
        <Card>
            <CardHeader title={title} description={hint} actions={actions} />
            <CardBody className={cn('grid gap-3', columns)}>{children}</CardBody>
            {footer && <CardFooter className="justify-end">{footer}</CardFooter>}
        </Card>
    );
}

/** A read-only figure. */
export function Stat({ label, value, strong }: { label: string; value: ReactNode; strong?: boolean }) {
    return (
        <div>
            <p className="text-xs text-muted">{label}</p>
            <p className={`tabular-nums ${strong ? 'text-base font-semibold' : 'text-sm font-medium'}`}>{value}</p>
        </div>
    );
}

/** A server value (number, numeric string, or empty) as the number a `CurrencyInput` holds. */
export const toAmount = (value: string | number | null | undefined) => {
    if (value === null || value === undefined || value === '') {
        return null;
    }

    const n = Number(value);

    return Number.isFinite(n) ? n : null;
};

/** A whole amount (rupiah by default; `prefix={null}` for other whole numbers such as m²). Sends a number, or null when empty. */
export function MoneyField({
    label,
    value,
    onChange,
    error,
    disabled,
    className,
    prefix = 'Rp',
    required,
}: {
    label: string;
    value: string | number | null;
    onChange: (value: number | null) => void;
    error?: string;
    disabled?: boolean;
    className?: string;
    prefix?: string | null;
    required?: boolean;
}) {
    return (
        <Field label={label} error={error} className={className} required={required}>
            <CurrencyInput prefix={prefix} maxDigits={12} disabled={disabled} value={toAmount(value)} onValueChange={onChange} />
        </Field>
    );
}

/** A decimal such as a rate in percent; the text typed is sent as it is (the server checks it as `numeric`). */
export function DecimalField({
    label,
    value,
    onChange,
    error,
    disabled,
    className,
    hint,
}: {
    label: string;
    value: string | number | null;
    onChange: (value: string) => void;
    error?: string;
    disabled?: boolean;
    className?: string;
    hint?: string;
}) {
    return (
        <Field label={label} error={error} className={className} hint={hint}>
            <FilteredInput
                allowed={DECIMAL_CHARACTER}
                inputMode="decimal"
                maxLength={12}
                disabled={disabled}
                value={String(value ?? '')}
                onValueChange={onChange}
                className="text-right tabular-nums"
            />
        </Field>
    );
}

/** A plain whole number of a few digits (months, a year). */
export function DigitsField({
    label,
    value,
    digits,
    onChange,
    error,
    disabled,
    required,
    className,
}: {
    label: string;
    value: string | number | null;
    digits: number;
    onChange: (value: string) => void;
    error?: string;
    disabled?: boolean;
    required?: boolean;
    className?: string;
}) {
    return (
        <Field label={label} error={error} required={required} className={className}>
            <MaskedInput
                mask={'9'.repeat(digits)}
                disabled={disabled}
                value={String(value ?? '')}
                onValueChange={onChange}
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
        <Field label={label} error={error} className={className} required={required}>
            <Input disabled={disabled} maxLength={maxLength} value={value ?? ''} onChange={(e) => onChange(e.target.value)} />
        </Field>
    );
}

export type ItemColumn = {
    key: string;
    label: string;
    /** `money` is a whole rupiah amount (sent as a number), `decimal` a quantity (sent as typed). */
    type: 'text' | 'decimal' | 'money';
};

export type ItemValue = string | number | null;

// Rows have no id of their own until saved, so the list keeps a key per row that follows it when rows are added or removed.
let nextRowKey = 0;
const rowKey = () => {
    nextRowKey += 1;

    return `row-${nextRowKey}`;
};

/** An editable list of rows (goods, obligations, ...). */
export function ItemRows<T extends Record<string, ItemValue>>({
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
    const [keys, setKeys] = useState<string[]>(() => rows.map(rowKey));

    // Rows replaced from outside (Reset) get keys again.
    if (keys.length !== rows.length) {
        setKeys(rows.map(rowKey));
    }

    const set = (index: number, key: string, value: ItemValue) => onChange(rows.map((row, i) => (i === index ? { ...row, [key]: value } : row)));
    const add = () => {
        setKeys([...keys, rowKey()]);
        onChange([...rows, emptyRow]);
    };
    const remove = (index: number) => {
        setKeys(keys.filter((_, i) => i !== index));
        onChange(rows.filter((_, i) => i !== index));
    };

    const control = (row: T, index: number, column: ItemColumn) => {
        const value = row[column.key] ?? null;

        if (column.type === 'money') {
            return <CurrencyInput maxDigits={12} disabled={disabled} value={toAmount(value)} onValueChange={(v) => set(index, column.key, v)} />;
        }

        if (column.type === 'decimal') {
            return (
                <FilteredInput
                    allowed={DECIMAL_CHARACTER}
                    inputMode="decimal"
                    maxLength={12}
                    disabled={disabled}
                    value={String(value ?? '')}
                    onValueChange={(v) => set(index, column.key, v)}
                    className="text-right tabular-nums"
                />
            );
        }

        return <Input maxLength={150} disabled={disabled} value={String(value ?? '')} onChange={(e) => set(index, column.key, e.target.value)} />;
    };

    return (
        <div className="flex flex-col gap-2">
            {rows.length > 0 && (
                <div className="flex flex-col gap-2">
                    {rows.map((row, index) => (
                        <div key={keys[index] ?? `extra-${index}`} className="flex flex-wrap items-start gap-2 rounded-md border border-line p-2">
                            {columns.map((column) => (
                                <Field key={column.key} label={column.label} error={errors?.[`items.${index}.${column.key}`]}>
                                    {control(row, index, column)}
                                </Field>
                            ))}
                            {!disabled && (
                                <Tip label="Hapus baris">
                                    <Button type="button" variant="ghost" size="icon" className="mt-5" aria-label="Hapus baris" onClick={() => remove(index)}>
                                        <Trash2 />
                                    </Button>
                                </Tip>
                            )}
                        </div>
                    ))}
                </div>
            )}
            {!disabled && (
                <div>
                    <Button type="button" variant="outline" size="sm" onClick={add}>
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
            <span className="text-xs text-muted">{note ? `Terakhir disimpan ${formatDateTime(note)}` : 'Belum disimpan'}</span>
            <div className="flex gap-2">
                <Button type="button" variant="outline" disabled={!dirty || processing} onClick={onReset}>
                    Kembalikan
                </Button>
                <Button type="button" loading={processing} disabled={!dirty} onClick={onSave}>
                    Simpan semua
                </Button>
            </div>
        </div>
    );
}
