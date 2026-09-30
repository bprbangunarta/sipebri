export type User = {
    id: number;
    name: string;
    email: string;
    role: string | null;
    permissions: string[];
};

export type Auth = {
    user: User;
};

export type NavLinkItem = {
    label: string;
    icon: string | null;
    href: string;
    group: string | null;
};

export type NavItem =
    | NavLinkItem
    | { label: string; icon: string; children: NavLinkItem[] };

export type NavSection = { label: string | null; items: NavItem[] };

export type NotificationItem = {
    id: number;
    title: string;
    body: string | null;
    level: string;
    url: string | null;
    read: boolean;
    time: string;
};

export type SecuritySummary = {
    enabled: boolean;
    method: 'totp' | 'email' | null;
};
