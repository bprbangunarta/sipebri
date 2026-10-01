import { router, useForm } from '@inertiajs/react';
import { Plus, Store, Trash2 } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { ConfirmDialog, DialogFooter, Modal } from '@/components/ui/dialog';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { Field } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { rupiah } from '@/lib/format';
import type { BusinessRow } from '@/pages/credit-analysis/types';

export const BUSINESS_TYPES: { key: BusinessRow['type']; label: string }[] = [
    { key: 'trade', label: 'Usaha Perdagangan' },
    { key: 'farm', label: 'Usaha Pertanian' },
    { key: 'service', label: 'Usaha Jasa' },
    { key: 'other', label: 'Usaha Lainnya' },
];

/** The businesses of one type; each opens its own worksheet. */
export function BusinessesSection({
    loanId,
    businesses,
    type,
    onType,
    canEdit,
}: {
    loanId: number;
    businesses: BusinessRow[];
    type: BusinessRow['type'];
    onType: (type: BusinessRow['type']) => void;
    canEdit: boolean;
}) {
    const [adding, setAdding] = useState(false);
    const [toDelete, setToDelete] = useState<BusinessRow | null>(null);
    const form = useForm({ type, name: '' });
    const label = BUSINESS_TYPES.find((t) => t.key === type)?.label ?? '';
    const rows = businesses.filter((b) => b.type === type);

    const columns: Column<BusinessRow>[] = [
        {
            key: 'code',
            header: 'Kode',
            className: 'font-mono text-xs',
            cell: (b) => b.code,
        },
        {
            key: 'name',
            header: 'Nama Usaha',
            className: 'font-medium',
            cell: (b) => b.name,
        },
        {
            key: 'revenue',
            header: 'Pendapatan',
            align: 'right',
            hideBelow: 'sm',
            className: 'tabular-nums',
            cell: (b) => rupiah(b.revenue),
        },
        {
            key: 'expense',
            header: 'Pengeluaran',
            align: 'right',
            hideBelow: 'md',
            className: 'tabular-nums',
            cell: (b) => rupiah(b.expense),
        },
        {
            key: 'monthly',
            header: 'Hasil Bersih Perbulan',
            align: 'right',
            className: 'tabular-nums',
            cell: (b) => rupiah(b.monthly_income),
        },
        {
            key: 'actions',
            header: 'Aksi',
            srOnly: true,
            narrow: true,
            align: 'right',
            cell: (b) =>
                canEdit && (
                    <Button
                        variant="ghost"
                        size="icon"
                        aria-label={`Hapus ${b.code}`}
                        onClick={(e) => {
                            e.stopPropagation();
                            setToDelete(b);
                        }}
                    >
                        <Trash2 />
                    </Button>
                ),
        },
    ];

    return (
        <div className="flex flex-col gap-3">
            <div className="flex flex-wrap items-center justify-between gap-2">
                <div className="flex flex-wrap gap-1 rounded-md border border-line bg-canvas p-1">
                    {BUSINESS_TYPES.map((t) => (
                        <button
                            key={t.key}
                            type="button"
                            onClick={() => onType(t.key)}
                            className={`cursor-pointer rounded px-3 py-1 text-xs font-medium transition-colors ${t.key === type ? 'bg-surface text-ink shadow-sm' : 'text-muted hover:text-ink'}`}
                        >
                            {t.label.replace('Usaha ', '')} (
                            {businesses.filter((b) => b.type === t.key).length})
                        </button>
                    ))}
                </div>
                {canEdit && (
                    <Button
                        onClick={() => {
                            form.setData({ type, name: '' });
                            form.clearErrors();
                            setAdding(true);
                        }}
                    >
                        <Plus /> Tambah {label}
                    </Button>
                )}
            </div>

            <DataTable
                rows={rows}
                rowKey={(b) => b.id}
                columns={columns}
                onRowClick={(b) =>
                    router.visit(
                        `/credit-analysis/${loanId}/businesses/${b.id}`,
                    )
                }
                empty={{
                    icon: <Store />,
                    title: `Belum ada ${label.toLowerCase()} yang dicatat.`,
                    description: canEdit
                        ? `Tambahkan satu, lalu lengkapi datanya.`
                        : undefined,
                }}
            />

            <Modal
                open={adding}
                onOpenChange={setAdding}
                title={`Tambah ${label}`}
                description="Datanya dilengkapi di halaman berikutnya."
            >
                <form
                    noValidate
                    onSubmit={(e) => {
                        e.preventDefault();
                        form.post(`/credit-analysis/${loanId}/businesses`);
                    }}
                >
                    <div className="p-4">
                        <Field
                            label="Nama Usaha"
                            required
                            error={form.errors.name}
                        >
                            <Input
                                autoFocus
                                maxLength={150}
                                value={form.data.name}
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
                            onClick={() => setAdding(false)}
                        >
                            Batal
                        </Button>
                        <Button
                            type="submit"
                            loading={form.processing}
                            disabled={form.data.name.trim() === ''}
                        >
                            Simpan &amp; Lanjut
                        </Button>
                    </DialogFooter>
                </form>
            </Modal>

            <ConfirmDialog
                open={toDelete !== null}
                onOpenChange={(open) => !open && setToDelete(null)}
                title="Hapus usaha?"
                description={
                    <>
                        Usaha <strong>{toDelete?.name}</strong> beserta
                        rinciannya akan dihapus permanen.
                    </>
                }
                onConfirm={() => {
                    if (toDelete) {
                        router.delete(
                            `/credit-analysis/${loanId}/businesses/${toDelete.id}`,
                            {
                                preserveScroll: true,
                                onFinish: () => setToDelete(null),
                            },
                        );
                    }
                }}
            />
        </div>
    );
}
