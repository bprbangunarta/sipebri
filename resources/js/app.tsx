import { createInertiaApp } from '@inertiajs/react';
import { ErrorBoundary } from '@/components/error-boundary';
import AppLayout from '@/layouts/app-layout';

const appName = import.meta.env.VITE_APP_NAME || 'SIPEBRI';

void createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    // Login and error pages are full-screen, without the admin layout; the layout needs a signed-in user anyway.
    layout: (name, page) => (name.startsWith('auth/') || name === 'error' || !page.props.auth ? null : AppLayout),
    // A render error in one screen shows the error screen instead of a blank page.
    withApp: (app) => <ErrorBoundary>{app}</ErrorBoundary>,
    progress: {
        // The progress bar takes the theme token, so it follows the primary colour.
        color: 'var(--color-primary)',
    },
});
