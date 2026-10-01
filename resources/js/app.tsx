import { createInertiaApp } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';

const appName = import.meta.env.VITE_APP_NAME || 'SIPEBRI';

void createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    // Login and error pages are full-screen, without the admin layout; the layout needs a signed-in user anyway.
    layout: (name, page) =>
        name.startsWith('auth/') || name === 'error' || !page.props.auth
            ? null
            : AppLayout,
    progress: {
        color: '#4B5563',
    },
});
