import { Head, Link, usePage } from '@inertiajs/react';
import { Lock } from 'lucide-react';
import { Card, EmptyState, PageHeader } from '@/components/ui/misc';

/** Home for a signed-in person whose role may not open any page yet: a clear message instead of an error loop. */
export default function NoAccess() {
    const user = usePage().props.auth?.user;

    return (
        <>
            <Head title="Belum ada akses" />
            <PageHeader
                title={`Selamat datang${user ? `, ${user.name}` : ''}`}
                description={user?.role ? `Peran: ${user.role}` : undefined}
            />
            <Card>
                <EmptyState
                    icon={<Lock />}
                    title="Peran Anda belum punya akses ke halaman mana pun"
                    description="Minta administrator memberikan izin untuk peran Anda lewat Data Peranan. Setelah diberikan, muat ulang halaman ini."
                    action={
                        <Link
                            href="/profile"
                            className="text-sm text-primary hover:underline"
                        >
                            Buka profil
                        </Link>
                    }
                />
            </Card>
        </>
    );
}
