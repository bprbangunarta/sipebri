import { Head, router } from '@inertiajs/react';
import { format } from 'date-fns';
import { Download, Eye, ScrollText, ShieldCheck, X } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { DatePicker } from '@/components/ui/date-picker';
import { DialogFooter, Modal } from '@/components/ui/dialog';
import { FilterBar, SearchInput } from '@/components/ui/filter-bar';
import {
    Badge,
    Card,
    EmptyState,
    ErrorState,
    PageHeader,
    Skeleton,
} from '@/components/ui/misc';
import type { BadgeTone } from '@/components/ui/misc';
import { Pagination } from '@/components/ui/pagination';
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

            <Card>
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
                        options={modules.map((m) => ({ value: m, label: m }))}
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

                {error ? (
                    <ErrorState message={error} onRetry={() => visit({})} />
                ) : (
                    <div className="relative overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead className="border-b border-line bg-canvas text-xs text-muted">
                                <tr>
                                    <th
                                        scope="col"
                                        className="px-3 py-2 text-left font-medium"
                                    >
                                        Time
                                    </th>
                                    <th
                                        scope="col"
                                        className="px-3 py-2 text-left font-medium"
                                    >
                                        User
                                    </th>
                                    <th
                                        scope="col"
                                        className="px-3 py-2 text-left font-medium"
                                    >
                                        Event
                                    </th>
                                    <th
                                        scope="col"
                                        className="hidden px-3 py-2 text-left font-medium md:table-cell"
                                    >
                                        Record
                                    </th>
                                    <th
                                        scope="col"
                                        className="px-3 py-2 text-left font-medium"
                                    >
                                        Outcome
                                    </th>
                                    <th scope="col" className="w-10 px-3 py-2">
                                        <span className="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                className={
                                    loading && logs.data.length > 0
                                        ? 'divide-y divide-line opacity-50'
                                        : 'divide-y divide-line'
                                }
                            >
                                {loading && logs.data.length === 0
                                    ? Array.from({ length: 5 }).map((_, i) => (
                                          <tr key={i}>
                                              <td
                                                  colSpan={6}
                                                  className="px-3 py-2.5"
                                              >
                                                  <Skeleton className="h-4 w-full" />
                                              </td>
                                          </tr>
                                      ))
                                    : logs.data.map((log) => (
                                          <tr
                                              key={log.id}
                                              className="hover:bg-canvas/60"
                                          >
                                              <td className="px-3 py-1.5 whitespace-nowrap">
                                                  {format(
                                                      new Date(
                                                          log.at.replace(
                                                              ' ',
                                                              'T',
                                                          ),
                                                      ),
                                                      'dd MMM yyyy HH:mm:ss',
                                                  )}
                                              </td>
                                              <td className="px-3 py-1.5">
                                                  <p className="font-medium">
                                                      {log.user ?? 'System'}
                                                  </p>
                                                  <p className="text-xs text-muted">
                                                      {log.ip ?? ''}
                                                  </p>
                                              </td>
                                              <td className="px-3 py-1.5">
                                                  <p>{log.event}</p>
                                                  <p className="text-xs text-muted">
                                                      {log.module}
                                                  </p>
                                              </td>
                                              <td className="hidden px-3 py-1.5 md:table-cell">
                                                  {log.subject ?? '–'}
                                              </td>
                                              <td className="px-3 py-1.5">
                                                  <Badge
                                                      tone={TONES[log.outcome]}
                                                  >
                                                      {log.outcome}
                                                  </Badge>
                                              </td>
                                              <td className="px-3 py-1.5 text-right">
                                                  <Tip label="Details">
                                                      <Button
                                                          variant="ghost"
                                                          size="icon"
                                                          aria-label={`Details of entry ${log.id}`}
                                                          onClick={() =>
                                                              setSelected(log)
                                                          }
                                                      >
                                                          <Eye />
                                                      </Button>
                                                  </Tip>
                                              </td>
                                          </tr>
                                      ))}
                            </tbody>
                        </table>
                        {!loading && logs.data.length === 0 && (
                            <EmptyState
                                icon={<ScrollText />}
                                title={
                                    hasFilters
                                        ? 'No entries match your filters'
                                        : 'No entries yet'
                                }
                            />
                        )}
                    </div>
                )}

                <Pagination
                    meta={logs}
                    perPage={filters.per_page}
                    perPageOptions={[10, 25, 50]}
                    onPage={(page) => visit({ page })}
                    onPerPage={(per_page) => visit({ per_page })}
                />
            </Card>

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
                                <table className="mt-3 w-full text-xs">
                                    <thead className="border-b border-line text-muted">
                                        <tr>
                                            <th className="py-1 text-left font-medium">
                                                Field
                                            </th>
                                            <th className="py-1 text-left font-medium">
                                                Before
                                            </th>
                                            <th className="py-1 text-left font-medium">
                                                After
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-line">
                                        {Array.from(
                                            new Set([
                                                ...Object.keys(
                                                    selected.old ?? {},
                                                ),
                                                ...Object.keys(
                                                    selected.new ?? {},
                                                ),
                                            ]),
                                        ).map((field) => (
                                            <tr key={field}>
                                                <td className="py-1 pr-2 font-medium">
                                                    {field}
                                                </td>
                                                <td className="py-1 pr-2 break-all text-muted">
                                                    {show(
                                                        selected.old?.[field],
                                                    )}
                                                </td>
                                                <td className="py-1 break-all">
                                                    {show(
                                                        selected.new?.[field],
                                                    )}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
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
