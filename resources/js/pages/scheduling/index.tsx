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
    needs_reschedule: boolean;
    sent_back_reason: string | null;
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
    status: 'submitted',
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
                    ? 'Jadwalkan survei ulang'
                    : row?.schedule_count
                      ? 'Jadwalkan ulang survei'
                      : 'Jadwalkan survei'
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
                                Berkas ini sudah dijadwalkan{' '}
                                {row.schedule_count} kali. Pastikan masih layak
                                dilanjutkan.
                            </p>
                        )}
                        {row.walk_in && (
                            <p className="rounded-md bg-primary-soft p-2 text-xs text-primary">
                                Produk ini tanpa survei lapangan; berkas
                                langsung ke analisa.
                            </p>
                        )}
                        <Field
                            label="Tanggal survei"
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
                            hint={`Peran: ${row.surveyor_role}`}
                        >
                            <Combobox
                                options={row.surveyor_options}
                                placeholder={
                                    row.surveyor_options.length
                                        ? 'Pilih…'
                                        : 'Tidak ada pengguna dengan peran ini'
                                }
                                value={form.data.surveyor_id}
                                onChange={(v) =>
                                    form.setData('surveyor_id', v ?? '')
                                }
                                invalid={!!form.errors.surveyor_id}
                            />
                        </Field>
                        <Field label="Catatan" error={form.errors.note}>
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
                            Batal
                        </Button>
                        <Button type="submit" loading={form.processing}>
                            Simpan jadwal
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
    const hasFilters = Boolean(
        filters.search || filters.status !== DEFAULTS.status,
    );
    const sortBy = (column: string) => visit(nextSort(filters, column));

    const columns: Column<Row>[] = [
        {
            key: 'file',
            header: 'Berkas',
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
            header: 'Pemohon',
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
            header: 'Plafon',
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
            header: 'Survei',
            sort: 'survey_date',
            cell: (l) => (
                <>
                    {l.survey_date ? (
                        formatDate(l.survey_date)
                    ) : (
                        <span className="text-muted">Belum dijadwalkan</span>
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
                    {l.needs_reschedule ? (
                        <Tip
                            label={`Dikembalikan surveyor: ${l.sent_back_reason ?? ''}`}
                        >
                            <span>
                                <Badge tone="warning">
                                    Perlu dijadwalkan ulang
                                </Badge>
                            </span>
                        </Tip>
                    ) : (
                        <Badge tone={l.status_tone}>
                            {l.resurvey ? 'Disurvei' : l.status_label}
                        </Badge>
                    )}
                    {l.over_limit && (
                        <Tip label={`Dijadwalkan ${l.schedule_count} kali`}>
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
            header: 'Aksi',
            srOnly: true,
            narrow: true,
            align: 'right',
            cell: (l) => (
                <DropdownMenu>
                    <Tip label="Aksi">
                        <DropdownTrigger asChild>
                            <Button
                                variant="ghost"
                                size="icon"
                                aria-label={`Aksi untuk ${l.application_code}`}
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
                                    ? 'Jadwalkan survei ulang'
                                    : l.schedule_count
                                      ? 'Jadwalkan ulang'
                                      : 'Jadwalkan survei'}
                            </DropdownItem>
                        )}
                        {canManage && l.status === 'scheduling' && (
                            <DropdownItem
                                icon={<CalendarPlus />}
                                onSelect={() => setScheduling(l)}
                            >
                                Jadwalkan ulang
                            </DropdownItem>
                        )}
                        {l.can_cancel && (
                            <DropdownItem
                                icon={<XCircle />}
                                onSelect={() =>
                                    setCancelUrl(`/scheduling/${l.id}/cancel`)
                                }
                            >
                                Batalkan jadwal
                            </DropdownItem>
                        )}
                        <DropdownItem
                            icon={<History />}
                            onSelect={() => setHistory(l)}
                        >
                            Riwayat
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
                                    Batalkan pengajuan
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
            <Head title="Penjadwalan" />
            <PageHeader
                title="Penjadwalan"
                description="Tentukan tanggal survei dan surveyor untuk berkas yang diajukan"
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
                                placeholder="Cari kode, nama, NIK…"
                                label="Cari berkas"
                            />
                        }
                    >
                        <Combobox
                            className="w-full sm:w-40"
                            searchable={false}
                            options={[
                                { value: 'mine', label: 'Berkas saya' },
                                { value: 'all', label: 'Semua Kasi Analis' },
                            ]}
                            value={filters.scope}
                            onChange={(v) =>
                                visit({
                                    scope: (v ?? 'mine') as 'mine' | 'all',
                                })
                            }
                        />
                        <Combobox
                            className="w-full sm:w-44"
                            searchable={false}
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
                                <X /> Atur ulang
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
                        ? 'Tidak ada berkas yang cocok dengan filter'
                        : filters.scope === 'mine'
                          ? 'Tidak ada berkas yang menunggu jadwal dari Anda'
                          : 'Tidak ada berkas yang menunggu jadwal',
                    description: hasFilters
                        ? 'Coba pencarian lain atau atur ulang filter.'
                        : 'Berkas yang diajukan, dan berkas yang dikembalikan surveyor, tampil di sini. Pilih "Semua status" untuk melihat berkas terjadwal juga.',
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
                title="Batalkan jadwal survei"
                description="Berkas kembali ke Kasi Analis untuk dijadwalkan ulang."
                action={cancelUrl}
                confirmLabel="Batalkan jadwal"
                onClose={() => setCancelUrl(null)}
            />
            <ReasonDialog
                title="Batalkan pengajuan"
                description="Pengajuan ditutup dan tidak bisa dibuka kembali."
                action={voidUrl}
                confirmLabel="Batalkan pengajuan"
                onClose={() => setVoidUrl(null)}
            />

            <Modal
                open={history !== null}
                onOpenChange={(open) => !open && setHistory(null)}
                title="Riwayat penjadwalan"
                description={
                    history
                        ? `${history.application_code} · ${history.full_name}`
                        : undefined
                }
            >
                <ul className="max-h-[60vh] divide-y divide-line overflow-auto">
                    {history?.history.length === 0 && (
                        <li className="p-4 text-center text-xs text-muted">
                            Belum ada catatan.
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
