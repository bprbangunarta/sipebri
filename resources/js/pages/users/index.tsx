import { Head } from '@inertiajs/react';
import { UserX, X } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { FilterBar, SearchInput } from '@/components/ui/filter-bar';
import { Combobox } from '@/components/ui/combobox';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { Badge, PageHeader } from '@/components/ui/misc';
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

const columns: Column<UserRow>[] = [
    {
        key: 'user',
        header: 'User',
        cell: (u) => (
            <>
                <p className="font-medium">{u.name}</p>
                <p className="text-xs text-muted">
                    {[u.username, u.email].filter(Boolean).join(' · ')}
                </p>
            </>
        ),
    },
    {
        key: 'role',
        header: 'Role',
        cell: (u) => u.role ?? <span className="text-muted">No role</span>,
    },
    {
        key: 'office',
        header: 'Office',
        hideBelow: 'md',
        cell: (u) => u.office ?? '–',
    },
    {
        key: 'status',
        header: 'Status',
        cell: (u) => (
            <Badge tone={u.active ? 'success' : 'neutral'}>
                {u.active ? 'Active' : 'Inactive'}
            </Badge>
        ),
    },
    {
        key: 'created',
        header: 'Created',
        hideBelow: 'sm',
        className: 'whitespace-nowrap',
        cell: (u) => formatDate(u.created_at),
    },
];

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

            <DataTable
                rows={users.data}
                rowKey={(u) => u.id}
                loading={loading}
                error={error}
                onRetry={() => visit({})}
                toolbar={
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
                                onClick={() =>
                                    clear({ role: null, status: null })
                                }
                            >
                                <X /> Reset
                            </Button>
                        )}
                    </FilterBar>
                }
                columns={columns}
                empty={{
                    icon: <UserX />,
                    title: hasFilters
                        ? 'No users match your filters'
                        : 'No users yet',
                    description: hasFilters
                        ? 'Try a different search or clear the filters.'
                        : undefined,
                    action: hasFilters ? (
                        <Button
                            variant="outline"
                            size="sm"
                            onClick={() => clear({ role: null, status: null })}
                        >
                            Clear filters
                        </Button>
                    ) : undefined,
                }}
                pagination={{
                    meta: users,
                    perPage: filters.per_page,
                    options: [10, 25, 50],
                    onPage: (page) => visit({ page }),
                    onPerPage: (per_page) => visit({ per_page }),
                }}
            />
        </>
    );
}
