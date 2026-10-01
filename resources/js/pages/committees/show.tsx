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
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { Badge, PageHeader } from '@/components/ui/misc';
import { Tip } from '@/components/ui/tooltip';
import { rupiah } from '@/lib/format';
import type { PathRow } from '@/pages/committees/special-rules';

type Tier = {
    id: number;
    sort: number;
    label: string | null;
    role: string;
    is_individual: boolean;
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
    ['can_escalate', 'Naikkan'],
    ['can_approve', 'Setujui'],
    ['can_cancel', 'Batalkan'],
    ['can_reject', 'Tolak'],
] as const;

export default function CommitteeShow({ path, roles, canManage }: Props) {
    const [editing, setEditing] = useState<Tier | 'new' | null>(null);
    const [toDelete, setToDelete] = useState<Tier | null>(null);
    const [following, setFollowing] = useState(false);
    const byAmount = path.mechanism === 'plafon';
    const locked = path.follows_default;
    const editable = canManage && !locked;
    const form = useForm({
        label: '',
        role: '',
        is_individual: false,
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
                      is_individual: false,
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
                      is_individual: tier.is_individual,
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

    const columns: Column<Tier>[] = [
        {
            key: 'order',
            header: '#',
            narrow: true,
            className: 'text-muted tabular-nums',
            cell: (_t, i) => i + 1,
        },
        {
            key: 'tier',
            header: 'Jenjang',
            className: 'font-medium',
            cell: (t) => t.label ?? '–',
        },
        {
            key: 'role',
            header: 'Peran pemutus',
            cell: (t) => (
                <span className="flex flex-wrap items-center gap-1.5">
                    {t.role}
                    {t.is_individual && <Badge tone="info">Perorangan</Badge>}
                </span>
            ),
        },
        ...(byAmount
            ? [
                  {
                      key: 'range',
                      header: 'Rentang plafon',
                      align: 'right',
                      className: 'whitespace-nowrap tabular-nums',
                      cell: (t: Tier) =>
                          `${rupiah(t.min_amount ?? 0)} – ${t.max_amount === null ? 'tanpa batas' : rupiah(t.max_amount)}`,
                  } satisfies Column<Tier>,
              ]
            : []),
        {
            key: 'decisions',
            header: 'Keputusan',
            cell: (t) => (
                <span className="flex flex-wrap gap-1">
                    {DECISIONS.filter(([key]) => t[key]).map(([key, label]) => (
                        <Badge
                            key={key}
                            tone={
                                key === 'can_approve'
                                    ? 'success'
                                    : key === 'can_reject'
                                      ? 'danger'
                                      : key === 'can_escalate'
                                        ? 'info'
                                        : 'neutral'
                            }
                        >
                            {label}
                        </Badge>
                    ))}
                </span>
            ),
        },
        ...(editable
            ? [
                  {
                      key: 'actions',
                      header: 'Aksi',
                      srOnly: true,
                      align: 'right',
                      className: 'whitespace-nowrap',
                      cell: (t: Tier, i: number) => (
                          <>
                              <Tip label="Naikkan urutan">
                                  <Button
                                      variant="ghost"
                                      size="icon"
                                      aria-label="Naikkan urutan"
                                      disabled={i === 0}
                                      onClick={() =>
                                          router.put(
                                              `${base}/${t.id}/move/up`,
                                              {},
                                              { preserveScroll: true },
                                          )
                                      }
                                  >
                                      <ArrowUp />
                                  </Button>
                              </Tip>
                              <Tip label="Turunkan urutan">
                                  <Button
                                      variant="ghost"
                                      size="icon"
                                      aria-label="Turunkan urutan"
                                      disabled={i === path.tiers.length - 1}
                                      onClick={() =>
                                          router.put(
                                              `${base}/${t.id}/move/down`,
                                              {},
                                              { preserveScroll: true },
                                          )
                                      }
                                  >
                                      <ArrowDown />
                                  </Button>
                              </Tip>
                              <Tip label="Ubah">
                                  <Button
                                      variant="ghost"
                                      size="icon"
                                      aria-label={`Ubah ${t.role}`}
                                      onClick={() => openForm(t)}
                                  >
                                      <Pencil />
                                  </Button>
                              </Tip>
                              <Tip label="Hapus">
                                  <Button
                                      variant="ghost"
                                      size="icon"
                                      aria-label={`Hapus ${t.role}`}
                                      onClick={() => setToDelete(t)}
                                  >
                                      <Trash2 />
                                  </Button>
                              </Tip>
                          </>
                      ),
                  } satisfies Column<Tier>,
              ]
            : []),
    ];

    return (
        <>
            <Head title={path.title} />
            <PageHeader
                title={path.title}
                description={`${path.product_label} · ${path.mechanism_label}${path.note ? ` · ${path.note}` : ''}`}
                actions={
                    <>
                        <Badge tone={path.is_active ? 'success' : 'neutral'}>
                            {path.is_active ? 'Aktif' : 'Nonaktif'}
                        </Badge>
                        <Button
                            variant="outline"
                            onClick={() =>
                                router.visit('/committees/special-rules')
                            }
                        >
                            <ArrowLeft /> Kembali
                        </Button>
                        {editable && (
                            <Button onClick={() => openForm('new')}>
                                <Plus /> Tambah jenjang
                            </Button>
                        )}
                    </>
                }
            />

            {path.is_default && (
                <p className="mb-3 text-sm text-muted">
                    Jenjang ini dipakai bersama oleh {path.followers ?? 0} jalur
                    yang mengikuti bawaan. Setiap perubahan di sini berlaku
                    untuk semuanya. Pada jalur hierarki hanya urutan peran yang
                    dipakai.
                </p>
            )}
            {locked && canManage && (
                <div className="border-border mb-3 flex flex-wrap items-center justify-between gap-2 rounded-md border bg-surface px-3 py-2 text-sm">
                    <span>
                        Jalur ini mengikuti{' '}
                        <a
                            className="text-primary hover:underline"
                            href="/committees/levels"
                            onClick={(e) => {
                                e.preventDefault();
                                router.visit('/committees/levels');
                            }}
                        >
                            jenjang wewenang bawaan
                        </a>
                        . Beri jenjang sendiri untuk mengubahnya di sini.
                    </span>
                    <Button
                        size="sm"
                        variant="outline"
                        onClick={() =>
                            router.put(
                                `/committees/${path.id}/follow`,
                                { follow: false },
                                { preserveScroll: true },
                            )
                        }
                    >
                        Atur sendiri
                    </Button>
                </div>
            )}
            {!path.is_default && !locked && canManage && (
                <div className="border-border mb-3 flex flex-wrap items-center justify-between gap-2 rounded-md border bg-surface px-3 py-2 text-sm">
                    <span>Jalur ini memakai jenjang wewenang sendiri.</span>
                    <Button
                        size="sm"
                        variant="outline"
                        onClick={() => setFollowing(true)}
                    >
                        Ikuti bawaan
                    </Button>
                </div>
            )}

            <DataTable
                rows={path.tiers}
                rowKey={(t) => t.id}
                columns={columns}
                empty={{
                    icon: <Plus />,
                    title: 'Belum ada jenjang',
                    description:
                        'Tambahkan jenjang pemutus sesuai urutan naiknya.',
                }}
            />

            <Modal
                open={editing !== null}
                onOpenChange={(open) => !open && setEditing(null)}
                title={editing === 'new' ? 'Tambah jenjang' : 'Ubah jenjang'}
                description={
                    byAmount
                        ? 'Wewenang mengikuti rentang plafon.'
                        : 'Jenjang naik berurutan; hanya jenjang pemutus terakhir yang memutus.'
                }
            >
                <form onSubmit={submit} noValidate>
                    <div className="flex flex-col gap-3 p-4">
                        <Field label="Nama jenjang" error={form.errors.label}>
                            <Input
                                value={form.data.label}
                                maxLength={50}
                                onChange={(e) =>
                                    form.setData('label', e.target.value)
                                }
                                placeholder="mis. Komite I"
                            />
                        </Field>
                        <Field
                            label="Peran pemutus"
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
                        <label className="flex items-start gap-2 text-sm">
                            <input
                                type="checkbox"
                                className="mt-0.5 accent-primary"
                                checked={form.data.is_individual}
                                onChange={(e) =>
                                    form.setData(
                                        'is_individual',
                                        e.target.checked,
                                    )
                                }
                            />
                            <span>
                                Wewenang perorangan
                                <span className="block text-xs text-muted">
                                    Orang yang memegang berkas yang memutus,
                                    bukan komite. Pemegangnya tidak dimasukkan
                                    ke daftar anggota komite.
                                </span>
                            </span>
                        </label>
                        {byAmount && (
                            <div className="grid grid-cols-2 gap-3">
                                <Field
                                    label="Plafon minimum"
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
                                    label="Plafon maksimum"
                                    error={form.errors.max_amount}
                                    hint={
                                        form.data.max_amount !== ''
                                            ? rupiah(
                                                  Number(form.data.max_amount),
                                              )
                                            : 'Kosong = tanpa batas'
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
                                Keputusan yang diizinkan
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
                            Batal
                        </Button>
                        <Button type="submit" loading={form.processing}>
                            {editing === 'new' ? 'Tambah' : 'Simpan'}
                        </Button>
                    </DialogFooter>
                </form>
            </Modal>

            <ConfirmDialog
                open={following}
                onOpenChange={setFollowing}
                title="Ikuti jenjang bawaan?"
                description="Jenjang jalur ini akan diganti dengan salinan jenjang wewenang bawaan."
                confirmLabel="Ikuti bawaan"
                onConfirm={() =>
                    router.put(
                        `/committees/${path.id}/follow`,
                        { follow: true },
                        {
                            preserveScroll: true,
                            onFinish: () => setFollowing(false),
                        },
                    )
                }
            />

            <ConfirmDialog
                open={toDelete !== null}
                onOpenChange={(open) => !open && setToDelete(null)}
                title="Hapus jenjang?"
                description={
                    <>
                        Ini menghapus jenjang <strong>{toDelete?.role}</strong>{' '}
                        dari jalur ini.
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
