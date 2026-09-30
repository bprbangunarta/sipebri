import { Head } from '@inertiajs/react';
import { UserX, X } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { FilterBar, SearchInput } from '@/components/ui/filter-bar';
import { Combobox } from '@/components/ui/combobox';
import {
    Badge,
    Card,
    EmptyState,
    ErrorState,
    PageHeader,
    Skeleton,
} from '@/components/ui/misc';
import { Pagination } from '@/components/ui/pagination';
import type { PageMeta } from '@/components/ui/pagination';
import { useListQuery } from '@/hooks/use-list-query';
import { formatDate } from '@/lib/format';

type UserRow = {
    id: number;
    name: string;
    username: string | null;
    email: string;
    office: string | null;
    active: boolean;
    role: string | null;
    created_at: string | null;
};
type Filters = {
    search: string;
    role: string | null;
    status: 'active' | 'inactive' | null;
    per_page: number;
};

const STATUS_OPTIONS = [
    { value: 'active', label: 'Active' },
    { value: 'inactive', label: 'Inactive' },
];

type Props = {
    users: { data: UserRow[] } & PageMeta;
    filters: Filters;
    roles: string[];
};

const DEFAULTS = { per_page: 10 };

export default function UsersIndex({ users, filters, roles }: Props) {
    const { visit, search, onSearch, clear, loading, error } =
        useListQuery<Filters>({
            url: '/users',
            filters,
            defaults: DEFAULTS,
            only: ['users', 'filters'],
        });

    const hasFilters = Boolean(
        filters.search || filters.role || filters.status,
    );
    const roleOptions = roles.map((r) => ({ value: r, label: r }));

    return (
        <>
            <Head title="Users" />
            <PageHeader
                title="Users"
                description="People mirrored from Codex. Their account, role and status are managed there."
            />

            <Card>
                <FilterBar
                    search={
                        <SearchInput
                            value={search}
                            onChange={onSearch}
                            placeholder="Search name or email…"
                            label="Search users"
                        />
                    }
                >
                    <Combobox
                        className="w-full sm:w-48"
                        clearable
                        placeholder="Role"
                        options={roleOptions}
                        value={filters.role}
                        onChange={(v) => visit({ role: v })}
                    />
                    <Combobox
                        className="w-full sm:w-36"
                        clearable
                        searchable={false}
                        placeholder="Status"
                        options={STATUS_OPTIONS}
                        value={filters.status}
                        onChange={(v) =>
                            visit({ status: v as Filters['status'] })
                        }
                    />
                    {hasFilters && (
                        <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => clear({ role: null, status: null })}
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
                                    <th
                                        scope="col"
                                        className="px-3 py-2 text-left font-medium"
                                    >
                                        User
                                    </th>
                                    <th
                                        scope="col"
                                        className="px-3 py-2 text-left font-medium"
                                    >
                                        Role
                                    </th>
                                    <th
                                        scope="col"
                                        className="hidden px-3 py-2 text-left font-medium md:table-cell"
                                    >
                                        Office
                                    </th>
                                    <th
                                        scope="col"
                                        className="px-3 py-2 text-left font-medium"
                                    >
                                        Status
                                    </th>
                                    <th
                                        scope="col"
                                        className="hidden px-3 py-2 text-left font-medium sm:table-cell"
                                    >
                                        Created
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                className={
                                    loading && users.data.length > 0
                                        ? 'divide-y divide-line opacity-50'
                                        : 'divide-y divide-line'
                                }
                            >
                                {loading && users.data.length === 0
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
                                    : users.data.map((u) => (
                                          <tr
                                              key={u.id}
                                              className="hover:bg-canvas/60"
                                          >
                                              <td className="px-3 py-1.5">
                                                  <p className="font-medium">
                                                      {u.name}
                                                  </p>
                                                  <p className="text-xs text-muted">
                                                      {[u.username, u.email]
                                                          .filter(Boolean)
                                                          .join(' · ')}
                                                  </p>
                                              </td>
                                              <td className="px-3 py-1.5">
                                                  {u.role ?? (
                                                      <span className="text-muted">
                                                          No role
                                                      </span>
                                                  )}
                                              </td>
                                              <td className="hidden px-3 py-1.5 md:table-cell">
                                                  {u.office ?? '–'}
                                              </td>
                                              <td className="px-3 py-1.5">
                                                  <Badge
                                                      tone={
                                                          u.active
                                                              ? 'success'
                                                              : 'neutral'
                                                      }
                                                  >
                                                      {u.active
                                                          ? 'Active'
                                                          : 'Inactive'}
                                                  </Badge>
                                              </td>
                                              <td className="hidden px-3 py-1.5 whitespace-nowrap sm:table-cell">
                                                  {formatDate(u.created_at)}
                                              </td>
                                          </tr>
                                      ))}
                            </tbody>
                        </table>
                        {!loading && users.data.length === 0 && (
                            <EmptyState
                                icon={<UserX />}
                                title={
                                    hasFilters
                                        ? 'No users match your filters'
                                        : 'No users yet'
                                }
                                description={
                                    hasFilters
                                        ? 'Try a different search or clear the filters.'
                                        : undefined
                                }
                                action={
                                    hasFilters ? (
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            onClick={() =>
                                                clear({
                                                    role: null,
                                                    status: null,
                                                })
                                            }
                                        >
                                            Clear filters
                                        </Button>
                                    ) : undefined
                                }
                            />
                        )}
                    </div>
                )}

                <Pagination
                    meta={users}
                    perPage={filters.per_page}
                    perPageOptions={[10, 25, 50]}
                    onPage={(page) => visit({ page })}
                    onPerPage={(per_page) => visit({ per_page })}
                />
            </Card>
        </>
    );
}
