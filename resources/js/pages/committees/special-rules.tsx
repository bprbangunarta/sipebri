import { Head, router, useForm } from '@inertiajs/react';
import { Gavel, MoreHorizontal, Pencil, Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';
import { CommitteeTabs } from '@/components/committee-tabs';
import { Button } from '@/components/ui/button';
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
import { Tip } from '@/components/ui/tooltip';

type Option = { value: number | string; label: string };
export type PathRow = {
    id: number;
    product_id: number | null;
    product_label: string;
    condition: string | null;
    condition_label: string;
    mechanism: string;
    mechanism_label: string;
    is_active: boolean;
    is_default: boolean;
    follows_default: boolean;
    followers?: number | null;
    note: string | null;
    tiers_count: number;
    title: string;
};

type Props = {
    paths: PathRow[];
    productOptions: Option[];
    pathOptions: Option[];
    mechanisms: Option[];
    defaultLevels: { id: number; followers: number } | null;
    canManage: boolean;
};

export default function CommitteeSpecialRules({
    paths,
    productOptions,
    pathOptions,
    mechanisms,
    defaultLevels,
    canManage,
}: Props) {
    const [editing, setEditing] = useState<PathRow | 'new' | null>(null);
    const [toDelete, setToDelete] = useState<PathRow | null>(null);
    const [showAll, setShowAll] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const form = useForm({
        product_id: '',
        condition: '',
        mechanism: 'plafon',
        is_active: true,
        note: '',
        copy_from: '',
        follows_default: true,
    });

    const openForm = (row: PathRow | 'new') => {
        form.clearErrors();
        form.setData(
            row === 'new'
                ? {
                      product_id: '',
                      condition: '',
                      mechanism: 'plafon',
                      is_active: true,
                      note: '',
                      copy_from: '',
                      follows_default: true,
                  }
                : {
                      product_id: row.product_id ? String(row.product_id) : '',
                      condition: row.condition ?? '',
                      mechanism: row.mechanism,
                      is_active: row.is_active,
                      note: row.note ?? '',
                      copy_from: '',
                      follows_default: row.follows_default,
                  },
        );
        setEditing(row);
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        const options = {
            preserveScroll: true,
            onSuccess: () => setEditing(null),
        };
        if (editing === 'new') {
            form.post('/committees', options);
        } else if (editing) {
            form.put(`/committees/${editing.id}`, options);
        }
    };

    const confirmDelete = () => {
        if (!toDelete) {
            return;
        }
        router.delete(`/committees/${toDelete.id}`, {
            onStart: () => setDeleting(true),
            onFinish: () => {
                setDeleting(false);
                setToDelete(null);
            },
        });
    };

    const rows = showAll
        ? paths
        : paths.filter((p) => !p.follows_default || p.mechanism !== 'plafon');

    const columns: Column<PathRow>[] = [
        {
            key: 'product',
            header: 'Produk',
            className: 'font-medium',
            cell: (p) => p.product_label,
        },
        {
            key: 'condition',
            header: 'Kondisi',
            hideBelow: 'sm',
            cell: (p) => p.condition_label,
        },
        {
            key: 'mechanism',
            header: 'Mekanisme',
            hideBelow: 'md',
            cell: (p) => p.mechanism_label,
        },
        {
            key: 'tiers',
            header: 'Jenjang',
            align: 'right',
            hideBelow: 'sm',
            className: 'tabular-nums',
            cell: (p) => p.tiers_count,
        },
        {
            key: 'levels',
            header: 'Batas',
            hideBelow: 'sm',
            cell: (p) => (
                <Badge tone={p.follows_default ? 'info' : 'neutral'}>
                    {p.follows_default ? 'Bawaan' : 'Sendiri'}
                </Badge>
            ),
        },
        {
            key: 'status',
            header: 'Status',
            cell: (p) => (
                <Badge tone={p.is_active ? 'success' : 'neutral'}>
                    {p.is_active ? 'Aktif' : 'Nonaktif'}
                </Badge>
            ),
        },
        {
            key: 'actions',
            header: 'Aksi',
            srOnly: true,
            narrow: true,
            align: 'right',
            cell: (p) =>
                canManage && (
                    <DropdownMenu>
                        <Tip label="Aksi">
                            <DropdownTrigger asChild>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    aria-label={`Aksi untuk ${p.title}`}
                                >
                                    <MoreHorizontal />
                                </Button>
                            </DropdownTrigger>
                        </Tip>
                        <DropdownContent>
                            <DropdownItem
                                icon={<Pencil />}
                                onSelect={() => openForm(p)}
                            >
                                Ubah
                            </DropdownItem>
                            <DropdownSeparator />
                            <DropdownItem
                                danger
                                icon={<Trash2 />}
                                onSelect={() => setToDelete(p)}
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
            <Head title="Aturan Khusus Komite" />
            <PageHeader
                title="Data Komite"
                description="Jalur yang tidak sekadar mengikuti jenjang bawaan"
                actions={
                    canManage && (
                        <Button onClick={() => openForm('new')}>
                            <Plus /> Tambah jalur
                        </Button>
                    )
                }
            />
            <CommitteeTabs current="paths" />

            <div className="mb-3 flex flex-wrap items-center justify-between gap-2 text-sm text-muted">
                <span>
                    {showAll
                        ? `Semua ${paths.length} jalur.`
                        : `${paths.length - rows.length} jalur mengikuti jenjang bawaan dan disembunyikan; ${rows.length} jalur di bawah memakai hierarki atau jenjang sendiri.`}
                </span>
                <label className="flex items-center gap-2">
                    <input
                        type="checkbox"
                        className="accent-primary"
                        checked={showAll}
                        onChange={(e) => setShowAll(e.target.checked)}
                    />
                    Tampilkan semua jalur
                </label>
            </div>

            <DataTable
                rows={rows}
                rowKey={(p) => p.id}
                columns={columns}
                onRowClick={(p) => router.visit(`/committees/${p.id}`)}
                empty={{
                    icon: <Gavel />,
                    title: 'Belum ada jalur komite',
                    description:
                        'Buat satu jalur per produk (atau lintas produk untuk kondisi seperti RELOAN), lalu atur jenjangnya.',
                    action: canManage ? (
                        <Button size="sm" onClick={() => openForm('new')}>
                            <Plus /> Tambah jalur
                        </Button>
                    ) : undefined,
                }}
            />

            <Modal
                open={editing !== null}
                onOpenChange={(open) => !open && setEditing(null)}
                title={
                    editing === 'new'
                        ? 'Tambah jalur komite'
                        : 'Ubah jalur komite'
                }
                description="Satu jalur per produk dan kondisi."
            >
                <form onSubmit={submit} noValidate>
                    <div className="flex flex-col gap-3 p-4">
                        <Field
                            label="Produk"
                            error={form.errors.product_id}
                            hint="Kosongkan agar berlaku untuk semua produk."
                        >
                            <Combobox
                                clearable
                                placeholder="Semua produk"
                                options={productOptions}
                                value={form.data.product_id}
                                onChange={(v) =>
                                    form.setData('product_id', v ?? '')
                                }
                                invalid={!!form.errors.product_id}
                            />
                        </Field>
                        <Field
                            label="Kondisi / kategori"
                            error={form.errors.condition}
                            hint="Kosong berarti Normal. Disimpan huruf besar, mis. RELOAN."
                        >
                            <Input
                                className="uppercase"
                                value={form.data.condition}
                                maxLength={30}
                                onChange={(e) =>
                                    form.setData('condition', e.target.value)
                                }
                                aria-invalid={!!form.errors.condition}
                            />
                        </Field>
                        <Field
                            label="Mekanisme"
                            required
                            error={form.errors.mechanism}
                        >
                            <Combobox
                                searchable={false}
                                options={mechanisms}
                                value={form.data.mechanism}
                                onChange={(v) =>
                                    form.setData('mechanism', v ?? 'plafon')
                                }
                                invalid={!!form.errors.mechanism}
                            />
                        </Field>
                        {editing === 'new' && defaultLevels && (
                            <label className="flex items-start gap-2 text-sm">
                                <input
                                    type="checkbox"
                                    className="mt-0.5 accent-primary"
                                    checked={
                                        form.data.follows_default &&
                                        form.data.copy_from === ''
                                    }
                                    disabled={form.data.copy_from !== ''}
                                    onChange={(e) =>
                                        form.setData(
                                            'follows_default',
                                            e.target.checked,
                                        )
                                    }
                                />
                                <span>
                                    Ikuti jenjang wewenang bawaan
                                    <span className="block text-xs text-muted">
                                        Batas disimpan di satu tempat; ubah di
                                        sana dan jalur ini ikut berubah.
                                    </span>
                                </span>
                            </label>
                        )}
                        {editing === 'new' && (
                            <Field
                                label="Salin jenjang dari"
                                error={form.errors.copy_from}
                            >
                                <Combobox
                                    clearable
                                    placeholder="Jangan salin"
                                    options={pathOptions}
                                    value={form.data.copy_from}
                                    onChange={(v) =>
                                        form.setData('copy_from', v ?? '')
                                    }
                                />
                            </Field>
                        )}
                        <Field label="Catatan" error={form.errors.note}>
                            <Input
                                value={form.data.note}
                                maxLength={255}
                                onChange={(e) =>
                                    form.setData('note', e.target.value)
                                }
                            />
                        </Field>
                        <label className="flex items-center gap-2 text-sm">
                            <input
                                type="checkbox"
                                className="accent-primary"
                                checked={form.data.is_active}
                                onChange={(e) =>
                                    form.setData('is_active', e.target.checked)
                                }
                            />
                            Aktif
                        </label>
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
                title="Hapus jalur komite?"
                description={
                    <>
                        Ini menghapus <strong>{toDelete?.title}</strong> beserta
                        seluruh jenjangnya.
                    </>
                }
                loading={deleting}
                onConfirm={confirmDelete}
            />
        </>
    );
}
