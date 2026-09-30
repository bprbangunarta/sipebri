import { Head, router } from '@inertiajs/react';
import { MapPin } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { FilterBar, SearchInput } from '@/components/ui/filter-bar';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { PageHeader } from '@/components/ui/misc';
import { useListQuery } from '@/hooks/use-list-query';
import { formatDate, rupiah } from '@/lib/format';

type Row = {
    id: number;
    application_code: string;
    full_name: string;
    nik: string;
    requested_amount: number;
    requested_tenor: number;
    product_label: string | null;
    supervisor_name: string | null;
};
type Filters = { search: string };

const columns: Column<Row>[] = [
    {
        key: 'file',
        header: 'File',
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
        align: 'right',
        hideBelow: 'sm',
        className: 'whitespace-nowrap tabular-nums',
        cell: (l) => (
            <>
                {rupiah(l.requested_amount)}
                <p className="text-xs text-muted">{l.requested_tenor} months</p>
            </>
        ),
    },
    {
        key: 'head',
        header: 'Section head',
        hideBelow: 'md',
        cell: (l) => l.supervisor_name ?? '–',
    },
    {
        key: 'actions',
        header: 'Actions',
        srOnly: true,
        align: 'right',
        cell: (l) => (
            <Button size="sm" onClick={() => router.visit(`/surveys/${l.id}`)}>
                Open
            </Button>
        ),
    },
];

export default function SurveysIndex({
    loans,
    filters,
    today,
}: {
    loans: Row[];
    filters: Filters;
    today: string;
}) {
    const { visit, search, onSearch, loading, error } = useListQuery<Filters>({
        url: '/surveys',
        filters,
        defaults: {},
        only: ['loans', 'filters'],
    });

    return (
        <>
            <Head title="Surveys" />
            <PageHeader
                title="Surveys"
                description={`Files scheduled for you today, ${formatDate(today)}`}
            />

            <DataTable
                rows={loans}
                rowKey={(l) => l.id}
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
                empty={{
                    icon: <MapPin />,
                    title: 'No surveys today',
                    description:
                        'Files show up here on their scheduled survey date.',
                }}
            />
        </>
    );
}
