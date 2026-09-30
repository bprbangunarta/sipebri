import { Link, router, usePage } from '@inertiajs/react';
import {
    ChevronDown,
    LogOut,
    Menu,
    ShieldAlert,
    UserRound,
    X,
} from 'lucide-react';
import type { LucideIcon } from 'lucide-react';
import type { ReactNode } from 'react';
import { useEffect, useState } from 'react';
import { Toaster, toast } from 'sonner';
import { NetworkStatus } from '@/components/network-status';
import { Button } from '@/components/ui/button';
import {
    DropdownContent,
    DropdownItem,
    DropdownLabel,
    DropdownMenu,
    DropdownSeparator,
    DropdownTrigger,
} from '@/components/ui/dropdown';
import { navIcon } from '@/components/nav-icons';
import { NotificationBell } from '@/components/notification-bell';
import { TooltipProvider } from '@/components/ui/tooltip';
import type { NavLinkItem } from '@/types/auth';
import { cn } from '@/lib/utils';
import { dashboard, logout } from '@/routes';

function NavLink({
    href,
    icon: Icon,
    children,
    nested,
    onNavigate,
}: {
    href: string;
    icon: LucideIcon;
    children: ReactNode;
    nested?: boolean;
    onNavigate: () => void;
}) {
    const { url } = usePage();
    const path = url.split('?')[0];
    const active = path === href || path.startsWith(`${href}/`);

    return (
        <Link
            href={href}
            onClick={onNavigate}
            aria-current={active ? 'page' : undefined}
            className={cn(
                'flex h-8 items-center gap-2 rounded-md px-2.5 text-sm font-medium text-muted transition-colors hover:bg-canvas hover:text-ink [&_svg]:size-4',
                nested && 'ml-5 h-7',
                active &&
                    'bg-primary-soft text-primary hover:bg-primary-soft hover:text-primary',
            )}
        >
            <Icon />
            {children}
        </Link>
    );
}

function NavGroup({
    label,
    icon,
    children,
    onNavigate,
}: {
    label: string;
    icon: string;
    children: NavLinkItem[];
    onNavigate: () => void;
}) {
    const { url } = usePage();
    const active = children.some((item) => url.startsWith(item.href));
    const [open, setOpen] = useState(active);
    const expanded = open || active;
    const Icon = navIcon(icon);

    return (
        <>
            <button
                type="button"
                aria-expanded={expanded}
                onClick={() => setOpen((value) => !value)}
                className={cn(
                    'flex h-8 cursor-pointer items-center gap-2 rounded-md px-2.5 text-sm font-medium text-muted transition-colors hover:bg-canvas hover:text-ink',
                    active && 'text-ink',
                )}
            >
                <Icon className="size-4" />
                <span className="flex-1 text-left">{label}</span>
                <ChevronDown
                    className={cn(
                        'size-3.5 transition-transform',
                        expanded && 'rotate-180',
                    )}
                />
            </button>
            {expanded && (
                <div className="flex flex-col gap-0.5">
                    {children.map((item, index) => (
                        <div key={item.href} className="contents">
                            {item.group &&
                                item.group !== children[index - 1]?.group && (
                                    <p className="mt-1 ml-5 px-2.5 text-[11px] font-medium tracking-wide text-muted/80 uppercase">
                                        {item.group}
                                    </p>
                                )}
                            <NavLink
                                href={item.href}
                                icon={navIcon(item.icon)}
                                nested
                                onNavigate={onNavigate}
                            >
                                {item.label}
                            </NavLink>
                        </div>
                    ))}
                </div>
            )}
        </>
    );
}

function Sidebar({ onNavigate }: { onNavigate: () => void }) {
    const { navigation } = usePage().props;

    return (
        <nav
            className="flex flex-col gap-0.5 overflow-y-auto p-2"
            aria-label="Main"
        >
            {navigation.map((section, index) => (
                <div
                    key={section.label ?? index}
                    className="flex flex-col gap-0.5"
                >
                    {section.label && (
                        <p className="mt-2 px-2.5 pb-0.5 text-[11px] font-semibold tracking-wide text-muted/80 uppercase">
                            {section.label}
                        </p>
                    )}
                    {section.items.map((item) =>
                        'children' in item ? (
                            <NavGroup
                                key={item.label}
                                label={item.label}
                                icon={item.icon}
                                onNavigate={onNavigate}
                            >
                                {item.children}
                            </NavGroup>
                        ) : (
                            <NavLink
                                key={item.href}
                                href={item.href}
                                icon={navIcon(item.icon)}
                                onNavigate={onNavigate}
                            >
                                {item.label}
                            </NavLink>
                        ),
                    )}
                </div>
            ))}
        </nav>
    );
}

