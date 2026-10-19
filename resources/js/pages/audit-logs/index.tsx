import { Head, router } from '@inertiajs/react';
import { Download, Eye, ScrollText, ShieldCheck, X } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { DatePicker } from '@/components/ui/date-picker';
import { DialogBody, DialogFooter, Modal } from '@/components/ui/dialog';
import { FilterBar, SearchInput } from '@/components/ui/filter-bar';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { Alert, Badge, PageHeader } from '@/components/ui/misc';
import type { BadgeTone } from '@/components/ui/misc';
import type { PageMeta } from '@/components/ui/pagination';
import { Tip } from '@/components/ui/tooltip';
import { useListQuery } from '@/hooks/use-list-query';
import { formatDateTime } from '@/lib/format';

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
    { value: 'success', label: 'Berhasil' },
    { value: 'failure', label: 'Gagal' },
    { value: 'denied', label: 'Ditolak' },
];
const OUTCOME_LABEL = Object.fromEntries(OUTCOMES.map((o) => [o.value, o.label])) as Record<LogRow['outcome'], string>;
const TONES: Record<LogRow['outcome'], BadgeTone> = {
    success: 'success',
    failure: 'danger',
    denied: 'warning',
};
const DEFAULTS = { per_page: 25 };
const MIN_DATE = new Date(2020, 0, 1);

const show = (value: unknown): string => (value === null || value === undefined ? '–' : typeof value === 'string' ? value : JSON.stringify(value));

const changedFields = (log: LogRow): string[] => Array.from(new Set([...Object.keys(log.old ?? {}), ...Object.keys(log.new ?? {})]));

const diffColumns = (log: LogRow): Column<string>[] => [
    { key: 'field', header: 'Kolom', className: 'font-medium', cell: (f) => f },
    {
        key: 'before',
        header: 'Sebelum',
        className: 'break-all text-muted',
        cell: (f) => show(log.old?.[f]),
    },
    {
        key: 'after',
        header: 'Sesudah',
        className: 'break-all',
        cell: (f) => show(log.new?.[f]),
    },
];

