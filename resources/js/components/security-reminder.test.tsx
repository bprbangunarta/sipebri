import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import { SecurityReminder } from '@/components/security-reminder';

const post = vi.hoisted(() => vi.fn());

vi.mock('@inertiajs/react', () => ({
    router: { post },
    Link: ({ href, children }: { href: string; children: React.ReactNode }) => <a href={href}>{children}</a>,
}));

const security = { enabled: true, method: null, reminderDismissed: false } as const;

beforeEach(() => post.mockReset());

describe('SecurityReminder', () => {
    it('asks for two-step verification with a link to the profile', () => {
        render(<SecurityReminder security={security} />);

        expect(screen.getByRole('status')).toHaveTextContent('Akun Anda belum dilindungi verifikasi dua langkah.');
        expect(screen.getByRole('link', { name: 'Aktifkan' })).toHaveAttribute('href', '/profile');
    });

    it('can be closed, and tells the server so the bar does not come back on the next page', async () => {
        const user = userEvent.setup();
        render(<SecurityReminder security={security} />);

        await user.click(screen.getByRole('button', { name: 'Tutup pengingat' }));

        expect(post).toHaveBeenCalledWith('/profile/two-factor/reminder/dismiss', {}, expect.objectContaining({ preserveScroll: true, preserveState: true }));
    });

    it.each([
        ['closed by the person', { ...security, reminderDismissed: true }],
        ['two-step already on (authenticator)', { ...security, method: 'totp' as const }],
        ['two-step already on (email)', { ...security, method: 'email' as const }],
        ['switched off for everyone by the administrator', { ...security, enabled: false }],
        ['nobody is signed in', null],
    ])('shows nothing when %s', (_name, value) => {
        const { container } = render(<SecurityReminder security={value} />);

        expect(container).toBeEmptyDOMElement();
    });
});
