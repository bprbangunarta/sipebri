import { Head, router, useForm } from '@inertiajs/react';
import { KeyRound, Trash2, Wand2, X } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { ConfirmDialog, DialogFooter, Modal } from '@/components/ui/dialog';
import { Field } from '@/components/ui/field';
import { FilterBar, SearchInput } from '@/components/ui/filter-bar';
import { Input } from '@/components/ui/input';
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
import { Tip } from '@/components/ui/tooltip';
import { useListQuery } from '@/hooks/use-list-query';

type Row = {
    id: number;
    name: string;
    entity: string;
    action: string;
    module: string | null;
    custom: boolean;
    roles: number;
};
type Filters = {
    search: string;
    entity: string | null;
    type: 'system' | 'custom' | null;
    per_page: number;
};
type Props = {
    permissions: { data: Row[] } & PageMeta;
    filters: Filters;
    entities: string[];
    actions: string[];
};

const TYPES = [
    { value: 'system', label: 'System' },
    { value: 'custom', label: 'Custom' },
];
const DEFAULTS = { per_page: 25 };

export default function PermissionsIndex({
    permissions,
    filters,
    entities,
    actions,
}: Props) {
    const [generating, setGenerating] = useState(false);
    const [toDelete, setToDelete] = useState<Row | null>(null);
    const [deleting, setDeleting] = useState(false);
    const form = useForm({ entity: '', actions: actions as string[] });
    const { visit, search, onSearch, clear, loading, error } =
        useListQuery<Filters>({
            url: '/permissions',
            filters,
            defaults: DEFAULTS,
            only: ['permissions', 'filters'],
        });

    const hasFilters = Boolean(
        filters.search || filters.entity || filters.type,
    );

    const openGenerator = () => {
        form.clearErrors();
        form.setData({ entity: '', actions });
        setGenerating(true);
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post('/permissions/generate', {
            preserveScroll: true,
            onSuccess: () => setGenerating(false),
        });
    };

    const confirmDelete = () => {
        if (!toDelete) {
            return;
        }
        router.delete(`/permissions/${toDelete.id}`, {
            preserveScroll: true,
            onStart: () => setDeleting(true),
            onFinish: () => {
                setDeleting(false);
                setToDelete(null);
            },
        });
    };

    const toggle = (action: string, on: boolean) =>
        form.setData(
            'actions',
            on
                ? actions.filter(
                      (a) => a === action || form.data.actions.includes(a),
                  )
                : form.data.actions.filter((a) => a !== action),
        );

    return (
        <>
            <Head title="Permissions" />
            <PageHeader
                title="Permissions"
                description="Every permission item. System ones are defined in code; custom ones come from the generator."
                actions={
                    <Button onClick={openGenerator}>
                        <Wand2 /> Generate permissions
                    </Button>
                }
            />

            <Card>
                <FilterBar
                    search={
                        <SearchInput
                            value={search}
                            onChange={onSearch}
                            placeholder="Search permission…"
                            label="Search permissions"
                        />
                    }
                >
                    <Combobox
                        className="w-full sm:w-48"
                        clearable
                        placeholder="Entity"
                        options={entities.map((e) => ({ value: e, label: e }))}
                        value={filters.entity}
                        onChange={(v) => visit({ entity: v })}
                    />
                    <Combobox
                        className="w-full sm:w-36"
                        clearable
                        searchable={false}
                        placeholder="Type"
                        options={TYPES}
                        value={filters.type}
                        onChange={(v) => visit({ type: v as Filters['type'] })}
                    />
                    {hasFilters && (
                        <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => clear({ entity: null, type: null })}
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
                                        Permission
                                    </th>
                                    <th
                                        scope="col"
                                        className="hidden px-3 py-2 text-left font-medium sm:table-cell"
                                    >
                                        Module
                                    </th>
                                    <th
                                        scope="col"
                                        className="px-3 py-2 text-left font-medium"
                                    >
                                        Type
                                    </th>
                                    <th
                                        scope="col"
                                        className="px-3 py-2 text-right font-medium"
                                    >
                                        Roles
                                    </th>
                                    <th scope="col" className="w-10 px-3 py-2">
                                        <span className="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                className={
                                    loading && permissions.data.length > 0
                                        ? 'divide-y divide-line opacity-50'
                                        : 'divide-y divide-line'
                                }
                            >
                                {loading && permissions.data.length === 0
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
                                    : permissions.data.map((p) => (
                                          <tr
                                              key={p.id}
                                              className="hover:bg-canvas/60"
                                          >
                                              <td className="px-3 py-1.5 font-mono text-xs">
                                                  {p.name}
                                              </td>
                                              <td className="hidden px-3 py-1.5 sm:table-cell">
                                                  {p.module ?? (
                                                      <span className="text-muted">
                                                          –
                                                      </span>
                                                  )}
                                              </td>
                                              <td className="px-3 py-1.5">
                                                  <Badge
                                                      tone={
                                                          p.custom
                                                              ? 'info'
                                                              : 'neutral'
                                                      }
                                                  >
                                                      {p.custom
                                                          ? 'Custom'
                                                          : 'System'}
                                                  </Badge>
                                              </td>
                                              <td className="px-3 py-1.5 text-right tabular-nums">
                                                  {p.roles}
                                              </td>
                                              <td className="px-3 py-1.5 text-right">
                                                  {p.custom && (
                                                      <Tip label="Delete">
                                                          <Button
                                                              variant="ghost"
                                                              size="icon"
                                                              aria-label={`Delete ${p.name}`}
                                                              onClick={() =>
                                                                  setToDelete(p)
                                                              }
                                                          >
                                                              <Trash2 />
                                                          </Button>
                                                      </Tip>
                                                  )}
                                              </td>
                                          </tr>
                                      ))}
                            </tbody>
                        </table>
                        {!loading && permissions.data.length === 0 && (
                            <EmptyState
                                icon={<KeyRound />}
                                title={
                                    hasFilters
                                        ? 'No permissions match your filters'
                                        : 'No permissions yet'
                                }
                            />
                        )}
                    </div>
                )}

                <Pagination
                    meta={permissions}
                    perPage={filters.per_page}
                    perPageOptions={[10, 25, 50]}
                    onPage={(page) => visit({ page })}
                    onPerPage={(per_page) => visit({ per_page })}
                />
            </Card>

            <Modal
                open={generating}
                onOpenChange={setGenerating}
                title="Standard permission generator"
                description="Creates entity.action permissions; existing ones are skipped."
            >
                <form onSubmit={submit} noValidate>
                    <div className="flex flex-col gap-3 p-4">
                        <Field
                            label="Entity"
                            required
                            error={form.errors.entity}
                            hint='Lowercase with hyphens, e.g. "credit-analysis".'
                        >
                            <Input
                                autoFocus
                                value={form.data.entity}
                                onChange={(e) =>
                                    form.setData('entity', e.target.value)
                                }
                                aria-invalid={!!form.errors.entity}
                            />
                        </Field>
                        <Field
                            label="Standard actions"
                            error={
                                form.errors.actions ??
                                (form.errors as Record<string, string>)[
                                    'actions.0'
                                ]
                            }
                        >
                            <div className="grid grid-cols-2 gap-x-4 gap-y-1.5 rounded-md border border-line p-2">
                                {actions.map((a) => (
                                    <label
                                        key={a}
                                        className="flex items-center gap-1.5 font-mono text-xs"
                                    >
                                        <input
                                            type="checkbox"
                                            className="accent-primary"
                                            checked={form.data.actions.includes(
                                                a,
                                            )}
                                            onChange={(e) =>
                                                toggle(a, e.target.checked)
                                            }
                                        />
                                        {a}
                                    </label>
                                ))}
                            </div>
                        </Field>
                        <p className="text-xs text-muted">
                            A permission only protects something once a route or
                            policy checks it. Super Admin receives new
                            permissions automatically.
                        </p>
                    </div>
                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={() => setGenerating(false)}
                        >
                            Cancel
                        </Button>
                        <Button type="submit" loading={form.processing}>
                            Create permissions
                        </Button>
                    </DialogFooter>
                </form>
            </Modal>

            <ConfirmDialog
                open={toDelete !== null}
                onOpenChange={(open) => !open && !deleting && setToDelete(null)}
                title="Delete permission?"
                description={
                    <>
                        This removes <strong>{toDelete?.name}</strong>. It is
                        not held by any role.
                    </>
                }
                loading={deleting}
                onConfirm={confirmDelete}
            />
        </>
    );
}
