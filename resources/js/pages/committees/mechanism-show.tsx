import { Head, router } from '@inertiajs/react';
import { ArrowLeft, SlidersHorizontal, Users } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { Badge, PageHeader } from '@/components/ui/misc';
import { rupiah } from '@/lib/format';

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

type PathItem = {
    id: number;
    title: string;
    follows_default: boolean;
    is_active: boolean;
};

type Props = {
    mechanismKey: string;
    defaultId: number | null;
    levels: Level[];
    paths: PathItem[];
    members: { total: number; withoutNik: number } | null;
    canManage: boolean;
};

const DECISIONS = [
    ['can_escalate', 'Escalate'],
    ['can_approve', 'Approve'],
    ['can_cancel', 'Cancel'],
    ['can_reject', 'Reject'],
] as const;

const CONTENT: Record<
    string,
    { title: string; description: string; rules: string[] }
> = {
    plafon: {
        title: 'Authority by amount',
        description: 'The loan amount picks who decides.',
        rules: [
            'The file goes to the level whose amount range covers the loan. Every lower level it passes only escalates.',
            'An individual level (analyst staff) is the person who holds the file, not a sitting committee. Its people are not listed as committee members.',
            'A path follows the default levels, so a change to them reaches every such path at once. A path with levels of its own keeps its own limits.',
        ],
    },
    hierarki: {
        title: 'Committee hierarchy',
        description:
            'The amount does not matter: every committee is passed in order.',
        rules: [
            'The file climbs every committee in order; only the last one (President Director) decides.',
            'Individual levels (analyst staff) are not part of the climb.',
            'Only the order of the roles is taken from the default levels; the amounts are not used.',
        ],
    },
    conflict: {
        title: 'Applicant is a committee member',
        description: 'Nobody takes part in deciding their own file.',
        rules: [
            'An applicant is recognised by their national ID (NIK); a file can also be flagged by hand.',
            'When the level that would decide is the applicant’s, the level above decides.',
            'If the top committee member is the applicant, the highest level below decides, and the file is marked as an exception.',
            'If the applicant’s role has another holder (such as the second section head), that person acts and the level stays.',
            'The applicant can never be the section head or the surveyor of their own file.',
            'Scope: the applicant only. Close family is on the backlog.',
        ],
    },
};

export default function CommitteeMechanismShow(props: Props) {
    const { defaultId, levels, paths, members, canManage } = props;
    const key = props.mechanismKey;
    const content = CONTENT[key];
    const byAmount = key === 'plafon';

    const levelColumns: Column<Level>[] = [
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
        { key: 'role', header: 'Deciding role', cell: (l) => l.role },
        {
            key: 'kind',
            header: 'Kind',
            hideBelow: 'sm',
            cell: (l) => (
                <Badge tone={l.is_individual ? 'info' : 'neutral'}>
                    {l.is_individual ? 'Individual (file holder)' : 'Committee'}
                </Badge>
            ),
        },
        ...(byAmount
            ? [
                  {
                      key: 'range',
                      header: 'Amount range',
                      align: 'right',
                      className: 'whitespace-nowrap tabular-nums',
                      cell: (l: Level) =>
                          `${rupiah(l.min_amount ?? 0)} – ${l.max_amount === null ? 'no limit' : rupiah(l.max_amount)}`,
                  } satisfies Column<Level>,
                  {
                      key: 'decisions',
                      header: 'Decisions',
                      hideBelow: 'md',
                      cell: (l: Level) => (
                          <span className="flex flex-wrap gap-1">
                              {DECISIONS.filter(([k]) => l[k]).map(
                                  ([k, label]) => (
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
                                  ),
                              )}
                          </span>
                      ),
                  } satisfies Column<Level>,
              ]
            : []),
        {
            key: 'people',
            header: 'People',
            align: 'right',
            hideBelow: 'sm',
            className: 'tabular-nums',
            cell: (l) => l.people,
        },
    ];

    const pathColumns: Column<PathItem>[] = [
        {
            key: 'title',
            header: 'Path',
            className: 'font-medium',
            cell: (p) => p.title,
        },
        {
            key: 'levels',
            header: 'Levels',
            cell: (p) => (
                <Badge tone={p.follows_default ? 'info' : 'neutral'}>
                    {p.follows_default ? 'Default' : 'Own'}
                </Badge>
            ),
        },
        {
            key: 'status',
            header: 'Status',
            hideBelow: 'sm',
            cell: (p) => (
                <Badge tone={p.is_active ? 'success' : 'neutral'}>
                    {p.is_active ? 'Active' : 'Inactive'}
                </Badge>
            ),
        },
    ];

    return (
        <>
            <Head title={content.title} />
            <PageHeader
                title={content.title}
                description={content.description}
                actions={
                    <>
                        <Button
                            variant="outline"
                            onClick={() =>
                                router.visit('/committees/mechanism')
                            }
                        >
                            <ArrowLeft /> Back
                        </Button>
                        {canManage && defaultId && key !== 'conflict' && (
                            <Button
                                variant="outline"
                                onClick={() =>
                                    router.visit(`/committees/${defaultId}`)
                                }
                            >
                                <SlidersHorizontal /> Edit default levels
                            </Button>
                        )}
                        {key === 'conflict' && (
                            <Button
                                variant="outline"
                                onClick={() =>
                                    router.visit('/committees/members')
                                }
                            >
                                <Users /> Members
                            </Button>
                        )}
                    </>
                }
            />

            <h2 className="mb-2 text-sm font-semibold">Rules</h2>
            <ul className="border-border mb-5 flex list-disc flex-col gap-1.5 rounded-lg border bg-surface py-3 pr-3 pl-7 text-sm text-muted">
                {content.rules.map((rule) => (
                    <li key={rule}>{rule}</li>
                ))}
            </ul>

            {members && (
                <p className="mb-5 text-sm text-muted">
                    {members.total} committee{' '}
                    {members.total === 1 ? 'member' : 'members'} ·{' '}
                    {members.withoutNik} without a NIK on record.
                </p>
            )}

            {key !== 'conflict' && (
                <>
                    <h2 className="mb-2 text-sm font-semibold">
                        Default levels
                    </h2>
                    <DataTable
                        rows={levels}
                        rowKey={(l) => l.id}
                        columns={levelColumns}
                        empty={{
                            icon: <SlidersHorizontal />,
                            title: 'No default levels yet',
                        }}
                    />

                    <h2 className="mt-5 mb-2 text-sm font-semibold">
                        Paths using this mechanism
                    </h2>
                    <DataTable
                        rows={paths}
                        rowKey={(p) => p.id}
                        columns={pathColumns}
                        onRowClick={(p) => router.visit(`/committees/${p.id}`)}
                        empty={{
                            icon: <SlidersHorizontal />,
                            title: 'No paths use this mechanism',
                        }}
                    />
                </>
            )}
        </>
    );
}
