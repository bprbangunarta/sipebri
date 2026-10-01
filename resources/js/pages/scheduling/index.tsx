import { Head, useForm } from '@inertiajs/react';
import {
    AlertTriangle,
    CalendarDays,
    CalendarPlus,
    History,
    MoreHorizontal,
    Ban,
    XCircle,
    X,
} from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { FilterBar, SearchInput } from '@/components/ui/filter-bar';
import { ReasonDialog } from '@/components/reason-dialog';
import { Combobox } from '@/components/ui/combobox';
import { DatePicker } from '@/components/ui/date-picker';
import { DialogFooter, Modal } from '@/components/ui/dialog';
import {
    DropdownContent,
    DropdownItem,
    DropdownMenu,
    DropdownSeparator,
    DropdownTrigger,
} from '@/components/ui/dropdown';
import { Field } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { Badge, PageHeader } from '@/components/ui/misc';
import type { BadgeTone } from '@/components/ui/misc';
import type { PageMeta } from '@/components/ui/pagination';
import { nextSort } from '@/components/ui/sort-head';
import { Tip } from '@/components/ui/tooltip';
import { useListQuery } from '@/hooks/use-list-query';
import { formatDate, rupiah } from '@/lib/format';

type Option = { value: string | number; label: string };
type Row = {
    id: number;
    application_code: string;
    application_date: string;
    full_name: string;
    nik: string;
    status: string;
    status_label: string;
    status_tone: BadgeTone;
    product_label: string | null;
    office_label: string | null;
    supervisor_name: string | null;
    surveyor_name: string | null;
    survey_date: string | null;
    requested_amount: number;
    requested_tenor: number;
    schedule_count: number;
    over_limit: boolean;
    walk_in: boolean;
    resurvey: boolean;
    surveyor_role: string;
    surveyor_options: Option[];
    can_cancel: boolean;
    history: {
        id: number;
        action: string;
        survey_date: string | null;
        surveyor_name: string | null;
        note: string | null;
        reason: string | null;
        created_by: string;
        created_at: string;
    }[];
};
type Filters = {
    search: string;
    status: string | null;
    scope: 'mine' | 'all';
    sort: string;
    direction: 'asc' | 'desc';
    per_page: number;
};

type Props = {
    loans: { data: Row[] } & PageMeta;
    filters: Filters;
    perPageOptions: number[];
    statuses: Option[];
    maxSchedules: number;
    canManage: boolean;
    canCancel: boolean;
};

const DEFAULTS = {
    sort: 'application_date',
    direction: 'desc',
    per_page: 10,
    scope: 'mine',
};

function ScheduleDialog({
    row,
    onClose,
}: {
    row: Row | null;
    onClose: () => void;
}) {
    const form = useForm({ survey_date: '', surveyor_id: '', note: '' });

    return (
        <Modal
            open={row !== null}
            onOpenChange={(open) => !open && onClose()}
            title={
                row?.resurvey
                    ? 'Schedule a re-survey'
                    : row?.schedule_count
                      ? 'Reschedule survey'
                      : 'Schedule survey'
            }
            description={
                row ? `${row.application_code} · ${row.full_name}` : undefined
            }
        >
            {row && (
                <form
                    onSubmit={(e) => {
                        e.preventDefault();
                        form.post(`/scheduling/${row.id}`, {
                            preserveScroll: true,
                            onSuccess: () => {
                                form.reset();
                                onClose();
                            },
                        });
                    }}
                    noValidate
                >
                    <div className="flex flex-col gap-3 p-4">
                        {row.over_limit && (
                            <p className="flex items-start gap-1.5 rounded-md bg-amber-50 p-2 text-xs text-amber-700">
                                <AlertTriangle className="mt-0.5 size-3.5 shrink-0" />{' '}
                                This file was already scheduled{' '}
                                {row.schedule_count} times. Please make sure it
                                is still worth pursuing.
                            </p>
                        )}
                        {row.walk_in && (
                            <p className="rounded-md bg-primary-soft p-2 text-xs text-primary">
                                This product has no field survey; the file goes
                                straight to analysis.
                            </p>
                        )}
                        <Field
                            label="Survey date"
                            required
                            error={form.errors.survey_date}
                        >
                            <DatePicker
                                id="survey_date"
                                min={new Date()}
                                max={
                                    new Date(
                                        new Date().getFullYear() + 1,
                                        11,
                                        31,
                                    )
                                }
                                value={form.data.survey_date}
                                onChange={(v) => form.setData('survey_date', v)}
                                invalid={!!form.errors.survey_date}
                            />
                        </Field>
                        <Field
                            label="Surveyor"
                            required
                            error={form.errors.surveyor_id}
                            hint={`Role: ${row.surveyor_role}`}
                        >
                            <Combobox
                                options={row.surveyor_options}
                                placeholder={
                                    row.surveyor_options.length
                                        ? 'Select…'
                                        : 'No user holds this role'
                                }
                                value={form.data.surveyor_id}
                                onChange={(v) =>
                                    form.setData('surveyor_id', v ?? '')
                                }
                                invalid={!!form.errors.surveyor_id}
                            />
                        </Field>
                        <Field label="Note" error={form.errors.note}>
                            <Input
                                value={form.data.note}
                                maxLength={255}
                                onChange={(e) =>
                                    form.setData('note', e.target.value)
                                }
                            />
                        </Field>
                    </div>
                    <DialogFooter>
                        <Button variant="outline" onClick={onClose}>
                            Cancel
                        </Button>
                        <Button type="submit" loading={form.processing}>
                            Save schedule
                        </Button>
                    </DialogFooter>
                </form>
            )}
        </Modal>
    );
}

