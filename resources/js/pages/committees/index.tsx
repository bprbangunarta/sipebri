import { Head, router, useForm } from '@inertiajs/react';
import {
    Gavel,
    MoreHorizontal,
    Pencil,
    Plus,
    Scale,
    SlidersHorizontal,
    Trash2,
} from 'lucide-react';
import { useState } from 'react';
import { AuthorityCheck } from '@/pages/committees/authority-check';
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
    conditionMap: Record<string, string[]>;
    committeeMembers: Option[];
    mechanisms: Option[];
    defaultLevels: { id: number; followers: number } | null;
    canManage: boolean;
};

export default function CommitteesIndex({
    paths,
    productOptions,
    pathOptions,
    conditionMap,
    committeeMembers,
    mechanisms,
    defaultLevels,
    canManage,
}: Props) {
    const [editing, setEditing] = useState<PathRow | 'new' | null>(null);
    const [toDelete, setToDelete] = useState<PathRow | null>(null);
    const [checking, setChecking] = useState(false);
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

    const columns: Column<PathRow>[] = [
        {
            key: 'product',
            header: 'Product',
            className: 'font-medium',
            cell: (p) => p.product_label,
        },
        {
            key: 'condition',
            header: 'Condition',
            hideBelow: 'sm',
            cell: (p) => p.condition_label,
        },
        {
            key: 'mechanism',
            header: 'Mechanism',
            hideBelow: 'md',
            cell: (p) => p.mechanism_label,
        },
        {
            key: 'tiers',
            header: 'Tiers',
            align: 'right',
            hideBelow: 'sm',
            className: 'tabular-nums',
            cell: (p) => p.tiers_count,
        },
        {
            key: 'levels',
            header: 'Levels',
            hideBelow: 'sm',
            cell: (p) => (
                <Badge tone={p.follows_default ? 'info' : 'neutral'}>
                    {p.follows_default ? 'Default' : 'Own'}
                </Badge>
            ),
        },
        {
            key: 'status',
            header: 'Status',
            cell: (p) => (
                <Badge tone={p.is_active ? 'success' : 'neutral'}>
                    {p.is_active ? 'Active' : 'Inactive'}
                </Badge>
            ),
        },
        {
            key: 'actions',
            header: 'Actions',
            srOnly: true,
            narrow: true,
            align: 'right',
            cell: (p) =>
                canManage && (
                    <DropdownMenu>
                        <Tip label="Actions">
                            <DropdownTrigger asChild>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    aria-label={`Actions for ${p.title}`}
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
                                Edit
                            </DropdownItem>
                            <DropdownSeparator />
                            <DropdownItem
                                danger
                                icon={<Trash2 />}
                                onSelect={() => setToDelete(p)}
                            >
                                Delete
                            </DropdownItem>
                        </DropdownContent>
                    </DropdownMenu>
                ),
        },
    ];

    return (
        <>
            <Head title="Committee levels" />
            <PageHeader
                title="Committee levels"
                description="Who may decide a loan, by product, condition and amount"
                actions={
                    <>
                        <Button
                            variant="outline"
                            onClick={() => setChecking(true)}
                        >
                            <Scale /> Check authority
                        </Button>
                        {defaultLevels && (
                            <Button
                                variant="outline"
                                onClick={() =>
                                    router.visit(
                                        `/committees/${defaultLevels.id}`,
                                    )
                                }
                            >
                                <SlidersHorizontal /> Default levels
                            </Button>
                        )}
                        {canManage && (
                            <Button onClick={() => openForm('new')}>
                                <Plus /> Add path
                            </Button>
                        )}
                    </>
                }
            />

            <DataTable
                rows={paths}
                rowKey={(p) => p.id}
                columns={columns}
                onRowClick={(p) => router.visit(`/committees/${p.id}`)}
                empty={{
                    icon: <Gavel />,
                    title: 'No committee paths yet',
                    description:
                        'Create a path per product (or across products for conditions such as RELOAN), then define its tiers.',
                    action: canManage ? (
                        <Button size="sm" onClick={() => openForm('new')}>
                            <Plus /> Add path
                        </Button>
                    ) : undefined,
                }}
            />

            <Modal
                open={editing !== null}
                onOpenChange={(open) => !open && setEditing(null)}
                title={
                    editing === 'new'
                        ? 'Add committee path'
                        : 'Edit committee path'
                }
                description="One path per product and condition."
            >
                <form onSubmit={submit} noValidate>
                    <div className="flex flex-col gap-3 p-4">
                        <Field
                            label="Product"
                            error={form.errors.product_id}
                            hint="Leave empty to apply to all products."
                        >
                            <Combobox
                                clearable
                                placeholder="All products"
                                options={productOptions}
                                value={form.data.product_id}
                                onChange={(v) =>
                                    form.setData('product_id', v ?? '')
                                }
                                invalid={!!form.errors.product_id}
                            />
                        </Field>
                        <Field
                            label="Condition / category"
                            error={form.errors.condition}
                            hint="Empty means Normal. Stored in uppercase, e.g. RELOAN."
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
                            label="Mechanism"
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
                                    Follow the default authority levels
                                    <span className="block text-xs text-muted">
                                        Limits are kept in one place; change
                                        them there and this path follows.
                                    </span>
                                </span>
                            </label>
                        )}
                        {editing === 'new' && (
                            <Field
                                label="Copy tiers from"
                                error={form.errors.copy_from}
                            >
                                <Combobox
                                    clearable
                                    placeholder="Do not copy"
                                    options={pathOptions}
                                    value={form.data.copy_from}
                                    onChange={(v) =>
                                        form.setData('copy_from', v ?? '')
                                    }
                                />
                            </Field>
                        )}
                        <Field label="Note" error={form.errors.note}>
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
                            Active
                        </label>
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
                title="Delete committee path?"
                description={
                    <>
                        This removes <strong>{toDelete?.title}</strong> and all
                        of its tiers.
                    </>
                }
                loading={deleting}
                onConfirm={confirmDelete}
            />

            <AuthorityCheck
                open={checking}
                onOpenChange={setChecking}
                productOptions={productOptions}
                conditionMap={conditionMap}
                memberOptions={committeeMembers}
            />
        </>
    );
}
