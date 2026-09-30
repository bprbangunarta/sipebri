import { createInertiaApp } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';

const appName = import.meta.env.VITE_APP_NAME || 'SIPEBRI';

void createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    // Login has no layout; the layout needs a signed-in user, so error pages of guests have none either.
    layout: (name, page) =>
        name.startsWith('auth/') || !page.props.auth ? null : AppLayout,
    progress: {
        color: '#4B5563',
    },
});
