import { Head, router } from '@inertiajs/react';
import { Scale } from 'lucide-react';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { Badge, PageHeader } from '@/components/ui/misc';

type Item = {
    key: string;
    name: string;
    summary: string;
    paths: number | null;
    own: number | null;
};

export default function CommitteeMechanisms({ items }: { items: Item[] }) {
    const columns: Column<Item>[] = [
        {
            key: 'name',
            header: 'Mechanism',
            className: 'font-medium',
            cell: (i) => i.name,
        },
        {
            key: 'summary',
            header: 'How it works',
            hideBelow: 'sm',
            className: 'text-muted',
            cell: (i) => i.summary,
        },
        {
            key: 'paths',
            header: 'Paths',
            align: 'right',
            className: 'tabular-nums',
            cell: (i) => i.paths ?? '–',
        },
        {
            key: 'own',
            header: 'Own levels',
            align: 'right',
            hideBelow: 'md',
            cell: (i) =>
                i.own === null ? (
                    '–'
                ) : (
                    <Badge tone={i.own > 0 ? 'info' : 'neutral'}>{i.own}</Badge>
                ),
        },
    ];

    return (
        <>
            <Head title="Committee mechanism" />
            <PageHeader
                title="Committee mechanism"
                description="How the authority to decide a loan is worked out"
            />
            <DataTable
                rows={items}
                rowKey={(i) => i.key}
                columns={columns}
                onRowClick={(i) =>
                    router.visit(`/committees/mechanism/${i.key}`)
                }
                empty={{ icon: <Scale />, title: 'No mechanisms' }}
            />
        </>
    );
}
