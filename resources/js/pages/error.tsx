import { Head, router, usePage } from '@inertiajs/react';
import { ArrowLeft, Home, LogIn } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { dashboard, login, logout } from '@/routes';

const TEXT: Record<number, { title: string; description: string }> = {
    403: {
        title: 'Akses ditolak',
        description:
            'Peran Anda tidak diizinkan membuka halaman ini. Bila ini keliru, minta administrator memberikan aksesnya.',
    },
    404: {
        title: 'Halaman tidak ditemukan',
        description: 'Halaman yang Anda cari tidak ada atau sudah dipindahkan.',
    },
    419: {
        title: 'Halaman kedaluwarsa',
        description: 'Sesi Anda berakhir. Muat ulang halaman lalu coba lagi.',
    },
    500: {
        title: 'Terjadi kesalahan',
        description: 'Terjadi kesalahan tak terduga. Coba lagi sebentar lagi.',
    },
    503: {
        title: 'Layanan tidak tersedia',
        description:
            'Layanan sedang tidak tersedia sementara. Coba lagi sebentar lagi.',
    },
};

/** Shown for 4xx/5xx responses, full-screen. Always offers a way out, so nobody is stuck on a dead end. */
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
            <main className="flex min-h-screen flex-col items-center justify-center bg-canvas px-4 py-10 text-center">
                <div className="mb-8 flex items-center gap-2">
                    <span className="flex size-8 items-center justify-center rounded-lg bg-primary text-sm font-bold text-white">
                        S
                    </span>
                    <span className="text-base font-semibold">SIPEBRI</span>
                </div>

                <p className="text-8xl leading-none font-bold tracking-tight text-primary/20 select-none sm:text-9xl">
                    {status}
                </p>
                <h1 className="mt-4 text-2xl font-semibold">{text.title}</h1>
                <p className="mt-2 max-w-md text-sm text-muted">
                    {message || text.description}
                </p>

                <div className="mt-6 flex flex-wrap justify-center gap-2">
                    <Button
                        variant="outline"
                        onClick={() => window.history.back()}
                    >
                        <ArrowLeft /> Kembali
                    </Button>
                    {user ? (
                        <Button onClick={() => router.visit(dashboard().url)}>
                            <Home /> Ke beranda
                        </Button>
                    ) : (
                        <Button onClick={() => router.visit(login().url)}>
                            <LogIn /> Masuk
                        </Button>
                    )}
                </div>

                {user && (
                    <p className="mt-8 text-xs text-muted">
                        Masuk sebagai {user.name}
                        {user.role ? ` · ${user.role}` : ''} ·{' '}
                        <button
                            type="button"
                            className="cursor-pointer text-primary hover:underline"
                            onClick={() => router.post(logout().url)}
                        >
                            Keluar
                        </button>
                    </p>
                )}
            </main>
        </>
    );
}
