import { Combobox } from '@/components/ui/combobox';
import { RadioGroup } from '@/components/ui/choice';
import { CurrencyInput } from '@/components/ui/currency-input';
import { DatePicker } from '@/components/ui/date-picker';
import { FilteredInput } from '@/components/ui/filtered-input';
import { Input } from '@/components/ui/input';
import { MaskedInput } from '@/components/ui/masked-input';
import { Textarea } from '@/components/ui/textarea';
import { formatPercent, rupiah } from '@/lib/format';
import type { Choices, Item } from '@/pages/collaterals/appraisals/types';

/** The longest long text the server accepts. */
export const TEXTAREA_MAX = 2000;

const DECIMAL = /^[0-9.]$/;
const SIGNED_DECIMAL = /^[0-9.-]$/;
const EARLIEST = new Date(1950, 0, 1);
const LATEST = new Date(2100, 11, 31);

/** The options of a choice, in the order the form lists them. */
export const optionsOf = (item: Pick<Item, 'type' | 'options'>): { value: string; label: string }[] => {
    const scale: Record<string, Choices> = {
        yesno: { yes: 'Ya', no: 'Tidak' },
        rating: { good: 'Baik', fair: 'Cukup', poor: 'Kurang' },
        risk: { low: 'Rendah', medium: 'Sedang', high: 'Tinggi' },
    };

    return Object.entries(item.options ?? scale[item.type] ?? {}).map(([value, label]) => ({ value, label }));
};

/** What a figure the system works out looks like beside the answers. */
export const showComputed = (value: number | string | null | undefined, format: Item['format']): string => {
    if (value === null || value === undefined || value === '') {
        return '–';
    }

    if (format === 'money') {
        return rupiah(Number(value));
    }

    return format === 'percent' ? formatPercent(Number(value)) : String(value);
};

type Props = {
    item: Pick<Item, 'type' | 'options'>;
    value: string;
    onChange: (value: string) => void;
    disabled?: boolean;
    /** The accessible name when there is no visible label (a cell of a grid). */
    label?: string;
    /** Passed down by `Field`. */
    id?: string;
    compact?: boolean;
    invalid?: boolean;
};

/**
 * The control for one answer, chosen by the type of its item so that a field cannot hold a character the server would refuse:
 * digits for numbers, a masked rupiah amount, a decimal for percentages, a calendar for dates, and the fixed answers of a
 * check or a rating. The answer is always kept as the text the server stores.
 */
export function AnswerInput({ item, value, onChange, disabled, label, id, compact, invalid }: Props) {
    const common = { id, disabled, 'aria-label': label, 'aria-invalid': invalid || undefined };

    switch (item.type) {
        case 'textarea':
            return <Textarea {...common} maxLength={TEXTAREA_MAX} value={value} onChange={(e) => onChange(e.target.value)} />;
        case 'integer':
            return <MaskedInput {...common} mask="99999999" value={value} onValueChange={onChange} className="text-right tabular-nums" />;
        case 'money':
            return (
                <CurrencyInput
                    {...common}
                    prefix={compact ? null : 'Rp'}
                    maxDigits={12}
                    value={value === '' ? null : Number(value)}
                    onValueChange={(v) => onChange(v === null ? '' : String(v))}
                />
            );
        case 'percent':
        case 'adjustment':
            return (
                <FilteredInput
                    {...common}
                    allowed={item.type === 'adjustment' ? SIGNED_DECIMAL : DECIMAL}
                    inputMode="decimal"
                    maxLength={6}
                    value={value}
                    onValueChange={onChange}
                    className="text-right tabular-nums"
                />
            );
        case 'date':
            return <DatePicker {...common} min={EARLIEST} max={LATEST} value={value} onChange={onChange} />;
        case 'yesno':
        case 'rating':
        case 'risk':
            return (
                <RadioGroup
                    aria-label={label}
                    disabled={disabled}
                    className="flex-row gap-4"
                    options={optionsOf(item)}
                    value={value === '' ? undefined : value}
                    onValueChange={onChange}
                />
            );
        case 'choice':
            return (
                <Combobox
                    {...common}
                    searchable={false}
                    clearable
                    options={optionsOf(item)}
                    value={value === '' ? null : value}
                    onChange={(v) => onChange(v ?? '')}
                />
            );
        default:
            return <Input {...common} maxLength={255} value={value} onChange={(e) => onChange(e.target.value)} />;
    }
}
