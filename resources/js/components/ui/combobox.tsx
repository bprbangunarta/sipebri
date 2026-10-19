import { Check, ChevronsUpDown, X } from 'lucide-react';
import { useEffect, useId, useMemo, useRef, useState } from 'react';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Tip } from '@/components/ui/tooltip';
import { cn } from '@/lib/utils';
import { controlClass } from '@/components/ui/input';

export type Option = {
    value: string | number;
    label: string;
    /** A second, smaller line under the label (identity details that help telling similar options apart). Also searchable. */
    description?: string;
};

type Props = {
    options: Option[];
    value: string | number | null | undefined;
    onChange: (value: string | null) => void;
    placeholder?: string;
    /** Accessible name when there is no visible `Field` label (e.g. a toolbar filter). */
    'aria-label'?: string;
    /** Passed down by `Field`: the error and the hint that describe the field, and whether it is invalid. */
    'aria-describedby'?: string;
    'aria-invalid'?: boolean;
    searchable?: boolean;
    clearable?: boolean;
    invalid?: boolean;
    id?: string;
    className?: string;
};

/**
 * Selectable dropdown; shows a search box when `searchable` (default) so long lists stay usable.
 *
 * Keyboard: the field opens with Enter, Space, or the arrow keys. Focus then goes to the search box (or, without one, to the list itself);
 * the arrows move the highlighted option (`aria-activedescendant` says which), Home and End jump in a list without a search box, Enter
 * chooses, and Escape closes and gives focus back to the field.
 */
