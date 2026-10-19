import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import { NotificationBell } from '@/components/notification-bell';

const post = vi.hoisted(() => vi.fn());
const visit = vi.hoisted(() => vi.fn());
const page = vi.hoisted(() => ({ notifications: { unread: 2, items: [] as unknown[] } }));

vi.mock('@inertiajs/react', () => ({
    usePage: () => ({ props: page }),
    router: { post, visit },
    Link: ({
        href,
        onClick,
        children,
        className,
    }: {
        href: string;
        onClick?: (event: React.MouseEvent) => void;
        children: React.ReactNode;
        className?: string;
    }) => (
        <a
            href={href}
            className={className}
            onClick={(event) => {
                onClick?.(event);
                if (!event.defaultPrevented) {
                    visit(href);
                }
                event.preventDefault();
            }}
        >
            {children}
        </a>
    ),
}));

const item = (over: Record<string, unknown>) => ({
    id: 1,
    title: 'Berkas baru',
    body: null,
    level: 'info',
    url: '/loan-applications/5',
    read: false,
    time: '3 menit yang lalu',
    ...over,
});

async function openBell() {
    const user = userEvent.setup();
    render(<NotificationBell />);
    await user.click(screen.getByRole('button', { name: /Notifikasi/ }));

    return user;
}

beforeEach(() => {
    post.mockReset();
    visit.mockReset();
    page.notifications = {
        unread: 2,
        items: [item({}), item({ id: 2, title: 'Sudah dibaca', read: true }), item({ id: 3, title: 'Tanpa tautan', url: null })],
    };
});

describe('NotificationBell', () => {
    it('names the bell with how many are unread', () => {
        render(<NotificationBell />);

        expect(screen.getByRole('button', { name: 'Notifikasi (2 belum dibaca)' })).toBeInTheDocument();
    });

    it('marks an unread notification read first and opens its page only when that is done', async () => {
        const user = await openBell();

        await user.click(screen.getByRole('link', { name: /Berkas baru/ }));

        expect(post).toHaveBeenCalledWith('/notifications/1/read', {}, expect.objectContaining({ onFinish: expect.any(Function) }));
        expect(visit).not.toHaveBeenCalled();

        post.mock.calls[0][2].onFinish();

        expect(visit).toHaveBeenCalledWith('/loan-applications/5');
    });

    it('opens a notification that is already read straight away, without marking anything', async () => {
        const user = await openBell();

        await user.click(screen.getByRole('link', { name: /Sudah dibaca/ }));

        expect(post).not.toHaveBeenCalled();
        expect(visit).toHaveBeenCalledWith('/loan-applications/5');
    });

    it('leaves a click with the control key to the browser, so a new tab is not cancelled by a visit', async () => {
        const user = await openBell();

        await user.keyboard('{Control>}');
        await user.click(screen.getByRole('link', { name: /Berkas baru/ }));
        await user.keyboard('{/Control}');

        expect(post).not.toHaveBeenCalled();
    });

    it('marks a notification without a link read when it is pressed', async () => {
        const user = await openBell();

        await user.click(screen.getByRole('button', { name: /Tanpa tautan/ }));

        expect(post).toHaveBeenCalledWith('/notifications/3/read', {}, expect.anything());
    });

    it('tells a screen reader which ones are unread, not only by a dot', async () => {
        await openBell();

        expect(screen.getByRole('link', { name: /Belum dibaca\. Berkas baru/ })).toBeInTheDocument();
        expect(screen.getByRole('link', { name: /^Sudah dibaca/ })).toBeInTheDocument();
    });

    it('says so when there is nothing', async () => {
        page.notifications = { unread: 0, items: [] };
        await openBell();

        expect(screen.getByText('Tidak ada notifikasi baru.')).toBeInTheDocument();
    });
});
