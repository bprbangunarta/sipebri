import { Head, router } from '@inertiajs/react';
import { Scale, SlidersHorizontal } from 'lucide-react';
import { useState } from 'react';
import { CommitteeTabs } from '@/components/committee-tabs';
import { Button } from '@/components/ui/button';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { Badge, PageHeader } from '@/components/ui/misc';
import { rupiah } from '@/lib/format';
import { AuthorityCheck } from '@/pages/committees/authority-check';

type Option = { value: number | string; label: string };

type Level = {
    id: number;
    label: string | null;
    role: string;
    is_individual: boolean;
    min_amount: number | null;
    max_amount: number | null;
    can_escalate: boolean;
    can_approve: boolean;
    can_cancel: boolean;
    can_reject: boolean;
    people: number;
};

type Props = {
    defaultId: number | null;
    levels: Level[];
    followers: number;
    special: number;
    productOptions: Option[];
    conditionMap: Record<string, string[]>;
    committeeMembers: Option[];
    canManage: boolean;
};

const DECISIONS = [
    ['can_escalate', 'Naikkan'],
    ['can_approve', 'Setujui'],
    ['can_cancel', 'Batalkan'],
    ['can_reject', 'Tolak'],
] as const;

export default function CommitteeLevels({
    defaultId,
    levels,
    followers,
    special,
    productOptions,
    conditionMap,
    committeeMembers,
    canManage,
}: Props) {
    const [checking, setChecking] = useState(false);

    const columns: Column<Level>[] = [
        {
            key: 'order',
            header: '#',
            narrow: true,
            className: 'text-muted tabular-nums',
            cell: (_l, i) => i + 1,
        },
        {
            key: 'level',
            header: 'Jenjang',
            className: 'font-medium',
            cell: (l) => l.label ?? '–',
        },
        { key: 'role', header: 'Pemutus', cell: (l) => l.role },
        {
            key: 'kind',
            header: 'Jenis',
            hideBelow: 'sm',
            cell: (l) => (
                <Badge tone={l.is_individual ? 'info' : 'neutral'}>
                    {l.is_individual ? 'Perorangan' : 'Komite'}
                </Badge>
            ),
        },
        {
            key: 'range',
            header: 'Plafon',
            align: 'right',
            className: 'whitespace-nowrap tabular-nums',
            cell: (l) =>
                `${rupiah(l.min_amount ?? 0)} – ${l.max_amount === null ? 'tanpa batas' : rupiah(l.max_amount)}`,
        },
        {
            key: 'decisions',
            header: 'Keputusan',
            hideBelow: 'md',
            cell: (l) => (
                <span className="flex flex-wrap gap-1">
                    {DECISIONS.filter(([k]) => l[k]).map(([k, label]) => (
                        <Badge
                            key={k}
                            tone={
                                k === 'can_approve'
                                    ? 'success'
                                    : k === 'can_reject'
                                      ? 'danger'
                                      : k === 'can_escalate'
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
        {
            key: 'people',
            header: 'Orang',
            align: 'right',
            hideBelow: 'sm',
            className: 'tabular-nums',
            cell: (l) => l.people,
        },
    ];

    return (
        <>
            <Head title="Data Komite" />
            <PageHeader
                title="Data Komite"
                description="Siapa yang berwenang memutus kredit, menurut plafon"
                actions={
                    <>
                        <Button
                            variant="outline"
                            onClick={() => setChecking(true)}
                        >
                            <Scale /> Cek wewenang
                        </Button>
                        {canManage && defaultId && (
                            <Button
                                onClick={() =>
                                    router.visit(`/committees/${defaultId}`)
                                }
                            >
                                <SlidersHorizontal /> Ubah jenjang
                            </Button>
                        )}
                    </>
                }
            />
            <CommitteeTabs current="levels" />

            <DataTable
                rows={levels}
                rowKey={(l) => l.id}
                columns={columns}
                empty={{
                    icon: <SlidersHorizontal />,
                    title: 'Belum ada jenjang',
                    description:
                        'Jalankan seeder komite untuk membuat jenjang bawaan.',
                }}
            />

            <div className="mt-4 grid gap-3 text-sm text-muted md:grid-cols-3">
                <p className="border-border rounded-lg border bg-surface p-3">
                    <span className="mb-1 block font-medium text-ink">
                        Menurut plafon
                    </span>
                    Berkas masuk ke jenjang yang rentang plafonnya mencakup
                    kredit; jenjang di bawahnya hanya menaikkan. {followers}{' '}
                    jalur mengikuti jenjang ini, jadi mengubahnya di sini
                    berlaku untuk semuanya.
                </p>
                <p className="border-border rounded-lg border bg-surface p-3">
                    <span className="mb-1 block font-medium text-ink">
                        Perorangan atau komite
                    </span>
                    Jenjang perorangan (staf analis) diputus oleh orang yang
                    memegang berkas. Jenjang komite diputus oleh komitenya,
                    sehingga hanya mereka yang masuk daftar Anggota.
                </p>
                <p className="border-border rounded-lg border bg-surface p-3">
                    <span className="mb-1 block font-medium text-ink">
                        Aturan khusus
                    </span>
                    {special} jalur memakai hierarki (semua komite dilewati
                    berurutan, hanya yang terakhir memutus) atau batas sendiri.
                    Lihat tab Aturan khusus.
                </p>
            </div>

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
