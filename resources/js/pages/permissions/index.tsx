import { Head } from '@inertiajs/react';
import { KeyRound } from 'lucide-react';
import { Card, EmptyState, PageHeader } from '@/components/ui/misc';

export default function PermissionsIndex() {
    return (
        <>
            <Head title="Permissions" />
            <PageHeader
                title="Permissions"
                description="Permission items that roles can be granted"
            />

            <Card>
                <EmptyState
                    icon={<KeyRound />}
                    title="Nothing here yet"
                    description="Permission management will be added later."
                />
            </Card>
        </>
    );
}
