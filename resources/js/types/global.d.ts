import type {
    Auth,
    NavSection,
    NotificationItem,
    SecuritySummary,
} from '@/types/auth';

declare module 'react' {
    interface InputHTMLAttributes<T> {
        passwordrules?: string;
    }
}

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            security: SecuritySummary | null;
            navigation: NavSection[];
            notifications: { unread: number; items: NotificationItem[] };
            flash: { success?: string | null; error?: string | null };
            [key: string]: unknown;
        };
    }
}
