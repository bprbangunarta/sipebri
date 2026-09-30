import { Head, router, usePage } from '@inertiajs/react';
import { ArrowLeft, LogIn, LogOut, ShieldAlert } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/misc';
import { login, logout } from '@/routes';

const TEXT: Record<number, { title: string; description: string }> = {
    403: {
        title: 'Access denied',
        description:
            'Your role is not allowed to open this page. If you think this is a mistake, ask your administrator to grant the access.',
    },
    404: {
        title: 'Page not found',
        description:
            'The page you are looking for does not exist or has been moved.',
    },
    419: {
        title: 'Page expired',
        description: 'Your session expired. Reload the page and try again.',
    },
    500: {
        title: 'Something went wrong',
        description: 'An unexpected error occurred. Please try again shortly.',
    },
    503: {
        title: 'Service unavailable',
        description:
            'The service is temporarily unavailable. Please try again shortly.',
    },
};

/** Shown for 4xx/5xx responses. Always offers a way out, so nobody is stuck on a dead end. */
export default function ErrorPage({
    status,
    message,
}: {
    status: number;
    message: string;
}) {
    const user = usePage().props.auth?.user;
    const text = TEXT[status] ?? TEXT[500];

    return (
        <>
            <Head title={`${status} ${text.title}`} />
            <div className="mx-auto mt-10 max-w-sm">
                <Card className="flex flex-col items-center gap-2 p-6 text-center">
                    <ShieldAlert className="size-8 text-muted/60" />
                    <p className="text-xs font-medium text-muted">{status}</p>
                    <h1 className="text-base font-semibold">{text.title}</h1>
                    <p className="text-sm text-muted">
                        {message || text.description}
                    </p>
                    {user && (
                        <p className="text-xs text-muted">
                            Signed in as {user.name}
                            {user.role ? ` · ${user.role}` : ''}
                        </p>
                    )}
                    <div className="mt-2 flex flex-wrap justify-center gap-2">
                        <Button
                            variant="outline"
                            onClick={() => window.history.back()}
                        >
                            <ArrowLeft /> Go back
                        </Button>
                        {user ? (
                            <Button onClick={() => router.post(logout().url)}>
                                <LogOut /> Log out
                            </Button>
                        ) : (
                            <Button onClick={() => router.visit(login().url)}>
                                <LogIn /> Log in
                            </Button>
                        )}
                    </div>
                </Card>
            </div>
        </>
    );
}
