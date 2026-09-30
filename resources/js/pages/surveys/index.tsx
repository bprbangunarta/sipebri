import { Head, router } from '@inertiajs/react';
import { MapPin } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { FilterBar, SearchInput } from '@/components/ui/filter-bar';
import { Card, EmptyState, PageHeader } from '@/components/ui/misc';
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

export default function SurveysIndex({
    loans,
    filters,
    today,
}: {
    loans: Row[];
    filters: Filters;
    today: string;
}) {
    const { search, onSearch } = useListQuery<Filters>({
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

            <Card>
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
                {loans.length === 0 ? (
                    <EmptyState
                        icon={<MapPin />}
                        title="No surveys today"
                        description="Files show up here on their scheduled survey date."
                    />
                ) : (
                    <div className="relative overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead className="border-b border-line bg-canvas text-xs text-muted">
                                <tr>
                                    <th
                                        scope="col"
                                        className="px-3 py-2 text-left font-medium"
                                    >
                                        File
                                    </th>
                                    <th
                                        scope="col"
                                        className="px-3 py-2 text-left font-medium"
                                    >
                                        Applicant
                                    </th>
                                    <th
                                        scope="col"
                                        className="hidden px-3 py-2 text-right font-medium sm:table-cell"
                                    >
                                        Amount
                                    </th>
                                    <th
                                        scope="col"
                                        className="hidden px-3 py-2 text-left font-medium md:table-cell"
                                    >
                                        Section head
                                    </th>
                                    <th scope="col" className="w-24 px-3 py-2">
                                        <span className="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-line">
                                {loans.map((l) => (
                                    <tr
                                        key={l.id}
                                        className="hover:bg-canvas/60"
                                    >
                                        <td className="px-3 py-1.5">
                                            <p className="font-medium">
                                                {l.application_code}
                                            </p>
                                            <p className="text-xs text-muted">
                                                {l.product_label}
                                            </p>
                                        </td>
                                        <td className="px-3 py-1.5">
                                            {l.full_name}
                                            <p className="font-mono text-xs text-muted">
                                                {l.nik}
                                            </p>
                                        </td>
                                        <td className="hidden px-3 py-1.5 text-right whitespace-nowrap tabular-nums sm:table-cell">
                                            {rupiah(l.requested_amount)}
                                            <p className="text-xs text-muted">
                                                {l.requested_tenor} months
                                            </p>
                                        </td>
                                        <td className="hidden px-3 py-1.5 md:table-cell">
                                            {l.supervisor_name ?? '–'}
                                        </td>
                                        <td className="px-3 py-1.5 text-right">
                                            <Button
                                                size="sm"
                                                onClick={() =>
                                                    router.visit(
                                                        `/surveys/${l.id}`,
                                                    )
                                                }
                                            >
                                                Open
                                            </Button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </Card>
        </>
    );
}