export default function SchedulingIndex({
    loans,
    filters,
    perPageOptions,
    statuses,
    canManage,
}: Props) {
    const [scheduling, setScheduling] = useState<Row | null>(null);
    const [history, setHistory] = useState<Row | null>(null);
    const [cancelUrl, setCancelUrl] = useState<string | null>(null);
    const [voidUrl, setVoidUrl] = useState<string | null>(null);
    const { visit, search, onSearch, clear, loading, error } =
        useListQuery<Filters>({
            url: '/scheduling',
            filters,
            defaults: DEFAULTS,
            only: ['loans', 'filters'],
        });
    const hasFilters = Boolean(filters.search || filters.status);
    const sortBy = (column: string) => visit(nextSort(filters, column));

    const columns: Column<Row>[] = [
        {
            key: 'file',
            header: 'File',
            sort: 'application_code',
            cell: (l) => (
                <>
                    <p className="font-medium">{l.application_code}</p>
                    <p className="text-xs text-muted">{l.product_label}</p>
                </>
            ),
        },
        {
            key: 'applicant',
            header: 'Applicant',
            sort: 'full_name',
            cell: (l) => (
                <>
                    {l.full_name}
                    <p className="text-xs text-muted">
                        {l.office_label ?? '–'} · {l.supervisor_name ?? '–'}
                    </p>
                </>
            ),
        },
        {
            key: 'amount',
            header: 'Amount',
            align: 'right',
            hideBelow: 'md',
            className: 'whitespace-nowrap tabular-nums',
            cell: (l) => (
                <>
                    {rupiah(l.requested_amount)}
                    <p className="text-xs text-muted">
                        {l.requested_tenor} months
                    </p>
                </>
            ),
        },
        {
            key: 'survey',
            header: 'Survey',
            sort: 'survey_date',
            cell: (l) => (
                <>
                    {l.survey_date ? (
                        formatDate(l.survey_date)
                    ) : (
                        <span className="text-muted">Not scheduled</span>
                    )}
                    <p className="text-xs text-muted">
                        {l.surveyor_name ?? '–'}
                        {l.schedule_count > 0 && ` · ${l.schedule_count}×`}
                    </p>
                </>
            ),
        },
        {
            key: 'status',
            header: 'Status',
            sort: 'status',
            cell: (l) => (
                <span className="flex flex-wrap items-center gap-1">
                    <Badge tone={l.status_tone}>
                        {l.resurvey ? 'Surveyed' : l.status_label}
                    </Badge>
                    {l.over_limit && (
                        <Tip label={`Scheduled ${l.schedule_count} times`}>
                            <span>
                                <AlertTriangle className="size-3.5 text-amber-600" />
                            </span>
                        </Tip>
                    )}
                </span>
            ),
        },
        {
            key: 'actions',
            header: 'Actions',
            srOnly: true,
            narrow: true,
            align: 'right',
            cell: (l) => (
                <DropdownMenu>
                    <Tip label="Actions">
                        <DropdownTrigger asChild>
                            <Button
                                variant="ghost"
                                size="icon"
                                aria-label={`Actions for ${l.application_code}`}
                            >
                                <MoreHorizontal />
                            </Button>
                        </DropdownTrigger>
                    </Tip>
                    <DropdownContent>
                        {canManage && l.status !== 'scheduling' && (
                            <DropdownItem
                                icon={<CalendarPlus />}
                                onSelect={() => setScheduling(l)}
                            >
                                {l.resurvey
                                    ? 'Schedule re-survey'
                                    : l.schedule_count
                                      ? 'Reschedule'
                                      : 'Schedule survey'}
                            </DropdownItem>
                        )}
                        {canManage && l.status === 'scheduling' && (
                            <DropdownItem
                                icon={<CalendarPlus />}
                                onSelect={() => setScheduling(l)}
                            >
                                Reschedule
                            </DropdownItem>
                        )}
                        {l.can_cancel && (
                            <DropdownItem
                                icon={<XCircle />}
                                onSelect={() =>
                                    setCancelUrl(`/scheduling/${l.id}/cancel`)
                                }
                            >
                                Cancel schedule
                            </DropdownItem>
                        )}
                        <DropdownItem
                            icon={<History />}
                            onSelect={() => setHistory(l)}
                        >
                            History
                        </DropdownItem>
                        {canManage && (
                            <>
                                <DropdownSeparator />
                                <DropdownItem
                                    danger
                                    icon={<Ban />}
                                    onSelect={() =>
                                        setVoidUrl(`/scheduling/${l.id}/void`)
                                    }
                                >
                                    Void application
                                </DropdownItem>
                            </>
                        )}
                    </DropdownContent>
                </DropdownMenu>
            ),
        },
    ];

    return (
        <>
            <Head title="Survey scheduling" />
            <PageHeader
                title="Survey scheduling"
                description="Set the survey date and surveyor for submitted files"
            />

            <DataTable
                rows={loans.data}
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
                                placeholder="Search code, name, NIK…"
                                label="Search files"
                            />
                        }
                    >
                        <Combobox
                            className="w-full sm:w-40"
                            searchable={false}
                            options={[
                                { value: 'mine', label: 'My files' },
                                { value: 'all', label: 'All section heads' },
                            ]}
                            value={filters.scope}
                            onChange={(v) =>
                                visit({
                                    scope: (v ?? 'mine') as 'mine' | 'all',
                                })
                            }
                        />
                        <Combobox
                            className="w-full sm:w-40"
                            clearable
                            searchable={false}
                            placeholder="Status"
                            options={statuses}
                            value={filters.status}
                            onChange={(v) => visit({ status: v })}
                        />
                        {hasFilters && (
                            <Button
                                variant="ghost"
                                size="sm"
                                onClick={() => clear({ status: null })}
                            >
                                <X /> Reset
                            </Button>
                        )}
                    </FilterBar>
                }
                columns={columns}
                sort={{
                    sort: filters.sort,
                    direction: filters.direction,
                    onSort: sortBy,
                }}
                empty={{
                    icon: <CalendarDays />,
                    title: hasFilters
                        ? 'No files match your filters'
                        : filters.scope === 'mine'
                          ? 'No files waiting for you'
                          : 'No files to schedule',
                    description: hasFilters
                        ? 'Try a different search or clear the filters.'
                        : 'Submitted files assigned to you appear here.',
                }}
                pagination={{
                    meta: loans,
                    perPage: filters.per_page,
                    options: perPageOptions,
                    onPage: (page) => visit({ page }),
                    onPerPage: (per_page) => visit({ per_page }),
                }}
            />

            <ScheduleDialog
                key={scheduling?.id ?? 'none'}
                row={scheduling}
                onClose={() => setScheduling(null)}
            />
            <ReasonDialog
                title="Cancel survey schedule"
                description="The file returns to the section head for rescheduling."
                action={cancelUrl}
                confirmLabel="Cancel schedule"
                onClose={() => setCancelUrl(null)}
            />
            <ReasonDialog
                title="Void application"
                description="The application is closed and cannot be reopened."
                action={voidUrl}
                confirmLabel="Void application"
                onClose={() => setVoidUrl(null)}
            />

            <Modal
                open={history !== null}
                onOpenChange={(open) => !open && setHistory(null)}
                title="Scheduling history"
                description={
                    history
                        ? `${history.application_code} · ${history.full_name}`
                        : undefined
                }
            >
                <ul className="max-h-[60vh] divide-y divide-line overflow-auto">
                    {history?.history.length === 0 && (
                        <li className="p-4 text-center text-xs text-muted">
                            Nothing recorded yet.
                        </li>
                    )}
                    {history?.history.map((h) => (
                        <li key={h.id} className="px-4 py-2 text-sm">
                            <p className="font-medium">
                                {h.action}
                                {h.survey_date &&
                                    ` · ${formatDate(h.survey_date)}`}
                            </p>
                            <p className="text-xs text-muted">
                                {[h.surveyor_name, h.note ?? h.reason]
                                    .filter(Boolean)
                                    .join(' · ') || '–'}
                            </p>
                            <p className="text-[11px] text-muted">
                                {h.created_by} · {h.created_at}
                            </p>
                        </li>
                    ))}
                </ul>
            </Modal>
        </>
    );
}
