import { Head, router } from '@inertiajs/react';
import { Scale, SlidersHorizontal } from 'lucide-react';
import { useState } from 'react';
import { CommitteeTabs } from '@/components/committee-tabs';
import { Button } from '@/components/ui/button';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { Badge, PageHeader } from '@/components/ui/misc';
import { rupiah } from '@/lib/format';
import { AuthorityCheck } from '@/pages/committees/authority-check';

type Option = { value: number | string; label: string };

type Level = {
    id: number;
    label: string | null;
    role: string;
    is_individual: boolean;
    min_amount: number | null;
    max_amount: number | null;
    can_escalate: boolean;
    can_approve: boolean;
    can_cancel: boolean;
    can_reject: boolean;
    people: number;
};

type Props = {
    defaultId: number | null;
    levels: Level[];
    followers: number;
    special: number;
    productOptions: Option[];
    conditionMap: Record<string, string[]>;
    committeeMembers: Option[];
    canManage: boolean;
};

const DECISIONS = [
    ['can_escalate', 'Escalate'],
    ['can_approve', 'Approve'],
    ['can_cancel', 'Cancel'],
    ['can_reject', 'Reject'],
] as const;

export default function CommitteeLevels({
    defaultId,
    levels,
    followers,
    special,
    productOptions,
    conditionMap,
    committeeMembers,
    canManage,
}: Props) {
    const [checking, setChecking] = useState(false);

    const columns: Column<Level>[] = [
        {
            key: 'order',
            header: '#',
            narrow: true,
            className: 'text-muted tabular-nums',
            cell: (_l, i) => i + 1,
        },
        {
            key: 'level',
            header: 'Level',
            className: 'font-medium',
            cell: (l) => l.label ?? '–',
        },
        { key: 'role', header: 'Who decides', cell: (l) => l.role },
        {
            key: 'kind',
            header: 'Kind',
            hideBelow: 'sm',
            cell: (l) => (
                <Badge tone={l.is_individual ? 'info' : 'neutral'}>
                    {l.is_individual ? 'Individual' : 'Committee'}
                </Badge>
            ),
        },
        {
            key: 'range',
            header: 'Amount',
            align: 'right',
            className: 'whitespace-nowrap tabular-nums',
            cell: (l) =>
                `${rupiah(l.min_amount ?? 0)} – ${l.max_amount === null ? 'no limit' : rupiah(l.max_amount)}`,
        },
        {
            key: 'decisions',
            header: 'Decisions',
            hideBelow: 'md',
            cell: (l) => (
                <span className="flex flex-wrap gap-1">
                    {DECISIONS.filter(([k]) => l[k]).map(([k, label]) => (
                        <Badge
                            key={k}
                            tone={
                                k === 'can_approve'
                                    ? 'success'
                                    : k === 'can_reject'
                                      ? 'danger'
                                      : k === 'can_escalate'
                                        ? 'info'
                                        : 'neutral'
                            }
                        >
                            {label}
                        </Badge>
                    ))}
                </span>
            ),
        },
        {
            key: 'people',
            header: 'People',
            align: 'right',
            hideBelow: 'sm',
            className: 'tabular-nums',
            cell: (l) => l.people,
        },
    ];

    return (
        <>
            <Head title="Committees" />
            <PageHeader
                title="Committees"
                description="Who may decide a loan, by amount"
                actions={
                    <>
                        <Button
                            variant="outline"
                            onClick={() => setChecking(true)}
                        >
                            <Scale /> Check authority
                        </Button>
                        {canManage && defaultId && (
                            <Button
                                onClick={() =>
                                    router.visit(`/committees/${defaultId}`)
                                }
                            >
                                <SlidersHorizontal /> Edit levels
                            </Button>
                        )}
                    </>
                }
            />
            <CommitteeTabs current="levels" />

            <DataTable
                rows={levels}
                rowKey={(l) => l.id}
                columns={columns}
                empty={{
                    icon: <SlidersHorizontal />,
                    title: 'No levels yet',
                    description:
                        'Run the committee seeder to create the default levels.',
                }}
            />

            <div className="mt-4 grid gap-3 text-sm text-muted md:grid-cols-3">
                <p className="border-border rounded-lg border bg-surface p-3">
                    <span className="mb-1 block font-medium text-ink">
                        By amount
                    </span>
                    The file goes to the level whose amount range covers the
                    loan. Levels below it only escalate. {followers}{' '}
                    {followers === 1 ? 'path follows' : 'paths follow'} these
                    levels, so changing them here changes all of them.
                </p>
                <p className="border-border rounded-lg border bg-surface p-3">
                    <span className="mb-1 block font-medium text-ink">
                        Individual or committee
                    </span>
                    An individual level (analyst staff) is decided by the person
                    who holds the file. A committee level is decided by that
                    committee, so only those people are listed under Members.
                </p>
                <p className="border-border rounded-lg border bg-surface p-3">
                    <span className="mb-1 block font-medium text-ink">
                        Special rules
                    </span>
                    {special} {special === 1 ? 'path has' : 'paths have'} a
                    hierarchy (every committee in order, only the last decides)
                    or limits of their own. See the Special rules tab.
                </p>
            </div>

            <AuthorityCheck
                open={checking}
                onOpenChange={setChecking}
                productOptions={productOptions}
                conditionMap={conditionMap}
                memberOptions={committeeMembers}
            />
        </>
    );
}
