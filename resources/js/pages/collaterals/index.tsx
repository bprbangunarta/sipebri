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
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { PageHeader } from '@/components/ui/misc';
import type { PageMeta } from '@/components/ui/pagination';
import { nextSort } from '@/components/ui/sort-head';
import { Tip } from '@/components/ui/tooltip';
import { useListQuery } from '@/hooks/use-list-query';
import { rupiah } from '@/lib/format';

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

    const columns: Column<Row>[] = [
        {
            key: 'id',
            header: 'ID agunan',
            sort: 'cbs_id',
            className: 'font-medium',
            cell: (c) => c.cbs_id ?? `#${c.id}`,
        },
        {
            key: 'type',
            header: 'Jenis',
            sort: 'collateral_type_code',
            hideBelow: 'md',
            cell: (c) => c.type_label,
        },
        {
            key: 'owner',
            header: 'Pemilik',
            sort: 'owner_name',
            cell: (c) => (
                <>
                    {c.owner_name}
                    <p className="max-w-xs truncate text-xs text-muted">
                        {c.description}
                    </p>
                </>
            ),
        },
        {
            key: 'document',
            header: 'Dokumen',
            hideBelow: 'lg',
            cell: (c) => c.document_number,
        },
        {
            key: 'appraisal',
            header: 'Taksasi',
            sort: 'appraisal_value',
            align: 'right',
            className: 'whitespace-nowrap tabular-nums',
            cell: (c) => rupiah(c.appraisal_value),
        },
        {
            key: 'actions',
            header: 'Aksi',
            srOnly: true,
            narrow: true,
            align: 'right',
            cell: (c) =>
                canManage && (
                    <DropdownMenu>
                        <Tip label="Aksi">
                            <DropdownTrigger asChild>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    aria-label={`Aksi untuk ${c.cbs_id ?? c.id}`}
                                >
                                    <MoreHorizontal />
                                </Button>
                            </DropdownTrigger>
                        </Tip>
                        <DropdownContent>
                            <DropdownItem
                                icon={<Pencil />}
                                onSelect={() =>
                                    router.visit(`/collaterals/${c.id}/edit`)
                                }
                            >
                                Ubah
                            </DropdownItem>
                            <DropdownSeparator />
                            <DropdownItem
                                danger
                                icon={<Trash2 />}
                                onSelect={() => setToDelete(c)}
                            >
                                Hapus
                            </DropdownItem>
                        </DropdownContent>
                    </DropdownMenu>
                ),
        },
    ];

    return (
        <>
            <Head title="Jaminan" />
            <PageHeader
                title="Jaminan"
                description="Agunan yang diajukan untuk kredit, dicatat dalam format core banking"
                actions={
                    canManage && (
                        <Button
                            onClick={() => router.visit('/collaterals/create')}
                        >
                            <Plus /> Tambah jaminan
                        </Button>
                    )
                }
            />

            <DataTable
                rows={collaterals.data}
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
                                placeholder="Cari ID, pemilik, dokumen…"
                                label="Cari jaminan"
                            />
                        }
                    >
                        <Combobox
                            className="w-full sm:w-56"
                            clearable
                            placeholder="Jenis agunan"
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
                                <X /> Atur ulang
                            </Button>
                        )}
                    </FilterBar>
                }
                columns={columns}
                sort={{
                    sort: filters.sort,
                    direction: filters.direction,
                    onSort: sortBy,
                }}
                empty={{
                    icon: <Landmark />,
                    title: hasFilters
                        ? 'Tidak ada jaminan yang cocok dengan filter'
                        : 'Belum ada jaminan',
                    description: hasFilters
                        ? 'Coba pencarian lain atau hapus filter.'
                        : 'Jaminan juga bisa ditambahkan langsung dari pengajuan.',
                    action: hasFilters ? (
                        <Button
                            variant="outline"
                            size="sm"
                            onClick={() => clear({ type: null })}
                        >
                            Hapus filter
                        </Button>
                    ) : canManage ? (
                        <Button
                            size="sm"
                            onClick={() => router.visit('/collaterals/create')}
                        >
                            <Plus /> Tambah jaminan
                        </Button>
                    ) : undefined,
                }}
                pagination={{
                    meta: collaterals,
                    perPage: filters.per_page,
                    options: perPageOptions,
                    onPage: (page) => visit({ page }),
                    onPerPage: (per_page) => visit({ per_page }),
                }}
            />

            <ConfirmDialog
                open={toDelete !== null}
                onOpenChange={(open) => !open && !deleting && setToDelete(null)}
                title="Hapus jaminan?"
                description={
                    <>
                        Ini menghapus{' '}
                        <strong>
                            {toDelete?.cbs_id ?? `#${toDelete?.id}`}
                        </strong>
                        . Jaminan yang dilekatkan ke pengajuan tidak bisa
                        dihapus.
                    </>
                }
                loading={deleting}
                onConfirm={confirmDelete}
            />
        </>
    );
}
