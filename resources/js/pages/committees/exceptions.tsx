import { Head } from '@inertiajs/react';
import { CommitteeTabs } from '@/components/committee-tabs';
import { Card, CardBody, CardHeader, PageHeader } from '@/components/ui/misc';
import { AuthorityCheckButton } from '@/pages/committees/authority-check';
import type { AuthorityOptions } from '@/pages/committees/authority-check';

type Props = {
    members: { total: number; withoutNik: number };
    authority: AuthorityOptions;
};

const RULES = [
    'Pemohon dikenali dari NIK saat kredit diajukan. Berkas juga bisa ditandai manual.',
    'Tidak ada yang ikut memutus berkasnya sendiri. Bila jenjang yang seharusnya memutus adalah milik pemohon, jenjang di atasnya yang memutus.',
    'Bila anggota komite tertinggi adalah pemohon, jenjang tertinggi di bawahnya yang memutus dan berkas ditandai sebagai pengecualian.',
    'Bila peran pemohon dipegang orang lain (misalnya Kasi Analis kedua), orang itu yang bertindak dan jenjangnya tetap.',
    'Pemohon tidak pernah bisa menjadi Kasi Analis atau surveyor berkasnya sendiri.',
    'Cakupan: pemohon sendiri. Keluarga dekat masih dalam rencana.',
];

export default function CommitteeExceptions({ members, authority }: Props) {
    return (
        <>
            <Head title="Pengecualian Komite" />
            <PageHeader title="Data Komite" description="Bila pemohon adalah anggota komite" actions={<AuthorityCheckButton options={authority} />} />
            <CommitteeTabs current="exceptions" />

            <Card>
                <CardHeader
                    title="Aturan pemohon anggota komite"
                    description={`${members.total} anggota komite, ${members.withoutNik} belum punya NIK. Pakai Cek wewenang dan pilih pemohon untuk melihat bagaimana sebuah berkas akan diputus.`}
                />
                <CardBody>
                    <ul className="flex list-disc flex-col gap-1.5 pl-4 text-sm text-muted">
                        {RULES.map((rule) => (
                            <li key={rule}>{rule}</li>
                        ))}
                    </ul>
                </CardBody>
            </Card>
        </>
    );
}
