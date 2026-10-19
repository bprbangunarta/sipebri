import { Link, router, usePage } from '@inertiajs/react';
import { Dialog } from 'radix-ui';
import { ChevronDown, LogOut, Menu, ShieldAlert, UserRound, X } from 'lucide-react';
import type { ReactNode } from 'react';
import { useEffect, useId, useState } from 'react';
import { Toaster, toast } from 'sonner';
import { Avatar } from '@/components/avatar';
import { NetworkStatus } from '@/components/network-status';
import { Button } from '@/components/ui/button';
import { DropdownContent, DropdownItem, DropdownLabel, DropdownMenu, DropdownSeparator, DropdownTrigger } from '@/components/ui/dropdown';
import { navIcon } from '@/components/nav-icons';
import { NotificationBell } from '@/components/notification-bell';
import { overlayClass, useRestoreFocus } from '@/components/ui/dialog';
import { TooltipProvider } from '@/components/ui/tooltip';
import type { NavLinkItem } from '@/types/auth';
import { cn } from '@/lib/utils';
import { dashboard, logout } from '@/routes';

const rowClass =
    'flex h-8 w-full items-center gap-2 rounded-md px-2.5 text-sm font-medium text-muted transition-colors hover:bg-canvas hover:text-ink [&_svg]:size-4';

/** A menu link. Submenu entries (icon `dot`) get a plain dot instead of an icon, as in codex. */
function NavLink({ item, onNavigate }: { item: NavLinkItem; onNavigate: () => void }) {
    const active = useIsActive()(item.href);
    const plain = !item.icon || item.icon === 'dot';
    const Icon = navIcon(item.icon);

    return (
        <Link
            href={item.href}
            onClick={onNavigate}
            aria-current={active ? 'page' : undefined}
            className={cn(rowClass, plain && 'px-3', active && 'bg-primary-soft text-primary hover:bg-primary-soft hover:text-primary')}
        >
            {plain ? <span aria-hidden className="size-1 shrink-0 rounded-full bg-current opacity-60" /> : <Icon />}
            {item.label}
        </Link>
    );
}

/**
 * A menu item is active on its own page and on pages below it, unless another menu item matches the page more
 * specifically (e.g. /committees/members belongs to "Anggota", not to "/committees").
 */
function useIsActive() {
    const { url, props } = usePage();
    const path = url.split('?')[0];
    const hrefs = props.navigation.flatMap((section) =>
        section.items.flatMap((item) => ('children' in item ? item.children.map((child) => child.href) : [item.href])),
    );
    const matches = (href: string) => path === href || path.startsWith(`${href}/`);

    return (href: string) => matches(href) && !hrefs.some((other) => other.length > href.length && matches(other));
}

