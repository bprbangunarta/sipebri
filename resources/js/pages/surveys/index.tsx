import { Head, router } from '@inertiajs/react';
import { Check, LocateFixed, Loader2, MapPin } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { ConfirmDialog } from '@/components/ui/dialog';
import { FilterBar, SearchInput } from '@/components/ui/filter-bar';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { Badge, PageHeader } from '@/components/ui/misc';
import { Tip } from '@/components/ui/tooltip';
import { useListQuery } from '@/hooks/use-list-query';
import { formatDate, rupiah } from '@/lib/format';
import { markPosition } from '@/lib/geolocation';

type Row = {
    id: number;
    application_code: string;
    full_name: string;
    nik: string;
    requested_amount: number;
    requested_tenor: number;
    product_label: string | null;
    supervisor_name: string | null;
    has_location: boolean;
    located_at: string | null;
    survey_photos: number;
};
type Filters = { search: string };

/** What the survey still needs: its location and at least one photo of it. */
function Progress({ row }: { row: Row }) {
    return (
        <span className="flex flex-wrap gap-1">
            <Badge tone={row.has_location ? 'success' : 'warning'}>
                {row.has_location && <Check className="mr-0.5 size-3" />}
                Location
            </Badge>
            <Badge tone={row.survey_photos > 0 ? 'success' : 'warning'}>
                {row.survey_photos > 0 && <Check className="mr-0.5 size-3" />}
                Photos {row.survey_photos}
            </Badge>
        </span>
    );
}

export default function SurveysIndex({
    loans,
    filters,
    today,
}: {
    loans: Row[];
    filters: Filters;
    today: string;
}) {
    const [marking, setMarking] = useState<number | null>(null);
    const [replace, setReplace] = useState<Row | null>(null);
    const { visit, search, onSearch, loading, error } = useListQuery<Filters>({
        url: '/surveys',
        filters,
        defaults: {},
        only: ['loans', 'filters'],
    });

    /** On site: mark the survey location with the phone's GPS without opening the file. */
    const mark = async (l: Row) => {
        setMarking(l.id);
        await markPosition(
            l.id,
            { target: 'survey', collateral_id: null },
            () => setMarking(null),
        );
    };
    const ask = (l: Row) => (l.has_location ? setReplace(l) : void mark(l));

    const columns: Column<Row>[] = [
        {
            key: 'file',
            header: 'File',
            cell: (l) => (
                <>
                    <p className="font-medium">{l.application_code}</p>
                    <p className="text-xs text-muted">{l.product_label}</p>
                    {/* On phones the Progress column is hidden to keep the actions in view. */}
                    <span className="mt-1 block sm:hidden">
                        <Progress row={l} />
                    </span>
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
            key: 'progress',
            header: 'Progress',
            hideBelow: 'sm',
            cell: (l) => <Progress row={l} />,
        },
        {
            key: 'actions',
            header: 'Actions',
            srOnly: true,
            align: 'right',
            className: 'whitespace-nowrap',
            cell: (l) => (
                <span className="inline-flex items-center gap-1.5">
                    <Tip
                        label={
                            l.has_location
                                ? 'Mark the location again with your GPS'
                                : 'Mark the survey location with your GPS'
                        }
                    >
                        <Button
                            size="sm"
                            variant="outline"
                            aria-label={`Mark location of ${l.application_code}`}
                            disabled={marking !== null}
                            onClick={() => ask(l)}
                        >
                            {marking === l.id ? (
                                <Loader2 className="animate-spin" />
                            ) : (
                                <LocateFixed />
                            )}
                            <span className="hidden sm:inline">
                                {l.has_location
                                    ? 'Mark again'
                                    : 'Mark location'}
                            </span>
                        </Button>
                    </Tip>
                    <Button
                        size="sm"
                        onClick={() => router.visit(`/surveys/${l.id}`)}
                    >
                        Open
                    </Button>
                </span>
            ),
        },
    ];

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

            <ConfirmDialog
                open={replace !== null}
                onOpenChange={(open) => !open && setReplace(null)}
                title="Replace the saved position?"
                description={
                    <>
                        File <strong>{replace?.application_code}</strong>{' '}
                        already has a survey location from {replace?.located_at}
                        . Marking again replaces it with your current position.
                    </>
                }
                confirmLabel="Replace"
                onConfirm={() => {
                    const l = replace;
                    setReplace(null);

                    if (l) {
                        void mark(l);
                    }
                }}
            />
        </>
    );
}
