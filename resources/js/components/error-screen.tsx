import { ArrowLeft, Home, LogIn, RotateCw } from 'lucide-react';
import type { ReactNode } from 'react';
import { BrandMark } from '@/components/brand-mark';
import { Illustration } from '@/components/illustrations';
import { buttonVariants } from '@/components/ui/button';
import { errorPage } from '@/lib/error-pages';
import { cn } from '@/lib/utils';

/**
 * A full-screen error, outside the app shell: an illustration, what happened in words, and a way out. It uses plain links and `location`,
 * never the Inertia router, because it also shows when rendering itself failed (ErrorBoundary).
 */
export function ErrorScreen({ status, message, signedIn, footer }: { status: number; message?: string; signedIn: boolean; footer?: ReactNode }) {
    const spec = errorPage(status);
    const action = spec.action === 'login' && signedIn ? 'home' : spec.action;

    return (
        <div className="flex min-h-dvh flex-col bg-canvas px-4 pt-6 pb-8">
            <BrandMark className="justify-center" />
            <main className="mx-auto flex w-full max-w-md flex-1 flex-col items-center justify-center py-6 text-center">
                <Illustration name={spec.illustration} className="w-80 max-w-full sm:w-96" />
                <p className="mt-2 rounded-full bg-primary-soft px-2.5 py-0.5 text-2xs font-semibold tracking-wide text-primary uppercase">
                    Kode {spec.status}
                </p>
                <h1 className="mt-3 text-base font-semibold">{spec.title}</h1>
                <p className="mt-2 max-w-sm text-sm text-muted">{message || spec.description}</p>
                <div className="mt-6 grid w-full grid-cols-1 gap-2 sm:w-auto sm:auto-cols-fr sm:grid-flow-col">
                    <button type="button" onClick={() => window.history.back()} className={cn(buttonVariants({ variant: 'outline' }))}>
                        <ArrowLeft /> Kembali
                    </button>
                    {action === 'retry' ? (
                        <button type="button" onClick={() => window.location.reload()} className={cn(buttonVariants())}>
                            <RotateCw /> Coba lagi
                        </button>
                    ) : (
                        <a href={action === 'login' || !signedIn ? '/login' : '/'} className={cn(buttonVariants())}>
                            {action === 'login' || !signedIn ? <LogIn /> : <Home />}
                            {action === 'login' || !signedIn ? 'Masuk' : 'Ke beranda'}
                        </a>
                    )}
                </div>
                {footer}
            </main>
        </div>
    );
}