/** A parent entry with its submenu (codex pattern). It is open while one of its pages is shown, until the person closes it; otherwise closed until opened. */
function SubMenu({ label, icon, children, onNavigate }: { label: string; icon: string; children: NavLinkItem[]; onNavigate: () => void }) {
    const isActive = useIsActive();
    const id = useId();
    const holdsCurrentPage = children.some((item) => isActive(item.href));
    const [toggled, setToggled] = useState<boolean | null>(null);
    const open = toggled ?? holdsCurrentPage;
    const Icon = navIcon(icon);

    return (
        <div className="flex flex-col gap-0.5">
            <button
                type="button"
                aria-expanded={open}
                aria-controls={id}
                onClick={() => setToggled(!open)}
                className={cn(rowClass, 'cursor-pointer', holdsCurrentPage && 'text-ink')}
            >
                <Icon />
                {label}
                <ChevronDown className={cn('ml-auto transition-transform', open && 'rotate-180')} />
            </button>
            {open && (
                <div id={id} className="ml-[1.125rem] flex flex-col gap-0.5 border-l border-line">
                    {children.map((item, index) => (
                        <div key={item.href} className="contents">
                            {item.group && item.group !== children[index - 1]?.group && (
                                <p className="mt-1 px-3 text-2xs font-medium tracking-wide text-muted/80 uppercase">{item.group}</p>
                            )}
                            <NavLink item={item} onNavigate={onNavigate} />
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}

function Sidebar({ onNavigate }: { onNavigate: () => void }) {
    const { navigation } = usePage().props;

    return (
        <nav className="flex flex-col gap-0.5 overflow-y-auto p-2" aria-label="Menu utama">
            {navigation.map((section, index) => (
                <div key={section.label ?? index} className="flex flex-col gap-0.5">
                    {section.label && <p className="mt-2 px-2.5 pb-0.5 text-2xs font-semibold tracking-wide text-muted/80 uppercase">{section.label}</p>}
                    {section.items.map((item) =>
                        'children' in item ? (
                            <SubMenu key={item.label} label={item.label} icon={item.icon} onNavigate={onNavigate}>
                                {item.children}
                            </SubMenu>
                        ) : (
                            <NavLink key={item.href} item={item} onNavigate={onNavigate} />
                        ),
                    )}
                </div>
            ))}
        </nav>
    );
}

function Brand() {
    return (
        <Link href={dashboard().url} className="flex items-center gap-2 text-sm font-semibold">
            <span className="flex size-6 items-center justify-center rounded bg-primary text-xs font-bold text-white">S</span>
            SIPEBRI
        </Link>
    );
}

export default function AppLayout({ children }: { children: ReactNode }) {
    const { auth, flash, security } = usePage().props;
    const [mobileOpen, setMobileOpen] = useState(false);
    const restoreFocus = useRestoreFocus(mobileOpen);

    // The drawer is for small screens: close it when the window grows past the desktop breakpoint, so its scroll lock does not linger.
    useEffect(() => {
        const desktop = window.matchMedia('(min-width: 1024px)');
        const close = () => desktop.matches && setMobileOpen(false);
        desktop.addEventListener('change', close);

        return () => desktop.removeEventListener('change', close);
    }, []);

    useEffect(() => {
        if (flash?.success) {
            toast.success(flash.success);
        }
        if (flash?.error) {
            toast.error(flash.error);
        }
    }, [flash]);

    return (
        <TooltipProvider delayDuration={200}>
            <a
                href="#content"
                className="sr-only z-50 rounded-md bg-surface px-3 py-2 text-sm font-medium text-primary shadow-md focus:not-sr-only focus:fixed focus:top-2 focus:left-2"
            >
                Lewati ke konten
            </a>
            <div className="flex min-h-screen">
                <aside className="sticky top-0 hidden h-screen w-60 shrink-0 flex-col border-r border-line bg-surface lg:flex">
                    <div className="flex h-12 items-center border-b border-line px-4">
                        <Brand />
                    </div>
                    <Sidebar onNavigate={() => undefined} />
                </aside>

                <Dialog.Root open={mobileOpen} onOpenChange={setMobileOpen}>
                    <Dialog.Portal>
                        <Dialog.Overlay className={cn(overlayClass, 'z-40 lg:hidden')} />
                        <Dialog.Content
                            aria-describedby={undefined}
                            {...restoreFocus}
                            className="overlay-fade fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-surface shadow-xl focus:outline-none lg:hidden"
                        >
                            <div className="flex h-12 items-center justify-between border-b border-line px-4">
                                <Dialog.Title className="sr-only">Menu navigasi</Dialog.Title>
                                <Brand />
                                <Dialog.Close asChild>
                                    <Button variant="ghost" size="icon" aria-label="Tutup menu">
                                        <X />
                                    </Button>
                                </Dialog.Close>
                            </div>
                            <Sidebar onNavigate={() => setMobileOpen(false)} />
                        </Dialog.Content>
                    </Dialog.Portal>
                </Dialog.Root>

                <div className="flex min-w-0 flex-1 flex-col">
                    <header className="sticky top-0 z-30 flex h-12 items-center justify-between border-b border-line bg-surface px-3 sm:px-5">
                        <Button variant="ghost" size="icon" className="lg:hidden" aria-label="Buka menu" onClick={() => setMobileOpen(true)}>
                            <Menu />
                        </Button>
                        <div className="hidden lg:block" />
                        <div className="flex items-center gap-1">
                            <NotificationBell />
                            <DropdownMenu>
                                <DropdownTrigger asChild>
                                    <button type="button" className="flex cursor-pointer items-center gap-2 rounded-md px-1.5 py-1 hover:bg-canvas">
                                        <Avatar name={auth.user.name} src={null} className="size-6 text-xs" />
                                        <span className="hidden text-sm font-medium sm:block">{auth.user.name}</span>
                                        <ChevronDown className="size-3.5 text-muted" />
                                    </button>
                                </DropdownTrigger>
                                <DropdownContent>
                                    <DropdownLabel className="px-2 py-1.5 text-xs text-muted">
                                        {auth.user.email}
                                        {auth.user.role && <span className="block text-2xs">{auth.user.role}</span>}
                                    </DropdownLabel>
                                    <DropdownSeparator />
                                    <DropdownItem icon={<UserRound />} onSelect={() => router.visit('/profile')}>
                                        Profil
                                    </DropdownItem>
                                    <DropdownItem icon={<LogOut />} onSelect={() => router.post(logout().url)}>
                                        Keluar
                                    </DropdownItem>
                                </DropdownContent>
                            </DropdownMenu>
                        </div>
                    </header>
                    {security?.enabled && security.method === null && (
                        <div
                            role="status"
                            className="flex items-center gap-2 border-b border-warning-line bg-warning-soft px-3 py-1.5 text-xs text-warning-ink sm:px-5"
                        >
                            <ShieldAlert className="size-3.5 shrink-0" />
                            <span className="min-w-0 flex-1 truncate">Akun Anda belum dilindungi verifikasi dua langkah.</span>
                            <Link href="/profile" className="shrink-0 font-medium underline underline-offset-2 hover:no-underline">
                                Aktifkan
                            </Link>
                        </div>
                    )}
                    <main id="content" tabIndex={-1} className="flex-1 p-3 outline-none sm:p-5">
                        {children}
                    </main>
                </div>
            </div>
            <NetworkStatus />
            <Toaster position="top-right" richColors closeButton toastOptions={{ style: { fontSize: '0.8125rem' } }} />
        </TooltipProvider>
    );
}
