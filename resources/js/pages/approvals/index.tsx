import { Head, router } from '@inertiajs/react';
import { BadgeCheck, X } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { FilterBar, SearchInput } from '@/components/ui/filter-bar';
import { Badge, PageHeader } from '@/components/ui/misc';
import type { BadgeTone } from '@/components/ui/misc';
import type { PageMeta } from '@/components/ui/pagination';
import { nextSort } from '@/components/ui/sort-head';
import { useListQuery } from '@/hooks/use-list-query';
import { formatDate, formatNumber, rupiah } from '@/lib/format';

type Row = {
    id: number;
    application_code: string;
    full_name: string;
    nik: string;
    product_label: string | null;
    proposed_amount: number;
    proposed_tenor: number;
    status: string;
    status_label: string;
    pending_position: string | null;
    my_turn: boolean;
    submitted_at: string | null;
};

type Filters = {
    search: string;
    status: string;
    scope: 'mine' | 'all';
    sort: string;
    direction: 'asc' | 'desc';
    per_page: number;
};

type Props = {
    loans: { data: Row[] } & PageMeta;
    filters: Filters;
    perPageOptions: number[];
    statuses: { value: string; label: string }[];
};

const DEFAULTS = { status: 'committee', scope: 'mine', sort: 'application_code', direction: 'desc', per_page: 10 };
const TONES: Record<string, BadgeTone> = {
    committee: 'info',
    approved: 'success',
    rejected: 'danger',
    cancelled: 'neutral',
};

export default function ApprovalsIndex({ loans, filters, perPageOptions, statuses }: Props) {
    const { visit, search, onSearch, clear, sorted, loading, error } = useListQuery<Filters>({
        filters,
        url: '/approvals',
        defaults: DEFAULTS,
        only: ['loans', 'filters'],
    });
    const hasFilters = filters.search !== '' || filters.status !== DEFAULTS.status || filters.scope !== DEFAULTS.scope;
    const sortBy = (column: string) => visit(nextSort(filters, column));

    const columns: Column<Row>[] = [
        { key: 'code', header: 'Kode', sort: 'application_code', className: 'font-medium', cell: (l) => l.application_code },
        { key: 'applicant', header: 'Pemohon', sort: 'full_name', cell: (l) => l.full_name },
        { key: 'nik', header: 'NIK', sort: 'nik', className: 'font-mono', cell: (l) => l.nik },
        { key: 'product', header: 'Produk', sort: 'product', cell: (l) => l.product_label ?? '–' },
        {
            key: 'amount',
            header: 'Plafon usulan',
            sort: 'proposed_amount',
            align: 'right',
            className: 'tabular-nums',
            cell: (l) => rupiah(l.proposed_amount),
        },
        {
            key: 'tenor',
            header: 'Tenor usulan',
            sort: 'proposed_tenor',
            align: 'right',
            className: 'tabular-nums',
            cell: (l) => `${formatNumber(l.proposed_tenor)} bulan`,
        },
        { key: 'submitted', header: 'Diajukan', sort: 'submitted_at', cell: (l) => formatDate(l.submitted_at) },
        { key: 'status', header: 'Status', sort: 'status', cell: (l) => <Badge tone={TONES[l.status] ?? 'neutral'}>{l.status_label}</Badge> },
        { key: 'position', header: 'Posisi', sort: 'pending_position', cell: (l) => l.pending_position ?? '–' },
        { key: 'turn', header: 'Giliran', sort: 'my_turn', cell: (l) => (l.my_turn ? <Badge tone="success">Anda</Badge> : '–') },
    ];

    return (
        <>
            <Head title="Persetujuan" />
            <PageHeader title="Persetujuan" description="Keputusan komite atas berkas yang sudah dianalisa" />
            <DataTable
                rows={loans.data}
                rowKey={(l) => l.id}
                loading={loading}
                error={error}
                onRetry={() => visit({})}
                onRowClick={(l) => router.visit(`/approvals/${l.id}`)}
                toolbar={
                    <FilterBar search={<SearchInput value={search} onChange={onSearch} placeholder="Cari kode, nama, NIK…" label="Cari berkas" />}>
                        <Combobox
                            className="w-full sm:w-44"
                            searchable={false}
                            placeholder="Status"
                            options={statuses}
                            value={filters.status}
                            onChange={(v) => visit({ status: v ?? DEFAULTS.status })}
                        />
                        <Combobox
                            className="w-full sm:w-44"
                            searchable={false}
                            placeholder="Tampilan"
                            options={[
                                { value: 'mine', label: 'Menunggu saya' },
                                { value: 'all', label: 'Semua berkas saya' },
                            ]}
                            value={filters.scope}
                            onChange={(v) =>
                                visit({
                                    scope: (v ?? 'mine') as 'mine' | 'all',
                                })
                            }
                        />
                        {(hasFilters || sorted) && (
                            <Button
                                variant="ghost"
                                size="sm"
                                onClick={() =>
                                    clear({
                                        status: 'committee',
                                        scope: 'mine',
                                    })
                                }
                            >
                                <X /> Atur ulang
                            </Button>
                        )}
                    </FilterBar>
                }
                columns={columns}
                sort={{ sort: filters.sort, direction: filters.direction, onSort: sortBy }}
                empty={{
                    icon: <BadgeCheck />,
                    title: filters.scope === 'mine' && filters.status === 'committee' ? 'Tidak ada berkas yang menunggu keputusan Anda' : 'Tidak ada berkas',
                    description:
                        filters.scope === 'mine'
                            ? 'Pilih "Semua berkas saya" untuk melihat berkas yang Anda ikuti tetapi belum giliran Anda.'
                            : 'Berkas muncul di sini setelah analisanya diajukan ke komite.',
                }}
                pagination={{
                    meta: loans,
                    perPage: filters.per_page,
                    options: perPageOptions,
                    onPage: (page) => visit({ page }),
                    onPerPage: (per_page) => visit({ per_page }),
                }}
            />
        </>
    );
}
