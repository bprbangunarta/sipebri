import { Head, router, useForm } from '@inertiajs/react';
import { CircleAlert, Pencil, UserRound, X } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { ConfirmDialog, DialogBody, DialogFooter, Modal } from '@/components/ui/dialog';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { Field } from '@/components/ui/field';
import { FilterBar, SearchInput } from '@/components/ui/filter-bar';
import { MaskedInput } from '@/components/ui/masked-input';
import { CommitteeTabs } from '@/components/committee-tabs';
import { Alert, Badge, PageHeader } from '@/components/ui/misc';
import type { PageMeta } from '@/components/ui/pagination';
import { Tip } from '@/components/ui/tooltip';
import { useListQuery } from '@/hooks/use-list-query';
import { MASKS } from '@/lib/mask';
import { AuthorityCheckButton } from '@/pages/committees/authority-check';
import type { AuthorityOptions } from '@/pages/committees/authority-check';
import { nextSort } from '@/components/ui/sort-head';

type Member = {
    id: number;
    name: string;
    username: string | null;
    role: string | null;
    office: string | null;
    nik: string | null;
    nik_source: 'codex' | 'manual' | null;
};
type Filters = { search: string; role: string | null; sort: string; direction: 'asc' | 'desc'; per_page: number };
type Props = {
    members: { data: Member[] } & PageMeta;
    filters: Filters;
    roles: string[];
    withoutNik: number;
    authority: AuthorityOptions;
};

const DEFAULTS = { sort: 'name', direction: 'asc', per_page: 25 };

