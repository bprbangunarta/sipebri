import { Head, router } from '@inertiajs/react';
import { format } from 'date-fns';
import { Download, Eye, ScrollText, ShieldCheck, X } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { DatePicker } from '@/components/ui/date-picker';
import { DialogFooter, Modal } from '@/components/ui/dialog';
import { FilterBar, SearchInput } from '@/components/ui/filter-bar';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { Badge, PageHeader } from '@/components/ui/misc';
import type { BadgeTone } from '@/components/ui/misc';
import type { PageMeta } from '@/components/ui/pagination';
import { Tip } from '@/components/ui/tooltip';
import { useListQuery } from '@/hooks/use-list-query';

type Values = Record<string, unknown>;
type LogRow = {
    id: number;
    at: string;
    user: string | null;
    username: string | null;
    event: string;
    module: string;
    action: string;
    outcome: 'success' | 'failure' | 'denied';
    subject: string | null;
    subject_type: string | null;
    subject_id: number | null;
    old: Values | null;
    new: Values | null;
    context: Values | null;
    ip: string | null;
    user_agent: string | null;
    method: string | null;
    url: string | null;
    request_id: string;
    hash: string;
};
type Filters = {
    search: string;
    module: string | null;
    outcome: 'success' | 'failure' | 'denied' | null;
    from: string | null;
    to: string | null;
    per_page: number;
};
type Verification = {
    ok: boolean;
    checked: number;
    broken_at: number | null;
    reason: string | null;
};
type Props = {
    logs: { data: LogRow[] } & PageMeta;
    filters: Filters;
    modules: string[];
    verification: Verification | null;
};

const OUTCOMES = [
    { value: 'success', label: 'Success' },
    { value: 'failure', label: 'Failure' },
    { value: 'denied', label: 'Denied' },
];
const TONES: Record<LogRow['outcome'], BadgeTone> = {
    success: 'success',
    failure: 'danger',
    denied: 'warning',
};
const DEFAULTS = { per_page: 25 };
const MIN_DATE = new Date(2020, 0, 1);

const show = (value: unknown): string =>
    value === null || value === undefined
        ? '–'
        : typeof value === 'string'
          ? value
          : JSON.stringify(value);

const changedFields = (log: LogRow): string[] =>
    Array.from(
        new Set([...Object.keys(log.old ?? {}), ...Object.keys(log.new ?? {})]),
    );

const diffColumns = (log: LogRow): Column<string>[] => [
    { key: 'field', header: 'Field', className: 'font-medium', cell: (f) => f },
    {
        key: 'before',
        header: 'Before',
        className: 'break-all text-muted',
        cell: (f) => show(log.old?.[f]),
    },
    {
        key: 'after',
        header: 'After',
        className: 'break-all',
        cell: (f) => show(log.new?.[f]),
    },
];

