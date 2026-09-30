import { Head, router, useForm } from '@inertiajs/react';
import { ArrowLeft, Lock, Save, Trash2 } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { ConfirmDialog } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Card, PageHeader } from '@/components/ui/misc';

type Matrix = {
    group: string;
    modules: {
        key: string;
        label: string;
        abilities: { name: string; label: string }[];
    }[];
}[];

type Props = {
    role: { id: number; name: string; locked: boolean };
    matrix: Matrix;
    granted: string[];
    canManage: boolean;
};

export default function RoleShow({ role, matrix, granted, canManage }: Props) {
    const editable = canManage && !role.locked;
    const form = useForm({ permissions: granted });
    const rename = useForm({ name: role.name });
    const [confirmDelete, setConfirmDelete] = useState(false);

    const toggle = (name: string, checked: boolean) => {
        let next = checked
            ? [...form.data.permissions, name]
            : form.data.permissions.filter((p) => p !== name);
        // Managing implies viewing; removing view removes manage.
        if (checked && name.endsWith('.manage')) {
            next = [...new Set([...next, name.replace('.manage', '.view')])];
        }
        if (!checked && name.endsWith('.view')) {
            next = next.filter((p) => p !== name.replace('.view', '.manage'));
        }
        form.setData('permissions', next);
    };

    const isChecked = (name: string) =>
        role.locked || form.data.permissions.includes(name);

    return (
        <>
            <Head title={role.name} />
            <PageHeader
                title={role.name}
                description={
                    role.locked
                        ? 'This role always has every permission and cannot be changed.'
                        : 'Choose what this role is allowed to do'
                }
                actions={
                    <>
                        <Button
                            variant="outline"
                            onClick={() => router.visit('/roles')}
                        >
                            <ArrowLeft /> Back
                        </Button>
                        {editable && (
                            <>
                                <Button
                                    variant="outline"
                                    onClick={() => setConfirmDelete(true)}
                                >
                                    <Trash2 /> Delete
                                </Button>
                                <Button
                                    loading={form.processing}
                                    onClick={() =>
                                        form.put(
                                            `/roles/${role.id}/permissions`,
                                            { preserveScroll: true },
                                        )
                                    }
                                >
                                    <Save /> Save permissions
                                </Button>
                            </>
                        )}
                    </>
                }
            />

            {editable && (
                <Card className="mb-3 max-w-xl p-3">
                    <form
                        className="flex items-end gap-2"
                        onSubmit={(e) => {
                            e.preventDefault();
                            rename.put(`/roles/${role.id}`, {
                                preserveScroll: true,
                            });
                        }}
                    >
                        <div className="flex-1">
                            <label
                                className="mb-1 block text-xs font-medium"
                                htmlFor="role-name"
                            >
                                Role name
                            </label>
                            <Input
                                id="role-name"
                                value={rename.data.name}
                                onChange={(e) =>
                                    rename.setData('name', e.target.value)
                                }
                                aria-invalid={!!rename.errors.name}
                            />
                            {rename.errors.name && (
                                <p className="mt-1 text-xs text-danger">
                                    {rename.errors.name}
                                </p>
                            )}
                        </div>
                        <Button
                            type="submit"
                            variant="outline"
                            loading={rename.processing}
                        >
                            Rename
                        </Button>
                    </form>
                </Card>
            )}

            <div className="grid gap-3 lg:grid-cols-2">
                {matrix.map((group) => (
                    <Card key={group.group}>
                        <h2 className="flex items-center gap-1.5 border-b border-line px-3 py-2 text-sm font-semibold">
                            {group.group}
                            {role.locked && (
                                <Lock className="size-3 text-muted" />
                            )}
                        </h2>
                        <ul className="divide-y divide-line">
                            {group.modules.map((module) => (
                                <li
                                    key={module.key}
                                    className="flex items-center justify-between gap-3 px-3 py-1.5 text-sm"
                                >
                                    <span>{module.label}</span>
                                    <span className="flex items-center gap-3">
                                        {module.abilities.map((ability) => (
                                            <label
                                                key={ability.name}
                                                className="flex items-center gap-1.5 text-xs text-muted"
                                            >
                                                <input
                                                    type="checkbox"
                                                    className="accent-primary"
                                                    checked={isChecked(
                                                        ability.name,
                                                    )}
                                                    disabled={!editable}
                                                    onChange={(e) =>
                                                        toggle(
                                                            ability.name,
                                                            e.target.checked,
                                                        )
                                                    }
                                                />
                                                {ability.label}
                                            </label>
                                        ))}
                                    </span>
                                </li>
                            ))}
                        </ul>
                    </Card>
                ))}
            </div>

            <ConfirmDialog
                open={confirmDelete}
                onOpenChange={setConfirmDelete}
                title="Delete role?"
                description={
                    <>
                        This removes the role <strong>{role.name}</strong>.
                        Roles assigned to users cannot be deleted.
                    </>
                }
                onConfirm={() => router.delete(`/roles/${role.id}`)}
            />
        </>
    );
}
