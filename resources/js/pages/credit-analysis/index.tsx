import { Head, router } from '@inertiajs/react';
import { ClipboardCheck, X } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { Badge, PageHeader } from '@/components/ui/misc';
import { FilterBar, SearchInput } from '@/components/ui/filter-bar';
import type { PageMeta } from '@/components/ui/pagination';
import { nextSort } from '@/components/ui/sort-head';
import { useListQuery } from '@/hooks/use-list-query';
import { formatDate, formatNumber, rupiah } from '@/lib/format';

type Row = {
    id: number;
    application_code: string;
    survey_date: string | null;
    full_name: string;
    nik: string;
    product_label: string | null;
    requested_amount: number;
    requested_tenor: number;
    surveyed: boolean;
    collaterals_count: number;
    in_analysis: boolean;
};
type Filters = {
    search: string;
    sort: string;
    direction: 'asc' | 'desc';
    per_page: number;
};

const DEFAULTS = { sort: 'application_code', direction: 'desc', per_page: 10 };

export default function AnalysisIndex({ loans, filters, perPageOptions }: { loans: { data: Row[] } & PageMeta; filters: Filters; perPageOptions: number[] }) {
    const { visit, search, onSearch, clear, sorted, loading, error } = useListQuery<Filters>({
        url: '/credit-analysis',
        filters,
        defaults: DEFAULTS,
        only: ['loans', 'filters'],
    });
    const sortBy = (column: string) => visit(nextSort(filters, column));

    const columns: Column<Row>[] = [
        { key: 'code', header: 'Kode', sort: 'application_code', className: 'font-medium', cell: (l) => l.application_code },
        { key: 'product', header: 'Produk', sort: 'product', cell: (l) => l.product_label ?? '–' },
        { key: 'applicant', header: 'Pemohon', sort: 'full_name', cell: (l) => l.full_name },
        { key: 'nik', header: 'NIK', sort: 'nik', className: 'font-mono', cell: (l) => l.nik },
        {
            key: 'amount',
            header: 'Plafon',
            sort: 'requested_amount',
            align: 'right',
            className: 'tabular-nums',
            cell: (l) => rupiah(l.requested_amount),
        },
        {
            key: 'tenor',
            header: 'Tenor',
            sort: 'requested_tenor',
            align: 'right',
            className: 'tabular-nums',
            cell: (l) => `${formatNumber(l.requested_tenor)} bulan`,
        },
        { key: 'survey_date', header: 'Tanggal survei', sort: 'survey_date', cell: (l) => formatDate(l.survey_date) },
        {
            key: 'visit',
            header: 'Kunjungan',
            sort: 'surveys_count',
            cell: (l) => <Badge tone={l.surveyed ? 'success' : 'info'}>{l.surveyed ? 'Disurvei' : 'Tanpa kunjungan'}</Badge>,
        },
        {
            key: 'stage',
            header: 'Tahap',
            sort: 'status',
            cell: (l) => <Badge tone={l.in_analysis ? 'info' : 'neutral'}>{l.in_analysis ? 'Dalam analisa' : 'Siap dianalisa'}</Badge>,
        },
        {
            key: 'collateral',
            header: 'Jaminan',
            sort: 'collaterals_count',
            align: 'right',
            className: 'tabular-nums',
            cell: (l) => l.collaterals_count,
        },
    ];

    return (
        <>
            <Head title="Analisa Kredit" />
            <PageHeader
                title="Analisa Kredit"
                description="Berkas hasil survei yang ditugaskan kepada Anda. Buka satu berkas untuk mengisi lembar analisanya"
            />

            <DataTable
                rows={loans.data}
                rowKey={(r) => r.id}
                loading={loading}
                error={error}
                onRetry={() => visit({})}
                onRowClick={(l) => router.visit(`/credit-analysis/${l.id}`)}
                toolbar={
                    <FilterBar search={<SearchInput value={search} onChange={onSearch} placeholder="Cari kode, nama, NIK…" label="Cari berkas" />}>
                        {(filters.search !== '' || sorted) && (
                            <Button variant="ghost" size="sm" onClick={() => clear()}>
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
                    icon: <ClipboardCheck />,
                    title: filters.search ? 'Tidak ada berkas yang cocok dengan pencarian' : 'Belum ada yang perlu dianalisa',
                    description: filters.search
                        ? 'Coba kata kunci lain.'
                        : 'Berkas tampil di sini setelah surveinya disimpan (atau, untuk produk tanpa survei lapangan, setelah dijadwalkan).',
                    action: filters.search ? (
                        <button type="button" className="cursor-pointer text-xs text-primary hover:underline" onClick={() => clear()}>
                            Hapus pencarian
                        </button>
                    ) : undefined,
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
