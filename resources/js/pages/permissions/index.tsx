import { Head, router, useForm } from '@inertiajs/react';
import { KeyRound, Trash2, Wand2, X } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { ConfirmDialog, DialogFooter, Modal } from '@/components/ui/dialog';
import { Field } from '@/components/ui/field';
import { FilterBar, SearchInput } from '@/components/ui/filter-bar';
import { Input } from '@/components/ui/input';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { Badge, PageHeader } from '@/components/ui/misc';
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
    { value: 'system', label: 'Sistem' },
    { value: 'custom', label: 'Kustom' },
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

    const columns: Column<Row>[] = [
        {
            key: 'name',
            header: 'Izin',
            className: 'font-mono text-xs',
            cell: (p) => p.name,
        },
        {
            key: 'module',
            header: 'Modul',
            hideBelow: 'sm',
            cell: (p) => p.module ?? <span className="text-muted">–</span>,
        },
        {
            key: 'type',
            header: 'Jenis',
            cell: (p) => (
                <Badge tone={p.custom ? 'info' : 'neutral'}>
                    {p.custom ? 'Kustom' : 'Sistem'}
                </Badge>
            ),
        },
        {
            key: 'roles',
            header: 'Peran',
            align: 'right',
            className: 'tabular-nums',
            cell: (p) => p.roles,
        },
        {
            key: 'actions',
            header: 'Aksi',
            srOnly: true,
            narrow: true,
            align: 'right',
            cell: (p) =>
                p.custom && (
                    <Tip label="Hapus">
                        <Button
                            variant="ghost"
                            size="icon"
                            aria-label={`Hapus ${p.name}`}
                            onClick={() => setToDelete(p)}
                        >
                            <Trash2 />
                        </Button>
                    </Tip>
                ),
        },
    ];

    return (
        <>
            <Head title="Data Perizinan" />
            <PageHeader
                title="Data Perizinan"
                description="Semua item izin. Izin sistem ditetapkan di kode; izin kustom dibuat lewat generator."
                actions={
                    <Button onClick={openGenerator}>
                        <Wand2 /> Buat izin
                    </Button>
                }
            />

            <DataTable
                rows={permissions.data}
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
                                placeholder="Cari izin…"
                                label="Cari izin"
                            />
                        }
                    >
                        <Combobox
                            className="w-full sm:w-48"
                            clearable
                            placeholder="Entitas"
                            options={entities.map((e) => ({
                                value: e,
                                label: e,
                            }))}
                            value={filters.entity}
                            onChange={(v) => visit({ entity: v })}
                        />
                        <Combobox
                            className="w-full sm:w-36"
                            clearable
                            searchable={false}
                            placeholder="Jenis"
                            options={TYPES}
                            value={filters.type}
                            onChange={(v) =>
                                visit({ type: v as Filters['type'] })
                            }
                        />
                        {hasFilters && (
                            <Button
                                variant="ghost"
                                size="sm"
                                onClick={() =>
                                    clear({ entity: null, type: null })
                                }
                            >
                                <X /> Atur ulang
                            </Button>
                        )}
                    </FilterBar>
                }
                columns={columns}
                empty={{
                    icon: <KeyRound />,
                    title: hasFilters
                        ? 'Tidak ada izin yang cocok dengan filter'
                        : 'Belum ada izin',
                }}
                pagination={{
                    meta: permissions,
                    perPage: filters.per_page,
                    options: [10, 25, 50],
                    onPage: (page) => visit({ page }),
                    onPerPage: (per_page) => visit({ per_page }),
                }}
            />

            <Modal
                open={generating}
                onOpenChange={setGenerating}
                title="Generator izin standar"
                description="Membuat izin entitas.aksi; yang sudah ada dilewati."
            >
                <form onSubmit={submit} noValidate>
                    <div className="flex flex-col gap-3 p-4">
                        <Field
                            label="Entitas"
                            required
                            error={form.errors.entity}
                            hint='Huruf kecil dengan tanda hubung, mis. "credit-analysis".'
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
                            label="Aksi standar"
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
                            Sebuah izin baru melindungi sesuatu setelah route
                            atau policy memeriksanya. Super Admin otomatis
                            menerima izin baru.
                        </p>
                    </div>
                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={() => setGenerating(false)}
                        >
                            Batal
                        </Button>
                        <Button type="submit" loading={form.processing}>
                            Buat izin
                        </Button>
                    </DialogFooter>
                </form>
            </Modal>

            <ConfirmDialog
                open={toDelete !== null}
                onOpenChange={(open) => !open && !deleting && setToDelete(null)}
                title="Hapus izin?"
                description={
                    <>
                        Ini menghapus <strong>{toDelete?.name}</strong>. Izin
                        ini tidak dipegang peran mana pun.
                    </>
                }
                loading={deleting}
                onConfirm={confirmDelete}
            />
        </>
    );
}