export default function CommitteeMembers({ members, filters, roles, withoutNik, authority }: Props) {
    const [editing, setEditing] = useState<Member | null>(null);
    const [removing, setRemoving] = useState<Member | null>(null);
    const [busy, setBusy] = useState(false);
    const form = useForm({ nik: '' });
    const { visit, search, onSearch, clear, loading, error } = useListQuery<Filters>({
        url: '/committees/members',
        filters,
        defaults: DEFAULTS,
        only: ['members', 'filters', 'withoutNik'],
    });
    const hasFilters = Boolean(filters.search || filters.role);

    const columns: Column<Member>[] = [
        {
            key: 'name',
            header: 'Anggota',
            sort: 'name',
            className: 'font-medium',
            cell: (m) => m.name,
        },
        { key: 'username', header: 'Username', sort: 'username', cell: (m) => m.username ?? '–' },
        { key: 'role', header: 'Peran di komite', sort: 'role', cell: (m) => m.role ?? '–' },
        {
            key: 'office',
            header: 'Kantor',
            sort: 'office',
            cell: (m) => m.office ?? '–',
        },
        {
            key: 'nik',
            header: 'NIK (KTP)',
            sort: 'nik',
            cell: (m) =>
                m.nik ? (
                    <span className="flex items-center gap-1.5">
                        <span className="font-mono text-xs">{m.nik}</span>
                        <Badge>{m.nik_source === 'codex' ? 'Codex' : 'Diketik'}</Badge>
                    </span>
                ) : (
                    <Badge tone="warning">Belum diisi</Badge>
                ),
        },
        {
            key: 'actions',
            header: 'Aksi',
            srOnly: true,
            narrow: true,
            align: 'right',
            className: 'whitespace-nowrap',
            cell: (m) => (
                <>
                    <Tip label="Ketik atau ganti NIK">
                        <Button
                            variant="ghost"
                            size="icon"
                            aria-label={`Ubah NIK ${m.name}`}
                            onClick={() => {
                                form.reset();
                                form.clearErrors();
                                setEditing(m);
                            }}
                        >
                            <Pencil />
                        </Button>
                    </Tip>
                    {m.nik && (
                        <Tip label="Hapus NIK">
                            <Button variant="ghost" size="icon" aria-label={`Hapus NIK ${m.name}`} onClick={() => setRemoving(m)}>
                                <X />
                            </Button>
                        </Tip>
                    )}
                </>
            ),
        },
    ];

    return (
        <>
            <Head title="Anggota Komite" />
            <PageHeader
                title="Data Komite"
                description="Orang yang perannya memutus di jenjang komite. NIK membuat sistem mengenali mereka saat mengajukan kredit."
                actions={<AuthorityCheckButton options={authority} />}
            />
            <CommitteeTabs current="members" />

            {withoutNik > 0 && (
                <Alert tone="warning" icon={<CircleAlert />} className="mb-3">
                    {withoutNik} anggota belum punya NIK, sehingga kredit yang mereka ajukan tidak bisa dikenali otomatis. NIK biasanya datang dari Codex;
                    sampai itu terjadi, ketik di sini.
                </Alert>
            )}

            <DataTable
                rows={members.data}
                rowKey={(m) => m.id}
                loading={loading}
                error={error}
                onRetry={() => visit({})}
                toolbar={
                    <FilterBar search={<SearchInput value={search} onChange={onSearch} placeholder="Cari nama atau username…" label="Cari anggota" />}>
                        <Combobox
                            className="w-full sm:w-56"
                            clearable
                            placeholder="Peran"
                            options={roles.map((r) => ({ value: r, label: r }))}
                            value={filters.role}
                            onChange={(v) => visit({ role: v })}
                        />
                        {hasFilters && (
                            <Button variant="ghost" size="sm" onClick={() => clear({ role: null })}>
                                <X /> Atur ulang
                            </Button>
                        )}
                    </FilterBar>
                }
                columns={columns}
                sort={{ sort: filters.sort, direction: filters.direction, onSort: (column) => visit(nextSort(filters, column)) }}
                empty={{
                    icon: <UserRound />,
                    title: hasFilters ? 'Tidak ada anggota yang cocok dengan filter' : 'Belum ada anggota komite',
                    description: hasFilters ? undefined : 'Anggota muncul setelah sebuah peran dipakai di jenjang komite.',
                }}
                pagination={{
                    meta: members,
                    perPage: filters.per_page,
                    options: [10, 25, 50],
                    onPage: (page) => visit({ page }),
                    onPerPage: (per_page) => visit({ per_page }),
                }}
            />

            <Modal
                open={editing !== null}
                onOpenChange={(open) => !open && setEditing(null)}
                persistent
                title={`NIK ${editing?.name ?? ''}`}
                description="16 digit. Setelah disimpan hanya empat digit terakhir yang tampil."
            >
                <form
                    noValidate
                    onSubmit={(e) => {
                        e.preventDefault();
                        if (editing) {
                            form.put(`/committees/members/${editing.id}`, {
                                preserveScroll: true,
                                onSuccess: () => setEditing(null),
                            });
                        }
                    }}
                >
                    <DialogBody>
                        <Field
                            label="NIK (KTP)"
                            required
                            error={form.errors.nik}
                            hint={
                                editing?.nik_source === 'codex'
                                    ? 'NIK ini berasal dari Codex; Codex menggantikan yang diketik di sini saat mengirim NIK lagi.'
                                    : undefined
                            }
                        >
                            <MaskedInput autoFocus mask={MASKS.nik} value={form.data.nik} onValueChange={(v) => form.setData('nik', v)} />
                        </Field>
                    </DialogBody>
                    <DialogFooter>
                        <Button variant="outline" onClick={() => setEditing(null)}>
                            Batal
                        </Button>
                        <Button type="submit" loading={form.processing} disabled={form.data.nik.length !== 16}>
                            Simpan
                        </Button>
                    </DialogFooter>
                </form>
            </Modal>

            <ConfirmDialog
                open={removing !== null}
                onOpenChange={(open) => !open && !busy && setRemoving(null)}
                loading={busy}
                title="Hapus NIK?"
                description={
                    <>
                        NIK <strong>{removing?.name}</strong> akan dihapus, sehingga ia tidak lagi dikenali saat mengajukan kredit sampai NIK diisi lagi.
                    </>
                }
                confirmLabel="Hapus"
                onConfirm={() => {
                    if (removing) {
                        router.delete(`/committees/members/${removing.id}`, {
                            preserveScroll: true,
                            onStart: () => setBusy(true),
                            onFinish: () => {
                                setBusy(false);
                                setRemoving(null);
                            },
                        });
                    }
                }}
            />
        </>
    );
}
