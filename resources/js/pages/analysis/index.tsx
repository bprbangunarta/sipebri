import { Head } from '@inertiajs/react';
import { ClipboardCheck } from 'lucide-react';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { Badge, PageHeader } from '@/components/ui/misc';
import { FilterBar, SearchInput } from '@/components/ui/filter-bar';
import type { PageMeta } from '@/components/ui/pagination';
import { nextSort } from '@/components/ui/sort-head';
import { useListQuery } from '@/hooks/use-list-query';
import { formatDate, rupiah } from '@/lib/format';

type Row = {
    id: number;
    application_code: string;
    application_date: string;
    survey_date: string | null;
    full_name: string;
    nik: string;
    product_label: string | null;
    office_label: string | null;
    supervisor_name: string | null;
    requested_amount: number;
    requested_tenor: number;
    surveyed: boolean;
    collaterals_count: number;
};
type Filters = {
    search: string;
    sort: string;
    direction: 'asc' | 'desc';
    per_page: number;
};

const DEFAULTS = { sort: 'application_code', direction: 'desc', per_page: 10 };

export default function AnalysisIndex({
    loans,
    filters,
    perPageOptions,
}: {
    loans: { data: Row[] } & PageMeta;
    filters: Filters;
    perPageOptions: number[];
}) {
    const { visit, search, onSearch, clear, loading, error } =
        useListQuery<Filters>({
            url: '/analysis',
            filters,
            defaults: DEFAULTS,
            only: ['loans', 'filters'],
        });
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
                    <p className="font-mono text-xs text-muted">{l.nik}</p>
                </>
            ),
        },
        {
            key: 'amount',
            header: 'Amount',
            sort: 'requested_amount',
            align: 'right',
            hideBelow: 'sm',
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
            hideBelow: 'md',
            cell: (l) => (
                <>
                    {formatDate(l.survey_date)}
                    <p>
                        <Badge tone={l.surveyed ? 'success' : 'info'}>
                            {l.surveyed ? 'Surveyed' : 'No visit needed'}
                        </Badge>
                    </p>
                </>
            ),
        },
        {
            key: 'collateral',
            header: 'Collateral',
            align: 'right',
            hideBelow: 'lg',
            className: 'tabular-nums',
            cell: (l) => l.collaterals_count,
        },
    ];

    return (
        <>
            <Head title="Analysis" />
            <PageHeader
                title="Analysis"
                description="Surveyed files assigned to you that are ready to be analysed"
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
                    />
                }
                columns={columns}
                sort={{
                    sort: filters.sort,
                    direction: filters.direction,
                    onSort: sortBy,
                }}
                empty={{
                    icon: <ClipboardCheck />,
                    title: filters.search
                        ? 'No files match your search'
                        : 'Nothing to analyse yet',
                    description: filters.search
                        ? 'Try a different search term.'
                        : 'Files appear here once their survey is saved (or, for walk-in products, once they are scheduled).',
                    action: filters.search ? (
                        <button
                            type="button"
                            className="cursor-pointer text-xs text-primary hover:underline"
                            onClick={() => clear()}
                        >
                            Clear search
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
