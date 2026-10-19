import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { afterEach, describe, expect, it, vi } from 'vitest';
import { ErrorScreen } from '@/components/error-screen';

afterEach(() => vi.unstubAllGlobals());

describe('ErrorScreen', () => {
    it('says what happened in words and offers a way out for a signed-in person', () => {
        render(<ErrorScreen status={404} signedIn />);

        expect(screen.getByRole('heading', { name: 'Halaman tidak ditemukan' })).toBeInTheDocument();
        expect(screen.getByText('Kode 404')).toBeInTheDocument();
        expect(screen.getByRole('link', { name: 'Ke beranda' })).toHaveAttribute('href', '/');
    });

    it('sends a guest to sign in, and offers to try again on a server error', () => {
        const { unmount } = render(<ErrorScreen status={401} signedIn={false} />);
        expect(screen.getByRole('link', { name: 'Masuk' })).toHaveAttribute('href', '/login');
        unmount();

        render(<ErrorScreen status={500} signedIn />);
        expect(screen.getByRole('button', { name: 'Coba lagi' })).toBeInTheDocument();
    });

    it('goes back when there is a page before this one, and to the start page when there is none', async () => {
        const back = vi.fn();
        const assign = vi.fn();
        const user = userEvent.setup();
        render(<ErrorScreen status={404} signedIn />);

        vi.stubGlobal('history', { length: 3, back });
        vi.stubGlobal('location', { ...window.location, assign });
        await user.click(screen.getByRole('button', { name: 'Kembali' }));

        expect(back).toHaveBeenCalledTimes(1);
        expect(assign).not.toHaveBeenCalled();

        vi.stubGlobal('history', { length: 1, back });
        await user.click(screen.getByRole('button', { name: 'Kembali' }));

        expect(assign).toHaveBeenCalledWith('/');
        expect(back).toHaveBeenCalledTimes(1);
    });

    it('uses the words of the shared list for the status, and the server text when it brings its own', () => {
        render(<ErrorScreen status={403} message="Hanya pemilik berkas yang boleh membuka halaman ini." signedIn />);

        expect(screen.getByText('Hanya pemilik berkas yang boleh membuka halaman ini.')).toBeInTheDocument();
    });
});
