import { usePage } from '@inertiajs/react';

/** Client-side convenience only; every permission is also enforced on the server. */
export function usePermission() {
    const permissions = usePage().props.auth.user.permissions;

    return (permission: string) => permissions.includes(permission);
}
