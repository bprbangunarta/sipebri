import { Link } from '@inertiajs/react';
import { cn } from '@/lib/utils';

const TABS = [
    { key: 'levels', label: 'Levels', href: '/committees' },
    { key: 'paths', label: 'Special rules', href: '/committees/paths' },
    { key: 'members', label: 'Members', href: '/committees/members' },
    { key: 'exceptions', label: 'Exceptions', href: '/committees/exceptions' },
] as const;

export type CommitteeTab = (typeof TABS)[number]['key'];

/** The four parts of Data Komite, one page each, so the menu stays a single item. */
export function CommitteeTabs({ current }: { current: CommitteeTab }) {
    return (
        <nav
            aria-label="Committee sections"
            className="mb-4 flex gap-4 overflow-x-auto border-b border-line"
        >
            {TABS.map((tab) => (
                <Link
                    key={tab.key}
                    href={tab.href}
                    aria-current={tab.key === current ? 'page' : undefined}
                    className={cn(
                        '-mb-px border-b-2 border-transparent px-1 py-2 text-sm font-medium whitespace-nowrap text-muted hover:text-ink',
                        tab.key === current && 'border-primary text-primary',
                    )}
                >
                    {tab.label}
                </Link>
            ))}
        </nav>
    );
}
