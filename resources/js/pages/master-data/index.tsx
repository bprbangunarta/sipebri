import { Head, router, useForm } from '@inertiajs/react';
import { Database, MoreHorizontal, Pencil, Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { FilterBar, SearchInput } from '@/components/ui/filter-bar';
import { Combobox } from '@/components/ui/combobox';
import { ConfirmDialog, DialogFooter, Modal } from '@/components/ui/dialog';
import {
    DropdownContent,
    DropdownItem,
    DropdownMenu,
    DropdownSeparator,
    DropdownTrigger,
} from '@/components/ui/dropdown';
import { Field } from '@/components/ui/field';
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
import { cn } from '@/lib/utils';

type FieldDef = {
    name: string;
    label: string;
    type: 'text' | 'number' | 'select' | 'boolean';
    required?: boolean;
    uppercase?: boolean;
    max?: number;
    hint?: string;
    default?: string | number | boolean;
    hide_below?: 'md';
    options?: { value: string | number; label: string }[];
};

type Item = { id: number; usage_count: number } & Record<
    string,
    string | number | boolean | null
>;
type Filters = { search: string; per_page: number };

type Props = {
    items: { data: Item[] } & PageMeta;
    filters: Filters;
    perPageOptions: number[];
    canManage: boolean;
    resource: {
        slug: string;
        label: string;
        section: string;
        tracks_usage: boolean;
        fields: FieldDef[];
        actions: { label: string; url: string }[];
        store_url: string;
        item_url: string;
    };
};

const DEFAULTS = { per_page: 25 };

function cell(field: FieldDef, item: Item) {
    const value = item[field.name];

    if (field.type === 'boolean') {
        return (
            <Badge tone={value ? 'success' : 'neutral'}>
                {value ? 'Active' : 'Inactive'}
            </Badge>
        );
    }
    if (field.type === 'select') {
        return (
            field.options?.find((o) => String(o.value) === String(value))
                ?.label ?? value
        );
    }
    if (field.type === 'number') {
        return <span className="tabular-nums">{value}</span>;
    }

    return value ?? '–';
}

export default function MasterDataIndex({
    items,
    filters,
    perPageOptions,
    canManage,
    resource,
}: Props) {
    const [editing, setEditing] = useState<Item | 'new' | null>(null);
    const [toDelete, setToDelete] = useState<Item | null>(null);
    const [deleting, setDeleting] = useState(false);
    const blank = () =>
        Object.fromEntries(
            resource.fields.map((f) => [
                f.name,
                f.default ?? (f.type === 'boolean' ? false : ''),
            ]),
        );
    const form =
        useForm<Record<string, string | number | boolean | null>>(blank());
    const { visit, search, onSearch, clear, loading, error } =
        useListQuery<Filters>({
            url: `/master-data/${resource.slug}`,
            filters,
            defaults: DEFAULTS,
            only: ['items', 'filters'],
        });

    const itemUrl = (item: Item) =>
        resource.item_url.replace('__id__', String(item.id));
    const label = resource.label.toLowerCase();
    const primary = resource.fields[0].name;
    const columns = resource.fields;

    const openForm = (item: Item | 'new') => {
        form.clearErrors();
        form.setData(
            item === 'new'
                ? blank()
                : Object.fromEntries(
                      resource.fields.map((f) => [
                          f.name,
                          item[f.name] ?? (f.type === 'boolean' ? false : ''),
                      ]),
                  ),
        );
        setEditing(item);
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        const options = {
            preserveScroll: true,
            onSuccess: () => setEditing(null),
        };
        if (editing === 'new') {
            form.post(resource.store_url, options);
        } else if (editing) {
            form.put(itemUrl(editing), options);
        }
    };

    const confirmDelete = () => {
        if (!toDelete) {
            return;
        }
        router.delete(itemUrl(toDelete), {
            preserveScroll: true,
            onStart: () => setDeleting(true),
            onFinish: () => {
                setDeleting(false);
                setToDelete(null);
            },
        });
    };

    const hasSearch = filters.search !== '';
    const colSpan = columns.length + (resource.tracks_usage ? 1 : 0) + 1;

    return (
        <>
            <Head title={resource.label} />
            <PageHeader
                title={resource.label}
                description={`${resource.section} · ${items.total} ${items.total === 1 ? 'record' : 'records'}`}
                actions={
                    canManage && (
                        <Button onClick={() => openForm('new')}>
                            <Plus /> Add {label}
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
                            placeholder={`Search ${label}…`}
                            label="Search"
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
                                    {columns.map((f) => (
                                        <th
                                            key={f.name}
                                            scope="col"
                                            className={cn(
                                                'px-3 py-2 text-left font-medium',
                                                f.hide_below &&
                                                    'hidden md:table-cell',
                                                f.type === 'number' &&
                                                    'text-right',
                                            )}
                                        >
                                            {f.label}
                                        </th>
                                    ))}
                                    {resource.tracks_usage && (
                                        <th
                                            scope="col"
                                            className="px-3 py-2 text-right font-medium"
                                        >
                                            In use
                                        </th>
                                    )}
                                    <th scope="col" className="w-10 px-3 py-2">
                                        <span className="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                className={cn(
                                    'divide-y divide-line',
                                    loading &&
                                        items.data.length > 0 &&
                                        'opacity-50',
                                )}
                            >
                                {loading && items.data.length === 0
                                    ? Array.from({ length: 5 }).map((_, i) => (
                                          <tr key={i}>
                                              <td
                                                  colSpan={colSpan}
                                                  className="px-3 py-2.5"
                                              >
                                                  <Skeleton className="h-4 w-full" />
                                              </td>
                                          </tr>
                                      ))
                                    : items.data.map((item) => (
                                          <tr
                                              key={item.id}
                                              className="hover:bg-canvas/60"
                                          >
                                              {columns.map((f) => (
                                                  <td
                                                      key={f.name}
                                                      className={cn(
                                                          'px-3 py-1.5',
                                                          f.name === primary &&
                                                              'font-medium',
                                                          f.hide_below &&
                                                              'hidden md:table-cell',
                                                          f.type === 'number' &&
                                                              'text-right',
                                                      )}
                                                  >
                                                      {cell(f, item)}
                                                  </td>
                                              ))}
                                              {resource.tracks_usage && (
                                                  <td className="px-3 py-1.5 text-right tabular-nums">
                                                      {item.usage_count}
                                                  </td>
                                              )}
                                              <td className="px-3 py-1.5 text-right">
                                                  {(canManage ||
                                                      resource.actions.length >
                                                          0) && (
                                                      <DropdownMenu>
                                                          <Tip label="Actions">
                                                              <DropdownTrigger
                                                                  asChild
                                                              >
                                                                  <Button
                                                                      variant="ghost"
                                                                      size="icon"
                                                                      aria-label={`Actions for ${item[primary]}`}
                                                                  >
                                                                      <MoreHorizontal />
                                                                  </Button>
                                                              </DropdownTrigger>
                                                          </Tip>
                                                          <DropdownContent>
                                                              {resource.actions.map(
                                                                  (a) => (
                                                                      <DropdownItem
                                                                          key={
                                                                              a.label
                                                                          }
                                                                          onSelect={() =>
                                                                              router.visit(
                                                                                  a.url.replace(
                                                                                      '{id}',
                                                                                      String(
                                                                                          item.id,
                                                                                      ),
                                                                                  ),
                                                                              )
                                                                          }
                                                                      >
                                                                          {
                                                                              a.label
                                                                          }
                                                                      </DropdownItem>
                                                                  ),
                                                              )}
                                                              {canManage && (
                                                                  <>
                                                                      <DropdownItem
                                                                          icon={
                                                                              <Pencil />
                                                                          }
                                                                          onSelect={() =>
                                                                              openForm(
                                                                                  item,
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
                                                                                  item,
                                                                              )
                                                                          }
                                                                      >
                                                                          Delete
                                                                      </DropdownItem>
                                                                  </>
                                                              )}
                                                          </DropdownContent>
                                                      </DropdownMenu>
                                                  )}
                                              </td>
                                          </tr>
                                      ))}
                            </tbody>
                        </table>
                        {!loading && items.data.length === 0 && (
                            <EmptyState
                                icon={<Database />}
                                title={
                                    hasSearch
                                        ? 'No results found'
                                        : `No ${label} records yet`
                                }
                                description={
                                    hasSearch
                                        ? 'Try a different search term.'
                                        : undefined
                                }
                                action={
                                    hasSearch ? (
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            onClick={() => clear()}
                                        >
                                            Clear search
                                        </Button>
                                    ) : canManage ? (
                                        <Button
                                            size="sm"
                                            onClick={() => openForm('new')}
                                        >
                                            <Plus /> Add {label}
                                        </Button>
                                    ) : undefined
                                }
                            />
                        )}
                    </div>
                )}

                <Pagination
                    meta={items}
                    perPage={filters.per_page}
                    perPageOptions={perPageOptions}
                    onPage={(page) => visit({ page })}
                    onPerPage={(per_page) => visit({ per_page })}
                />
            </Card>

            <Modal
                open={editing !== null}
                onOpenChange={(open) => !open && setEditing(null)}
                title={editing === 'new' ? `Add ${label}` : `Edit ${label}`}
            >
                <form onSubmit={submit} noValidate>
                    <div className="flex flex-col gap-3 p-4">
                        {resource.fields.map((f, index) => (
                            <Field
                                key={f.name}
                                label={f.label}
                                required={f.required}
                                error={form.errors[f.name]}
                                hint={f.hint}
                            >
                                {f.type === 'select' ? (
                                    <Combobox
                                        options={f.options ?? []}
                                        value={form.data[f.name] as string}
                                        onChange={(v) =>
                                            form.setData(f.name, v ?? '')
                                        }
                                        invalid={!!form.errors[f.name]}
                                    />
                                ) : f.type === 'boolean' ? (
                                    <label className="flex h-8 items-center gap-2 text-sm">
                                        <input
                                            type="checkbox"
                                            className="accent-primary"
                                            checked={Boolean(form.data[f.name])}
                                            onChange={(e) =>
                                                form.setData(
                                                    f.name,
                                                    e.target.checked,
                                                )
                                            }
                                        />
                                        {f.label}
                                    </label>
                                ) : (
                                    <Input
                                        autoFocus={index === 0}
                                        type={
                                            f.type === 'number'
                                                ? 'number'
                                                : 'text'
                                        }
                                        className={cn(
                                            f.uppercase && 'uppercase',
                                        )}
                                        value={
                                            (form.data[f.name] as
                                                | string
                                                | number) ?? ''
                                        }
                                        maxLength={
                                            f.type === 'text'
                                                ? f.max
                                                : undefined
                                        }
                                        onChange={(e) =>
                                            form.setData(f.name, e.target.value)
                                        }
                                        aria-invalid={!!form.errors[f.name]}
                                    />
                                )}
                            </Field>
                        ))}
                    </div>
                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={() => setEditing(null)}
                        >
                            Cancel
                        </Button>
                        <Button type="submit" loading={form.processing}>
                            {editing === 'new' ? 'Create' : 'Save'}
                        </Button>
                    </DialogFooter>
                </form>
            </Modal>

            <ConfirmDialog
                open={toDelete !== null}
                onOpenChange={(open) => !open && !deleting && setToDelete(null)}
                title={`Delete ${label}?`}
                description={
                    toDelete && toDelete.usage_count > 0 ? (
                        <>
                            <strong>{String(toDelete[primary])}</strong> is in
                            use by {toDelete.usage_count} record(s) and cannot
                            be deleted until they are reassigned.
                        </>
                    ) : (
                        <>
                            This will permanently remove{' '}
                            <strong>{String(toDelete?.[primary] ?? '')}</strong>
                            .
                        </>
                    )
                }
                loading={deleting}
                onConfirm={confirmDelete}
            />
        </>
    );
}
