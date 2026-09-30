import { Head, router, useForm } from '@inertiajs/react';
import { Lock, Plus, Settings2, Shield } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { DialogFooter, Modal } from '@/components/ui/dialog';
import { Field } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { PageHeader } from '@/components/ui/misc';
import { Tip } from '@/components/ui/tooltip';

type RoleRow = {
    id: number;
    name: string;
    users_count: number;
    permissions_count: number;
    locked: boolean;
};

const columns: Column<RoleRow>[] = [
    {
        key: 'role',
        header: 'Role',
        className: 'font-medium',
        cell: (r) => (
            <span className="inline-flex items-center gap-1.5">
                {r.name}
                {r.locked && (
                    <Lock className="size-3 text-muted" aria-label="Locked" />
                )}
            </span>
        ),
    },
    {
        key: 'users',
        header: 'Users',
        align: 'right',
        className: 'tabular-nums',
        cell: (r) => r.users_count,
    },
    {
        key: 'permissions',
        header: 'Permissions',
        align: 'right',
        className: 'tabular-nums',
        cell: (r) => (r.locked ? 'All' : r.permissions_count),
    },
    {
        key: 'actions',
        header: 'Actions',
        srOnly: true,
        narrow: true,
        align: 'right',
        cell: (r) => (
            <Tip label="Open">
                <Button
                    variant="ghost"
                    size="icon"
                    aria-label={`Open ${r.name}`}
                    onClick={() => router.visit(`/roles/${r.id}`)}
                >
                    <Settings2 />
                </Button>
            </Tip>
        ),
    },
];

export default function RolesIndex({
    roles,
    canManage,
}: {
    roles: RoleRow[];
    canManage: boolean;
}) {
    const [creating, setCreating] = useState(false);
    const form = useForm({ name: '' });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post('/roles', { onSuccess: () => setCreating(false) });
    };

    return (
        <>
            <Head title="Roles" />
            <PageHeader
                title="Roles"
                description="A role is a set of permissions; each user has exactly one role"
                actions={
                    canManage && (
                        <Button
                            onClick={() => {
                                form.reset();
                                form.clearErrors();
                                setCreating(true);
                            }}
                        >
                            <Plus /> Add role
                        </Button>
                    )
                }
            />

            <DataTable
                className="max-w-3xl"
                rows={roles}
                rowKey={(r) => r.id}
                columns={columns}
                empty={{ icon: <Shield />, title: 'No roles yet' }}
            />

            <Modal
                open={creating}
                onOpenChange={setCreating}
                title="Add role"
                description="You choose its permissions next."
            >
                <form onSubmit={submit} noValidate>
                    <div className="p-4">
                        <Field label="Name" required error={form.errors.name}>
                            <Input
                                autoFocus
                                value={form.data.name}
                                maxLength={100}
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
                            onClick={() => setCreating(false)}
                        >
                            Cancel
                        </Button>
                        <Button type="submit" loading={form.processing}>
                            Create
                        </Button>
                    </DialogFooter>
                </form>
            </Modal>
        </>
    );
}