export function Combobox({
    options,
    value,
    onChange,
    placeholder = 'Pilih…',
    'aria-label': ariaLabel,
    'aria-describedby': describedBy,
    'aria-invalid': ariaInvalid,
    searchable = true,
    clearable = false,
    invalid,
    id,
    className,
}: Props) {
    const [open, setOpen] = useState(false);
    const [query, setQuery] = useState('');
    const [active, setActive] = useState(0);
    const list = useRef<HTMLUListElement>(null);
    const listId = useId();
    const optionId = (index: number) => `${listId}-option-${index}`;

    const selected = options.find((o) => String(o.value) === String(value ?? ''));
    const filtered = useMemo(
        () => options.filter((o) => `${o.label} ${o.description ?? ''}`.toLowerCase().includes(query.trim().toLowerCase())),
        [options, query],
    );

    // Keyboard navigation must keep the highlighted option in view.
    useEffect(() => {
        if (open) {
            (list.current?.children[active] as HTMLElement | undefined)?.scrollIntoView({ block: 'nearest' });
        }
    }, [active, open, filtered]);

    const changeOpen = (next: boolean) => {
        setOpen(next);
        setQuery('');
        setActive(
            Math.max(
                0,
                options.findIndex((o) => String(o.value) === String(value ?? '')),
            ),
        );
    };

    const choose = (option: Option) => {
        onChange(String(option.value));
        setOpen(false);
    };

    // `inSearch`: the key was pressed in the search box, where Home and End must still move the caret.
    const onKeyDown = (event: React.KeyboardEvent, inSearch: boolean) => {
        if (event.key === 'ArrowDown') {
            event.preventDefault();
            setActive((i) => Math.min(i + 1, filtered.length - 1));
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            setActive((i) => Math.max(i - 1, 0));
        } else if (event.key === 'Home' && !inSearch) {
            event.preventDefault();
            setActive(0);
        } else if (event.key === 'End' && !inSearch) {
            event.preventDefault();
            setActive(Math.max(filtered.length - 1, 0));
        } else if (event.key === 'Enter' && filtered[active]) {
            event.preventDefault();
            choose(filtered[active]);
        }
    };

    const activeId = filtered[active] ? optionId(active) : undefined;

    return (
        <Popover open={open} onOpenChange={changeOpen}>
            <div className={cn('relative', className)}>
                <PopoverTrigger asChild>
                    <button
                        id={id}
                        type="button"
                        role="combobox"
                        aria-haspopup="listbox"
                        aria-expanded={open}
                        aria-controls={open ? listId : undefined}
                        aria-label={ariaLabel}
                        aria-describedby={describedBy}
                        aria-invalid={invalid || ariaInvalid || undefined}
                        title={selected?.label}
                        className={cn(controlClass, 'flex items-center justify-between gap-1 text-left', clearable && selected && 'pr-12')}
                        onKeyDown={(event) => {
                            if (!open && (event.key === 'ArrowDown' || event.key === 'ArrowUp')) {
                                event.preventDefault();
                                changeOpen(true);
                            }
                        }}
                    >
                        <span className={cn('truncate', !selected && 'text-muted')}>{selected?.label ?? placeholder}</span>
                        <ChevronsUpDown className="size-3.5 shrink-0 text-muted" />
                    </button>
                </PopoverTrigger>
                {clearable && selected && (
                    <Tip label="Hapus pilihan">
                        <button
                            type="button"
                            aria-label="Hapus pilihan"
                            className="absolute top-1/2 right-7 -translate-y-1/2 rounded p-0.5 text-muted hover:text-ink focus-visible:ring-2 focus-visible:ring-primary focus-visible:outline-none"
                            onClick={() => onChange(null)}
                        >
                            <X className="size-3" />
                        </button>
                    </Tip>
                )}
            </div>
            <PopoverContent
                aria-label={ariaLabel ?? placeholder}
                className="w-max max-w-[min(36rem,calc(100vw-2rem))] min-w-(--radix-popover-trigger-width) p-1"
                // Inside a dialog the page scroll lock swallows wheel/touch scrolling of portaled content; keep it for the list.
                onWheel={(e) => e.stopPropagation()}
                onTouchMove={(e) => e.stopPropagation()}
                // Focus goes to the search box, or to the list itself, never to the frame around them: the arrow keys are read on those two.
                onOpenAutoFocus={(e) => {
                    e.preventDefault();
                    (searchable ? (e.currentTarget as HTMLElement).querySelector('input') : list.current)?.focus();
                }}
            >
                {searchable && (
                    <input
                        value={query}
                        onChange={(e) => {
                            setQuery(e.target.value);
                            setActive(0);
                        }}
                        onKeyDown={(event) => onKeyDown(event, true)}
                        aria-label="Cari"
                        aria-controls={listId}
                        aria-activedescendant={activeId}
                        placeholder="Cari…"
                        className="mb-1 h-7 w-full rounded border border-line px-2 text-sm focus:border-primary focus:outline-none"
                    />
                )}
                <ul
                    ref={list}
                    id={listId}
                    role="listbox"
                    aria-label={ariaLabel ?? placeholder}
                    aria-activedescendant={searchable ? undefined : activeId}
                    className="max-h-56 overflow-auto overscroll-contain focus:outline-none"
                    onKeyDown={(event) => onKeyDown(event, false)}
                    tabIndex={-1}
                >
                    {filtered.length === 0 && <li className="px-2 py-2 text-center text-xs text-muted">Tidak ada hasil</li>}
                    {filtered.map((option, index) => {
                        const isSelected = String(option.value) === String(value ?? '');

                        return (
                            // Keyboard selection is handled on the list (Arrow/Enter); this handler is the pointer path.
                            // eslint-disable-next-line jsx-a11y/click-events-have-key-events -- weakening-allowed: keyboard selection is handled on the list itself
                            <li
                                id={optionId(index)}
                                key={option.value}
                                role="option"
                                aria-selected={isSelected}
                                onMouseEnter={() => setActive(index)}
                                onClick={() => choose(option)}
                                className={cn(
                                    'flex cursor-pointer items-start justify-between gap-2 rounded px-2 py-1.5 text-sm',
                                    index === active && 'bg-canvas',
                                )}
                            >
                                <span className="min-w-0 break-words">
                                    {option.label}
                                    {option.description && <span className="block text-xs text-muted">{option.description}</span>}
                                </span>
                                {isSelected && <Check className="mt-0.5 size-3.5 shrink-0 text-primary" />}
                            </li>
                        );
                    })}
                </ul>
            </PopoverContent>
        </Popover>
    );
}
