import { Head, router, useForm } from '@inertiajs/react';
import { Lock, Plus, Settings2, Shield, X } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { DialogFooter, Modal } from '@/components/ui/dialog';
import { Field } from '@/components/ui/field';
import { FilterBar, SearchInput } from '@/components/ui/filter-bar';
import { Input } from '@/components/ui/input';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { PageHeader } from '@/components/ui/misc';
import type { PageMeta } from '@/components/ui/pagination';
import { nextSort } from '@/components/ui/sort-head';
import { Tip } from '@/components/ui/tooltip';
import { useListQuery } from '@/hooks/use-list-query';

type RoleRow = {
    id: number;
    name: string;
    users_count: number;
    permissions_count: number;
    locked: boolean;
};
type Filters = {
    search: string;
    sort: string;
    direction: 'asc' | 'desc';
    per_page: number;
};

const DEFAULTS = { sort: 'name', direction: 'asc', per_page: 25 };

const columns: Column<RoleRow>[] = [
    {
        key: 'role',
        header: 'Role',
        sort: 'name',
        className: 'font-medium',
        cell: (r) => (
            <span className="inline-flex items-center gap-1.5">
                {r.name}
                {r.locked && (
                    <Lock className="size-3 text-muted" aria-label="Locked" />
                )}
            </span>
        ),
    },
    {
        key: 'users',
        header: 'Users',
        sort: 'users_count',
        align: 'right',
        className: 'tabular-nums',
        cell: (r) => r.users_count,
    },
    {
        key: 'permissions',
        header: 'Permissions',
        sort: 'permissions_count',
        align: 'right',
        className: 'tabular-nums',
        cell: (r) => (r.locked ? 'All' : r.permissions_count),
    },
    {
        key: 'actions',
        header: 'Actions',
        srOnly: true,
        narrow: true,
        align: 'right',
        cell: (r) => (
            <Tip label="Open">
                <Button
                    variant="ghost"
                    size="icon"
                    aria-label={`Open ${r.name}`}
                    onClick={() => router.visit(`/roles/${r.id}`)}
                >
                    <Settings2 />
                </Button>
            </Tip>
        ),
    },
];

export default function RolesIndex({
    roles,
    filters,
    canManage,
}: {
    roles: { data: RoleRow[] } & PageMeta;
    filters: Filters;
    canManage: boolean;
}) {
    const [creating, setCreating] = useState(false);
    const form = useForm({ name: '' });
    const { visit, search, onSearch, clear, loading, error } =
        useListQuery<Filters>({
            url: '/roles',
            filters,
            defaults: DEFAULTS,
            only: ['roles', 'filters'],
        });
    const hasSearch = filters.search !== '';

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post('/roles', { onSuccess: () => setCreating(false) });
    };

    return (
        <>
            <Head title="Roles" />
            <PageHeader
                title="Roles"
                description="A role is a set of permissions; each user has exactly one role"
                actions={
                    canManage && (
                        <Button
                            onClick={() => {
                                form.reset();
                                form.clearErrors();
                                setCreating(true);
                            }}
                        >
                            <Plus /> Add role
                        </Button>
                    )
                }
            />

            <DataTable
                rows={roles.data}
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
                                placeholder="Search role…"
                                label="Search roles"
                            />
                        }
                    >
                        {hasSearch && (
                            <Button
                                variant="ghost"
                                size="sm"
                                onClick={() => clear()}
                            >
                                <X /> Reset
                            </Button>
                        )}
                    </FilterBar>
                }
                columns={columns}
                sort={{
                    sort: filters.sort,
                    direction: filters.direction,
                    onSort: (column) => visit(nextSort(filters, column)),
                }}
                empty={{
                    icon: <Shield />,
                    title: hasSearch
                        ? 'No roles match your search'
                        : 'No roles yet',
                }}
                pagination={{
                    meta: roles,
                    perPage: filters.per_page,
                    options: [10, 25, 50],
                    onPage: (page) => visit({ page }),
                    onPerPage: (per_page) => visit({ per_page }),
                }}
            />

            <Modal
                open={creating}
                onOpenChange={setCreating}
                title="Add role"
                description="You choose its permissions next."
            >
                <form onSubmit={submit} noValidate>
                    <div className="p-4">
                        <Field label="Name" required error={form.errors.name}>
                            <Input
                                autoFocus
                                value={form.data.name}
                                maxLength={100}
                                onChange={(e) =>
                                    form.setData('name', e.target.value)
                                }
                                aria-invalid={!!form.errors.name}
                            />
                        </Field>
                    </div>
                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={() => setCreating(false)}
                        >
                            Cancel
                        </Button>
                        <Button type="submit" loading={form.processing}>
                            Create
                        </Button>
                    </DialogFooter>
                </form>
            </Modal>
        </>
    );
}
