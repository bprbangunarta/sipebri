import { Head, router } from '@inertiajs/react';
import {
    Landmark,
    MoreHorizontal,
    Pencil,
    Plus,
    Trash2,
    X,
} from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { FilterBar, SearchInput } from '@/components/ui/filter-bar';
import { Combobox } from '@/components/ui/combobox';
import { ConfirmDialog } from '@/components/ui/dialog';
import {
    DropdownContent,
    DropdownItem,
    DropdownMenu,
    DropdownSeparator,
    DropdownTrigger,
} from '@/components/ui/dropdown';
import {
    Card,
    EmptyState,
    ErrorState,
    PageHeader,
    Skeleton,
} from '@/components/ui/misc';
import { Pagination } from '@/components/ui/pagination';
import type { PageMeta } from '@/components/ui/pagination';
import { SortHead, nextSort } from '@/components/ui/sort-head';
import { Tip } from '@/components/ui/tooltip';
import { useListQuery } from '@/hooks/use-list-query';
import { rupiah } from '@/lib/format';
import { cn } from '@/lib/utils';

type Row = {
    id: number;
    cbs_id: string | null;
    collateral_type_code: string;
    type_label: string;
    owner_name: string | null;
    document_number: string | null;
    description: string | null;
    appraisal_value: number;
};
type Filters = {
    search: string;
    type: string | null;
    sort: string;
    direction: 'asc' | 'desc';
    per_page: number;
};

type Props = {
    collaterals: { data: Row[] } & PageMeta;
    filters: Filters;
    perPageOptions: number[];
    typeOptions: { value: string; label: string }[];
    canManage: boolean;
};

const DEFAULTS = { sort: 'created_at', direction: 'desc', per_page: 10 };

