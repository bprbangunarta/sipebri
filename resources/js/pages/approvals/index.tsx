import { Head } from '@inertiajs/react';
import { BadgeCheck } from 'lucide-react';
import { Card, EmptyState, PageHeader } from '@/components/ui/misc';

export default function ApprovalsIndex() {
    return (
        <>
            <Head title="Persetujuan" />
            <PageHeader
                title="Persetujuan"
                description="Keputusan komite atas berkas yang sudah dianalisa"
            />
            <Card>
                <EmptyState
                    icon={<BadgeCheck />}
                    title="Halaman ini belum tersedia"
                    description="Daftar berkas yang menunggu keputusan akan muncul di sini setelah lembar analisa selesai."
                />
            </Card>
        </>
    );
}
