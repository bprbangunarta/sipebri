import { Head } from '@inertiajs/react';
import { ClipboardCheck } from 'lucide-react';
import {
    Badge,
    Card,
    EmptyState,
    ErrorState,
    PageHeader,
    Skeleton,
} from '@/components/ui/misc';
import { FilterBar, SearchInput } from '@/components/ui/filter-bar';
import { Pagination } from '@/components/ui/pagination';
import type { PageMeta } from '@/components/ui/pagination';
import { SortHead, nextSort } from '@/components/ui/sort-head';
import { useListQuery } from '@/hooks/use-list-query';
import { formatDate, rupiah } from '@/lib/format';
import { cn } from '@/lib/utils';

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

    return (
        <>
            <Head title="Analysis" />
            <PageHeader
                title="Analysis"
                description="Surveyed files assigned to you that are ready to be analysed"
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

                {error ? (
                    <ErrorState message={error} onRetry={() => visit({})} />
                ) : (
                    <div className="relative overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead className="border-b border-line bg-canvas text-xs text-muted">
                                <tr>
                                    <SortHead
                                        column="application_code"
                                        sort={filters.sort}
                                        direction={filters.direction}
                                        onSort={sortBy}
                                    >
                                        File
                                    </SortHead>
                                    <SortHead
                                        column="full_name"
                                        sort={filters.sort}
                                        direction={filters.direction}
                                        onSort={sortBy}
                                    >
                                        Applicant
                                    </SortHead>
                                    <SortHead
                                        column="requested_amount"
                                        sort={filters.sort}
                                        direction={filters.direction}
                                        onSort={sortBy}
                                        className="hidden text-right sm:table-cell [&_button]:ml-auto"
                                    >
                                        Amount
                                    </SortHead>
                                    <SortHead
                                        column="survey_date"
                                        sort={filters.sort}
                                        direction={filters.direction}
                                        onSort={sortBy}
                                        className="hidden md:table-cell"
                                    >
                                        Survey
                                    </SortHead>
                                    <th
                                        scope="col"
                                        className="hidden px-3 py-2 text-right font-medium lg:table-cell"
                                    >
                                        Collateral
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                className={cn(
                                    'divide-y divide-line',
                                    loading &&
                                        loans.data.length > 0 &&
                                        'opacity-50',
                                )}
                            >
                                {loading && loans.data.length === 0
                                    ? Array.from({ length: 5 }).map((_, i) => (
                                          <tr key={i}>
                                              <td
                                                  colSpan={5}
                                                  className="px-3 py-2.5"
                                              >
                                                  <Skeleton className="h-4 w-full" />
                                              </td>
                                          </tr>
                                      ))
                                    : loans.data.map((l) => (
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
                                                  {formatDate(l.survey_date)}
                                                  <p>
                                                      <Badge
                                                          tone={
                                                              l.surveyed
                                                                  ? 'success'
                                                                  : 'info'
                                                          }
                                                      >
                                                          {l.surveyed
                                                              ? 'Surveyed'
                                                              : 'No visit needed'}
                                                      </Badge>
                                                  </p>
                                              </td>
                                              <td className="hidden px-3 py-1.5 text-right tabular-nums lg:table-cell">
                                                  {l.collaterals_count}
                                              </td>
                                          </tr>
                                      ))}
                            </tbody>
                        </table>
                        {!loading && loans.data.length === 0 && (
                            <EmptyState
                                icon={<ClipboardCheck />}
                                title={
                                    filters.search
                                        ? 'No files match your search'
                                        : 'Nothing to analyse yet'
                                }
                                description={
                                    filters.search
                                        ? 'Try a different search term.'
                                        : 'Files appear here once their survey is saved (or, for walk-in products, once they are scheduled).'
                                }
                                action={
                                    filters.search ? (
                                        <button
                                            type="button"
                                            className="cursor-pointer text-xs text-primary hover:underline"
                                            onClick={() => clear()}
                                        >
                                            Clear search
                                        </button>
                                    ) : undefined
                                }
                            />
                        )}
                    </div>
                )}

                <Pagination
                    meta={loans}
                    perPage={filters.per_page}
                    perPageOptions={perPageOptions}
                    onPage={(page) => visit({ page })}
                    onPerPage={(per_page) => visit({ per_page })}
                />
            </Card>
        </>
    );
}