export default function CollateralsIndex({
    collaterals,
    filters,
    perPageOptions,
    typeOptions,
    canManage,
}: Props) {
    const [toDelete, setToDelete] = useState<Row | null>(null);
    const [deleting, setDeleting] = useState(false);
    const { visit, search, onSearch, clear, loading, error } =
        useListQuery<Filters>({
            url: '/collaterals',
            filters,
            defaults: DEFAULTS,
            only: ['collaterals', 'filters'],
        });
    const hasFilters = Boolean(filters.search || filters.type);
    const sortBy = (column: string) => visit(nextSort(filters, column));

    const confirmDelete = () => {
        if (!toDelete) {
            return;
        }
        router.delete(`/collaterals/${toDelete.id}`, {
            preserveScroll: true,
            onStart: () => setDeleting(true),
            onFinish: () => {
                setDeleting(false);
                setToDelete(null);
            },
        });
    };

    return (
        <>
            <Head title="Collaterals" />
            <PageHeader
                title="Collaterals"
                description="Assets offered against loans, recorded in the core banking format"
                actions={
                    canManage && (
                        <Button
                            onClick={() => router.visit('/collaterals/create')}
                        >
                            <Plus /> Add collateral
                        </Button>
                    )
                }
            />

            <Card>
                <FilterBar
                    search={
                        <SearchInput
                            value={search}
                            onChange={onSearch}
                            placeholder="Search ID, owner, document…"
                            label="Search collaterals"
                        />
                    }
                >
                    <Combobox
                        className="w-full sm:w-56"
                        clearable
                        placeholder="Collateral type"
                        options={typeOptions}
                        value={filters.type}
                        onChange={(v) => visit({ type: v })}
                    />
                    {hasFilters && (
                        <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => clear({ type: null })}
                        >
                            <X /> Reset
                        </Button>
                    )}
                </FilterBar>

                {error ? (
                    <ErrorState message={error} onRetry={() => visit({})} />
                ) : (
                    <div className="relative overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead className="border-b border-line bg-canvas text-xs text-muted">
                                <tr>
                                    <SortHead
                                        column="cbs_id"
                                        sort={filters.sort}
                                        direction={filters.direction}
                                        onSort={sortBy}
                                    >
                                        Collateral ID
                                    </SortHead>
                                    <SortHead
                                        column="collateral_type_code"
                                        sort={filters.sort}
                                        direction={filters.direction}
                                        onSort={sortBy}
                                        className="hidden md:table-cell"
                                    >
                                        Type
                                    </SortHead>
                                    <SortHead
                                        column="owner_name"
                                        sort={filters.sort}
                                        direction={filters.direction}
                                        onSort={sortBy}
                                    >
                                        Owner
                                    </SortHead>
                                    <th
                                        scope="col"
                                        className="hidden px-3 py-2 text-left font-medium lg:table-cell"
                                    >
                                        Document
                                    </th>
                                    <SortHead
                                        column="appraisal_value"
                                        sort={filters.sort}
                                        direction={filters.direction}
                                        onSort={sortBy}
                                        className="text-right [&_button]:ml-auto"
                                    >
                                        Appraisal
                                    </SortHead>
                                    <th scope="col" className="w-10 px-3 py-2">
                                        <span className="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                className={cn(
                                    'divide-y divide-line',
                                    loading &&
                                        collaterals.data.length > 0 &&
                                        'opacity-50',
                                )}
                            >
                                {loading && collaterals.data.length === 0
                                    ? Array.from({ length: 5 }).map((_, i) => (
                                          <tr key={i}>
                                              <td
                                                  colSpan={6}
                                                  className="px-3 py-2.5"
                                              >
                                                  <Skeleton className="h-4 w-full" />
                                              </td>
                                          </tr>
                                      ))
                                    : collaterals.data.map((c) => (
                                          <tr
                                              key={c.id}
                                              className="hover:bg-canvas/60"
                                          >
                                              <td className="px-3 py-1.5 font-medium">
                                                  {c.cbs_id ?? `#${c.id}`}
                                              </td>
                                              <td className="hidden px-3 py-1.5 md:table-cell">
                                                  {c.type_label}
                                              </td>
                                              <td className="px-3 py-1.5">
                                                  {c.owner_name}
                                                  <p className="max-w-xs truncate text-xs text-muted">
                                                      {c.description}
                                                  </p>
                                              </td>
                                              <td className="hidden px-3 py-1.5 lg:table-cell">
                                                  {c.document_number}
                                              </td>
                                              <td className="px-3 py-1.5 text-right whitespace-nowrap tabular-nums">
                                                  {rupiah(c.appraisal_value)}
                                              </td>
                                              <td className="px-3 py-1.5 text-right">
                                                  {canManage && (
                                                      <DropdownMenu>
                                                          <Tip label="Actions">
                                                              <DropdownTrigger
                                                                  asChild
                                                              >
                                                                  <Button
                                                                      variant="ghost"
                                                                      size="icon"
                                                                      aria-label={`Actions for ${c.cbs_id ?? c.id}`}
                                                                  >
                                                                      <MoreHorizontal />
                                                                  </Button>
                                                              </DropdownTrigger>
                                                          </Tip>
                                                          <DropdownContent>
                                                              <DropdownItem
                                                                  icon={
                                                                      <Pencil />
                                                                  }
                                                                  onSelect={() =>
                                                                      router.visit(
                                                                          `/collaterals/${c.id}/edit`,
                                                                      )
                                                                  }
                                                              >
                                                                  Edit
                                                              </DropdownItem>
                                                              <DropdownSeparator />
                                                              <DropdownItem
                                                                  danger
                                                                  icon={
                                                                      <Trash2 />
                                                                  }
                                                                  onSelect={() =>
                                                                      setToDelete(
                                                                          c,
                                                                      )
                                                                  }
                                                              >
                                                                  Delete
                                                              </DropdownItem>
                                                          </DropdownContent>
                                                      </DropdownMenu>
                                                  )}
                                              </td>
                                          </tr>
                                      ))}
                            </tbody>
                        </table>
                        {!loading && collaterals.data.length === 0 && (
                            <EmptyState
                                icon={<Landmark />}
                                title={
                                    hasFilters
                                        ? 'No collaterals match your filters'
                                        : 'No collaterals yet'
                                }
                                description={
                                    hasFilters
                                        ? 'Try a different search or clear the filters.'
                                        : 'Collaterals can also be added straight from a loan application.'
                                }
                                action={
                                    hasFilters ? (
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            onClick={() =>
                                                clear({ type: null })
                                            }
                                        >
                                            Clear filters
                                        </Button>
                                    ) : canManage ? (
                                        <Button
                                            size="sm"
                                            onClick={() =>
                                                router.visit(
                                                    '/collaterals/create',
                                                )
                                            }
                                        >
                                            <Plus /> Add collateral
                                        </Button>
                                    ) : undefined
                                }
                            />
                        )}
                    </div>
                )}

                <Pagination
                    meta={collaterals}
                    perPage={filters.per_page}
                    perPageOptions={perPageOptions}
                    onPage={(page) => visit({ page })}
                    onPerPage={(per_page) => visit({ per_page })}
                />
            </Card>

            <ConfirmDialog
                open={toDelete !== null}
                onOpenChange={(open) => !open && !deleting && setToDelete(null)}
                title="Delete collateral?"
                description={
                    <>
                        This removes{' '}
                        <strong>
                            {toDelete?.cbs_id ?? `#${toDelete?.id}`}
                        </strong>
                        . Collaterals attached to a loan application cannot be
                        deleted.
                    </>
                }
                loading={deleting}
                onConfirm={confirmDelete}
            />
        </>
    );
}
