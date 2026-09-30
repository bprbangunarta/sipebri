import {
    Banknote,
    Building2,
    Gavel,
    KeyRound,
    Map,
    Briefcase,
    Database,
    Heart,
    LayoutDashboard,
    Shield,
    UserCog,
    Users,
    UsersRound,
    Circle,
    Minus,
    CalendarDays,
    ClipboardCheck,
    FileText,
    MapPin,
    Landmark,
    Settings,
    ScrollText,
} from 'lucide-react';
import type { LucideIcon } from 'lucide-react';

const icons: Record<string, LucideIcon> = {
    dashboard: LayoutDashboard,
    users: Users,
    'users-round': UsersRound,
    heart: Heart,
    briefcase: Briefcase,
    database: Database,
    'user-cog': UserCog,
    shield: Shield,
    settings: Settings,
    dot: Minus,
    'file-text': FileText,
    calendar: CalendarDays,
    'clipboard-check': ClipboardCheck,
    banknote: Banknote,
    building: Building2,
    gavel: Gavel,
    'key-round': KeyRound,
    map: Map,
    'map-pin': MapPin,
    landmark: Landmark,
    'scroll-text': ScrollText,
};

/** Sidebar icons are named by the server; add new ones here. */
export function navIcon(name: string | null | undefined): LucideIcon {
    return (name && icons[name]) || Circle;
}