function Brand() {
    return (
        <Link
            href={dashboard().url}
            className="flex items-center gap-2 text-sm font-semibold"
        >
            <span className="flex size-6 items-center justify-center rounded bg-primary text-xs font-bold text-white">
                S
            </span>
            SIPEBRI
        </Link>
    );
}

export default function AppLayout({ children }: { children: ReactNode }) {
    const { auth, flash, security } = usePage().props;
    const [mobileOpen, setMobileOpen] = useState(false);

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
            <div className="flex min-h-screen">
                <aside className="sticky top-0 hidden h-screen w-60 shrink-0 flex-col border-r border-line bg-surface lg:flex">
                    <div className="flex h-12 items-center border-b border-line px-4">
                        <Brand />
                    </div>
                    <Sidebar onNavigate={() => undefined} />
                </aside>

                {mobileOpen && (
                    <div className="fixed inset-0 z-40 lg:hidden">
                        <div
                            className="absolute inset-0 bg-ink/40"
                            onClick={() => setMobileOpen(false)}
                        />
                        <aside className="relative h-full w-64 bg-surface shadow-xl">
                            <div className="flex h-12 items-center justify-between border-b border-line px-4">
                                <Brand />
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    aria-label="Close menu"
                                    onClick={() => setMobileOpen(false)}
                                >
                                    <X />
                                </Button>
                            </div>
                            <Sidebar onNavigate={() => setMobileOpen(false)} />
                        </aside>
                    </div>
                )}

                <div className="flex min-w-0 flex-1 flex-col">
                    <header className="sticky top-0 z-30 flex h-12 items-center justify-between border-b border-line bg-surface px-3 sm:px-5">
                        <Button
                            variant="ghost"
                            size="icon"
                            className="lg:hidden"
                            aria-label="Open menu"
                            onClick={() => setMobileOpen(true)}
                        >
                            <Menu />
                        </Button>
                        <div className="hidden lg:block" />
                        <div className="flex items-center gap-1">
                            <NotificationBell />
                            <DropdownMenu>
                                <DropdownTrigger asChild>
                                    <button
                                        type="button"
                                        className="flex cursor-pointer items-center gap-2 rounded-md px-1.5 py-1 hover:bg-canvas"
                                    >
                                        <span className="flex size-6 items-center justify-center rounded-full bg-primary-soft text-xs font-semibold text-primary">
                                            {auth.user.name
                                                .charAt(0)
                                                .toUpperCase()}
                                        </span>
                                        <span className="hidden text-sm font-medium sm:block">
                                            {auth.user.name}
                                        </span>
                                        <ChevronDown className="size-3.5 text-muted" />
                                    </button>
                                </DropdownTrigger>
                                <DropdownContent>
                                    <DropdownLabel className="px-2 py-1.5 text-xs text-muted">
                                        {auth.user.email}
                                        {auth.user.role && (
                                            <span className="block text-[11px]">
                                                {auth.user.role}
                                            </span>
                                        )}
                                    </DropdownLabel>
                                    <DropdownSeparator />
                                    <DropdownItem
                                        icon={<UserRound />}
                                        onSelect={() =>
                                            router.visit('/profile')
                                        }
                                    >
                                        Profile
                                    </DropdownItem>
                                    <DropdownItem
                                        icon={<LogOut />}
                                        onSelect={() =>
                                            router.post(logout().url)
                                        }
                                    >
                                        Log out
                                    </DropdownItem>
                                </DropdownContent>
                            </DropdownMenu>
                        </div>
                    </header>
                    {security?.enabled && security.method === null && (
                        <div
                            role="status"
                            className="flex items-center gap-2 border-b border-amber-200 bg-amber-50 px-3 py-1.5 text-xs text-amber-800 sm:px-5"
                        >
                            <ShieldAlert className="size-3.5 shrink-0" />
                            <span className="min-w-0 flex-1 truncate">
                                Your account is not protected by two-factor
                                authentication yet.
                            </span>
                            <Link
                                href="/profile"
                                className="shrink-0 font-medium underline underline-offset-2 hover:no-underline"
                            >
                                Turn it on
                            </Link>
                        </div>
                    )}
                    <main className="flex-1 p-3 sm:p-5">{children}</main>
                </div>
            </div>
            <NetworkStatus />
            <Toaster
                position="top-right"
                richColors
                closeButton
                toastOptions={{ style: { fontSize: '0.8125rem' } }}
            />
        </TooltipProvider>
    );
}
