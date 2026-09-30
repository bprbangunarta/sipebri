import { Head, router, useForm } from '@inertiajs/react';
import {
    ArrowDown,
    ArrowLeft,
    ArrowUp,
    Pencil,
    Plus,
    Trash2,
} from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { ConfirmDialog, DialogFooter, Modal } from '@/components/ui/dialog';
import { Field } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Badge, Card, EmptyState, PageHeader } from '@/components/ui/misc';
import { rupiah } from '@/lib/format';
import type { PathRow } from '@/pages/committees/index';

type Tier = {
    id: number;
    sort: number;
    label: string | null;
    role: string;
    min_amount: number | null;
    max_amount: number | null;
    can_escalate: boolean;
    can_approve: boolean;
    can_cancel: boolean;
    can_reject: boolean;
};

type Props = {
    path: PathRow & { tiers: Tier[] };
    roles: string[];
    canManage: boolean;
};

const DECISIONS = [
    ['can_escalate', 'Escalate'],
    ['can_approve', 'Approve'],
    ['can_cancel', 'Cancel'],
    ['can_reject', 'Reject'],
] as const;

export default function CommitteeShow({ path, roles, canManage }: Props) {
    const [editing, setEditing] = useState<Tier | 'new' | null>(null);
    const [toDelete, setToDelete] = useState<Tier | null>(null);
    const byAmount = path.mechanism === 'plafon';
    const form = useForm({
        label: '',
        role: '',
        min_amount: '' as string | number,
        max_amount: '' as string | number,
        can_escalate: false,
        can_approve: false,
        can_cancel: false,
        can_reject: false,
    });
    const base = `/committees/${path.id}/tiers`;

    const openForm = (tier: Tier | 'new') => {
        form.clearErrors();
        form.setData(
            tier === 'new'
                ? {
                      label: '',
                      role: '',
                      min_amount: '',
                      max_amount: '',
                      can_escalate: false,
                      can_approve: false,
                      can_cancel: false,
                      can_reject: false,
                  }
                : {
                      label: tier.label ?? '',
                      role: tier.role,
                      min_amount: tier.min_amount ?? '',
                      max_amount: tier.max_amount ?? '',
                      can_escalate: tier.can_escalate,
                      can_approve: tier.can_approve,
                      can_cancel: tier.can_cancel,
                      can_reject: tier.can_reject,
                  },
        );
        setEditing(tier);
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        const options = {
            preserveScroll: true,
            onSuccess: () => setEditing(null),
        };
        if (editing === 'new') {
            form.post(base, options);
        } else if (editing) {
            form.put(`${base}/${editing.id}`, options);
        }
    };

    return (
        <>
            <Head title={path.title} />
            <PageHeader
                title={path.title}
                description={`${path.product_label} · ${path.mechanism_label}${path.note ? ` · ${path.note}` : ''}`}
                actions={
                    <>
                        <Badge tone={path.is_active ? 'success' : 'neutral'}>
                            {path.is_active ? 'Active' : 'Inactive'}
                        </Badge>
                        <Button
                            variant="outline"
                            onClick={() => router.visit('/committees')}
                        >
                            <ArrowLeft /> Back
                        </Button>
                        {canManage && (
                            <Button onClick={() => openForm('new')}>
                                <Plus /> Add tier
                            </Button>
                        )}
                    </>
                }
            />

            <Card>
                {path.tiers.length === 0 ? (
                    <EmptyState
                        icon={<Plus />}
                        title="No tiers yet"
                        description="Add the deciding levels in the order they escalate."
                    />
                ) : (
                    <div className="relative overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead className="border-b border-line bg-canvas text-xs text-muted">
                                <tr>
                                    <th
                                        scope="col"
                                        className="w-10 px-3 py-2 text-left font-medium"
                                    >
                                        #
                                    </th>
                                    <th
                                        scope="col"
                                        className="px-3 py-2 text-left font-medium"
                                    >
                                        Tier
                                    </th>
                                    <th
                                        scope="col"
                                        className="px-3 py-2 text-left font-medium"
                                    >
                                        Deciding role
                                    </th>
                                    {byAmount && (
                                        <th
                                            scope="col"
                                            className="px-3 py-2 text-right font-medium"
                                        >
                                            Amount range
                                        </th>
                                    )}
                                    <th
                                        scope="col"
                                        className="px-3 py-2 text-left font-medium"
                                    >
                                        Decisions
                                    </th>
                                    {canManage && (
                                        <th
                                            scope="col"
                                            className="w-32 px-3 py-2"
                                        >
                                            <span className="sr-only">
                                                Actions
                                            </span>
                                        </th>
                                    )}
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-line">
                                {path.tiers.map((t, i) => (
                                    <tr
                                        key={t.id}
                                        className="hover:bg-canvas/60"
                                    >
                                        <td className="px-3 py-1.5 text-muted tabular-nums">
                                            {i + 1}
                                        </td>
                                        <td className="px-3 py-1.5 font-medium">
                                            {t.label ?? '–'}
                                        </td>
                                        <td className="px-3 py-1.5">
                                            {t.role}
                                        </td>
                                        {byAmount && (
                                            <td className="px-3 py-1.5 text-right whitespace-nowrap tabular-nums">
                                                {rupiah(t.min_amount ?? 0)} –{' '}
                                                {t.max_amount === null
                                                    ? 'no limit'
                                                    : rupiah(t.max_amount)}
                                            </td>
                                        )}
                                        <td className="px-3 py-1.5">
                                            <span className="flex flex-wrap gap-1">
                                                {DECISIONS.filter(
                                                    ([key]) => t[key],
                                                ).map(([key, label]) => (
                                                    <Badge
                                                        key={key}
                                                        tone={
                                                            key ===
                                                            'can_approve'
                                                                ? 'success'
                                                                : key ===
                                                                    'can_reject'
                                                                  ? 'danger'
                                                                  : key ===
                                                                      'can_escalate'
                                                                    ? 'info'
                                                                    : 'neutral'
                                                        }
                                                    >
                                                        {label}
                                                    </Badge>
                                                ))}
                                            </span>
                                        </td>
                                        {canManage && (
                                            <td className="px-3 py-1.5 text-right whitespace-nowrap">
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    aria-label="Move up"
                                                    disabled={i === 0}
                                                    onClick={() =>
                                                        router.put(
                                                            `${base}/${t.id}/move/up`,
                                                            {},
                                                            {
                                                                preserveScroll: true,
                                                            },
                                                        )
                                                    }
                                                >
                                                    <ArrowUp />
                                                </Button>
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    aria-label="Move down"
                                                    disabled={
                                                        i ===
                                                        path.tiers.length - 1
                                                    }
                                                    onClick={() =>
                                                        router.put(
                                                            `${base}/${t.id}/move/down`,
                                                            {},
                                                            {
                                                                preserveScroll: true,
                                                            },
                                                        )
                                                    }
                                                >
                                                    <ArrowDown />
                                                </Button>
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    aria-label={`Edit ${t.role}`}
                                                    onClick={() => openForm(t)}
                                                >
                                                    <Pencil />
                                                </Button>
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    aria-label={`Delete ${t.role}`}
                                                    onClick={() =>
                                                        setToDelete(t)
                                                    }
                                                >
                                                    <Trash2 />
                                                </Button>
                                            </td>
                                        )}
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </Card>

            <Modal
                open={editing !== null}
                onOpenChange={(open) => !open && setEditing(null)}
                title={editing === 'new' ? 'Add tier' : 'Edit tier'}
                description={
                    byAmount
                        ? 'Authority follows the amount range.'
                        : 'Tiers escalate in order; only the last deciding tier decides.'
                }
            >
                <form onSubmit={submit} noValidate>
                    <div className="flex flex-col gap-3 p-4">
                        <Field label="Tier name" error={form.errors.label}>
                            <Input
                                value={form.data.label}
                                maxLength={50}
                                onChange={(e) =>
                                    form.setData('label', e.target.value)
                                }
                                placeholder="e.g. Committee I"
                            />
                        </Field>
                        <Field
                            label="Deciding role"
                            required
                            error={form.errors.role}
                        >
                            <Combobox
                                options={roles.map((r) => ({
                                    value: r,
                                    label: r,
                                }))}
                                value={form.data.role}
                                onChange={(v) => form.setData('role', v ?? '')}
                                invalid={!!form.errors.role}
                            />
                        </Field>
                        {byAmount && (
                            <div className="grid grid-cols-2 gap-3">
                                <Field
                                    label="Minimum amount"
                                    error={form.errors.min_amount}
                                    hint={
                                        form.data.min_amount !== ''
                                            ? rupiah(
                                                  Number(form.data.min_amount),
                                              )
                                            : undefined
                                    }
                                >
                                    <Input
                                        type="number"
                                        min={0}
                                        value={form.data.min_amount}
                                        onChange={(e) =>
                                            form.setData(
                                                'min_amount',
                                                e.target.value,
                                            )
                                        }
                                        aria-invalid={!!form.errors.min_amount}
                                    />
                                </Field>
                                <Field
                                    label="Maximum amount"
                                    error={form.errors.max_amount}
                                    hint={
                                        form.data.max_amount !== ''
                                            ? rupiah(
                                                  Number(form.data.max_amount),
                                              )
                                            : 'Empty = no limit'
                                    }
                                >
                                    <Input
                                        type="number"
                                        min={0}
                                        value={form.data.max_amount}
                                        onChange={(e) =>
                                            form.setData(
                                                'max_amount',
                                                e.target.value,
                                            )
                                        }
                                        aria-invalid={!!form.errors.max_amount}
                                    />
                                </Field>
                            </div>
                        )}
                        <fieldset>
                            <legend className="mb-1 text-xs font-medium">
                                Decisions allowed
                            </legend>
                            <div className="grid grid-cols-2 gap-1.5">
                                {DECISIONS.map(([key, label]) => (
                                    <label
                                        key={key}
                                        className="flex items-center gap-2 text-sm"
                                    >
                                        <input
                                            type="checkbox"
                                            className="accent-primary"
                                            checked={form.data[key]}
                                            onChange={(e) =>
                                                form.setData(
                                                    key,
                                                    e.target.checked,
                                                )
                                            }
                                        />
                                        {label}
                                    </label>
                                ))}
                            </div>
                        </fieldset>
                    </div>
                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={() => setEditing(null)}
                        >
                            Cancel
                        </Button>
                        <Button type="submit" loading={form.processing}>
                            {editing === 'new' ? 'Add' : 'Save'}
                        </Button>
                    </DialogFooter>
                </form>
            </Modal>

            <ConfirmDialog
                open={toDelete !== null}
                onOpenChange={(open) => !open && setToDelete(null)}
                title="Delete tier?"
                description={
                    <>
                        This removes the tier <strong>{toDelete?.role}</strong>{' '}
                        from this path.
                    </>
                }
                onConfirm={() => {
                    if (toDelete) {
                        router.delete(`${base}/${toDelete.id}`, {
                            preserveScroll: true,
                            onFinish: () => setToDelete(null),
                        });
                    }
                }}
            />
        </>
    );
}
