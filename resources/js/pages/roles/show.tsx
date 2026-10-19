import { Head, router, useForm } from '@inertiajs/react';
import { ArrowLeft, Lock, Save, Trash2 } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/choice';
import { ConfirmDialog } from '@/components/ui/dialog';
import { Field } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Alert, Card, CardBody, CardFooter, CardHeader, PageHeader } from '@/components/ui/misc';

type Matrix = {
    group: string;
    modules: {
        key: string;
        label: string;
        abilities: { name: string; label: string }[];
    }[];
}[];

type Props = {
    role: { id: number; name: string; locked: boolean; named_by_code: boolean; holders: number };
    matrix: Matrix;
    granted: string[];
    canManage: boolean;
};

export default function RoleShow({ role, matrix, granted, canManage }: Props) {
    const editable = canManage && !role.locked;
    const form = useForm({ permissions: granted });
    const rename = useForm({ name: role.name });
    const [confirmDelete, setConfirmDelete] = useState(false);
    const [deleting, setDeleting] = useState(false);
    // The name is the key Codex and the code use: it stays while the code refers to it or people hold it, and the role cannot be deleted then either.
    const fixed = role.named_by_code || role.holders > 0;

    const toggle = (name: string, checked: boolean) => {
        let next = checked ? [...form.data.permissions, name] : form.data.permissions.filter((p) => p !== name);
        // Managing implies viewing; removing view removes manage.
        if (checked && name.endsWith('.manage')) {
            next = [...new Set([...next, name.replace('.manage', '.view')])];
        }
        if (!checked && name.endsWith('.view')) {
            next = next.filter((p) => p !== name.replace('.view', '.manage'));
        }
        form.setData('permissions', next);
    };

    const isChecked = (name: string) => role.locked || form.data.permissions.includes(name);

    return (
        <>
            <Head title={role.name} />
            <PageHeader
                title={role.name}
                description={role.locked ? 'Peran ini selalu punya semua izin dan tidak bisa diubah.' : 'Pilih apa saja yang boleh dilakukan peran ini'}
                actions={
                    <>
                        <Button variant="outline" onClick={() => router.visit('/roles')}>
                            <ArrowLeft /> Kembali
                        </Button>
                        {editable && (
                            <>
                                {!fixed && (
                                    <Button variant="outline" onClick={() => setConfirmDelete(true)}>
                                        <Trash2 /> Hapus
                                    </Button>
                                )}
                                <Button loading={form.processing} onClick={() => form.put(`/roles/${role.id}/permissions`, { preserveScroll: true })}>
                                    <Save /> Simpan izin
                                </Button>
                            </>
                        )}
                    </>
                }
            />

            {editable && (
                <Card className="mb-3 max-w-xl">
                    <form
                        noValidate
                        onSubmit={(e) => {
                            e.preventDefault();
                            rename.put(`/roles/${role.id}`, {
                                preserveScroll: true,
                            });
                        }}
                    >
                        <CardHeader title="Nama peran" />
                        <CardBody className="flex flex-col gap-3">
                            {fixed && (
                                <Alert tone="neutral">
                                    {role.named_by_code
                                        ? 'Kode aplikasi memakai nama peran ini, jadi namanya tidak bisa diubah dan perannya tidak bisa dihapus.'
                                        : `${role.holders} pengguna (termasuk yang nonaktif) memegang peran ini. Codex mencocokkan peran lewat nama, jadi namanya tidak bisa diubah dan perannya tidak bisa dihapus selagi dipegang.`}
                                </Alert>
                            )}
                            <Field label="Nama" required error={rename.errors.name}>
                                <Input
                                    id="role-name"
                                    maxLength={100}
                                    disabled={fixed}
                                    value={rename.data.name}
                                    onChange={(e) => rename.setData('name', e.target.value)}
                                />
                            </Field>
                        </CardBody>
                        {!fixed && (
                            <CardFooter className="justify-end">
                                <Button type="submit" loading={rename.processing}>
                                    Ubah nama
                                </Button>
                            </CardFooter>
                        )}
                    </form>
                </Card>
            )}

            <div className="grid gap-3 lg:grid-cols-2">
                {matrix.map((group) => (
                    <Card key={group.group}>
                        <CardHeader title={group.group} actions={role.locked && <Lock className="size-3 text-muted" aria-label="Terkunci" />} />
                        <ul className="divide-y divide-line">
                            {group.modules.map((module) => (
                                <li key={module.key} className="flex flex-wrap items-center justify-between gap-2 px-3 py-1.5 text-sm">
                                    <span className="min-w-0">{module.label}</span>
                                    <span className="flex items-center gap-3">
                                        {module.abilities.map((ability) => (
                                            <Checkbox
                                                key={ability.name}
                                                label={ability.label}
                                                checked={isChecked(ability.name)}
                                                disabled={!editable}
                                                onCheckedChange={(checked) => toggle(ability.name, checked === true)}
                                            />
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
                onOpenChange={(open) => !open && !deleting && setConfirmDelete(false)}
                loading={deleting}
                title="Hapus peran?"
                description={
                    <>
                        Peran <strong>{role.name}</strong> akan dihapus permanen. Peran yang masih dipakai pengguna tidak bisa dihapus.
                    </>
                }
                onConfirm={() =>
                    router.delete(`/roles/${role.id}`, {
                        onStart: () => setDeleting(true),
                        onFinish: () => {
                            setDeleting(false);
                            setConfirmDelete(false);
                        },
                    })
                }
            />
        </>
    );
}