export default function AuditLogsIndex({
    logs,
    filters,
    modules,
    verification,
}: Props) {
    const [selected, setSelected] = useState<LogRow | null>(null);
    const [verifying, setVerifying] = useState(false);
    const { visit, search, onSearch, clear, loading, error } =
        useListQuery<Filters>({
            url: '/audit-logs',
            filters,
            defaults: DEFAULTS,
            only: ['logs', 'filters'],
        });

    const hasFilters = Boolean(
        filters.search ||
        filters.module ||
        filters.outcome ||
        filters.from ||
        filters.to,
    );
    const iso = (d: string) => (d ? d : null);

    const exportCsv = () => {
        const params = new URLSearchParams();
        (['search', 'module', 'outcome', 'from', 'to'] as const).forEach(
            (key) => filters[key] && params.set(key, filters[key] as string),
        );
        window.location.assign(`/audit-logs/export?${params.toString()}`);
    };

    const verify = () =>
        router.post(
            '/audit-logs/verify',
            {},
            {
                preserveScroll: true,
                onStart: () => setVerifying(true),
                onFinish: () => setVerifying(false),
            },
        );

    const columns: Column<LogRow>[] = [
        {
            key: 'time',
            header: 'Time',
            className: 'whitespace-nowrap',
            cell: (log) =>
                format(
                    new Date(log.at.replace(' ', 'T')),
                    'dd MMM yyyy HH:mm:ss',
                ),
        },
        {
            key: 'user',
            header: 'User',
            cell: (log) => (
                <>
                    <p className="font-medium">{log.user ?? 'System'}</p>
                    <p className="text-xs text-muted">{log.ip ?? ''}</p>
                </>
            ),
        },
        {
            key: 'event',
            header: 'Event',
            cell: (log) => (
                <>
                    <p>{log.event}</p>
                    <p className="text-xs text-muted">{log.module}</p>
                </>
            ),
        },
        {
            key: 'record',
            header: 'Record',
            hideBelow: 'md',
            cell: (log) => log.subject ?? '–',
        },
        {
            key: 'outcome',
            header: 'Outcome',
            cell: (log) => (
                <Badge tone={TONES[log.outcome]}>{log.outcome}</Badge>
            ),
        },
        {
            key: 'actions',
            header: 'Actions',
            srOnly: true,
            narrow: true,
            align: 'right',
            cell: (log) => (
                <Tip label="Details">
                    <Button
                        variant="ghost"
                        size="icon"
                        aria-label={`Details of entry ${log.id}`}
                        onClick={() => setSelected(log)}
                    >
                        <Eye />
                    </Button>
                </Tip>
            ),
        },
    ];

    return (
        <>
            <Head title="Audit Log" />
            <PageHeader
                title="Audit Log"
                description="Tamper-evident record of who did what, when and from where"
                actions={
                    <div className="flex items-center gap-2">
                        <Button
                            variant="outline"
                            onClick={verify}
                            disabled={verifying}
                        >
                            <ShieldCheck />{' '}
                            {verifying ? 'Verifying…' : 'Verify integrity'}
                        </Button>
                        <Button onClick={exportCsv}>
                            <Download /> Export CSV
                        </Button>
                    </div>
                }
            />

            {verification && (
                <div
                    role="status"
                    className={
                        verification.ok
                            ? 'mb-3 rounded-md border border-emerald-600/20 bg-emerald-50 px-3 py-2 text-sm text-emerald-700'
                            : 'mb-3 rounded-md border border-danger/30 bg-red-50 px-3 py-2 text-sm text-danger'
                    }
                >
                    {verification.ok
                        ? `Trail intact: ${verification.checked} entries verified.`
                        : `Trail broken at entry #${verification.broken_at}. ${verification.reason}`}
                </div>
            )}

            <DataTable
                rows={logs.data}
                rowKey={(r) => r.id}
                loading={loading}
                error={error}
                onRetry={() => visit({})}
                toolbar={
                    <FilterBar
                        search={
                            <SearchInput
                                value={search}
                                onChange={onSearch}
                                placeholder="Search user, event, record, IP…"
                                label="Search audit log"
                            />
                        }
                    >
                        <div className="w-full sm:w-36">
                            <DatePicker
                                value={filters.from ?? ''}
                                min={MIN_DATE}
                                placeholder="From"
                                onChange={(v) => visit({ from: iso(v) })}
                            />
                        </div>
                        <div className="w-full sm:w-36">
                            <DatePicker
                                value={filters.to ?? ''}
                                min={MIN_DATE}
                                placeholder="To"
                                onChange={(v) => visit({ to: iso(v) })}
                            />
                        </div>
                        <Combobox
                            className="w-full sm:w-40"
                            clearable
                            placeholder="Module"
                            options={modules.map((m) => ({
                                value: m,
                                label: m,
                            }))}
                            value={filters.module}
                            onChange={(v) => visit({ module: v })}
                        />
                        <Combobox
                            className="w-full sm:w-32"
                            clearable
                            searchable={false}
                            placeholder="Outcome"
                            options={OUTCOMES}
                            value={filters.outcome}
                            onChange={(v) =>
                                visit({ outcome: v as Filters['outcome'] })
                            }
                        />
                        {hasFilters && (
                            <Button
                                variant="ghost"
                                size="sm"
                                onClick={() =>
                                    clear({
                                        module: null,
                                        outcome: null,
                                        from: null,
                                        to: null,
                                    })
                                }
                            >
                                <X /> Reset
                            </Button>
                        )}
                    </FilterBar>
                }
                columns={columns}
                empty={{
                    icon: <ScrollText />,
                    title: hasFilters
                        ? 'No entries match your filters'
                        : 'No entries yet',
                }}
                pagination={{
                    meta: logs,
                    perPage: filters.per_page,
                    options: [10, 25, 50],
                    onPage: (page) => visit({ page }),
                    onPerPage: (per_page) => visit({ per_page }),
                }}
            />

            <Modal
                wide
                open={selected !== null}
                onOpenChange={(open) => !open && setSelected(null)}
                title={selected ? `Entry #${selected.id}` : 'Entry'}
                description={selected?.event}
            >
                {selected && (
                    <>
                        <div className="max-h-[70vh] overflow-y-auto p-4 text-sm">
                            <dl className="grid grid-cols-[7rem_1fr] gap-x-3 gap-y-1">
                                {(
                                    [
                                        ['Time', selected.at],
                                        [
                                            'User',
                                            [selected.user, selected.username]
                                                .filter(Boolean)
                                                .join(' · ') || 'System',
                                        ],
                                        [
                                            'Record',
                                            [
                                                selected.subject_type,
                                                selected.subject_id
                                                    ? `#${selected.subject_id}`
                                                    : null,
                                                selected.subject,
                                            ]
                                                .filter(Boolean)
                                                .join(' · ') || '–',
                                        ],
                                        ['Outcome', selected.outcome],
                                        ['IP address', selected.ip ?? '–'],
                                        [
                                            'Request',
                                            [selected.method, selected.url]
                                                .filter(Boolean)
                                                .join(' ') || '–',
                                        ],
                                        ['Browser', selected.user_agent ?? '–'],
                                        ['Request ID', selected.request_id],
                                        ['Hash', selected.hash],
                                    ] as [string, string][]
                                ).map(([label, value]) => (
                                    <div key={label} className="contents">
                                        <dt className="text-xs text-muted">
                                            {label}
                                        </dt>
                                        <dd className="break-all">{value}</dd>
                                    </div>
                                ))}
                            </dl>

                            {(selected.old || selected.new) && (
                                <div className="mt-3 rounded-md border border-line">
                                    <DataTable
                                        bare
                                        dense
                                        rows={changedFields(selected)}
                                        rowKey={(field) => field}
                                        columns={diffColumns(selected)}
                                        empty={{
                                            icon: <ScrollText />,
                                            title: 'No field changes',
                                        }}
                                    />
                                </div>
                            )}

                            {selected.context && (
                                <pre className="mt-3 rounded bg-canvas p-2 text-xs break-all whitespace-pre-wrap">
                                    {JSON.stringify(selected.context, null, 2)}
                                </pre>
                            )}
                        </div>
                        <DialogFooter>
                            <Button
                                variant="outline"
                                onClick={() => setSelected(null)}
                            >
                                Close
                            </Button>
                        </DialogFooter>
                    </>
                )}
            </Modal>
        </>
    );
}
