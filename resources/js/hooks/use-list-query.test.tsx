import { act, renderHook } from '@testing-library/react';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { useListQuery } from '@/hooks/use-list-query';

const visit = vi.hoisted(() => vi.fn());
const listen = vi.hoisted(() => vi.fn());

vi.mock('@inertiajs/react', () => ({ router: { get: visit, on: listen } }));

type Filters = { search?: string; status?: string; sort?: string; direction?: string; per_page?: number };

const defaults = { sort: 'name', direction: 'asc', per_page: 15 };

function setup(filters: Filters = { search: '', sort: 'name', direction: 'asc', per_page: 15 }) {
    return renderHook(() => useListQuery<Filters>({ url: '/loan-applications', filters, defaults, only: ['loans', 'filters'] }));
}

function lastVisit() {
    const call = visit.mock.calls.at(-1);

    return { url: call?.[0], params: call?.[1], options: call?.[2] };
}

beforeEach(() => {
    vi.useFakeTimers();
    visit.mockReset();
    listen.mockReset();
    listen.mockReturnValue(() => undefined);
});

afterEach(() => {
    vi.useRealTimers();
});

describe('useListQuery', () => {
    it('waits for a pause in typing before asking the server, and asks once with the last text', () => {
        const { result } = setup();

        act(() => {
            result.current.onSearch('a');
        });
        act(() => {
            vi.advanceTimersByTime(200);
        });
        act(() => {
            result.current.onSearch('ab');
        });
        act(() => {
            vi.advanceTimersByTime(299);
        });
        expect(visit).not.toHaveBeenCalled();

        act(() => {
            vi.advanceTimersByTime(1);
        });

        expect(visit).toHaveBeenCalledTimes(1);
        expect(lastVisit().params).toEqual({ search: 'ab' });
        expect(result.current.search).toBe('ab');
    });

    it('trims the text it sends but keeps what the person typed in the box', () => {
        const { result } = setup();

        act(() => {
            result.current.onSearch('  budi  ');
        });
        act(() => {
            vi.advanceTimersByTime(300);
        });

        expect(lastVisit().params).toEqual({ search: 'budi' });
        expect(result.current.search).toBe('  budi  ');
    });

    it('keeps the URL clean: empty values and values equal to their default are left out', () => {
        const { result } = setup({ search: 'x', status: 'draft', sort: 'name', direction: 'asc', per_page: 15 });

        act(() => result.current.visit({ status: '', per_page: 15, sort: 'name' }));

        expect(lastVisit().params).toEqual({ search: 'x' });
    });

    it('sends a value that differs from its default, and goes back to the first page on a new filter', () => {
        const { result } = setup({ search: '', status: 'draft', sort: 'name', direction: 'asc', per_page: 15 });

        act(() => result.current.visit({ per_page: 50, sort: 'amount', direction: 'desc' }));

        expect(lastVisit().params).toEqual({ status: 'draft', per_page: 50, sort: 'amount', direction: 'desc' });
        expect(lastVisit().params).not.toHaveProperty('page');
    });

    it('goes to the page that was asked for', () => {
        const { result } = setup();

        act(() => result.current.visit({ page: 3 }));

        expect(lastVisit().params).toEqual({ page: 3 });
    });

    it('asks only for the listed props, without losing the place on the page or filling the history', () => {
        const { result } = setup();

        act(() => result.current.visit({ status: 'draft' }));

        expect(lastVisit().url).toBe('/loan-applications');
        expect(lastVisit().options).toMatchObject({ only: ['loans', 'filters'], preserveState: true, preserveScroll: true, replace: true });
    });

    it('is loading from the start of a visit to its end, and a new visit clears an old error', () => {
        const { result } = setup();
        act(() => result.current.visit({ status: 'draft' }));
        const { onStart, onFinish } = lastVisit().options;

        act(() => {
            onStart();
        });
        expect(result.current.loading).toBe(true);

        act(() => {
            onFinish();
        });
        expect(result.current.loading).toBe(false);
    });

    it('shows a message when the server cannot be reached, and clears it when the next visit starts', () => {
        let onNetworkError: () => void = () => undefined;
        listen.mockImplementation((_event: string, handler: () => void) => {
            onNetworkError = handler;

            return () => undefined;
        });
        const { result } = setup();

        act(() => {
            onNetworkError();
        });
        expect(result.current.error).toBe('Server tidak dapat dihubungi. Periksa koneksi Anda lalu coba lagi.');

        act(() => result.current.visit({ status: 'draft' }));
        act(() => {
            lastVisit().options.onStart();
        });
        expect(result.current.error).toBeNull();
    });

    it('"Atur ulang" empties the box, drops the other filters given, and returns to the default order', () => {
        const { result } = setup({ search: 'budi', status: 'draft', sort: 'amount', direction: 'desc', per_page: 15 });

        act(() => {
            result.current.onSearch('budi');
        });
        act(() => result.current.clear({ status: '' }));

        expect(result.current.search).toBe('');
        expect(lastVisit().params).toEqual({});
    });

    it('knows when the list is not in its default order, so "Atur ulang" has something to undo', () => {
        expect(setup({ sort: 'name', direction: 'asc' }).result.current.sorted).toBe(false);
        expect(setup({ sort: 'amount', direction: 'asc' }).result.current.sorted).toBe(true);
        expect(setup({ sort: 'name', direction: 'desc' }).result.current.sorted).toBe(true);
        expect(setup({}).result.current.sorted).toBe(false);
    });

    it('does not ask the server after the page is left', () => {
        const { result, unmount } = setup();

        act(() => {
            result.current.onSearch('budi');
        });
        unmount();
        act(() => {
            vi.advanceTimersByTime(1000);
        });

        expect(visit).not.toHaveBeenCalled();
    });

    it('stops listening for network errors when the page is left', () => {
        const stop = vi.fn();
        listen.mockReturnValue(stop);
        const { unmount } = setup();

        unmount();

        expect(stop).toHaveBeenCalled();
    });
});
