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
import { useListQuery } from '@/hooks/use-list-query';
import { formatDate, rupiah } from '@/lib/format';

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
    per_page: number;
};

type Props = {
    loans: { data: Row[] } & PageMeta;
    filters: Filters;
    perPageOptions: number[];
    statuses: { value: string; label: string }[];
};

const DEFAULTS = { status: 'committee', scope: 'mine', per_page: 10 };
const TONES: Record<string, BadgeTone> = {
    committee: 'info',
    approved: 'success',
    rejected: 'danger',
    cancelled: 'neutral',
};

export default function ApprovalsIndex({
    loans,
    filters,
    perPageOptions,
    statuses,
}: Props) {
    const { visit, search, onSearch, clear, loading, error } =
        useListQuery<Filters>({
            filters,
            url: '/approvals',
            defaults: DEFAULTS,
            only: ['loans', 'filters'],
        });
    const hasFilters =
        filters.search !== '' ||
        filters.status !== DEFAULTS.status ||
        filters.scope !== DEFAULTS.scope;

    const columns: Column<Row>[] = [
        {
            key: 'code',
            header: 'Berkas',
            className: 'font-mono text-xs',
            cell: (l) => l.application_code,
        },
        {
            key: 'applicant',
            header: 'Pemohon',
            cell: (l) => (
                <>
                    <p className="font-medium">{l.full_name}</p>
                    <p className="text-xs text-muted">{l.nik}</p>
                </>
            ),
        },
        {
            key: 'product',
            header: 'Produk',
            hideBelow: 'md',
            cell: (l) => l.product_label ?? '–',
        },
        {
            key: 'proposal',
            header: 'Usulan',
            align: 'right',
            className: 'whitespace-nowrap tabular-nums',
            cell: (l) =>
                `${rupiah(l.proposed_amount)} · ${l.proposed_tenor} bln`,
        },
        {
            key: 'submitted',
            header: 'Diajukan',
            hideBelow: 'lg',
            cell: (l) => formatDate(l.submitted_at),
        },
        {
            key: 'status',
            header: 'Status',
            cell: (l) => (
                <span className="flex flex-col items-start gap-0.5">
                    <Badge tone={TONES[l.status] ?? 'neutral'}>
                        {l.status_label}
                    </Badge>
                    {l.pending_position && (
                        <span className="text-xs text-muted">
                            {l.my_turn ? 'Giliran Anda · ' : ''}
                            {l.pending_position}
                        </span>
                    )}
                </span>
            ),
        },
    ];

    return (
        <>
            <Head title="Persetujuan" />
            <PageHeader
                title="Persetujuan"
                description="Keputusan komite atas berkas yang sudah dianalisa"
            />
            <DataTable
                rows={loans.data}
                rowKey={(l) => l.id}
                loading={loading}
                error={error}
                onRetry={() => visit({})}
                onRowClick={(l) => router.visit(`/approvals/${l.id}`)}
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
                            className="w-full sm:w-44"
                            searchable={false}
                            placeholder="Status"
                            options={statuses}
                            value={filters.status}
                            onChange={(v) =>
                                visit({ status: v ?? DEFAULTS.status })
                            }
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
                        {hasFilters && (
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
                empty={{
                    icon: <BadgeCheck />,
                    title:
                        filters.scope === 'mine' &&
                        filters.status === 'committee'
                            ? 'Tidak ada berkas yang menunggu keputusan Anda'
                            : 'Tidak ada berkas',
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
