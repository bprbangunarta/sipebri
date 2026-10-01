import { Head, router, useForm } from '@inertiajs/react';
import { CircleAlert, Pencil, UserRound, X } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { ConfirmDialog, DialogFooter, Modal } from '@/components/ui/dialog';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { Field } from '@/components/ui/field';
import { FilterBar, SearchInput } from '@/components/ui/filter-bar';
import { Input } from '@/components/ui/input';
import { Badge, PageHeader } from '@/components/ui/misc';
import type { PageMeta } from '@/components/ui/pagination';
import { Tip } from '@/components/ui/tooltip';
import { useListQuery } from '@/hooks/use-list-query';

type Member = {
    id: number;
    name: string;
    username: string | null;
    role: string | null;
    office: string | null;
    nik: string | null;
    nik_source: 'codex' | 'manual' | null;
};
type Filters = { search: string; role: string | null; per_page: number };
type Props = {
    members: { data: Member[] } & PageMeta;
    filters: Filters;
    roles: string[];
    withoutNik: number;
};

const DEFAULTS = { per_page: 25 };

export default function CommitteeMembers({
    members,
    filters,
    roles,
    withoutNik,
}: Props) {
    const [editing, setEditing] = useState<Member | null>(null);
    const [removing, setRemoving] = useState<Member | null>(null);
    const form = useForm({ nik: '' });
    const { visit, search, onSearch, clear, loading, error } =
        useListQuery<Filters>({
            url: '/committees/members',
            filters,
            defaults: DEFAULTS,
            only: ['members', 'filters', 'withoutNik'],
        });
    const hasFilters = Boolean(filters.search || filters.role);

    const columns: Column<Member>[] = [
        {
            key: 'name',
            header: 'Member',
            cell: (m) => (
                <>
                    <p className="font-medium">{m.name}</p>
                    <p className="text-xs text-muted">{m.username ?? '–'}</p>
                </>
            ),
        },
        { key: 'role', header: 'Committee role', cell: (m) => m.role ?? '–' },
        {
            key: 'office',
            header: 'Office',
            hideBelow: 'md',
            cell: (m) => m.office ?? '–',
        },
        {
            key: 'nik',
            header: 'National ID (NIK)',
            cell: (m) =>
                m.nik ? (
                    <span className="flex flex-wrap items-center gap-1.5">
                        <span className="font-mono text-xs">{m.nik}</span>
                        <Badge>
                            {m.nik_source === 'codex' ? 'Codex' : 'Typed'}
                        </Badge>
                    </span>
                ) : (
                    <Badge tone="warning">Not set</Badge>
                ),
        },
        {
            key: 'actions',
            header: 'Actions',
            srOnly: true,
            narrow: true,
            align: 'right',
            className: 'whitespace-nowrap',
            cell: (m) => (
                <>
                    <Tip label="Type or replace the NIK">
                        <Button
                            variant="ghost"
                            size="icon"
                            aria-label={`Edit NIK of ${m.name}`}
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
                        <Tip label="Remove the NIK">
                            <Button
                                variant="ghost"
                                size="icon"
                                aria-label={`Remove NIK of ${m.name}`}
                                onClick={() => setRemoving(m)}
                            >
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
            <Head title="Committee members" />
            <PageHeader
                title="Committee members"
                description="People whose role decides in a committee tier. Their national ID lets the system recognise them when they apply for a credit."
            />

            {withoutNik > 0 && (
                <div
                    role="status"
                    className="mb-3 flex items-start gap-2 rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-800"
                >
                    <CircleAlert className="mt-0.5 size-3.5 shrink-0" />
                    <span>
                        {withoutNik}{' '}
                        {withoutNik === 1 ? 'member has' : 'members have'} no
                        NIK yet, so a credit applied for by{' '}
                        {withoutNik === 1 ? 'them' : 'any of them'} cannot be
                        recognised automatically. The NIK normally comes from
                        Codex; until it does, type it here.
                    </span>
                </div>
            )}

            <DataTable
                rows={members.data}
                rowKey={(m) => m.id}
                loading={loading}
                error={error}
                onRetry={() => visit({})}
                toolbar={
                    <FilterBar
                        search={
                            <SearchInput
                                value={search}
                                onChange={onSearch}
                                placeholder="Search name or username…"
                                label="Search members"
                            />
                        }
                    >
                        <Combobox
                            className="w-full sm:w-56"
                            clearable
                            placeholder="Role"
                            options={roles.map((r) => ({ value: r, label: r }))}
                            value={filters.role}
                            onChange={(v) => visit({ role: v })}
                        />
                        {hasFilters && (
                            <Button
                                variant="ghost"
                                size="sm"
                                onClick={() => clear({ role: null })}
                            >
                                <X /> Reset
                            </Button>
                        )}
                    </FilterBar>
                }
                columns={columns}
                empty={{
                    icon: <UserRound />,
                    title: hasFilters
                        ? 'No members match your filters'
                        : 'No committee members yet',
                    description: hasFilters
                        ? undefined
                        : 'Members appear once a role is given to a tier of a committee path.',
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
                title={`NIK of ${editing?.name ?? ''}`}
                description="16 digits. Only the last four are shown afterwards."
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
                    <div className="p-4">
                        <Field
                            label="National ID (NIK)"
                            required
                            error={form.errors.nik}
                            hint={
                                editing?.nik_source === 'codex'
                                    ? 'This NIK came from Codex; Codex replaces what is typed here the next time it sends one.'
                                    : undefined
                            }
                        >
                            <Input
                                autoFocus
                                inputMode="numeric"
                                maxLength={16}
                                value={form.data.nik}
                                onChange={(e) =>
                                    form.setData(
                                        'nik',
                                        e.target.value.replace(/\D/g, ''),
                                    )
                                }
                                aria-invalid={!!form.errors.nik}
                            />
                        </Field>
                    </div>
                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={() => setEditing(null)}
                        >
                            Cancel
                        </Button>
                        <Button
                            type="submit"
                            loading={form.processing}
                            disabled={form.data.nik.length !== 16}
                        >
                            Save
                        </Button>
                    </DialogFooter>
                </form>
            </Modal>

            <ConfirmDialog
                open={removing !== null}
                onOpenChange={(open) => !open && setRemoving(null)}
                title="Remove the NIK?"
                description={
                    <>
                        <strong>{removing?.name}</strong> will no longer be
                        recognised when applying for a credit, until a NIK is
                        set again.
                    </>
                }
                confirmLabel="Remove"
                onConfirm={() => {
                    const member = removing;
                    setRemoving(null);

                    if (member) {
                        router.delete(`/committees/members/${member.id}`, {
                            preserveScroll: true,
                        });
                    }
                }}
            />
        </>
    );
}
