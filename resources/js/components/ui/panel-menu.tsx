import { cn } from '@/lib/utils';

export type PanelMenuItem = { key: string; label: string; /** Something on this entry is typed and not saved yet. */ unsaved?: boolean };

/**
 * The menu of a page that has several parts (a submenu panel inside the page, the "multi menu" pattern): a small heading and a list of
 * parts separated by hairlines, with a line on the side that borders the content. It is a column on a wide screen and a row that scrolls
 * sideways on a phone. Put it as the first child of a `grid lg:grid-cols-[16rem_minmax(0,1fr)]` card, with the content in the second.
 * Labels are capitalised and wrap, so they are always read in full.
 */
export function PanelMenu({ label, items, active, onSelect }: { label: string; items: PanelMenuItem[]; active: string; onSelect: (key: string) => void }) {
    return (
        <nav
            aria-label={label}
            className="flex flex-row gap-1 overflow-x-auto border-b border-line p-2 lg:flex-col lg:gap-0 lg:divide-y lg:divide-line lg:border-r lg:border-b-0"
        >
            <p className="hidden px-2.5 pt-1 pb-1.5 text-2xs font-semibold tracking-wider text-muted uppercase lg:block">{label}</p>
            {items.map((item) => (
                <button
                    key={item.key}
                    type="button"
                    aria-current={item.key === active ? 'true' : undefined}
                    onClick={() => onSelect(item.key)}
                    className={cn(
                        'flex min-h-8 shrink-0 cursor-pointer items-center gap-2 rounded-md px-2.5 py-1.5 text-left text-sm font-medium whitespace-nowrap capitalize transition-colors lg:whitespace-normal',
                        item.key === active ? 'bg-primary-soft text-primary' : 'text-muted hover:bg-canvas hover:text-ink',
                    )}
                >
                    <span className="flex-1">{item.label}</span>
                    {item.unsaved && (
                        <>
                            <span className="size-1.5 shrink-0 rounded-full bg-warning" aria-hidden="true" />
                            <span className="sr-only">Belum disimpan</span>
                        </>
                    )}
                </button>
            ))}
        </nav>
    );
}
