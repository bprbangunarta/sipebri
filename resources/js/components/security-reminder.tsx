import { Link, router } from '@inertiajs/react';
import { ShieldAlert, X } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Tip } from '@/components/ui/tooltip';
import type { SecuritySummary } from '@/types/auth';

/**
 * The bar under the header that asks a person to turn on two-step verification. It can be closed; the server remembers that until the
 * person signs out, so it is back at the next sign-in and does not take the height of every screen in the meantime.
 */
export function SecurityReminder({ security }: { security: SecuritySummary | null }) {
    if (!security?.enabled || security.method !== null || security.reminderDismissed) {
        return null;
    }

    return (
        <div role="status" className="flex items-center gap-2 border-b border-warning-line bg-warning-soft py-1.5 pr-1.5 pl-3 text-xs text-warning-ink sm:pl-5">
            <ShieldAlert className="size-3.5 shrink-0" />
            <span className="min-w-0 flex-1 truncate">Akun Anda belum dilindungi verifikasi dua langkah.</span>
            <Link href="/profile" className="shrink-0 font-medium underline underline-offset-2 hover:no-underline">
                Aktifkan
            </Link>
            <Tip label="Tutup pengingat">
                <Button
                    variant="ghost"
                    size="icon"
                    className="-my-1.5 text-warning-ink hover:bg-warning-line/40"
                    aria-label="Tutup pengingat"
                    onClick={() => router.post('/profile/two-factor/reminder/dismiss', {}, { preserveScroll: true, preserveState: true })}
                >
                    <X />
                </Button>
            </Tip>
        </div>
    );
}
