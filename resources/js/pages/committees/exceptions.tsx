import { Head, router } from '@inertiajs/react';
import { Scale, Users } from 'lucide-react';
import { useState } from 'react';
import { CommitteeTabs } from '@/components/committee-tabs';
import { Button } from '@/components/ui/button';
import { PageHeader } from '@/components/ui/misc';
import { AuthorityCheck } from '@/pages/committees/authority-check';

type Option = { value: number | string; label: string };

type Props = {
    members: { total: number; withoutNik: number };
    productOptions: Option[];
    conditionMap: Record<string, string[]>;
    committeeMembers: Option[];
};

const RULES = [
    'Pemohon dikenali dari NIK saat kredit diajukan. Berkas juga bisa ditandai manual.',
    'Tidak ada yang ikut memutus berkasnya sendiri. Bila jenjang yang seharusnya memutus adalah milik pemohon, jenjang di atasnya yang memutus.',
    'Bila anggota komite tertinggi adalah pemohon, jenjang tertinggi di bawahnya yang memutus dan berkas ditandai sebagai pengecualian.',
    'Bila peran pemohon dipegang orang lain (misalnya Kasi Analis kedua), orang itu yang bertindak dan jenjangnya tetap.',
    'Pemohon tidak pernah bisa menjadi Kasi Analis atau surveyor berkasnya sendiri.',
    'Cakupan: pemohon sendiri. Keluarga dekat masih dalam rencana.',
];

export default function CommitteeExceptions({
    members,
    productOptions,
    conditionMap,
    committeeMembers,
}: Props) {
    const [checking, setChecking] = useState(false);

    return (
        <>
            <Head title="Pengecualian Komite" />
            <PageHeader
                title="Data Komite"
                description="Bila pemohon adalah anggota komite"
                actions={
                    <>
                        <Button
                            variant="outline"
                            onClick={() => router.visit('/committees/members')}
                        >
                            <Users /> Anggota
                        </Button>
                        <Button onClick={() => setChecking(true)}>
                            <Scale /> Cek wewenang
                        </Button>
                    </>
                }
            />
            <CommitteeTabs current="exceptions" />

            <ul className="border-border flex list-disc flex-col gap-1.5 rounded-lg border bg-surface py-3 pr-3 pl-7 text-sm text-muted">
                {RULES.map((rule) => (
                    <li key={rule}>{rule}</li>
                ))}
            </ul>
            <p className="mt-3 text-sm text-muted">
                {members.total} anggota komite · {members.withoutNik} belum
                punya NIK. Pakai Cek wewenang dan pilih pemohon untuk melihat
                bagaimana sebuah berkas akan diputus.
            </p>

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
