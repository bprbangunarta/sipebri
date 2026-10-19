import { Head, router, usePage } from '@inertiajs/react';
import { ErrorScreen } from '@/components/error-screen';
import { errorPage } from '@/lib/error-pages';
import { logout } from '@/routes';

/** Shown for 4xx/5xx responses, full-screen. Always offers a way out, so nobody is stuck on a dead end. */
export default function ErrorPage({ status, message }: { status: number; message: string }) {
    const user = usePage().props.auth?.user;

    return (
        <>
            <Head title={errorPage(status).title} />
            <ErrorScreen
                status={status}
                message={message}
                signedIn={!!user}
                footer={
                    user && (
                        <p className="mt-8 text-xs text-muted">
                            Masuk sebagai {user.name}
                            {user.role ? ` · ${user.role}` : ''} ·{' '}
                            <button type="button" className="cursor-pointer text-primary hover:underline" onClick={() => router.post(logout().url)}>
                                Keluar
                            </button>
                        </p>
                    )
                }
            />
        </>
    );
}
