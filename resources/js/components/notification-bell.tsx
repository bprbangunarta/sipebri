import { Link, router, usePage } from '@inertiajs/react';
import { Bell, CheckCheck } from 'lucide-react';
import { Button } from '@/components/ui/button';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { cn } from '@/lib/utils';

export function NotificationBell() {
    const { unread, items } = usePage().props.notifications;

    return (
        <Popover>
            <PopoverTrigger asChild>
                <Button
                    variant="ghost"
                    size="icon"
                    aria-label={`Notifikasi${unread ? ` (${unread} belum dibaca)` : ''}`}
                    className="relative"
                >
                    <Bell />
                    {unread > 0 && (
                        <span className="absolute -top-0.5 -right-0.5 flex min-w-4 items-center justify-center rounded-full bg-danger px-1 text-[10px] leading-4 font-semibold text-white">
                            {unread > 9 ? '9+' : unread}
                        </span>
                    )}
                </Button>
            </PopoverTrigger>
            <PopoverContent align="end" className="w-80 p-0">
                <div className="flex items-center justify-between border-b border-line px-3 py-2">
                    <p className="text-sm font-semibold">Notifikasi</p>
                    {unread > 0 && (
                        <button
                            type="button"
                            onClick={() =>
                                router.post(
                                    '/notifications/read-all',
                                    {},
                                    { preserveScroll: true },
                                )
                            }
                            className="flex cursor-pointer items-center gap-1 text-xs text-primary hover:underline"
                        >
                            <CheckCheck className="size-3.5" /> Tandai semua
                            dibaca
                        </button>
                    )}
                </div>
                {items.length === 0 ? (
                    <p className="px-3 py-8 text-center text-xs text-muted">
                        Tidak ada notifikasi baru.
                    </p>
                ) : (
                    <ul className="max-h-80 divide-y divide-line overflow-auto">
                        {items.map((n) => {
                            const content = (
                                <>
                                    <div className="flex items-start gap-2">
                                        {!n.read && (
                                            <span className="mt-1.5 size-1.5 shrink-0 rounded-full bg-primary" />
                                        )}
                                        <div className="min-w-0">
                                            <p
                                                className={cn(
                                                    'text-sm',
                                                    !n.read && 'font-medium',
                                                )}
                                            >
                                                {n.title}
                                            </p>
                                            {n.body && (
                                                <p className="text-xs text-muted">
                                                    {n.body}
                                                </p>
                                            )}
                                            <p className="mt-0.5 text-[11px] text-muted">
                                                {n.time}
                                            </p>
                                        </div>
                                    </div>
                                </>
                            );

                            return (
                                <li key={n.id} className="hover:bg-canvas">
                                    {n.url ? (
                                        <Link
                                            href={n.url}
                                            className="block px-3 py-2"
                                            onClick={() =>
                                                !n.read &&
                                                router.post(
                                                    `/notifications/${n.id}/read`,
                                                    {},
                                                    {
                                                        preserveState: true,
                                                        preserveScroll: true,
                                                    },
                                                )
                                            }
                                        >
                                            {content}
                                        </Link>
                                    ) : (
                                        <button
                                            type="button"
                                            className="block w-full cursor-pointer px-3 py-2 text-left"
                                            onClick={() =>
                                                !n.read &&
                                                router.post(
                                                    `/notifications/${n.id}/read`,
                                                    {},
                                                    { preserveScroll: true },
                                                )
                                            }
                                        >
                                            {content}
                                        </button>
                                    )}
                                </li>
                            );
                        })}
                    </ul>
                )}
            </PopoverContent>
        </Popover>
    );
}
