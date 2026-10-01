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
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { Badge, PageHeader } from '@/components/ui/misc';
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
                {value ? 'Aktif' : 'Nonaktif'}
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

export default function ReferencesIndex({
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
            url: `/references/${resource.slug}`,
            filters,
            defaults: DEFAULTS,
            only: ['items', 'filters'],
        });

    const itemUrl = (item: Item) =>
        resource.item_url.replace('__id__', String(item.id));
    const label = resource.label;
    const primary = resource.fields[0].name;

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

    const columns: Column<Item>[] = [
        ...resource.fields.map((f): Column<Item> => ({
            key: f.name,
            header: f.label,
            hideBelow: f.hide_below ? 'md' : undefined,
            align: f.type === 'number' ? 'right' : undefined,
            className: f.name === primary ? 'font-medium' : undefined,
            cell: (item) => cell(f, item),
        })),
        ...(resource.tracks_usage
            ? [
                  {
                      key: 'usage',
                      header: 'Dipakai',
                      align: 'right',
                      className: 'tabular-nums',
                      cell: (item: Item) => item.usage_count,
                  } satisfies Column<Item>,
              ]
            : []),
        {
            key: 'actions',
            header: 'Aksi',
            srOnly: true,
            narrow: true,
            align: 'right',
            cell: (item) =>
                (canManage || resource.actions.length > 0) && (
                    <DropdownMenu>
                        <Tip label="Aksi">
                            <DropdownTrigger asChild>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    aria-label={`Aksi untuk ${item[primary]}`}
                                >
                                    <MoreHorizontal />
                                </Button>
                            </DropdownTrigger>
                        </Tip>
                        <DropdownContent>
                            {resource.actions.map((a) => (
                                <DropdownItem
                                    key={a.label}
                                    onSelect={() =>
                                        router.visit(
                                            a.url.replace(
                                                '{id}',
                                                String(item.id),
                                            ),
                                        )
                                    }
                                >
                                    {a.label}
                                </DropdownItem>
                            ))}
                            {canManage && (
                                <>
                                    <DropdownItem
                                        icon={<Pencil />}
                                        onSelect={() => openForm(item)}
                                    >
                                        Ubah
                                    </DropdownItem>
                                    <DropdownSeparator />
                                    <DropdownItem
                                        danger
                                        icon={<Trash2 />}
                                        onSelect={() => setToDelete(item)}
                                    >
                                        Hapus
                                    </DropdownItem>
                                </>
                            )}
                        </DropdownContent>
                    </DropdownMenu>
                ),
        },
    ];

    return (
        <>
            <Head title={`Data ${resource.label}`} />
            <PageHeader
                title={`Data ${resource.label}`}
                description={`${resource.section} · ${items.total} data`}
                actions={
                    canManage && (
                        <Button onClick={() => openForm('new')}>
                            <Plus /> Tambah {label}
                        </Button>
                    )
                }
            />

            <DataTable
                rows={items.data}
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
                                placeholder={`Cari ${label}…`}
                                label="Cari"
                            />
                        }
                    />
                }
                columns={columns}
                empty={{
                    icon: <Database />,
                    title: hasSearch
                        ? 'Tidak ada hasil'
                        : `Belum ada data ${label}`,
                    description: hasSearch
                        ? 'Coba kata kunci lain.'
                        : undefined,
                    action: hasSearch ? (
                        <Button
                            variant="outline"
                            size="sm"
                            onClick={() => clear()}
                        >
                            Hapus pencarian
                        </Button>
                    ) : canManage ? (
                        <Button size="sm" onClick={() => openForm('new')}>
                            <Plus /> Tambah {label}
                        </Button>
                    ) : undefined,
                }}
                pagination={{
                    meta: items,
                    perPage: filters.per_page,
                    options: perPageOptions,
                    onPage: (page) => visit({ page }),
                    onPerPage: (per_page) => visit({ per_page }),
                }}
            />

            <Modal
                open={editing !== null}
                onOpenChange={(open) => !open && setEditing(null)}
                title={editing === 'new' ? `Tambah ${label}` : `Ubah ${label}`}
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
                            Batal
                        </Button>
                        <Button type="submit" loading={form.processing}>
                            {editing === 'new' ? 'Buat' : 'Simpan'}
                        </Button>
                    </DialogFooter>
                </form>
            </Modal>

            <ConfirmDialog
                open={toDelete !== null}
                onOpenChange={(open) => !open && !deleting && setToDelete(null)}
                title={`Hapus ${label}?`}
                description={
                    toDelete && toDelete.usage_count > 0 ? (
                        <>
                            <strong>{String(toDelete[primary])}</strong> dipakai
                            oleh {toDelete.usage_count} data dan tidak bisa
                            dihapus sebelum data itu dialihkan.
                        </>
                    ) : (
                        <>
                            Ini akan menghapus permanen{' '}
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
