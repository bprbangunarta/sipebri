import { Check, ChevronsUpDown, X } from 'lucide-react';
import { useMemo, useState } from 'react';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import { controlClass } from '@/components/ui/input';

export type Option = { value: string | number; label: string };

type Props = {
    options: Option[];
    value: string | number | null | undefined;
    onChange: (value: string | null) => void;
    placeholder?: string;
    searchable?: boolean;
    clearable?: boolean;
    invalid?: boolean;
    id?: string;
    className?: string;
};

/**
 * Selectable dropdown; shows a search box when `searchable` (default) so long lists stay usable.
 */
export function Combobox({
    options,
    value,
    onChange,
    placeholder = 'Select…',
    searchable = true,
    clearable = false,
    invalid,
    id,
    className,
}: Props) {
    const [open, setOpen] = useState(false);
    const [query, setQuery] = useState('');
    const [active, setActive] = useState(0);

    const selected = options.find((o) => String(o.value) === String(value ?? ''));
    const filtered = useMemo(
        () =>
            options.filter((o) =>
                o.label.toLowerCase().includes(query.trim().toLowerCase()),
            ),
        [options, query],
    );

    const choose = (option: Option) => {
        onChange(String(option.value));
        setOpen(false);
    };

    const onKeyDown = (e: React.KeyboardEvent) => {
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            setActive((i) => Math.min(i + 1, filtered.length - 1));
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            setActive((i) => Math.max(i - 1, 0));
        } else if (e.key === 'Enter' && filtered[active]) {
            e.preventDefault();
            choose(filtered[active]);
        }
    };

    return (
        <Popover
            open={open}
            onOpenChange={(next) => {
                setOpen(next);
                setQuery('');
                setActive(Math.max(0, options.findIndex((o) => String(o.value) === String(value ?? ''))));
            }}
        >
            <div className={cn('relative', className)}>
                <PopoverTrigger asChild>
                    <button
                        id={id}
                        type="button"
                        role="combobox"
                        aria-expanded={open}
                        aria-invalid={invalid || undefined}
                        className={cn(controlClass, 'flex items-center justify-between gap-1 text-left', clearable && selected && 'pr-12')}
                    >
                        <span className={cn('truncate', !selected && 'text-muted/70')}>
                            {selected?.label ?? placeholder}
                        </span>
                        <ChevronsUpDown className="size-3.5 shrink-0 text-muted" />
                    </button>
                </PopoverTrigger>
                {clearable && selected && (
                    <button
                        type="button"
                        aria-label="Clear selection"
                        className="absolute top-1/2 right-7 -translate-y-1/2 rounded p-0.5 text-muted hover:text-ink"
                        onClick={() => onChange(null)}
                    >
                        <X className="size-3" />
                    </button>
                )}
            </div>
            <PopoverContent
                className="w-(--radix-popover-trigger-width) min-w-44 p-1"
                onOpenAutoFocus={(e) => {
                    if (!searchable) {
                        return;
                    }
                    e.preventDefault();
                    (e.currentTarget as HTMLElement).querySelector('input')?.focus();
                }}
            >
                {searchable && (
                    <input
                        value={query}
                        onChange={(e) => {
                            setQuery(e.target.value);
                            setActive(0);
                        }}
                        onKeyDown={onKeyDown}
                        placeholder="Search…"
                        className="mb-1 h-7 w-full rounded border border-line px-2 text-sm focus:border-primary focus:outline-none"
                    />
                )}
                <ul role="listbox" className="max-h-56 overflow-auto" onKeyDown={onKeyDown} tabIndex={-1}>
                    {filtered.length === 0 && (
                        <li className="px-2 py-2 text-center text-xs text-muted">
                            No results found
                        </li>
                    )}
                    {filtered.map((option, index) => {
                        const isSelected = String(option.value) === String(value ?? '');

                        return (
                            <li
                                key={option.value}
                                role="option"
                                aria-selected={isSelected}
                                onMouseEnter={() => setActive(index)}
                                onClick={() => choose(option)}
                                className={cn(
                                    'flex cursor-pointer items-center justify-between rounded px-2 py-1.5 text-sm',
                                    index === active && 'bg-canvas',
                                )}
                            >
                                <span className="truncate">{option.label}</span>
                                {isSelected && <Check className="size-3.5 text-primary" />}
                            </li>
                        );
                    })}
                </ul>
            </PopoverContent>
        </Popover>
    );
}
