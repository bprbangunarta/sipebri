import { Head, router, useForm } from '@inertiajs/react';
import { Lock, Plus, Settings2, Shield } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { DialogFooter, Modal } from '@/components/ui/dialog';
import { Field } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Card, EmptyState, PageHeader } from '@/components/ui/misc';

type RoleRow = {
    id: number;
    name: string;
    users_count: number;
    permissions_count: number;
    locked: boolean;
};

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

            <Card className="max-w-3xl">
                {roles.length === 0 ? (
                    <EmptyState icon={<Shield />} title="No roles yet" />
                ) : (
                    <table className="w-full text-sm">
                        <thead className="border-b border-line bg-canvas text-xs text-muted">
                            <tr>
                                <th
                                    scope="col"
                                    className="px-3 py-2 text-left font-medium"
                                >
                                    Role
                                </th>
                                <th
                                    scope="col"
                                    className="px-3 py-2 text-right font-medium"
                                >
                                    Users
                                </th>
                                <th
                                    scope="col"
                                    className="px-3 py-2 text-right font-medium"
                                >
                                    Permissions
                                </th>
                                <th scope="col" className="w-10 px-3 py-2">
                                    <span className="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-line">
                            {roles.map((r) => (
                                <tr key={r.id} className="hover:bg-canvas/60">
                                    <td className="px-3 py-1.5 font-medium">
                                        <span className="inline-flex items-center gap-1.5">
                                            {r.name}
                                            {r.locked && (
                                                <Lock
                                                    className="size-3 text-muted"
                                                    aria-label="Locked"
                                                />
                                            )}
                                        </span>
                                    </td>
                                    <td className="px-3 py-1.5 text-right tabular-nums">
                                        {r.users_count}
                                    </td>
                                    <td className="px-3 py-1.5 text-right tabular-nums">
                                        {r.locked ? 'All' : r.permissions_count}
                                    </td>
                                    <td className="px-3 py-1.5 text-right">
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            aria-label={`Open ${r.name}`}
                                            onClick={() =>
                                                router.visit(`/roles/${r.id}`)
                                            }
                                        >
                                            <Settings2 />
                                        </Button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                )}
            </Card>

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