export default function AuditLogsIndex({ logs, filters, modules, verification }: Props) {
    const [selected, setSelected] = useState<LogRow | null>(null);
    const [verifying, setVerifying] = useState(false);
    const { visit, search, onSearch, clear, loading, error } = useListQuery<Filters>({
        url: '/audit-logs',
        filters,
        defaults: DEFAULTS,
        only: ['logs', 'filters'],
    });

    const hasFilters = Boolean(filters.search || filters.module || filters.outcome || filters.from || filters.to);
    const iso = (d: string) => (d ? d : null);

    const exportCsv = () => {
        const params = new URLSearchParams();
        (['search', 'module', 'outcome', 'from', 'to'] as const).forEach((key) => filters[key] && params.set(key, filters[key] as string));
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
            header: 'Waktu',
            cell: (log) => formatDateTime(log.at),
        },
        {
            key: 'user',
            header: 'Pengguna',
            className: 'font-medium',
            cell: (log) => log.user ?? 'Sistem',
        },
        {
            key: 'ip',
            header: 'Alamat IP',
            cell: (log) => log.ip ?? '–',
        },
        {
            key: 'event',
            header: 'Kejadian',
            cell: (log) => log.event,
        },
        {
            key: 'module',
            header: 'Modul',
            cell: (log) => log.module,
        },
        {
            key: 'record',
            header: 'Data',
            cell: (log) => log.subject ?? '–',
        },
        {
            key: 'outcome',
            header: 'Hasil',
            cell: (log) => <Badge tone={TONES[log.outcome]}>{OUTCOME_LABEL[log.outcome]}</Badge>,
        },
        {
            key: 'actions',
            header: 'Aksi',
            srOnly: true,
            narrow: true,
            align: 'right',
            cell: (log) => (
                <Tip label="Detail">
                    <Button variant="ghost" size="icon" aria-label={`Detail entri ${log.id}`} onClick={() => setSelected(log)}>
                        <Eye />
                    </Button>
                </Tip>
            ),
        },
    ];

    return (
        <>
            <Head title="Audit Logs" />
            <PageHeader
                title="Audit Logs"
                description="Catatan anti-rusak tentang siapa melakukan apa, kapan, dan dari mana"
                actions={
                    <>
                        <Button variant="outline" onClick={verify} disabled={verifying}>
                            <ShieldCheck /> {verifying ? 'Memeriksa…' : 'Periksa integritas'}
                        </Button>
                        <Button onClick={exportCsv}>
                            <Download /> Ekspor CSV
                        </Button>
                    </>
                }
            />

            {verification && (
                <Alert tone={verification.ok ? 'success' : 'danger'} className="mb-3">
                    {verification.ok
                        ? `Rantai utuh: ${verification.checked} entri terverifikasi.`
                        : `Rantai terputus di entri #${verification.broken_at}. ${verification.reason}`}
                </Alert>
            )}

            <DataTable
                rows={logs.data}
                rowKey={(r) => r.id}
                loading={loading}
                error={error}
                onRetry={() => visit({})}
                toolbar={
                    <FilterBar
                        search={<SearchInput value={search} onChange={onSearch} placeholder="Cari pengguna, kejadian, data, IP…" label="Cari audit log" />}
                    >
                        <div className="w-full sm:w-36">
                            <DatePicker value={filters.from ?? ''} min={MIN_DATE} placeholder="Dari" onChange={(v) => visit({ from: iso(v) })} />
                        </div>
                        <div className="w-full sm:w-36">
                            <DatePicker value={filters.to ?? ''} min={MIN_DATE} placeholder="Sampai" onChange={(v) => visit({ to: iso(v) })} />
                        </div>
                        <Combobox
                            className="w-full sm:w-40"
                            clearable
                            placeholder="Modul"
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
                            placeholder="Hasil"
                            options={OUTCOMES}
                            value={filters.outcome}
                            onChange={(v) => visit({ outcome: v as Filters['outcome'] })}
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
                                <X /> Atur ulang
                            </Button>
                        )}
                    </FilterBar>
                }
                columns={columns}
                empty={{
                    icon: <ScrollText />,
                    title: hasFilters ? 'Tidak ada entri yang cocok dengan filter' : 'Belum ada entri',
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
                title={selected ? `Entri #${selected.id}` : 'Entri'}
                description={selected?.event}
            >
                {selected && (
                    <>
                        <DialogBody className="text-sm">
                            <dl className="grid grid-cols-[7rem_1fr] gap-x-3 gap-y-1">
                                {(
                                    [
                                        ['Waktu', formatDateTime(selected.at)],
                                        ['Pengguna', [selected.user, selected.username].filter(Boolean).join(' · ') || 'Sistem'],
                                        [
                                            'Data',
                                            [selected.subject_type, selected.subject_id ? `#${selected.subject_id}` : null, selected.subject]
                                                .filter(Boolean)
                                                .join(' · ') || '–',
                                        ],
                                        ['Hasil', OUTCOME_LABEL[selected.outcome]],
                                        ['Alamat IP', selected.ip ?? '–'],
                                        ['Permintaan', [selected.method, selected.url].filter(Boolean).join(' ') || '–'],
                                        ['Peramban', selected.user_agent ?? '–'],
                                        ['ID permintaan', selected.request_id],
                                        ['Hash', selected.hash],
                                    ] as [string, string][]
                                ).map(([label, value]) => (
                                    <div key={label} className="contents">
                                        <dt className="text-xs text-muted">{label}</dt>
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
                                            title: 'Tidak ada perubahan kolom',
                                        }}
                                    />
                                </div>
                            )}

                            {selected.context && (
                                <pre className="mt-3 rounded bg-canvas p-2 text-xs break-all whitespace-pre-wrap">
                                    {JSON.stringify(selected.context, null, 2)}
                                </pre>
                            )}
                        </DialogBody>
                        <DialogFooter>
                            <Button variant="outline" onClick={() => setSelected(null)}>
                                Tutup
                            </Button>
                        </DialogFooter>
                    </>
                )}
            </Modal>
        </>
    );
}
