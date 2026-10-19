import { act, render, screen } from '@testing-library/react';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { NetworkStatus } from '@/components/network-status';

const on = vi.hoisted(() => vi.fn());

vi.mock('@inertiajs/react', () => ({ router: { on } }));

const fetchMock = vi.fn();

const flush = async (ms = 0) => {
    await act(async () => {
        await vi.advanceTimersByTimeAsync(ms);
    });
};

beforeEach(() => {
    vi.useFakeTimers();
    fetchMock.mockReset();
    on.mockReset();
    on.mockReturnValue(() => undefined);
    vi.stubGlobal('fetch', fetchMock);
    Object.defineProperty(navigator, 'onLine', { value: true, configurable: true });
});

afterEach(() => {
    vi.useRealTimers();
    vi.unstubAllGlobals();
});

describe('NetworkStatus', () => {
    it('is silent while everything is fine', () => {
        render(<NetworkStatus />);

        expect(screen.queryByRole('status')).not.toBeInTheDocument();
    });

    it('says so, politely, as soon as the browser reports the connection is gone, and stays until the server answers again', async () => {
        fetchMock.mockRejectedValue(new TypeError('failed'));
        render(<NetworkStatus />);

        await act(async () => {
            window.dispatchEvent(new Event('offline'));
        });

        const pill = screen.getByRole('status');
        expect(pill).toHaveTextContent('Tidak ada koneksi. Perubahan belum bisa disimpan.');
        expect(pill).toHaveAttribute('aria-live', 'polite');

        // The browser says "online" but the server does not answer: still offline, and it keeps trying every few seconds.
        await act(async () => {
            window.dispatchEvent(new Event('online'));
        });
        await flush(5000);
        expect(screen.getByRole('status')).toHaveTextContent('Tidak ada koneksi');
        expect(fetchMock.mock.calls.length).toBeGreaterThanOrEqual(2);
    });

    it('says it is back only when the server answers, then goes quiet after a moment', async () => {
        fetchMock.mockResolvedValue({ ok: false, status: 503 });
        render(<NetworkStatus />);

        await act(async () => {
            window.dispatchEvent(new Event('offline'));
        });
        await act(async () => {
            window.dispatchEvent(new Event('online'));
        });
        await flush();

        // Any HTTP answer, even an error status, proves the network itself works.
        expect(screen.getByRole('status')).toHaveTextContent('Kembali online.');

        await flush(3000);
        expect(screen.queryByRole('status')).not.toBeInTheDocument();
    });

    it('checks the server when a request of the app fails on the network, and goes offline when it does not answer', async () => {
        fetchMock.mockRejectedValue(new TypeError('failed'));
        render(<NetworkStatus />);

        const [event, listener] = on.mock.calls[0];
        expect(event).toBe('networkError');

        await act(async () => {
            listener();
        });
        await flush();

        expect(screen.getByRole('status')).toHaveTextContent('Tidak ada koneksi');
    });

    it('starts offline when the browser already knows it', async () => {
        Object.defineProperty(navigator, 'onLine', { value: false, configurable: true });
        render(<NetworkStatus />);
        await flush();

        expect(screen.getByRole('status')).toHaveTextContent('Tidak ada koneksi');
    });
});
